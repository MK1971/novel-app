#!/usr/bin/env bash
# Run on the dev/staging server from the Laravel project root after `git pull`.
# Clears Laravel caches, runs migrations, rebuilds Vite assets when npm exists.
#
#   cd /path/to/novel-app && bash scripts/deploy/dev_after_pull.sh
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_ROOT="${NOVEL_APP_ROOT:-$(cd "$SCRIPT_DIR/../.." && pwd)}"
cd "$APP_ROOT"

resolve_composer() {
  if command -v composer >/dev/null 2>&1; then
    command -v composer
    return 0
  fi
  for c in /usr/local/bin/composer /usr/bin/composer "$HOME/bin/composer" "$HOME/.composer/vendor/bin/composer"; do
    if [[ -x "$c" ]]; then
      echo "$c"
      return 0
    fi
  done
  return 1
}

load_nvm_for_build() {
  export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
  if [[ -s "$NVM_DIR/nvm.sh" ]]; then
    # shellcheck disable=SC1090
    . "$NVM_DIR/nvm.sh"
    if command -v nvm >/dev/null 2>&1; then
      nvm use default >/dev/null 2>&1 || nvm use 20 >/dev/null 2>&1 || true
    fi
  fi
}

echo "==> Git tip (expect Development / 01cbe51 or newer for release 1.9.55)"
git log -1 --oneline || true

echo "==> Sanity: landing copy marker"
if grep -q "What You Could Win" resources/views/welcome.blade.php 2>/dev/null; then
  echo "    OK: welcome.blade.php contains 'What You Could Win'"
else
  echo "    WARN: marker not found — wrong branch, path, or pull incomplete"
fi

echo "==> Composer"
COMPOSER_BIN="$(resolve_composer || true)"
if [[ -z "${COMPOSER_BIN:-}" ]]; then
  echo "ERROR: composer not found in PATH or common locations."
  exit 1
fi
"$COMPOSER_BIN" install --no-interaction --prefer-dist

echo "==> Migrate"
php artisan migrate --force

echo "==> Seed blog stories (shared across environments)"
php artisan db:seed --class=BlogPostSeeder --force

echo "==> Clear all Laravel caches (views, config, routes, bootstrap cache)"
php artisan optimize:clear

load_nvm_for_build

if command -v npm >/dev/null 2>&1; then
  echo "==> npm build (Vite)"
  npm ci
  npm run build
else
  echo "==> npm not in PATH; skip Vite build (install Node or use NVM)"
fi

echo "==> Optional: dev often skips heavy caching; uncomment if you use config:cache in dev"
# php artisan config:cache
# php artisan route:cache

echo "==> Queue restart (if workers)"
php artisan queue:restart || true

echo "==> Verify release"
bash scripts/deploy/verify_release.sh "$APP_ROOT"

echo "==> Done. Hard-refresh the browser (Cmd+Shift+R) or use a private window."
echo "    If still stale: restart PHP-FPM from Cloudways or purge Cloudflare cache for dev."
