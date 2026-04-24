#!/usr/bin/env bash
#
# Cron-friendly: update from GitHub, sync into the live Laravel tree, then run deploy steps.
#
# Cloudways layout: git checkout lives in ../git_repo next to public_html (NOT inside public_html).
# Set NOVEL_APP_ROOT to public_html (where artisan lives). Optionally set NOVEL_GIT_DIR to the
# git_repo path; default: $(dirname NOVEL_APP_ROOT)/git_repo
#
#   NOVEL_APP_ROOT     Required. Laravel project root (e.g. .../public_html).
#   NOVEL_GIT_DIR      Optional. Git working copy (e.g. .../git_repo). Default: sibling git_repo.
#   NOVEL_GIT_BRANCH   Tracked branch (default: Development).
#   NOVEL_GIT_REMOTE   Default: origin
#   NOVEL_DEPLOY_PROFILE  dev → dev_after_pull.sh | production → server_post_deploy.sh
#
# Example Application Cron (every 5 min) — dev app; log path must be writable:
#
#   NOVEL_APP_ROOT=/home/1611332.cloudwaysapps.com/ktdekqzmvx/public_html
#   NOVEL_GIT_DIR=/home/1611332.cloudwaysapps.com/ktdekqzmvx/git_repo
#   NOVEL_GIT_BRANCH=Development
#   NOVEL_DEPLOY_PROFILE=dev
#   */5 * * * * . "$HOME/.nvm/nvm.sh" 2>/dev/null; env NOVEL_APP_ROOT=... NOVEL_GIT_DIR=... NOVEL_GIT_BRANCH=Development NOVEL_DEPLOY_PROFILE=dev /bin/bash /home/.../public_html/scripts/deploy/cron_git_pull_deploy.sh >> /home/.../public_html/storage/logs/cron-deploy.log 2>&1
#
# If git fetch fails with "Permission denied" on .git, the repo is owned by root: run
#   sudo bash scripts/deploy/cloudways_all_envs_once.sh
# once, or run this cron as root, or chown the git_repo .git to the cron user.
#
set -euo pipefail

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z' 2>/dev/null || date)" "$*"
}

: "${NOVEL_APP_ROOT:?Set NOVEL_APP_ROOT to the Laravel project root (absolute path, e.g. .../public_html)}"

NOVEL_GIT_BRANCH="${NOVEL_GIT_BRANCH:-Development}"
NOVEL_GIT_REMOTE="${NOVEL_GIT_REMOTE:-origin}"
NOVEL_DEPLOY_PROFILE="${NOVEL_DEPLOY_PROFILE:-dev}"

APP_BASE="$(cd "$(dirname "$NOVEL_APP_ROOT")" && pwd)"
NOVEL_GIT_DIR="${NOVEL_GIT_DIR:-${APP_BASE}/git_repo}"

if [[ ! -d "${NOVEL_GIT_DIR}/.git" ]]; then
  log "ERROR: not a git repository: $NOVEL_GIT_DIR (set NOVEL_GIT_DIR to your Cloudways git_repo path)"
  exit 1
fi

LOCK_FILE="${NOVEL_DEPLOY_LOCK:-$NOVEL_APP_ROOT/storage/logs/cron-deploy.lock}"
mkdir -p "$(dirname "$LOCK_FILE")"
exec 200>"$LOCK_FILE"
if ! flock -n 200; then
  log "skip: another deploy is running (lock: $LOCK_FILE)"
  exit 0
fi

UPSTREAM="${NOVEL_GIT_REMOTE}/${NOVEL_GIT_BRANCH}"

log "git fetch in $NOVEL_GIT_DIR ($NOVEL_GIT_BRANCH)"
if ! git -C "$NOVEL_GIT_DIR" fetch "$NOVEL_GIT_REMOTE" "$NOVEL_GIT_BRANCH" 2>&1; then
  log "ERROR: git fetch failed — if you see 'Permission denied' on .git, run: sudo bash scripts/deploy/cloudways_all_envs_once.sh"
  exit 1
fi

if ! git -C "$NOVEL_GIT_DIR" rev-parse --verify "$UPSTREAM" >/dev/null 2>&1; then
  log "ERROR: missing $UPSTREAM after fetch — check branch name"
  exit 1
fi

LOCAL=$(git -C "$NOVEL_GIT_DIR" rev-parse HEAD)
REMOTE=$(git -C "$NOVEL_GIT_DIR" rev-parse "$UPSTREAM")

UP_TO_DATE=0
if [[ "$LOCAL" == "$REMOTE" ]]; then
  UP_TO_DATE=1
  log "up-to-date $(git -C "$NOVEL_GIT_DIR" rev-parse --short HEAD) ($NOVEL_GIT_BRANCH)"
  if [[ -f "$NOVEL_APP_ROOT/scripts/deploy/verify_release.sh" ]]; then
    if bash "$NOVEL_APP_ROOT/scripts/deploy/verify_release.sh" "$NOVEL_APP_ROOT"; then
      log "verify_release passed; skipping deploy work for unchanged commit"
      exit 0
    fi
    log "WARN: verify_release failed on unchanged commit; forcing rsync + deploy self-heal"
  else
    exit 0
  fi
else
  log "deploy ${NOVEL_GIT_BRANCH}: $(git -C "$NOVEL_GIT_DIR" rev-parse --short HEAD) -> $(git -C "$NOVEL_GIT_DIR" rev-parse --short "$REMOTE")"
  git -C "$NOVEL_GIT_DIR" checkout "$NOVEL_GIT_BRANCH"
  git -C "$NOVEL_GIT_DIR" merge --ff-only "$UPSTREAM"
fi

log "rsync git_repo -> public_html (preserving .env storage vendor)"
rsync -rt --no-perms --no-owner --no-group --omit-dir-times \
  --exclude ".env" \
  --exclude ".env.*" \
  --exclude "storage/" \
  --exclude "vendor/" \
  --exclude "node_modules/" \
  --exclude ".git/" \
  "${NOVEL_GIT_DIR}/" "${NOVEL_APP_ROOT}/"

run_deploy() {
  case "$NOVEL_DEPLOY_PROFILE" in
    dev)
      (cd "$NOVEL_APP_ROOT" && bash "$NOVEL_APP_ROOT/scripts/deploy/dev_after_pull.sh")
      ;;
    production)
      (cd "$NOVEL_APP_ROOT" && bash "$NOVEL_APP_ROOT/scripts/deploy/server_post_deploy.sh")
      ;;
    *)
      log "ERROR: NOVEL_DEPLOY_PROFILE must be 'dev' or 'production', got: $NOVEL_DEPLOY_PROFILE"
      exit 1
      ;;
  esac
}

if ! run_deploy; then
  log "ERROR: post-deploy steps failed; attempting emergency cache clear to keep app responsive"
  (
    cd "$NOVEL_APP_ROOT" && php artisan optimize:clear
  ) || log "WARN: emergency optimize:clear failed"
  exit 1
fi

if [[ "$UP_TO_DATE" -eq 1 ]]; then
  log "done (self-heal) $NOVEL_GIT_BRANCH=$(git -C "$NOVEL_GIT_DIR" rev-parse --short HEAD)"
else
  log "done $NOVEL_GIT_BRANCH=$(git -C "$NOVEL_GIT_DIR" rev-parse --short HEAD)"
fi
