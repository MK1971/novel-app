# WhatsMyBookName

Collaborative story platform where readers submit paid edits, earn points, and unlock voting on chapter variants.

## Core docs

- **[Local development](docs/local-development.md)** — run the app locally, cookies/sessions, troubleshooting.
- **[OAuth setup](docs/oauth-google-apple-setup.md)** — Google/Apple sign-in configuration by environment.
- **[Cloud environment setup](docs/cloud-environment-setup.md)** — env variable checklist for staging/production.
- **[Cloudways runbook](docs/cloudways-deploy-readiness-and-runbook.md)** — step-by-step deploy and post-deploy checks.
- **[Legal docs index](docs/legal/README.md)** — legal page map (in-app hub is `/legal`).

## Quick start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Deployment

1. Configure environment variables on host (never commit secrets).
2. Pull latest code.
3. Run:

```bash
bash scripts/deploy/server_post_deploy.sh
```

4. Verify:
   - login and chapter read/edit flow
   - donation checkout and webhook
   - admin donations report and CSV export
   - legal pages (`/legal`, `/terms`, `/privacy`)
