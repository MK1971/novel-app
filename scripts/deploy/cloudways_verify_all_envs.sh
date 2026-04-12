#!/usr/bin/env bash
#
# Read-only: confirm each Cloudways app's git_repo branch/HEAD and that public_html matches git_repo.
# Run over SSH as the master user (no sudo needed if you can read git_repo):
#
#   bash /home/1611332.cloudwaysapps.com/ktdekqzmvx/git_repo/scripts/deploy/cloudways_verify_all_envs.sh
#
# Exit code 0 = all OK; 1 = missing path or git_repo/public_html mismatch on checks.
#
set -euo pipefail

ROOT_BASE="${CLOUDWAYS_APPS_ROOT:-/home/1611332.cloudwaysapps.com}"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%dT%H:%M:%S%z' 2>/dev/null || date)" "$*"; }

ERR=0

check_app() {
  local APP_ID="$1" EXPECT_BRANCH="$2"
  local GR="${ROOT_BASE}/${APP_ID}/git_repo"
  local PH="${ROOT_BASE}/${APP_ID}/public_html"

  echo ""
  echo "=== ${APP_ID} (expect branch: ${EXPECT_BRANCH}) ==="

  if [[ ! -d "${GR}/.git" ]]; then
    log "ERROR: missing git repo: $GR"
    ERR=1
    return
  fi
  if [[ ! -f "${PH}/artisan" ]]; then
    log "ERROR: missing Laravel root: $PH"
    ERR=1
    return
  fi

  git -C "$GR" fetch origin -q 2>/dev/null || true

  local BR
  BR="$(git -C "$GR" rev-parse --abbrev-ref HEAD 2>/dev/null || true)"
  if [[ "$BR" != "$EXPECT_BRANCH" ]]; then
    log "WARN: branch is '$BR' (expected '$EXPECT_BRANCH')"
    ERR=1
  else
    log "branch: $BR"
  fi

  git -C "$GR" log -1 --oneline
  echo "HEAD: $(git -C "$GR" rev-parse HEAD)"

  if cmp -s "${GR}/routes/web.php" "${PH}/routes/web.php" 2>/dev/null; then
    log "sync: routes/web.php matches git_repo → public_html"
  else
    log "ERROR: routes/web.php differs — run deploy (rsync + post-deploy)"
    ERR=1
  fi

  if [[ -f "${GR}/scripts/deploy/cron_git_pull_deploy.sh" ]] && [[ -f "${PH}/scripts/deploy/cron_git_pull_deploy.sh" ]]; then
    if cmp -s "${GR}/scripts/deploy/cron_git_pull_deploy.sh" "${PH}/scripts/deploy/cron_git_pull_deploy.sh" 2>/dev/null; then
      log "sync: cron_git_pull_deploy.sh matches"
    else
      log "WARN: cron_git_pull_deploy.sh differs between git_repo and public_html"
      ERR=1
    fi
  else
    log "WARN: cron script missing in git_repo or public_html"
  fi
}

log "Cloudways verify (ROOT_BASE=$ROOT_BASE)"
check_app ktdekqzmvx Development
check_app qnekzyfwpm staging
check_app cuwvnwmwrf Production

if [[ "$ERR" -ne 0 ]]; then
  log "Done with errors (see above). Deploy: sudo bash .../scripts/deploy/cloudways_all_envs_once.sh"
  exit 1
fi

log "All checks passed."
exit 0
