# Phase 10: Deployment to GoDaddy cPanel

## Step 10.1: Prepare for Production (run locally)

```bash
cd ~/Documents/novel-app
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 10.2: Upload Files via FTP

1. Connect to GoDaddy cPanel with FileZilla (or another FTP client)
2. Upload the **entire** `novel-app` folder to your home directory
3. Rename it to `laravel` (or `app`) for clarity
   - Path will be: `/home/yourusername/laravel/`

## Step 10.3: Set Up public_html

1. In cPanel File Manager, go to `public_html/`
2. Copy all contents from `laravel/public/` into `public_html/`
   - This includes: index.php, .htaccess, and the build folder

## Step 10.4: Edit public_html/index.php

Open `public_html/index.php` and change the require paths:

**From:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**To:**
```php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

(Use your folder name if different from `laravel`)

## Step 10.5: Create .env on Server

Create or edit `.env` in the `laravel` folder on the server:

```
APP_NAME="Novel App"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password

PAYPAL_MODE=live
PAYPAL_LIVE_CLIENT_ID=your_client_id
PAYPAL_LIVE_CLIENT_SECRET=your_client_secret

ADMIN_EMAIL=your@email.com
```

## Step 10.6: Run Migrations on Server

1. In cPanel, open **Terminal** (or use SSH)
2. Run:
```bash
cd ~/laravel
php artisan migrate --force
php artisan db:seed --force
```

## Step 10.7: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

## Step 10.8: Clear Caches (optional)

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## Troubleshooting

- **500 error**: Check `storage/logs/laravel.log`, ensure storage and bootstrap/cache are writable
- **Class not found**: Run `composer dump-autoload` in the laravel folder
- **PayPal errors**: Use live credentials (PAYPAL_LIVE_CLIENT_ID, PAYPAL_LIVE_CLIENT_SECRET) for production
