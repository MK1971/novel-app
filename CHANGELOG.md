# Changelog: Novel App Development

This document summarizes the key changes and enhancements made to the `novel-app` project during its development.

## Version 1.0.0 - Initial Development & Feature Implementation
- Core setup, authentication fixes, and initial feature implementation.

## Version 1.1.0 - User Experience & Privacy Enhancements
- Registration redirection and admin privacy on leaderboard.

## Version 1.2.0 - Security & Accessibility Enhancements
- Case-insensitive email handling and session security.

## Version 1.3.0 - Admin & UX Enhancements
- Password hint popup and initial user management list.

## Version 1.4.0 - Sidebar Navigation
- Added sidebar navigation and role-based link visibility.

## Version 1.5.0 - Roadmap Implementation & Visual Overhaul
- New landing page design, global theme update, and redesigned public pages.

## Version 1.6.0 - Final Roadmap Features & Content Seeding
- Animated landing page, content seeding, and advanced voting logic.

## Version 1.7.0 - Critical Bug Fixes and Feature Refinements
- Auth modal fixes, chapter stats, and reading progress tracking.

## [2026-03-21] - Final Critical Fixes & Restoration
### Fixed
- **Reading Progress Persistence**: Scroll position is now saved to the database and restored upon returning to a chapter.
- **Direct Navigation**: Users are now automatically redirected to their last read chapter when clicking "Start Reading".
- **Rejected Edits Display**: The user dashboard now correctly displays the count of rejected suggestions.
- **Explore Section Auth**: Fixed the landing page to correctly show "Go to Dashboard" for logged-in users.
- **Voting Rights**: Restored voting eligibility by checking for any accepted edits (full or inline) in the user's history.
- **Admin Redirect**: Admins are now redirected to the moderation panel upon login.
- **Admin User Management**: Implemented user editing and role management for administrators.
- **UI Restoration**: Restored the modern, high-end look and feel across all views after accidental reversion.
- **Inline Edits**: Re-integrated paragraph-level edit suggestions into the modern chapter view.
- **Missing Parameter Error**: Fixed a critical crash in the chapter view caused by a missing route parameter.

### Added
- `is_admin` column to `users` table for role-based access control.
- `AdminMiddleware` to protect administrative routes.
- `UserManagementController` for administrative user operations.
- `track-progress` endpoint for real-time reading progress updates.

## [2026-03-22] - Final Feature Implementation & Deployment Preparation
### Added
- **Reading Progress Persistence**: Implemented persistence of reading progress, including scroll position tracking.
- **Smart Navigation**: Added smart navigation to resume the last read chapter.
- **Inline Paragraph Editing System**: Developed an inline paragraph editing system.
- **Admin Moderation Panel**: Created an admin moderation panel for edits and votes.
- **User Management Interface**: Implemented a user management interface for administrators.
- **Chapter Locking System**: Introduced a chapter locking system, allowing only the latest chapter to be editable.
- **Voting Rights Enforcement**: Enforced voting rights, requiring accepted edits for eligibility.
- **Achievement System**: Integrated an achievement system into the user dashboard.
- **Password Update Functionality**: Added functionality for users to update their passwords.
- **Activity Feed Fixes**: Applied fixes to the activity feed.
- **Modern UI Restoration and Consistency**: Restored and ensured consistency of the modern UI.

### Changed
- **Deployment Preparation**: Updated `.env` file with GoDaddy MySQL configuration placeholders.
- **Database Seeder**: Modified `DatabaseSeeder.php` to include admin account and initial chapters.

### Fixed
- Resolved various issues related to activity feed, chapter stats, and user name display.
- Addressed multiple iterations of chapter locking and consolidation issues.

## Version 1.8.0 - UI/UX Enhancements and Bug Fixes
### Fixed
- **Navigation Menu**: Made the main navigation menu sticky at the top of the screen, ensuring it remains visible on scroll.
- **Peter Trull Chapter Locking**: Resolved a bug where Peter Trull chapters remained locked for editing after payment, allowing users to submit edits as intended.

### Changed
- **Payment Feedback**: Enhanced payment success and failure messages for clarity and user guidance.
- **Post-Payment Scroll**: Implemented auto-scrolling to the edit submission box after a successful payment for improved user experience.

## Version 1.9.0 - Landing UX, Live Stats, and Stability
### Added
- **Skip to main content**: Off-screen skip control implemented as a `<button>` (Safari-friendly default Tab order) with reduced-motion-aware scroll and focus move to `#main-content`.
- **Landmarks and structure**: `#landing-root`, `<main id="main-content" tabindex="-1">`, and an `sr-only` heading for the community stats strip with `aria-labelledby`.
- **Mobile navigation**: Hamburger menu (below `md`) with a compact dropdown panel anchored under the control; Escape and click-outside to close.
- **Hero enhancements**: Stronger hero overlay and text shadows for readability; static headline (no typewriter caret) below `768px`; subline under “Start Your Adventure” clarifying the chapters CTA.
- **Motion and contrast**: `prefers-reduced-motion` handling for typewriter, hero ping, journey cards, and hero buttons; focus ring styles for nav, hero, sections, and footer.
- **Trust and stats**: Social proof block (project tagline / “Reader-powered fiction”); live stats on the home route from the database—contributors with accepted edits, accepted edits count, published chapter count; campaign prize line via `config/marketing.php` and optional `LANDING_PRIZE_POOL_DISPLAY` in `.env` (documented in `.env.example`); footnote distinguishing live tallies from the prize goal.
- **Background behavior**: `background-attachment: scroll` on small viewports; `fixed` from `md` and up to reduce iOS jank with parallax-style hero.
- **Developer tooling**: `docs/landing-ux-suggestions.txt` checklist; local-only route and view `/dev/landing-ux-suggestions` (when `app()->isLocal()`); `.cursor/rules/revert-snapshots.mdc` describing the `_local_backups/welcome-original.blade.php` baseline and optional timestamped snapshots.
- **Tests**: `WelcomeLandingTest` (structure, a11y hooks, motion/focus CSS markers); `VoteIndexTest` for incomplete Peter Trull A/B chapter pairs; `RefreshDatabase` on `ExampleTest` for home DB queries; `ProfileTest` expectations aligned with redirect to `profile.edit` after profile update.
- **Chapters (consistency)**: `ChapterController@show` redirects to the chapter index when a chapter is locked and the book is not *Peter Trull Solitary Detective* (replacing a fragile `header()`/`exit` in the Blade view).

### Changed
- **Landing navigation**: Opaque white bar with amber text links and `bg-amber-500` primary actions (Join Now / Dashboard) so the bar stays visible over light sections; brand and mobile menu control styled to match.
- **Home route**: Computes `$landingStats` and passes them to `welcome` (abbreviated counts for large numbers).
- **Repository hygiene**: `.gitignore` extended for `/_local_backups` and `/.revert-snapshots`; `.env` is not committed—each environment keeps its own file or secrets (see `.env.example`).

### Fixed
- **Peter Trull / vote index**: Prevented errors when a chapter number has only version A or only version B—logic that used both versions now runs only when A and B exist.
- **App layout dropdown**: Corrected Blade attribute quoting in `layouts/app.blade.php` (`route('…')`, `onclick` / `closest('form')`) so compiled views no longer throw parse errors and authenticated layouts render reliably.
- **Chapters index (merged behavior)**: Locked chapters show the dashed “Chapter locked” teaser; open chapters keep paragraph-level content with inline edit affordances where applicable.
