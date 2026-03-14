# Project Status: Novel App

## Current Progress
- **GitHub Connection:** Successfully connected to GitHub (Account: MK1971).
- **Repository:** Cloned `MK1971/novel-app` to `/home/ubuntu/novel-app`.
- **Build & Setup:** 
  - PHP 8.2 and Node.js dependencies installed.
  - SQLite database configured and migrated.
  - Frontend assets built with Vite/Tailwind.
- **Features Implemented:**
  - **Public Access:** Chapters and Leaderboard are accessible to guests.
  - **Peter Trull Page:** Side-by-side voting interface for chapter versions.
  - **Admin Dashboard:** Secure area for reviewing edits and uploading chapters.
  - **Visual Design:** Warm, amber-themed aesthetic applied site-wide.
  - **Global Leader:** Top contributor displayed on all pages.
- **In-Progress Fixes:**
  - **Auth Redirects:** Login/Register now returns users to their previous page (implemented in `AuthenticatedSessionController` and `modals.blade.php`).
  - **Admin Privacy:** Working on hiding the admin account from the public leaderboard.

## Admin Credentials
- **Email:** `admin@example.com`
- **Password:** `password123`

## Test User Credentials
- **Email:** `test@example.com`
- **Password:** `password123`

## Next Steps for Desktop App
1. Complete the `RegisteredUserController` update for redirects.
2. Update the Leaderboard logic to exclude `admin@example.com`.
3. Verify all changes in the desktop environment.
