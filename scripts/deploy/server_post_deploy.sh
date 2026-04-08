#!/usr/bin/env bash
set -euo pipefail

echo "==> Composer install"
composer install --no-dev --optimize-autoloader

echo "==> Run migrations"
php artisan migrate --force

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

echo "Deployment post steps complete."
