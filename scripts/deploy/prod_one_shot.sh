#!/usr/bin/env bash
set -euo pipefail

# One-shot production deploy script for this project.
# Covers issues seen on dev/staging: missing .env, missing vendor, migration edge cases,
# missing Vite manifest, cache rebuild order, and optional root fallback entry files.
#
# APP_*, PayPal, Google OAuth, DB_*, MAIL_*: place exports in scripts/deploy/prod_secrets.local.sh (gitignored),
# or set the same variables in the environment before running. The script merges into .env when
# those values are set (see merge_* functions below).

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Cloudways PHP stacks often ship an old system Node; NVM under $HOME/.nvm fixes Vite (20.19+).
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

merge_app_into_dotenv() {
  if [[ -z "${APP_URL:-}${APP_ENV:-}${APP_DEBUG:-}${SESSION_SECURE_COOKIE:-}" ]]; then
    return 0
  fi
  export APP_URL APP_ENV APP_DEBUG SESSION_SECURE_COOKIE
  php <<'PHP'
<?php
$path = '.env';
if (! is_file($path)) {
    fwrite(STDERR, ".env missing; cannot merge APP_* keys\n");
    exit(1);
}
$candidates = ['APP_URL', 'APP_ENV', 'APP_DEBUG', 'SESSION_SECURE_COOKIE'];
$updates = [];
foreach ($candidates as $k) {
    $v = getenv($k);
    if ($v !== false && $v !== '') {
        $updates[$k] = $v;
    }
}
if ($updates === []) {
    exit(0);
}
$lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
$keys = array_keys($updates);
$out = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($trim !== '' && $trim[0] === '#') {
        $out[] = $line;
        continue;
    }
    foreach ($keys as $key) {
        if (str_starts_with($line, $key.'=')) {
            continue 2;
        }
    }
    $out[] = $line;
}
foreach ($updates as $k => $v) {
    $out[] = $k.'="'.str_replace(['\\', '"'], ['\\\\', '\\"'], $v).'"';
}
file_put_contents($path, implode("\n", $out)."\n");
fwrite(STDERR, "==> Merged APP_* keys into .env\n");
PHP
}

merge_paypal_into_dotenv() {
  [[ -n "${PAYPAL_LIVE_CLIENT_ID:-}" ]] || return 0
  export PAYPAL_LIVE_CLIENT_ID PAYPAL_LIVE_CLIENT_SECRET PAYPAL_WEBHOOK_ID
  php <<'PHP'
<?php
$path = '.env';
if (! is_file($path)) {
    fwrite(STDERR, ".env missing; cannot merge PayPal keys\n");
    exit(1);
}
$id = getenv('PAYPAL_LIVE_CLIENT_ID') ?: '';
if ($id === '') {
    exit(0);
}
$updates = [
    'PAYPAL_MODE' => 'live',
    'PAYPAL_LIVE_CLIENT_ID' => $id,
    'PAYPAL_LIVE_CLIENT_SECRET' => getenv('PAYPAL_LIVE_CLIENT_SECRET') ?: '',
    'PAYPAL_WEBHOOK_ID' => getenv('PAYPAL_WEBHOOK_ID') ?: '',
];
$lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
$keys = array_keys($updates);
$out = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($trim !== '' && $trim[0] === '#') {
        $out[] = $line;
        continue;
    }
    foreach ($keys as $key) {
        if (str_starts_with($line, $key.'=')) {
            continue 2;
        }
    }
    $out[] = $line;
}
foreach ($updates as $k => $v) {
    $out[] = $k.'="'.str_replace(['\\', '"'], ['\\\\', '\\"'], $v).'"';
}
file_put_contents($path, implode("\n", $out)."\n");
fwrite(STDERR, "==> Merged PayPal live keys into .env\n");
PHP
}

merge_google_into_dotenv() {
  [[ -n "${GOOGLE_CLIENT_ID:-}" ]] || return 0
  export GOOGLE_CLIENT_ID GOOGLE_CLIENT_SECRET
  # Optional: only pass through if set so PHP can add GOOGLE_REDIRECT_URI to .env
  export GOOGLE_REDIRECT_URI="${GOOGLE_REDIRECT_URI:-}"
  php <<'PHP'
<?php
$path = '.env';
if (! is_file($path)) {
    fwrite(STDERR, ".env missing; cannot merge Google OAuth keys\n");
    exit(1);
}
$id = getenv('GOOGLE_CLIENT_ID') ?: '';
if ($id === '') {
    exit(0);
}
$updates = [
    'GOOGLE_CLIENT_ID' => $id,
    'GOOGLE_CLIENT_SECRET' => getenv('GOOGLE_CLIENT_SECRET') ?: '',
];
$redirect = getenv('GOOGLE_REDIRECT_URI');
if ($redirect !== false && $redirect !== '') {
    $updates['GOOGLE_REDIRECT_URI'] = $redirect;
}
$lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
$keys = array_keys($updates);
$out = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($trim !== '' && $trim[0] === '#') {
        $out[] = $line;
        continue;
    }
    foreach ($keys as $key) {
        if (str_starts_with($line, $key.'=')) {
            continue 2;
        }
    }
    $out[] = $line;
}
foreach ($updates as $k => $v) {
    $out[] = $k.'="'.str_replace(['\\', '"'], ['\\\\', '\\"'], $v).'"';
}
file_put_contents($path, implode("\n", $out)."\n");
fwrite(STDERR, "==> Merged Google OAuth keys into .env\n");
PHP
}

merge_db_into_dotenv() {
  if [[ -z "${DB_DATABASE:-}" && -z "${DB_HOST:-}" && -z "${DB_PORT:-}" && -z "${DB_USERNAME:-}" && -z "${DB_PASSWORD:-}" && -z "${DB_CONNECTION:-}" ]]; then
    return 0
  fi
  export DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD
  php <<'PHP'
<?php
$path = '.env';
if (! is_file($path)) {
    fwrite(STDERR, ".env missing; cannot merge DB keys\n");
    exit(1);
}
$candidates = ['DB_CONNECTION', 'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
$updates = [];
foreach ($candidates as $k) {
    $v = getenv($k);
    if ($v !== false && $v !== '') {
        $updates[$k] = $v;
    }
}
if ($updates === []) {
    exit(0);
}
$lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
$keys = array_keys($updates);
$out = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($trim !== '' && $trim[0] === '#') {
        $out[] = $line;
        continue;
    }
    foreach ($keys as $key) {
        if (str_starts_with($line, $key.'=')) {
            continue 2;
        }
    }
    $out[] = $line;
}
foreach ($updates as $k => $v) {
    $out[] = $k.'="'.str_replace(['\\', '"'], ['\\\\', '\\"'], $v).'"';
}
file_put_contents($path, implode("\n", $out)."\n");
fwrite(STDERR, "==> Merged DB_* keys into .env\n");
PHP
}

merge_mail_into_dotenv() {
  if [[ -z "${MAIL_FROM_ADDRESS:-}${MAIL_FROM_NAME:-}${MAIL_MAILER:-}${MAIL_SCHEME:-}${MAIL_HOST:-}${MAIL_PORT:-}${MAIL_USERNAME:-}${MAIL_PASSWORD:-}${MAIL_ENCRYPTION:-}" ]]; then
    return 0
  fi
  export MAIL_MAILER MAIL_SCHEME MAIL_HOST MAIL_PORT MAIL_USERNAME MAIL_PASSWORD MAIL_ENCRYPTION MAIL_FROM_ADDRESS MAIL_FROM_NAME
  php <<'PHP'
<?php
$path = '.env';
if (! is_file($path)) {
    fwrite(STDERR, ".env missing; cannot merge MAIL_* keys\n");
    exit(1);
}
$candidates = ['MAIL_MAILER', 'MAIL_SCHEME', 'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS', 'MAIL_FROM_NAME'];
$updates = [];
foreach ($candidates as $k) {
    $v = getenv($k);
    if ($v !== false && $v !== '') {
        $updates[$k] = $v;
    }
}
if ($updates === []) {
    exit(0);
}
$lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];
$keys = array_keys($updates);
$out = [];
foreach ($lines as $line) {
    $trim = ltrim($line);
    if ($trim !== '' && $trim[0] === '#') {
        $out[] = $line;
        continue;
    }
    foreach ($keys as $key) {
        if (str_starts_with($line, $key.'=')) {
            continue 2;
        }
    }
    $out[] = $line;
}
foreach ($updates as $k => $v) {
    $out[] = $k.'="'.str_replace(['\\', '"'], ['\\\\', '\\"'], $v).'"';
}
file_put_contents($path, implode("\n", $out)."\n");
fwrite(STDERR, "==> Merged MAIL_* keys into .env\n");
PHP
}

APP_DIR="${1:-$(pwd)}"
APP_URL_EXPECTED="${APP_URL_EXPECTED:-}"
FIX_WEBROOT_FALLBACK="${FIX_WEBROOT_FALLBACK:-0}" # set to 1 to copy public/index.php + .htaccess to app root

cd "$APP_DIR"
echo "==> Working directory: $APP_DIR"

if [[ ! -f ".env" ]]; then
  echo "==> .env missing, copying from .env.example"
  cp .env.example .env
fi

if [[ ! -d vendor ]]; then
  echo "==> vendor missing, composer install (needed before php artisan)"
  composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
fi

if ! grep -q '^APP_KEY=' .env || grep -q '^APP_KEY=$' .env; then
  echo "==> APP_KEY missing/empty, generating"
  php artisan key:generate --force || true
fi

SECRETS_FILE="$SCRIPT_DIR/prod_secrets.local.sh"
if [[ -f "$SECRETS_FILE" ]]; then
  echo "==> Sourcing $SECRETS_FILE"
  # shellcheck disable=SC1090
  source "$SECRETS_FILE"
fi
merge_app_into_dotenv
merge_paypal_into_dotenv
merge_google_into_dotenv
merge_db_into_dotenv
merge_mail_into_dotenv

echo "==> Composer install"
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

echo "==> Run migrations (first pass)"
set +e
MIGRATE_OUTPUT="$(php artisan migrate --force 2>&1)"
MIGRATE_EXIT=$?
set -e
echo "$MIGRATE_OUTPUT"

if [[ $MIGRATE_EXIT -ne 0 ]]; then
  if echo "$MIGRATE_OUTPUT" | grep -q "paragraph_reactions" && echo "$MIGRATE_OUTPUT" | grep -Eq "already exists|is too long"; then
    echo "==> Detected paragraph_reactions migration edge case, recovering"
    php -r '
      $env = @file(".env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
      $kv = [];
      foreach ($env as $line) {
          if ($line === "" || $line[0] === "#") continue;
          $parts = explode("=", $line, 2);
          if (count($parts) === 2) {
              $kv[$parts[0]] = trim($parts[1], "\"");
          }
      }
      $host = $kv["DB_HOST"] ?? "127.0.0.1";
      $port = $kv["DB_PORT"] ?? "3306";
      $db   = $kv["DB_DATABASE"] ?? "";
      $user = $kv["DB_USERNAME"] ?? "";
      $pass = $kv["DB_PASSWORD"] ?? "";
      if ($db === "" || $user === "") { fwrite(STDERR, "Missing DB creds in .env\n"); exit(1); }
      $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
      $pdo->exec("DROP TABLE IF EXISTS paragraph_reactions");
      echo "Dropped paragraph_reactions\n";
    '
    php artisan migrate --force
  else
    echo "Migration failed for another reason; stopping."
    exit 1
  fi
fi

echo "==> Build frontend assets (Node/npm — loads NVM when ~/.nvm exists, e.g. Cloudways)"
load_nvm_for_build
if command -v npm >/dev/null 2>&1; then
  echo "==> Using node: $(command -v node) ($(node -v 2>/dev/null || echo '?'))"
  npm ci --cache .npm-cache
  npm run build --cache .npm-cache || npm run build
else
  echo "npm not found; skipping build (expected only if assets are prebuilt)"
fi

if [[ ! -f "public/build/manifest.json" ]]; then
  echo "Missing public/build/manifest.json after build."
  exit 1
fi

if [[ "$FIX_WEBROOT_FALLBACK" == "1" ]]; then
  echo "==> Applying root fallback entry files"
  cp public/index.php index.php
  cp public/.htaccess .htaccess
fi

echo "==> Rebuild Laravel caches"
php artisan optimize:clear || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart || true

echo "==> Health checks"
php artisan about --only=environment --only=drivers || true
if [[ -n "$APP_URL_EXPECTED" ]]; then
  curl -I --max-time 20 "${APP_URL_EXPECTED}?cb=$(date +%s)" || true
fi

echo "==> Done"
