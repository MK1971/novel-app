# Reset database for testing (admin only)

Use this on a **local** or **throwaway** database only. **Never** run against production.

## What it does

The Artisan command **`db:reset-app-data`**:

1. **Truncates** every application table (SQLite / MySQL / MariaDB / PostgreSQL), except **`migrations`** (and SQLite housekeeping).
2. Runs **`Database\Seeders\AdminOnlySeeder`**, which creates **one** user with **`is_admin = true`**.

All other rows (chapters, edits, payments, notifications, non-admin users, etc.) are removed.

## Command

Interactive (asks for confirmation):

```bash
php artisan db:reset-app-data
```

Non-interactive (scripts / CI on a dev DB):

```bash
php artisan db:reset-app-data --force
```

## Admin login after reset

- **Email:** **`ADMIN_EMAIL`** from **`.env`**, or default **`admin@example.com`**.
- **Password:** **`password`** (set by **`AdminOnlySeeder`**).

Override the email before reset if you need a specific admin address:

```env
ADMIN_EMAIL=you@example.local
```

## Implementation reference

- **`app/Console/Commands/ResetAppDataCommand.php`** — discovers table names per driver, disables FK checks, truncates, then seeds.
- **`database/seeders/AdminOnlySeeder.php`** — single admin account.

## After reset

- Re-create books/chapters via **Admin** (or run a demo seeder if you maintain one).
- Run **`php artisan migrate`** if the schema changed since last run.
- If you use **`php artisan storage:link`** for uploads, the link remains; **`public/storage`** files are unchanged unless you delete them manually.
