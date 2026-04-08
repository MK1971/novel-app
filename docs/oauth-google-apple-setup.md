# Google & Apple OAuth: console setup and `.env` per environment

This app uses **Laravel Socialite** for Google and **`socialiteproviders/apple`** for Apple. Redirect URIs must **exactly** match what the app sends (scheme, host, port, path). **`APP_URL`** in each environment should be the public origin users see.

**Sign in with Apple (deferred):** the Apple button and `/auth/apple/*` flows stay **disabled** until you set **`APPLE_SIGN_IN_ENABLED=true`** in `.env` **and** complete Apple credentials below. Routes and Socialite registration remain in code so you can enable later without a redeploy beyond config.

**Callback paths (fixed in code):**

- Google: `{APP_URL}/auth/google/callback`
- Apple: `{APP_URL}/auth/apple/callback`

**Environment variables** (see also `config/services.php` and `.env.example`):

| Variable | Purpose |
|----------|---------|
| `APP_URL` | Public site URL (no trailing slash). Drives default redirects if overrides are unset. |
| `GOOGLE_CLIENT_ID` | OAuth 2.0 Client ID from Google Cloud. |
| `GOOGLE_CLIENT_SECRET` | OAuth client secret. |
| `GOOGLE_REDIRECT_URI` | Optional; defaults to `APP_URL/auth/google/callback`. |
| `APPLE_SIGN_IN_ENABLED` | Set **`true`** when ready to show Apple in the UI; default/false keeps Apple off while credentials can sit in `.env` for later. |
| `APPLE_CLIENT_ID` | Apple **Services ID** (used as OAuth client id). |
| `APPLE_REDIRECT_URI` | Optional; defaults to `APP_URL/auth/apple/callback`. |
| `APPLE_CLIENT_SECRET` | JWT client secret (Apple allows ~6 months max lifetime), **or** leave unset and use key trio below. |
| `APPLE_TEAM_ID` | Apple Developer Team ID. |
| `APPLE_KEY_ID` | Key ID for the Sign in with Apple **private key**. |
| `APPLE_PRIVATE_KEY` | **Absolute path** to the `.p8` file on the server (recommended for production). |
| `APPLE_PASSPHRASE` | Only if the `.p8` is passphrase-protected (usually empty). |

After any `.env` change: `php artisan config:clear`.

### Error 400: `redirect_uri_mismatch`

Google compares the **`redirect_uri`** Socialite sends with **Authorized redirect URIs** in Cloud Console **character for character** (scheme, host, **port**, path—no trailing slash on the path).

- **Wrong port or missing port:** `APP_URL=http://localhost` sends `http://localhost/auth/google/callback`, but `php artisan serve` is usually **`http://127.0.0.1:8000`** — use **`APP_URL=http://127.0.0.1:8000`** (or `http://localhost:8000`) and set **`GOOGLE_REDIRECT_URI`** to the same origin + `/auth/google/callback`, or add that exact URI in the Console.
- **`localhost` vs `127.0.0.1`:** they are different hosts. Register **both** redirect URIs in Google if you switch, or pick one and always open the site with that host.
- **Stale config:** run **`php artisan config:clear`** after changing **`APP_URL`** or **`GOOGLE_REDIRECT_URI`**.

### Git vs test / staging / production

**`.env` is not meant to be updated “through git.”** It stays **gitignored** on purpose so secrets never land in the repository. The same applies to **`client_secret*.json`**.

| What | Role |
|------|------|
| **`.env.example`** (in git) | Documents **variable names** only; no real secrets. Copy to `.env` locally. |
| **`.env` on your machine** | Your **local** dev values only. |
| **Staging / production** | Configure **on each host** (or in that host’s **environment variables** UI). You SSH in, edit `.env` there, *or* set `GOOGLE_CLIENT_ID`, `APP_URL`, etc. in Forge / Vapor / Docker / Kubernetes / your PaaS—Laravel reads **`$_ENV` / `getenv()`**; a physical `.env` file is optional if the platform injects vars. |
| **CI / automated tests** | Use your CI’s **secrets** (e.g. GitHub Actions **Secrets**) and export them in the workflow before `php artisan test`, or generate a temporary `.env` in the job (still **not** committed). Prefer a **dedicated test OAuth client** or mocks so production secrets never run in CI logs. |

So: **one repo**, **many environments**—each environment gets its own secrets and `APP_URL` **outside** git, using the same variable **names** as in `.env.example`.

---

## 1. Google Cloud Console

### 1.1 Create or select a project

1. Open [Google Cloud Console](https://console.cloud.google.com/).
2. Create a project (or pick an existing one) for this product.

### 1.2 Enable the Google+ / People API (if prompted)

1. **APIs & Services** → **Library**.
2. Enable **Google+ API** or the OAuth-related APIs Google lists for “Sign in with Google” (the console UI changes; follow the enable prompts).

### 1.3 Configure OAuth consent screen

1. **APIs & Services** → **OAuth consent screen**.
2. Choose **External** (or **Internal** if Workspace-only).
3. Fill app name, support email, and required fields; add scopes typically **`email`**, **`profile`**, **`openid`** for basic sign-in.

### 1.4 Create OAuth client (Web application)

1. **APIs & Services** → **Credentials** → **Create credentials** → **OAuth client ID**.
2. Application type: **Web application**.
3. **Authorized JavaScript origins** — add each environment origin (no path):
   - Local: `http://127.0.0.1:8000` **or** `http://localhost:8000` (use **one** consistently with `APP_URL`).
   - Staging: `https://staging.example.com`
   - Production: `https://yourdomain.com`
4. **Authorized redirect URIs** — add **one URI per environment** (must match Socialite):
   - Local: `http://127.0.0.1:8000/auth/google/callback` (or localhost, matching `APP_URL`).
   - Staging: `https://staging.example.com/auth/google/callback`
   - Production: `https://yourdomain.com/auth/google/callback`
5. Save; copy **Client ID** and **Client secret**.

### 1.5 Downloaded `client_secret_*.json` (optional)

Google Cloud may offer a **JSON** download named like `client_secret_<CLIENT_ID>.apps.googleusercontent.com.json`. After you **reset the client secret** or download again, the filename may get a numeric prefix (e.g. `client_secret_2_<CLIENT_ID>.apps.googleusercontent.com.json`). Any of these match **`.gitignore`** `client_secret*.json`.

If you save that file in the Novel App folder, treat it as **credentials**: the `web` object includes **`client_id`**, **`client_secret`**, and may list **`redirect_uris`** / **`javascript_origins`** for reference.

- **Do not commit** that file. This repo **`.gitignore`** includes **`client_secret*.json`** so it stays out of git.
- Laravel does **not** read this JSON automatically. **Use it by copying values into `.env`:** **`web.client_id`** → **`GOOGLE_CLIENT_ID`**, **`web.client_secret`** → **`GOOGLE_CLIENT_SECRET`**. Then run **`php artisan config:clear`** locally and update **staging / production** secrets the same way (see **Git vs test / staging / production** above). When only the secret was rotated, **`GOOGLE_CLIENT_ID`** usually stays the same; **`GOOGLE_CLIENT_SECRET`** must match the **current** `web.client_secret` in the newest download.

### 1.6 Novel App — one Google Web client (development, staging, production)

This project uses a **single** OAuth 2.0 **Web application** client for local dev, staging, and production. That works as long as **every** origin and **every** redirect URI you use is listed on that client in Google Cloud (see **1.4** above). Each deployed environment still has its own **`APP_URL`** and usually its own **`.env`** (or secrets store); only **`GOOGLE_CLIENT_SECRET`** must stay private—never commit it.

**`GOOGLE_CLIENT_ID`** (all environments):

```text
887404542021-pq0u70ni93tbkr1au8n3di178v7o19pj.apps.googleusercontent.com
```

### 1.7 `.env` (Google) per environment

| Environment | `APP_URL` example | `GOOGLE_REDIRECT_URI` (if not using default) |
|-------------|-------------------|-----------------------------------------------|
| Local | `http://127.0.0.1:8000` | Same as `APP_URL` + `/auth/google/callback` |
| Staging | `https://staging.example.com` | `https://staging.example.com/auth/google/callback` |
| Production | `https://yourdomain.com` | `https://yourdomain.com/auth/google/callback` |

Use the **same** `GOOGLE_CLIENT_ID` in each `.env`. Set **`GOOGLE_CLIENT_SECRET`** from the same Google client (identical across envs unless you rotate and update all deployments).

```dotenv
GOOGLE_CLIENT_ID=887404542021-pq0u70ni93tbkr1au8n3di178v7o19pj.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-secret-from-google-console
# Optional if APP_URL is correct:
# GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

---

## 2. Apple Developer

### 2.1 App ID (bundle / app identifier)

1. [Apple Developer](https://developer.apple.com/) → **Certificates, Identifiers & Profiles**.
2. **Identifiers** → **+** → **App IDs** → **App**.
3. Enable **Sign In with Apple** for your app id (used if you have a native app; for web-only flows you still need Services ID + key).

### 2.2 Services ID (this is `APPLE_CLIENT_ID` for web)

1. **Identifiers** → **+** → **Services IDs**.
2. Create an identifier (e.g. `com.yourcompany.novel.web`).
3. Enable **Sign In with Apple**, click **Configure**:
   - **Primary App ID**: select the App ID from 2.1.
   - **Domains and Subdomains**: e.g. `yourdomain.com`, `staging.example.com` (no `https://`).
   - **Return URLs**: **exact** callback URLs:
     - `https://yourdomain.com/auth/apple/callback`
     - `https://staging.example.com/auth/apple/callback`
     - Local (Apple often requires HTTPS; many teams use **ngrok** or similar): `https://your-tunnel.ngrok.io/auth/apple/callback`
4. Save, continue, register.

### 2.3 Sign in with Apple **key** (.p8)

1. **Keys** → **+** → name it → enable **Sign In with Apple** → **Configure** → pick Primary App ID → save.
2. **Download** the `.p8` once; note **Key ID** and your **Team ID** (membership page).
3. Store the file **outside the web root** on each server; set `APPLE_PRIVATE_KEY` to the **absolute path**.

### 2.4 Alternative: static JWT as `APPLE_CLIENT_SECRET`

You can generate a JWT (kid, iss=Team ID, sub=Services ID, exp ≤ ~6 months) and set **`APPLE_CLIENT_SECRET`** instead of the key trio. Rotate before expiry; see **`docs/local-development.md`** (Apple client secret rotation).

### 2.5 `.env` (Apple) per environment

Return URLs in Apple Developer must match **`APPLE_REDIRECT_URI`** (or `APP_URL/auth/apple/callback`).

| Environment | Notes |
|-------------|--------|
| Local | Apple may reject plain `http://localhost`; use HTTPS tunnel or test Apple on staging. |
| Staging / Production | HTTPS domain registered under Services ID; return URL includes `/auth/apple/callback`. |

**Recommended (key file):**

```dotenv
APPLE_CLIENT_ID=com.yourcompany.novel.web
APPLE_TEAM_ID=XXXXXXXXXX
APPLE_KEY_ID=YYYYYYYYYY
APPLE_PRIVATE_KEY=/secure/path/AuthKey_YYYYYYYYYY.p8
# APPLE_REDIRECT_URI="${APP_URL}/auth/apple/callback"
```

**Or JWT:**

```dotenv
APPLE_CLIENT_ID=com.yourcompany.novel.web
APPLE_CLIENT_SECRET=eyJ...
```

---

## 3. Checklist before go-live

- [ ] Each environment’s **`APP_URL`** matches how users open the site.
- [ ] After a Google **client secret** rotation, **`GOOGLE_CLIENT_SECRET`** is updated everywhere (local `.env`, staging, production, CI) from the latest **`client_secret_*.json`** or the Console, then **`php artisan config:clear`** where applicable.
- [ ] Google **redirect URIs** include every environment you use.
- [ ] Apple **Return URLs** and **domains** include every HTTPS host you use for Apple login.
- [ ] `.p8` not committed to git; path on server is readable by PHP only as needed.
- [ ] `php artisan config:clear` after deploy.
- [ ] Apple callback is **POST**; this app disables CSRF only for `auth/apple/callback` (see `bootstrap/app.php`).

For day-to-day local URL and session tips, see **[local-development.md](local-development.md)**.
