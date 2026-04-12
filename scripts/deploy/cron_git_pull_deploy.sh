#!/usr/bin/env bash
#
# Cron-friendly: fetch GitHub, fast-forward local branch if behind, then run deploy steps.
# Intended for Cloudways (or any Linux host) with deploy keys / SSH access to GitHub.
#
# Configure with environment variables (recommended in cron wrapper or systemd):
#
#   NOVEL_APP_ROOT     Required. Absolute path to the Laravel project root (where artisan lives).
#   NOVEL_GIT_BRANCH   Tracked branch for this server (default: Development).
#   NOVEL_GIT_REMOTE   Default: origin
#   NOVEL_DEPLOY_PROFILE  "dev" = scripts/deploy/dev_after_pull.sh
#                         "production" = scripts/deploy/server_post_deploy.sh (composer --no-dev, caches)
#
# Example crontab (every 5 minutes):
#
#   NOVEL_APP_ROOT=/home/master/applications/XXXX/public_html
#   NOVEL_GIT_BRANCH=Development
#   NOVEL_DEPLOY_PROFILE=dev
#   */5 * * * * . $HOME/.profile 2>/dev/null; env NOVEL_APP_ROOT=... NOVEL_GIT_BRANCH=Development NOVEL_DEPLOY_PROFILE=dev /bin/bash /path/to/novel-app/scripts/deploy/cron_git_pull_deploy.sh >> /path/to/novel-app/storage/logs/cron-deploy.log 2>&1
#
# Notes:
# - Use flock so overlapping cron runs do not stack composer/npm twice.
# - Ensure git on the server can fetch (SSH deploy key or HTTPS token); test: git fetch origin.
# - For production, set NOVEL_GIT_BRANCH=Production and NOVEL_DEPLOY_PROFILE=production.
#
set -euo pipefail

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z' 2>/dev/null || date)" "$*"
}

: "${NOVEL_APP_ROOT:?Set NOVEL_APP_ROOT to the Laravel project root (absolute path)}"

NOVEL_GIT_BRANCH="${NOVEL_GIT_BRANCH:-Development}"
NOVEL_GIT_REMOTE="${NOVEL_GIT_REMOTE:-origin}"
NOVEL_DEPLOY_PROFILE="${NOVEL_DEPLOY_PROFILE:-dev}"

cd "$NOVEL_APP_ROOT"

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  log "ERROR: not a git repository: $NOVEL_APP_ROOT"
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

log "fetch $NOVEL_GIT_REMOTE $NOVEL_GIT_BRANCH"
git fetch "$NOVEL_GIT_REMOTE" "$NOVEL_GIT_BRANCH"

if ! git rev-parse --verify "$UPSTREAM" >/dev/null 2>&1; then
  log "ERROR: missing $UPSTREAM after fetch — check branch name and remote"
  exit 1
fi

LOCAL=$(git rev-parse HEAD)
REMOTE=$(git rev-parse "$UPSTREAM")

if [[ "$LOCAL" == "$REMOTE" ]]; then
  log "up-to-date $LOCAL ($NOVEL_GIT_BRANCH)"
  exit 0
fi

log "deploy: $LOCAL -> $REMOTE ($NOVEL_GIT_BRANCH)"

# Ensure we are on the right branch (avoids detached HEAD surprises)
git checkout "$NOVEL_GIT_BRANCH"

# Fast-forward only — fails if local commits diverge (needs manual merge)
git merge --ff-only "$UPSTREAM"

run_deploy() {
  case "$NOVEL_DEPLOY_PROFILE" in
    dev)
      bash "$NOVEL_APP_ROOT/scripts/deploy/dev_after_pull.sh"
      ;;
    production)
      bash "$NOVEL_APP_ROOT/scripts/deploy/server_post_deploy.sh"
      ;;
    *)
      log "ERROR: NOVEL_DEPLOY_PROFILE must be 'dev' or 'production', got: $NOVEL_DEPLOY_PROFILE"
      exit 1
      ;;
  esac
}

run_deploy

log "done $NOVEL_GIT_BRANCH=$(git rev-parse --short HEAD)"
