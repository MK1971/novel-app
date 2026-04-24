#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-$(pwd)}"
APP_URL_EXPECTED="${APP_URL_EXPECTED:-}"

cd "$APP_DIR"

if [[ -z "$APP_URL_EXPECTED" ]]; then
  if [[ -f ".env" ]]; then
    APP_URL_EXPECTED="$(php -r '
      $env = @file(".env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
      $value = "";
      foreach ($env as $line) {
          if ($line === "" || $line[0] === "#") continue;
          if (str_starts_with($line, "APP_URL=")) {
              $value = trim(substr($line, 8), "\"");
              break;
          }
      }
      echo $value;
    ')"
  fi
fi

if [[ -z "$APP_URL_EXPECTED" ]]; then
  APP_URL_EXPECTED="http://127.0.0.1:8000"
fi

APP_URL_EXPECTED="${APP_URL_EXPECTED%/}"

echo "==> Verify release at: $APP_URL_EXPECTED"

if [[ ! -f "public/build/manifest.json" ]]; then
  echo "ERROR: public/build/manifest.json missing"
  exit 1
fi

HOME_STATUS="$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL_EXPECTED/" || true)"
BLOG_STATUS="$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL_EXPECTED/blog" || true)"

echo "    GET / => $HOME_STATUS"
echo "    GET /blog => $BLOG_STATUS"

if [[ "$HOME_STATUS" != "200" || "$BLOG_STATUS" != "200" ]]; then
  echo "ERROR: One or more core pages failed health checks"
  exit 1
fi

ASSET_URLS_RAW="$(php -r '
  $manifestPath = "public/build/manifest.json";
  $m = json_decode(file_get_contents($manifestPath), true);
  if (!is_array($m)) { exit(1); }
  foreach (["resources/css/app.css", "resources/js/app.js"] as $key) {
      if (!empty($m[$key]["file"])) {
          echo "/build/".$m[$key]["file"].PHP_EOL;
      }
  }
')"

if [[ -z "$ASSET_URLS_RAW" ]]; then
  echo "ERROR: Could not read CSS/JS assets from manifest"
  exit 1
fi

while IFS= read -r asset; do
  [[ -n "$asset" ]] || continue
  STATUS="$(curl -s -o /dev/null -w "%{http_code}" "$APP_URL_EXPECTED$asset" || true)"
  echo "    GET $asset => $STATUS"
  if [[ "$STATUS" != "200" ]]; then
    echo "ERROR: Asset check failed for $asset"
    exit 1
  fi
done <<< "$ASSET_URLS_RAW"

echo "==> Release verification passed"
