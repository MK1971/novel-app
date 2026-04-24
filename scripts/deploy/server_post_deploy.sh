#!/usr/bin/env bash
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

echo "==> Composer install"
COMPOSER_BIN="$(resolve_composer || true)"
if [[ -z "${COMPOSER_BIN:-}" ]]; then
  echo "ERROR: composer not found in PATH or common locations."
  exit 1
fi
"$COMPOSER_BIN" install --no-dev --optimize-autoloader

echo "==> Run migrations"
php artisan migrate --force

echo "==> Seed blog stories (shared across environments)"
php artisan db:seed --class=BlogPostSeeder --force

echo "==> Clear caches"
php artisan optimize:clear

echo "==> Storage link"
php artisan storage:link || true

if command -v npm >/dev/null 2>&1; then
  echo "==> Build frontend assets"
  npm ci
  npm run build
else
  echo "==> npm not found, skipping frontend build"
fi

echo "==> Cache config/routes/views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Restart queue workers"
php artisan queue:restart || true

echo "==> Verify release"
bash scripts/deploy/verify_release.sh "$APP_ROOT"

echo "Deployment post steps complete."
