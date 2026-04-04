# Local development

## URL and sessions

- Pick **one** origin and stick to it: either `http://localhost:8000` or `http://127.0.0.1:8000`. Browsers treat them as different sites; mixing them breaks login and CSRF.
- Set **`APP_URL`** in `.env` to exactly that origin (scheme, host, port).
- For plain **HTTP** locally, keep **`SESSION_SECURE_COOKIE=false`** (see `.env.example`). For **HTTPS** (including production), set **`SESSION_SECURE_COOKIE=true`** so browsers treat the session cookie as secure-only.
- **`SESSION_DOMAIN`** is usually **`null`** locally unless you know you need a custom cookie domain.
- After changing session or URL settings, run **`php artisan config:clear`** and sign in again.

## Reading progress / AJAX

- Progress and similar routes need a valid session and CSRF token. In DevTools → Network, **`track-progress`** (or related POSTs) should return **200**. **419** usually means CSRF or session mismatch—check **`APP_URL`**, origin consistency, and that **`npm run build`** (or `npm run dev`) has produced current assets.

## Database

- Copy **`.env.example`** to **`.env`**, set **`APP_KEY`**, run migrations and seeders as documented for this project. Local and deployed environments each use their own database; data does not sync between them.
- To **wipe application data** and keep **only the admin user** (for a clean manual test pass), see **[Reset database for testing](development/reset-database-for-testing.md)**.
