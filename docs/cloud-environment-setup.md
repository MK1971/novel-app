# Cloud environments: what to set (dev, staging, production)

Use this as a **living checklist** when you move Novel App to a cloud host (or add staging/production). **Update this file** when you add new `env()` configuration or integrations.

**Rules**

- **Never commit** real secrets (`.env`, `client_secret*.json`, PayPal keys, DB passwords). Keep them in each host’s **environment variables** or **secrets manager**.
- **Each environment** gets its own values: at minimum different **`APP_URL`**, **`APP_ENV`**, database, and usually OAuth redirect registration (same Google *client* can list multiple URLs—see [oauth-google-apple-setup.md](oauth-google-apple-setup.md)).
- After deploy: **`php artisan config:clear`** (or **`config:cache`** in production after verifying env).

Canonical variable **names** also appear in **`.env.example`**.

---

## 1. Quick reference by environment

| Concern | Development | Staging | Production |
|--------|-------------|---------|------------|
| **`APP_ENV`** | `local` | `staging` | `production` |
| **`APP_DEBUG`** | `true` | `false` (recommended) | **`false`** |
| **`APP_URL`** | Your dev origin (one of `http://127.0.0.1:8000` or `http://localhost:8000`) | `https://staging.yourdomain.com` | `https://www.yourdomain.com` (or apex—be consistent) |
| **`APP_KEY`** | `php artisan key:generate` | Unique per env | Unique per env |
| **`SESSION_SECURE_COOKIE`** | `false` if HTTP | `true` (HTTPS) | **`true`** |
| **`SESSION_DOMAIN`** | Usually `null` | `null` or `.yourdomain.com` if sharing cookies across subdomains | Same rule as staging |
| **Database** | Local sqlite or shared dev DB | **Dedicated** staging DB | **Dedicated** production DB |
| **PayPal `PAYPAL_MODE`** | `sandbox` | `sandbox` (typical) | **`live`** |
| **Mail** | `log` or Mailpit | Real SMTP or transactional provider | Real SMTP / Postmark / etc. |
| **OAuth Google** | Same client ID OK if redirect/origins registered | Same | Same |
| **OAuth Apple** | Often tested on staging (HTTPS) | Return URL for staging host | Return URL for prod host |

**Novel App–specific public hosts** (from your Google OAuth registration—keep in sync with Google Cloud Console):

- Local: `http://127.0.0.1:8000/auth/google/callback` (match `APP_URL`)
- Staging: `https://staging.whatsmybookname.com/auth/google/callback`
- Production: `https://www.whatsmybookname.com/auth/google/callback`

If domains change, update **Google** (and **Apple**) consoles **and** this table.

---

## 2. Application core

| Variable | Required | Notes |
|----------|----------|--------|
| `APP_NAME` | Yes | Display name (e.g. site title). |
| `APP_ENV` | Yes | `local` / `staging` / `production`. |
| `APP_KEY` | Yes | `base64:...` from `php artisan key:generate`. |
| `APP_DEBUG` | Yes | **`false`** in staging/prod** except short-lived debugging. |
| `APP_URL` | Yes | Must match browser origin (scheme + host + port). Drives URLs, OAuth defaults, mail, storage URL. |
| `ADMIN_EMAIL` | Recommended | Admin gate + seeders + notifications; see `.env.example`. |
| `LEGAL_ENTITY_NAME`, `LEGAL_ENTITY_ADDRESS`, `LEGAL_CONTACT_EMAIL`, `LEGAL_JURISDICTION` | Recommended | Rendered on legal pages so public policies show your registered business identity and governing jurisdiction. |

---

## 3. Database

| Variable | Required | Notes |
|----------|----------|--------|
| `DB_CONNECTION` | Yes | e.g. `mysql`, `pgsql`, or `sqlite` (dev only). |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | If not sqlite | **Separate database** per staging vs production. |
| `DB_URL` | Optional | Some PaaS provide a single URL instead of discrete vars. |

Run **`php artisan migrate`** (and **`--force`** in production). Use seeders only where appropriate (avoid prod admin seed mistakes).

---

## 4. Session, cookies, HTTPS

| Variable | Required | Notes |
|----------|----------|--------|
| `SESSION_DRIVER` | Yes | Often `database` (this project’s `.env.example`); ensure `sessions` table exists. |
| `SESSION_LIFETIME` | Optional | Minutes (default 120). |
| `SESSION_DOMAIN` | Optional | Usually `null`; set only if you need cross-subdomain cookies. |
| `SESSION_SECURE_COOKIE` | Yes for HTTPS | **`true`** when the site is only served over HTTPS. |
| `SESSION_ENCRYPT` | Optional | Can enable for extra cookie encryption. |

See [local-development.md](local-development.md) for local URL vs cookie pitfalls.

---

## 5. Cache, queue, Redis

| Variable | Required | Notes |
|----------|----------|--------|
| `CACHE_STORE` | Yes | `database` or `redis` in cloud. |
| `QUEUE_CONNECTION` | Yes | `database` or `redis`; run a **queue worker** in cloud. |
| `REDIS_*` | If using Redis | Host, password, port from your provider. |

**Scheduler:** configure the host to run **`php artisan schedule:run`** every minute (cron or platform scheduler).

---

## 6. Mail

| Variable | Required | Notes |
|----------|----------|--------|
| `MAIL_MAILER` | Yes | `smtp`, `log`, `postmark`, etc. |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` | If SMTP | From provider. |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Yes | Must be allowed by your mail provider. |

Test: **`php artisan mail:test you@example.com`** (if available in your Laravel version) or trigger a real notification.

---

## 7. Files / storage (optional S3)

| Variable | Required | Notes |
|----------|----------|--------|
| `FILESYSTEM_DISK` | Yes | `local` or `s3` for cloud multi-instance. |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` | If S3 | For uploads and public URLs. |

Post-deploy: **`php artisan storage:link`** when using local `public` disk for user-visible files.

---

## 8. OAuth — Google

| Variable | Required | Notes |
|----------|----------|--------|
| `GOOGLE_CLIENT_ID` | If using Google sign-in | Same ID can span envs if Console lists all origins/redirects. |
| `GOOGLE_CLIENT_SECRET` | If using Google sign-in | **Rotate** in Console updates secret everywhere. |
| `GOOGLE_REDIRECT_URI` | Optional | Defaults to `APP_URL/auth/google/callback`. |

Details: [oauth-google-apple-setup.md](oauth-google-apple-setup.md). Copy from **`client_secret_*.json`** into `.env` only on the server—do not commit JSON.

---

## 9. OAuth — Apple

| Variable | Required | Notes |
|----------|----------|--------|
| `APPLE_SIGN_IN_ENABLED` | To show Apple in UI | Default **`false`** until you intentionally enable Apple; must be **`true`** plus credentials below. |
| `APPLE_CLIENT_ID` | If using Apple | Services ID. |
| `APPLE_REDIRECT_URI` | Optional | Defaults to `APP_URL/auth/apple/callback`. |
| **Either** `APPLE_CLIENT_SECRET` (JWT) **or** `APPLE_TEAM_ID` + `APPLE_KEY_ID` + `APPLE_PRIVATE_KEY` (.p8 path) | If using Apple | Key path must exist on server; not in web root. |

Register **Return URLs** for **each** HTTPS host (staging + production). Apple often cannot use plain `http://localhost`.

---

## 10. PayPal

| Variable | Required | Notes |
|----------|----------|--------|
| `PAYPAL_MODE` | Yes | **`sandbox`** for dev/staging; **`live`** for production. |
| `PAYPAL_SANDBOX_CLIENT_ID`, `PAYPAL_SANDBOX_CLIENT_SECRET` | If sandbox | From [PayPal Developer](https://developer.paypal.com). |
| `PAYPAL_LIVE_CLIENT_ID`, `PAYPAL_LIVE_CLIENT_SECRET` | If live | Production credentials only on production. |
| `PAYPAL_WEBHOOK_ID` | Recommended | Enables PayPal signature verification for webhook authenticity checks. |
| `PAYPAL_WEBHOOK_TOKEN` | Fallback | Shared-secret fallback for local/testing if webhook ID is not configured. |
| `PAYPAL_LIVE_APP_ID` | Optional | If your integration needs it. |

**Do not commit** live keys. Replace any sample keys in local `.env` with your own.

---

## 11. Marketing / SEO (optional)

| Variable | Notes |
|----------|--------|
| `LANDING_PRIZE_POOL_DISPLAY` | Landing strip prize line. |
| `LANDING_SOFT_STATS_WHEN_EMPTY` | `true`/`false`. |
| `SEO_DEFAULT_DESCRIPTION` | Default meta description. |
| `SEO_OG_IMAGE_URL` | Open Graph image URL. |

---

## 12. Vite / front-end

| Variable | Notes |
|----------|--------|
| `VITE_APP_NAME` | Usually `"${APP_NAME}"`. |

Build assets in CI or on deploy: **`npm ci`** && **`npm run build`**.

---

## 13. Optional integrations (if you enable them)

| Variable | Service |
|----------|---------|
| `POSTMARK_API_KEY` | Postmark |
| `RESEND_API_KEY` | Resend |
| `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` | Slack notifications |

---

## 14. Post-deploy commands (production-oriented)

Run on the server after code and env are in place (exact order may vary):

1. Install Composer deps: `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan storage:link` (if using public disk)
4. `npm ci && npm run build` (or use prebuilt assets from CI)
5. `php artisan config:cache` && `php artisan route:cache` && `php artisan view:cache` (only after env is correct)
6. Restart queue workers / PHP-FPM / Octane as your platform requires

For troubleshooting config: temporarily `php artisan config:clear`.

---

## 15. External consoles (sync with env)

When **`APP_URL`** or domains change, update:

- [ ] **Google Cloud** — Authorized JavaScript origins + redirect URIs for **each** environment URL.
- [ ] **Apple Developer** — Services ID domains + Return URLs for **each** HTTPS host.
- [ ] **PayPal** — App return/cancel URLs if PayPal dashboard requires them for your integration.

---

## 16. Blank template (copy per environment)

Fill one block per host (paste into secrets UI or `.env` on server—**not** into git):

```dotenv
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=

ADMIN_EMAIL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=

CACHE_STORE=
QUEUE_CONNECTION=

MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=

LEGAL_ENTITY_NAME=
LEGAL_ENTITY_ADDRESS=
LEGAL_CONTACT_EMAIL=
LEGAL_JURISDICTION=
LEGAL_DISPUTE_NOTICE_DAYS=30

FILESYSTEM_DISK=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

APPLE_SIGN_IN_ENABLED=false
APPLE_CLIENT_ID=
APPLE_TEAM_ID=
APPLE_KEY_ID=
APPLE_PRIVATE_KEY=
# APPLE_CLIENT_SECRET=

PAYPAL_MODE=
PAYPAL_SANDBOX_CLIENT_ID=
PAYPAL_SANDBOX_CLIENT_SECRET=
PAYPAL_LIVE_CLIENT_ID=
PAYPAL_LIVE_CLIENT_SECRET=

VITE_APP_NAME="${APP_NAME}"
```

Add **`REDIS_*`** if you switch cache/queue to Redis.

---

*Last reviewed: keep in sync with `.env.example` and `config/*.php` when the app gains new environment variables.*
