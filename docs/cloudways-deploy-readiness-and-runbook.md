# Cloudways deploy readiness and runbook

This is the operator checklist for deploying this repository to **staging** and **production** on Cloudways.

Use this file as your step-by-step script when each environment is first created and for every subsequent deploy.

---

## 1) One-time per environment (Cloudways panel)

1. Create app/server in Cloudways.
2. Set application path to this repo and connect Git deployment.
3. In **Application Settings > Environment Variables**, set all required variables (see section 2).
4. Ensure SSL is enabled for staging/prod domains.
5. Configure PHP version compatible with app (same major as local successful test run).
6. Configure cron for scheduler:
   - `* * * * * cd /home/master/applications/<APP_ID>/public_html && php artisan schedule:run >> /dev/null 2>&1`
7. Configure queue worker (Supervisor or Cloudways process manager) to run:
   - `php artisan queue:work --sleep=3 --tries=3 --timeout=90`

---

## 2) Required env keys by environment

### Common

- `APP_NAME=WhatsMyBookName`
- `APP_ENV=staging` or `production`
- `APP_DEBUG=false`
- `APP_URL=https://<env-domain>`
- `APP_KEY=<generated key>`
- `MAIL_FROM_NAME=WhatsMyBookName`

### Database/session/cache/queue

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER=database`
- `SESSION_SECURE_COOKIE=true`
- `CACHE_STORE=database` (or redis if configured)
- `QUEUE_CONNECTION=database` (or redis if configured)

### Mail

- `MAIL_MAILER=smtp` (or provider)
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS=whatsmybookname@gmail.com` (or your sender)

### PayPal

- `PAYPAL_MODE=sandbox` on staging; `live` on production
- staging: `PAYPAL_SANDBOX_CLIENT_ID`, `PAYPAL_SANDBOX_CLIENT_SECRET`
- production: `PAYPAL_LIVE_CLIENT_ID`, `PAYPAL_LIVE_CLIENT_SECRET`
- webhook security:
  - `PAYPAL_WEBHOOK_ID=<paypal webhook id>` (recommended, signature verification mode)
  - `PAYPAL_WEBHOOK_TOKEN=<long random secret>` (fallback/local testing)

### OAuth

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI=https://<env-domain>/auth/google/callback`
- Apple keys only if enabled.

---

## 3) Deploy script (run on server after code pull)

From app root:

```bash
bash scripts/deploy/server_post_deploy.sh
```

This script runs:

1. `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan optimize:clear`
4. `php artisan storage:link`
5. `npm ci && npm run build` (if node is available on server)
6. `php artisan config:cache`
7. `php artisan route:cache`
8. `php artisan view:cache`

If node build is done in CI instead, skip server-side npm/build and deploy built assets.

---

## 4) PayPal webhook setup (dashboard)

1. In PayPal Developer Dashboard, open your app webhooks.
2. Add endpoint:
   - `https://<env-domain>/payment/donation/webhook`
3. Subscribe to:
   - `PAYMENT.CAPTURE.COMPLETED`
   - `CHECKOUT.ORDER.COMPLETED`
4. Copy Webhook ID into `PAYPAL_WEBHOOK_ID` env.
5. Redeploy or run:
   - `php artisan optimize:clear`

To verify auth mode in UI:
- open `/admin/donations`
- check badge:
  - `Signature verification enabled` (preferred)
  - `Token fallback mode` (if webhook id missing)

---

## 5) Post-deploy smoke test (browser)

1. Login works.
2. `/chapters/{id}`:
   - queue edit
   - remove queued edit
   - submit current + queued payment
3. `/edits/public`:
   - visibility and feedback behavior
4. `/dashboard` donation checkout:
   - complete donation
   - confirm donor receipt mail + admin donation mail
5. `/admin/donations`:
   - row visible
   - export CSV works
6. webhook:
   - trigger PayPal test webhook
   - one donation row (deduped on repeated event)

---

## 6) Commands you may need on server

- Clear caches:
  - `php artisan optimize:clear`
- Re-cache config/routes/views:
  - `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- Restart queue worker:
  - `php artisan queue:restart`
- Check failed jobs:
  - `php artisan queue:failed`

---

## 7) Handoff note

Once staging and production environments are up, we can run through this runbook together and execute each step in order.
