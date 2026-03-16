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

## Version 1.4.2 - Landing Page and Sidebar Fixes

This release addresses two critical display issues reported by the user. The landing page had a broken HTML `</head>` tag that could cause rendering inconsistencies across browsers. The sidebar navigation was rendering but appeared compressed due to missing flex-shrink constraints. Both issues have been resolved, and the frontend assets have been rebuilt to ensure all Tailwind CSS classes are properly compiled.

## Version 1.4.3 - Dashboard Link Visibility Fix

The Dashboard link in the sidebar is now hidden for guest users. Previously, clicking the Dashboard link as a guest would redirect to the sign-in page, which was confusing. The link now only appears for authenticated users who have access to the dashboard. Guest users will see only the public navigation links: Chapters, Leaderboard, and Peter Trull.

## Version 1.6.0 - Final Roadmap Features & Content Seeding

### Added
- **Animated Landing Page:** Implemented an animated typing headline on the landing page that cycles through the book's premise.
- **Visual Enhancements:** Added a high-quality background image with a gradient overlay and dual Call-to-Action (CTA) buttons to the landing page.
- **Content Seeding:** Extracted and seeded the actual chapter content from the provided Word documents:
    - *The Book With No Name*: Chapter 1 (The Day I Was Born).
    - *Peter Trull Solitary Detective*: Chapter 1 Version A and Version B.
- **Voting Logic:** Implemented strict voting restrictions for the Peter Trull story, ensuring only contributors to "The Book With No Name" can participate.
- **UI Refinements:** Added a feedback icon to the sidebar and updated the sidebar navigation for better clarity.
- **Feedback System:** Integrated a feedback submission system for users and an admin management interface.
- **Archives & Analytics:** Added an archives page for past chapters and an analytics hub for administrative overview.
- **User Profiles:** Created detailed user profile pages with contribution statistics and reading progress tracking.

## Version 1.6.0 - Final Roadmap Features & Content Seeding

### Added
- **Animated Landing Page:** Implemented an animated typing headline on the landing page that cycles through the book's premise.
- **Visual Enhancements:** Added a high-quality background image with a gradient overlay and dual Call-to-Action (CTA) buttons to the landing page.
- **Content Seeding:** Extracted and seeded the actual chapter content from the provided Word documents:
    - *The Book With No Name*: Chapter 1 (The Day I Was Born).
    - *Peter Trull Solitary Detective*: Chapter 1 Version A and Version B.
- **Voting Logic:** Implemented strict voting restrictions for the Peter Trull story, ensuring only contributors to "The Book With No Name" can participate.
- **UI Refinements:** Added a feedback icon to the sidebar and updated the sidebar navigation for better clarity.
- **Feedback System:** Integrated a feedback submission system for users and an admin management interface.
- **Archives & Analytics:** Added an archives page for past chapters and an analytics hub for administrative overview.
- **User Profiles:** Created detailed user profile pages with contribution statistics and reading progress tracking.

## Version 1.5.0 - Roadmap Implementation & Visual Overhaul

### Added
- New landing page design based on the project roadmap.
- Integrated "How It Works" section with Part 1 and Part 2 details.
- Prize highlight section for the top contributor.
- Stats section showing community impact.
- Sidebar CTA for guest users to join the adventure.

### Changed
- Updated global theme with warm amber colors and modern typography (Nunito).
- Redesigned all public pages (Chapters, Leaderboard, Vote) with a card-based UI.
- Refined navigation flow: Sidebar is now the primary navigation for internal pages.
- Updated layouts to be more spacious and visually engaging.
- Improved mobile responsiveness for the new design elements.

## Version 1.7.0 - Critical Bug Fixes and Feature Refinements

### Fixed
- **Authentication Modals:** Resolved issues where "Sign In," "Create Account," and "Join" buttons were not correctly triggering their respective modals. All authentication modals are now fully functional.
- **Chapter Titles Display:** Corrected the display of chapter titles and subtitles. "Chapter 1: The Day I Was Born" and its subtitle are now correctly visible on the story pages.
- **Landing Page Text Readability:** Adjusted the color tone of the "A two-part collaborative journey..." text on the landing page from `text-white/70` to `text-white/90` for improved legibility.
- **Feedback Display:** Enhanced the feedback page to display recent feedback entries alongside the submission form, utilizing a two-column layout.
- **Admin Access Button:** Added a temporary "Admin Panel" button to the user dropdown menu, visible only to the admin user (`admin@example.com`).
- **Chapter Text Alignment:** Ensured that all chapter content, including the first line, is strictly left-aligned by removing conflicting CSS rules and applying consistent `text-left` styling.
- **Landing Page Headline Wrapping:** Fixed the animated headline on the landing page to wrap correctly, preventing text cutoff on various screen sizes.
- **Chapter Stats Update Logic:** Resolved an issue where chapter statistics (Total Reads, Total Edits, Accepted Edits, Votes) were not updating dynamically. The system now accurately reflects these metrics in real-time.
- **Edit Textarea Content:** Modified the "Your Edited Text" textarea in the "Suggest an Edit" form to start empty, providing a cleaner user experience.
- **Admin Dashboard Accepted Edits Count:** Corrected the "Accepted Edits" metric on the admin dashboard to include both `accepted_full` and `accepted_partial` statuses, providing a more accurate overview of contributions.
- **Points System Accuracy:** Adjusted the points awarded for edits to align with the 0, 1, or 2 point system. Test3's points were corrected to 1, and the UI now displays "+1-2 pts" for accepted edits.
- **Votes Reflection in Chapter Stats:** Ensured that votes cast on Peter Trull chapters are now correctly reflected in the `total_votes` count within the `ChapterStatistic` model, updating in real-time.

### Added
- **Reading Progress Tracking:** Implemented a system to track user reading progress within chapters.
- **Chapter Statistics Display:** Integrated real-time display of chapter engagement metrics (Reads, Edits, Accepted, Votes) on chapter pages.
- **Achievement Badges System:** Developed a system for awarding achievement badges based on user contributions and actions.
- **Live Activity Feed:** Created a live feed to display recent community activities.
- **Notification System:** Implemented a user notification system for various in-app events.

### Changed
- **Admin Dashboard Content:** Populated the Admin Dashboard with key metrics, pending edits, recent feedback, and top contributors, providing a comprehensive management interface.
- **Analytics Filtering:** Updated the Analytics page to filter out Peter Trull chapters from contribution activity, as they are for voting only.
- **Voting Logic & Session Persistence:** Implemented robust checks to ensure only users with accepted edits can vote on Peter Trull chapters, and users can vote only once per chapter. Voting buttons are now correctly grayed out for ineligible users.
