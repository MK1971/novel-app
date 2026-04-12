#!/usr/bin/env bash
#
# After you push to GitHub: pull every app's branch in git_repo, rsync → public_html, then
# dev_after_pull (Development app) / server_post_deploy (staging + Production).
#
# Run ON the Cloudways server over SSH (one password prompt for sudo):
#
#   bash /home/1611332.cloudwaysapps.com/ktdekqzmvx/git_repo/scripts/deploy/cloudways_deploy_all.sh
#
# Or from any directory:
#
#   bash /path/to/novel-app/scripts/deploy/cloudways_deploy_all.sh
#
# Same as: sudo bash .../cloudways_all_envs_once.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="${SCRIPT_DIR}/cloudways_all_envs_once.sh"

if [[ ! -f "$TARGET" ]]; then
  echo "ERROR: missing $TARGET"
  exit 1
fi

if [[ "${EUID:-0}" -ne 0 ]]; then
  exec sudo bash "$TARGET" "$@"
else
  exec bash "$TARGET" "$@"
fi
