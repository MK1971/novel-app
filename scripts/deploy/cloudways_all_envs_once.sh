#!/usr/bin/env bash
#
# ONE command for Cloudways after you push to GitHub: pull all three app branches,
# rsync git_repo → public_html, then composer/migrate/build per environment.
#
# Run ONCE per maintenance window (SSH into the server). Easiest (one sudo):
#
#   bash /path/to/git_repo/scripts/deploy/cloudways_deploy_all.sh
#
# Or explicitly:
#
#   sudo bash /path/to/git_repo/scripts/deploy/cloudways_all_envs_once.sh
#
# Verify only (no deploy, no sudo): scripts/deploy/cloudways_verify_all_envs.sh
#
# If the script is not on disk yet, paste from GitHub or copy from git_repo after a panel Pull.
# Requires root so git can write to /path/to/.../git_repo/.git (Cloudways default ownership).
#
set -euo pipefail

if [[ "${EUID:-0}" -ne 0 ]]; then
  echo "This must run as root (Cloudways git repos are usually root-owned)."
  echo "Run exactly once:"
  echo "  sudo bash \"$0\""
  exit 1
fi

ROOT_BASE="${CLOUDWAYS_APPS_ROOT:-/home/1611332.cloudwaysapps.com}"
MASTER_USER="${CLOUDWAYS_SSH_USER:-master_bjgxzdupkt}"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z' 2>/dev/null || date)" "$*"; }

rsync_repo_to_public() {
  local GR="$1" PH="$2"
  rsync -rt --no-perms --no-owner --no-group --omit-dir-times \
    --exclude ".env" \
    --exclude ".env.*" \
    --exclude "storage/" \
    --exclude "vendor/" \
    --exclude "node_modules/" \
    --exclude ".git/" \
    "${GR}/" "${PH}/"
}

deploy_one() {
  local APP_ID="$1" BRANCH="$2" PROFILE="$3"
  local GR="${ROOT_BASE}/${APP_ID}/git_repo"
  local PH="${ROOT_BASE}/${APP_ID}/public_html"

  log "=== ${APP_ID} (branch=${BRANCH} profile=${PROFILE}) ==="
  if [[ ! -d "${GR}/.git" ]]; then
    log "ERROR: missing git repo: $GR"
    return 1
  fi
  if [[ ! -f "${PH}/artisan" ]]; then
    log "ERROR: missing Laravel root: $PH"
    return 1
  fi

  git -C "$GR" fetch origin
  git -C "$GR" checkout "$BRANCH"
  git -C "$GR" merge --ff-only "origin/${BRANCH}"

  rsync_repo_to_public "$GR" "$PH"

  if id -u "$MASTER_USER" &>/dev/null; then
    sudo -u "$MASTER_USER" -- bash -c "cd \"${PH}\" && if [[ \"${PROFILE}\" == dev ]]; then bash scripts/deploy/dev_after_pull.sh; else bash scripts/deploy/server_post_deploy.sh; fi"
  else
    log "WARN: user ${MASTER_USER} not found; running post-deploy as root (not ideal)"
    ( cd "$PH" && if [[ "${PROFILE}" == dev ]]; then bash scripts/deploy/dev_after_pull.sh; else bash scripts/deploy/server_post_deploy.sh; fi )
  fi

  log "done ${APP_ID} $(git -C "$GR" rev-parse --short HEAD)"
}

deploy_one ktdekqzmvx Development dev
deploy_one qnekzyfwpm staging production
deploy_one cuwvnwmwrf Production production

log "All environments processed. Cron can use scripts/deploy/cron_git_pull_deploy.sh with NOVEL_GIT_DIR set (see script header)."
