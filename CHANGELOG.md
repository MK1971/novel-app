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

### Redirection & Layout Refinement
- **Global Auth Modals:** Refactored the authentication modals to be included globally in the `guest` and `app` layouts. This ensures that sign-in and registration can be initiated from any page without redundant code.
- **Persistent Page Redirection:** Enhanced the redirection logic to ensure that users stay on the exact page where they initiated the sign-in or registration process, providing a truly seamless experience across the entire site.
- **Cleaned Up Views:** Removed redundant modal inclusions from individual views (Chapters, Leaderboard, Vote), resulting in cleaner and more maintainable Blade templates.

## Version 1.2.0 - Security & Accessibility Enhancements

### Authentication Improvements
- **Case-Insensitive Email Handling:** Updated the login and registration logic to handle emails in a case-insensitive manner. All emails are now normalized to lowercase before being stored or used for authentication, preventing login issues caused by capitalization.

### Security & Browser Compatibility
- **Enhanced Session Security:** Updated the application's session and cookie configuration to use secure defaults. This includes enabling session encryption, enforcing secure cookies (HTTPS only), and setting `HttpOnly` and `SameSite` attributes to mitigate common web vulnerabilities and ensure a warning-free experience in modern browsers.
- **Environment Configuration:** Refined the `.env` file with secure session settings to ensure consistency across different environments.

## Version 1.2.1 - Final Security & Validation Fixes

### Authentication Fixes
- **Removed Email Case Restriction:** Removed the `lowercase` validation rule from the registration process. Users can now enter their email in any case, and the system will handle it correctly by normalizing it to lowercase in the background.

### Security & Proxy Configuration
- **Trusted Proxy Configuration:** Configured the application to trust all proxies in `bootstrap/app.php`. This ensures that the application correctly identifies the secure (HTTPS) connection provided by the proxy, resolving browser warnings about "information submitted over an unsecured line."

## Version 1.2.2 - Auth Modal UX Fix

### User Experience Improvements
- **Registration Failure Handling:** Fixed an issue where registration failures (e.g., email already taken) would incorrectly redirect the user to the login modal. The system now correctly identifies registration attempts and keeps the "Create account" modal open with the appropriate error messages, ensuring a smoother user experience.

## Version 1.3.0 - Admin & UX Enhancements

### User Experience Improvements
- **Password Hint Popup:** Added a helpful password requirement hint to the registration form. Users can now hover over or click an information icon to see the suggested password criteria (length, casing, numbers, and special characters).

### Admin Dashboard Enhancements
- **User Management List:** Implemented a new "Users" section in the admin dashboard. The administrator can now view a complete list of all registered contributors, including their names, emails, points, and join dates.
- **Navigation Updates:** Added direct links to the User Management section in both the desktop and mobile navigation menus for admin users.

## Version 1.4.0 - Sidebar Navigation

### User Experience Improvements
- **Sidebar Navigation Menu:** Added a stylish, semi-transparent sidebar to the left of the application. This provides a consistent and easily accessible way to navigate between the Dashboard, Chapters, Leaderboard, and Peter Trull pages.
- **Contextual Links:** The sidebar dynamically updates based on the user's role, showing administrative links (Review Suggestions, Upload Chapters, User Management) only to authorized admins.
- **Responsive Design:** The sidebar is optimized for larger screens and complements the existing top navigation bar, which remains available for mobile users.

## Version 1.4.1 - Sidebar Visibility Fix

### Bug Fixes
- **Sidebar Visibility:** Fixed an issue where the sidebar was hidden due to redundant navigation bars and incorrect responsive classes.
- **Layout Cleanup:** Removed redundant top navigation bars from guest views (Chapters, Leaderboard, Peter Trull) to allow the sidebar to be the primary navigation method.
- **Improved Responsiveness:** Adjusted sidebar visibility to appear on medium-sized screens and above, ensuring a better experience across different devices.
