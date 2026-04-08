# Local development

## URL and sessions

- Pick **one** origin and stick to it: either `http://localhost:8000` or `http://127.0.0.1:8000`. Browsers treat them as different sites; mixing them breaks login and CSRF.
- Set **`APP_URL`** in `.env` to exactly that origin (scheme, host, port).
- For plain **HTTP** locally, keep **`SESSION_SECURE_COOKIE=false`** (see `.env.example`). For **HTTPS** (including production), set **`SESSION_SECURE_COOKIE=true`** so browsers treat the session cookie as secure-only.
- **`SESSION_DOMAIN`** is usually **`null`** locally unless you know you need a custom cookie domain.
- After changing session or URL settings, run **`php artisan config:clear`** and sign in again.

## OAuth (Google / Apple)

For **step-by-step** console registration, redirect URIs per environment, keys, and a full **`.env` checklist**, see **[oauth-google-apple-setup.md](oauth-google-apple-setup.md)**.

- **Redirect URLs** in Google Cloud Console and Apple Developer must match **`APP_URL`** (e.g. `http://127.0.0.1:8000/auth/google/callback` and `.../auth/apple/callback`). Mismatch causes “redirect_uri_mismatch” or Apple `invalid_client`.
- Set **`GOOGLE_CLIENT_ID`** and **`GOOGLE_CLIENT_SECRET`** to show **Continue with Google** on login/register (modals and `/login` / `/register`). If either is unset, the Google button is hidden and the redirect route returns **404**. After editing **`.env`**, run **`php artisan config:clear`** (or avoid **`config:cache`** during local dev) so Laravel picks up new values.
- **Apple** is **off by default**: set **`APPLE_SIGN_IN_ENABLED=true`** when ready, then configure **`APPLE_CLIENT_ID`**, **`APPLE_REDIRECT_URI`**, and either a JWT **`APPLE_CLIENT_SECRET`** or **`APPLE_TEAM_ID`**, **`APPLE_KEY_ID`**, and **`APPLE_PRIVATE_KEY`**. Apple’s callback is a **POST**; CSRF is disabled only for **`auth/apple/callback`** in `bootstrap/app.php`.
- **Account linking:** if a Google/Apple email matches an existing user, the provider is attached and they sign in as that user (same email must be the verified identity from the provider).

### Google button missing?

- The **Continue with Google** control only renders when **`GOOGLE_CLIENT_ID`** and **`GOOGLE_CLIENT_SECRET`** are both non-empty in the running app (see **`SocialAuthController::providerConfigured('google')`**). It appears **above** the email fields on **`/login`**, **`/register`**, and in the **Sign in / Join** modals.
- After editing **`.env`**, run **`php artisan config:clear`**. If you previously ran **`php artisan config:cache`**, run **`config:clear`** again during development or rebuild cache with current env.
- Verify the process sees your vars: **`php artisan tinker`** then `config('services.google.client_id')` (should not be `null`).
- Ensure **`.env`** is in the **project root** (same folder as **`artisan`**), not only a JSON download in the folder.

### Apple client secret rotation

- If you use a **JWT** as **`APPLE_CLIENT_SECRET`**, Apple allows a maximum lifetime of about **six months**. Before it expires, generate a new secret in the Apple Developer portal (or with your usual script), update **`.env`**, then run **`php artisan config:clear`**.
- If you use **`APPLE_TEAM_ID`**, **`APPLE_KEY_ID`**, and **`APPLE_PRIVATE_KEY`** (`.p8` file), the Socialite provider builds a short-lived client secret per request; you still need to **rotate or revoke keys** in Apple Developer if a key is compromised. Replace the `.p8` path or file, update **`APPLE_KEY_ID`** if you create a new key, and redeploy.

## Reading progress / AJAX

- Progress and similar routes need a valid session and CSRF token. In DevTools → Network, **`track-progress`** (or related POSTs) should return **200**. **419** usually means CSRF or session mismatch—check **`APP_URL`**, origin consistency, and that **`npm run build`** (or `npm run dev`) has produced current assets.

## Database

- Copy **`.env.example`** to **`.env`**, set **`APP_KEY`**, run migrations and seeders as documented for this project. Local and deployed environments each use their own database; data does not sync between them.
- To **wipe application data** and keep **only the admin user** (for a clean manual test pass), see **[Reset database for testing](development/reset-database-for-testing.md)**.
