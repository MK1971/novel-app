# Novel App Setup Guide – Steps Completed Today

**Project:** Crowdsourced Novel App – "The Book With No Name"  
**Stack:** Laravel 12, PHP 8.3, MySQL, PayPal, Blade

---

## Phase 1: Initial Setup (Previously Completed)

- Installed Laravel with Breeze (auth)
- Installed Node.js and npm
- Set up basic project structure

---

## Phase 2: Database Migrations

### 1. Add points to users
- Created migration: `add_points_to_users_table`
- Added `points` column (unsigned integer, default 0)

### 2. Create books table
- Columns: id, name, status, winner_id, timestamps

### 3. Create chapters table
- Columns: id, book_id, title, number, content, version (A/B), status, timestamps

### 4. Create edits table
- Columns: id, user_id, chapter_id, type, original_text, edited_text, status, points_awarded, timestamps

### 5. Create payments table
- Columns: id, user_id, amount_cents, payment_id, status, edit_id, timestamps

### 6. Create votes table
- Columns: id, user_id, chapter_id, version_chosen, timestamps, unique (user_id, chapter_id)

### 7. Run migrations
```bash
php artisan migrate
```

---

## Phase 3: Models

- **User** – Added points to fillable, relationships: edits(), payments(), votes()
- **Book** – name, status, winner_id, relationships to chapters and winner
- **Chapter** – book_id, title, number, content, version, status
- **Edit** – user_id, chapter_id, type, original_text, edited_text, status
- **Payment** – user_id, amount_cents, payment_id, status, edit_id
- **Vote** – user_id, chapter_id, version_chosen

### BookSeeder
- Seeds one book ("The Book With No Name") and Chapter 1

---

## Phase 4: Stripe → PayPal

- Switched from Stripe to PayPal
- Updated `composer.json`: removed `stripe/stripe-php`, added `srmklive/paypal:~3.0`
- Created `config/paypal.php`
- Updated `PaymentController` for PayPal Checkout flow

---

## Phase 5: Controllers & Routes

### Controllers
- **PaymentController** – checkout() and success() for PayPal
- **ChapterController** – index(), show()
- **EditController** – create(), store()
- **Admin/EditApprovalController** – index(), approve(), reject()
- **VoteController** – index(), store()

### Routes
- /chapters, /chapters/{chapter}
- /payment/checkout, /payment/success
- /chapters/{chapterId}/edit, /edits (POST)
- /vote, /vote/{chapter} (POST)
- /leaderboard
- /admin/edits (admin only)

### Admin gate
- Defined in `AppServiceProvider`: checks `ADMIN_EMAIL` from .env

---

## Phase 6: Views (Blade)

- chapters/index.blade.php – List chapters
- chapters/show.blade.php – Read chapter, Pay $2 button
- edits/create.blade.php – Edit submission form
- admin/edits/index.blade.php – Pending edits, approve/reject
- vote/index.blade.php – Vote on A vs B chapter versions
- leaderboard.blade.php – Top users by points
- Updated navigation with links: Chapters, Leaderboard, Vote, Admin

---

## Phase 7: PayPal Configuration

### .env
```
PAYPAL_MODE=sandbox
PAYPAL_SANDBOX_CLIENT_ID=your_client_id
PAYPAL_SANDBOX_CLIENT_SECRET=your_client_secret
```

### Credentials
- From [developer.paypal.com](https://developer.paypal.com) → My Apps & Credentials → Sandbox
- Create app or use existing → Copy Client ID and Secret

### Verification
- Run `php artisan paypal:check` to verify config
- Run `php artisan config:clear` after changing .env

---

## Phase 8: Composer & Dependencies

### Issues resolved
- Composer timeout: added `"process-timeout": 600` to composer.json
- Stripe removal: deleted vendor folder, ran `composer install` (or `composer remove stripe/stripe-php` then `composer update`)

---

## Phase 9: Deployment Preparation

- Created `DEPLOYMENT.md` with cPanel deployment steps
- Build commands: `composer install --optimize-autoloader`, `npm run build`
- Upload to GoDaddy, configure public_html, run migrations

---

## Commands Reference

| Task | Command |
|------|---------|
| Start server | `php artisan serve` |
| Run migrations | `php artisan migrate` |
| Seed database | `php artisan db:seed` |
| Clear config | `php artisan config:clear` |
| Check PayPal | `php artisan paypal:check` |
| Build assets | `npm run build` |

---

## Project Location

```
~/Documents/novel-app
```

---

## Can You Stop and Continue Tomorrow?

**Yes.** You can stop now and continue later. The project is in a working state:

- All code is saved
- Database is migrated and seeded
- PayPal (sandbox) is working
- You can run `php artisan serve` tomorrow and pick up where you left off

### To resume tomorrow
1. Open the project in Cursor/IDE
2. Run `php artisan serve`
3. Visit http://localhost:8000

---

## Converting This to Word

1. Open this file in Word: File → Open → select `SETUP_GUIDE_TODAY.md`
2. Or copy the contents and paste into a new Word document
3. Or use an online converter (e.g. markdown to Word)
