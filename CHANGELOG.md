# Changelog: Novel App Development

This document summarizes the key changes and enhancements made to the `novel-app` project during its development.

## Version 1.0.0 - Initial Development & Feature Implementation

### Core Setup and Environment Configuration
- **Repository Cloning:** The `MK1971/novel-app` GitHub repository was successfully cloned to the sandbox environment at `/home/ubuntu/novel-app`.
- **Dependency Installation:**
    - PHP 8.2 and its required extensions were installed.
    - Composer was used to install PHP dependencies.
    - pnpm was used to install Node.js dependencies.
- **Environment Configuration:** The `.env` file was set up, and the application key was generated. SQLite was configured as the database.
- **Database Migrations:** All necessary database migrations and seeders were executed to set up the application schema and initial data.
- **Frontend Build:** Frontend assets were built using Vite and Tailwind CSS.

### Authentication and User Experience Improvements
- **Sign-in/Registration Modals Fix:** Addressed an issue where sign-in and registration modals were not functioning due to incorrect `APP_URL` and `ASSET_URL` configurations. These were updated to reflect the public proxy URL, ensuring proper loading of JavaScript assets.
- **Login/Registration Redirection:** Implemented logic in `AuthenticatedSessionController` and `RegisteredUserController` to redirect users back to their previous page after successful login or registration, enhancing user flow.

### Feature Enhancements
- **"Start Your Adventure" Button:** Modified the landing page button to directly navigate to the chapters index page, allowing guests to browse content immediately.
- **Admin Page Security:**
    - Implemented a robust `admin` gate using Laravel's authorization system, restricting access to admin-specific routes (e.g., `/admin/edits`, `/admin/chapters`) to users with the `admin@example.com` email address.
    - Applied this gate to `AdminChapterController` and `EditApprovalController` to secure administrative actions.
- **Leaderboard Implementation:**
    - Introduced a dedicated leaderboard page displaying top contributors based on points.
    - Integrated a global leader display in the navigation bar, showing the current top contributor and their points on every page.
- **Peter Trull Solitary Detective Page:**
    - Made the Peter Trull comparison page (`/vote`) publicly accessible to guests.
    - Developed a side-by-side voting interface for chapter versions (Version A vs. Version B).
    - Restricted voting functionality to registered users who have contributed at least one edit to "The Book With No Name", with clear prompts for non-contributors.

### Theming and Visual Consistency
- **Site-wide Aesthetic:** Applied a consistent warm, amber-themed design across the entire application, including:
    - The landing page.
    - Chapters index and show pages.
    - Leaderboard page.
    - User dashboard.
    - Admin dashboard (Upload Chapters and Review Suggestions).
- **UI Refinements:** Updated various UI elements such as cards, buttons, and navigation bars to align with the new aesthetic, improving overall visual appeal and user experience.

### Test User Management
- **Admin User Creation:** Created an admin user with email `admin@example.com` and password `password123` for managing content and reviewing edits.
- **Test Contributor Creation:** Created a test user with email `test@example.com` and password `password123` to simulate a regular contributor's experience, including suggesting edits and voting.

## Version 1.1.0 - User Experience & Privacy Enhancements

### Authentication Improvements
- **Registration Redirection:** Completed the implementation of the registration redirect logic, ensuring users are returned to their previous page after creating an account.

### Privacy & Leaderboard Updates
- **Admin Privacy:** Updated the leaderboard and global leader display to exclude the administrator account (`admin@example.com`). This ensures that only regular contributors are featured in the rankings.
- **Global Leader Logic:** Refined the `AppServiceProvider` to filter out the admin when calculating the top contributor for the navigation bar display.
