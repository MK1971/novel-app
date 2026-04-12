#!/usr/bin/env bash
#
# Cloudways: git_repo is often root-owned; "Deployment → Pull" updates git_repo first.
# public_html may lag until the platform syncs — deploy helper scripts must exist in public_html
# for Application Cron (NOVEL_APP_ROOT points at public_html).
#
# Run on the server over SSH as the master user (same user that owns writes to public_html):
#
#   bash scripts/deploy/cloudways_copy_deploy_scripts.sh /home/1611332.cloudwaysapps.com/<APP_ID>
#
# Copies scripts/deploy/{cron_git_pull_deploy,dev_after_pull}.sh from <APP>/git_repo to <APP>/public_html.
# If your git_repo is still on an old commit, run Cloudways **Pull** in the UI first (uses elevated
# permissions), or copy from an app whose git_repo is already updated.
#
set -euo pipefail

APP_BASE="${1:?Usage: $0 /home/.../cloudwaysapps.com/<APP_ID>}"

GR="$APP_BASE/git_repo/scripts/deploy"
PH="$APP_BASE/public_html/scripts/deploy"

if [[ ! -d "$GR" ]]; then
  echo "ERROR: missing $GR"
  exit 1
fi
mkdir -p "$PH"

for f in cron_git_pull_deploy.sh dev_after_pull.sh; do
  if [[ -f "$GR/$f" ]]; then
    cp -a "$GR/$f" "$PH/"
    chmod +x "$PH/$f"
    echo "OK $f -> $PH"
  else
    echo "WARN: missing $GR/$f (git pull this app in Cloudways first)"
  fi
done
