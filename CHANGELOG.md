# Changelog: Novel App Development

This document summarizes the key changes and enhancements made to the `novel-app` project during its development.

## Version 1.9.60 - Cloudways: one-shot all-env deploy + cron uses git_repo
### Added
- **`scripts/deploy/cloudways_all_envs_once.sh`:** Run **once** with **`sudo bash …`** on the Cloudways server — pulls **Development**, **staging**, and **Production** in each **`git_repo`**, **rsync** to **`public_html`**, then **`dev_after_pull`** or **`server_post_deploy`** as the **`master_…`** user.

### Fixed
- **`cron_git_pull_deploy.sh`:** Uses **`NOVEL_GIT_DIR`** (default **`…/git_repo`**) for **`git fetch` / merge**; **rsync** into **`NOVEL_APP_ROOT`** (`public_html`) so cron matches Cloudways layout (repo is not inside `public_html`).

## Version 1.9.59 - Landing: Peter Trull card copy tweak
### Changed
- **`welcome.blade.php`:** Peter Trull teaser — “A mystery shaped by you” → “A mystery shaped by ghosts that haunt the traumatized.”

## Version 1.9.58 - Cloudways: copy deploy scripts into public_html
### Added
- **`scripts/deploy/cloudways_copy_deploy_scripts.sh`:** On Cloudways SSH, copies **`cron_git_pull_deploy.sh`** and **`dev_after_pull.sh`** from **`git_repo/scripts/deploy`** into **`public_html/scripts/deploy`** when the panel pull updated **`git_repo`** but **`public_html`** does not yet contain those files (needed for Application Cron / **`NOVEL_APP_ROOT`**).

## Version 1.9.57 - Cron git pull + deploy
### Added
- **`scripts/deploy/cron_git_pull_deploy.sh`:** For **cron** on the app server — **`git fetch`**, skip if already up to date, **`git merge --ff-only`** when GitHub is ahead, then **`dev_after_pull`** or **`server_post_deploy`** via **`NOVEL_DEPLOY_PROFILE`**. Uses **`flock`** to avoid overlapping runs; **`NOVEL_APP_ROOT`** / **`NOVEL_GIT_BRANCH`** / lock path documented in script header.

## Version 1.9.56 - Dev post-pull helper script
### Added
- **`scripts/deploy/dev_after_pull.sh`:** Run on Cloudways after **`git pull`** on dev/staging — composer, migrate, **`optimize:clear`**, optional **`npm run build`**, queue restart; prints git tip and checks for landing copy marker.

## Version 1.9.55 - Landing: prizes progression + journey copy
### Changed
- **`welcome.blade.php`:** Joy-first hero (“Be part of a living novel…”). **The Journey** reframed as “Two books. Two ways to play.” with a clear two-book explanation and **Learn how it all works →** to `#landing-how-steps`. New **What You Could Win** block after the Peter Trull card (prize ladder + leaderboard line + link to **`prizes`**).
- **`welcome.blade.php`:** Book 1 / Book 2 **Journey** cards — updated teaser copy under **The Book With No Name** and **Peter Trull Solitary Detective** headings (six-lives / Navy CPTSD storylines + $2 / voting lines).

## Version 1.9.54 - OAuth stateless session, pilot chapters, prizes ladder copy
### Fixed
- **Google OAuth (first visit):** Socialite **`stateless()`** on Google redirect/callback avoids session/state mismatch on the first OAuth round-trip in a fresh browser session.

### Added
- **TBWNN pilot chapters:** **`is_pilot`** and **`reader_blurb`** on chapters; pilot rounds close after **`TBWNN_PILOT_CLOSE_AFTER_ACCEPTED`** accepted suggestions (default 50), not the one-month calendar window; optional blurb for Peter Trull / book intros on the chapter page.
- **Prizes page:** Full **prize ladder** (character → book → cover → lasting recognition) above the grand-prize section.

### Changed
- **Chapter reader surfaces:** Clearer pilot messaging on chapter index and show; **`config/tbwnn.php`** documents pilot behavior.

## Version 1.9.53 - Security hardening: deps + HTTP headers
### Fixed
- **Composer advisories:** Bumped **league/commonmark** to **2.8.2** and **phpseclib/phpseclib** to **3.0.51** (addresses prior `composer audit` findings for embed allowed_domains and SSH2 HMAC handling).

### Added
- **`SecurityHeadersMiddleware`:** Sends **X-Content-Type-Options**, **Referrer-Policy**, **X-Frame-Options** on all HTTP responses; **Strict-Transport-Security** (with **includeSubDomains**) in **production** when **`APP_URL`** is **https**. Registered globally so **`/up`** and web routes are covered.
- **`SecurityHeadersTest`:** Asserts baseline headers on the health endpoint.

### Changed
- **`phpunit.xml`:** Sets **`APP_KEY`** for the test suite so feature tests bootstrap encryption consistently.

## Version 1.9.52 - ADMIN_EMAIL works with config:cache
### Fixed
- **Admin gate and `ADMIN_EMAIL`:** `ADMIN_EMAIL` is now **`config('app.admin_email')`**, set from **`.env`** in **`config/app.php`**. Runtime **`env('ADMIN_EMAIL')`** in **`AppServiceProvider`** and related code did not work after **`php artisan config:cache`** (Laravel only loads **`.env`** into config at cache build time), so production admins matched only **`is_admin`** or the wrong default.
- **Call sites:** **`LeaderboardController`**, **`AdminNotifier`**, **`ResetAppDataCommand`**, and **`AdminOnlySeeder`** now read the same config key.

### Added
- **`AdminEmailConfigGateTest`:** Asserts the admin gate honors **`app.admin_email`** when **`is_admin`** is false.

## Version 1.9.51 - Inline edit delete respects admin gate
### Fixed
- **`InlineEditController::destroy`:** Deleting another user’s inline edit now uses **`Gate::allows('admin')`** instead of **`is_admin`** on the model, so operators granted admin via **`ADMIN_EMAIL`** behave the same as **`is_admin`** users.

## Version 1.9.50 - OAuth: www vs apex host match for Google/Apple UI
### Fixed
- **Social login visibility:** `SocialAuthController` now treats the configured OAuth redirect host and the current request host as matching when they differ only by a leading `www.` (e.g. apex vs www), so the Google/Apple buttons show on both URLs while env still uses a single canonical `GOOGLE_REDIRECT_URI`.

## Version 1.9.49 - Production one-shot deploy + NVM for Vite
### Added
- **`scripts/deploy/prod_one_shot.sh`:** One-shot production deploy (composer before `key:generate`, merge `APP_*` / PayPal / Google / DB / `MAIL_*` from optional `prod_secrets.local.sh`, migration recovery for `paragraph_reactions`, Vite build, Laravel caches, health curl). Loads **NVM** (`~/.nvm`) before `npm` when present (e.g. Cloudways) and logs which `node` binary runs.
### Changed
- **`.gitignore`:** Ignore `scripts/deploy/prod_secrets.local.sh` so deploy credentials are not committed.

## Version 1.9.48 - Admin close flow + public edits column fix + modal usability
### Fixed
- **Public edits feed query:** Replaced an invalid `chapters.chapter_number` select with `chapters.number`, resolving SQL 1054 errors on `edits.public`.
- **Paragraph edit modal accessibility:** The chapter inline-edit modal now supports viewport-safe scrolling and includes a persistent top-right close button so actions remain reachable on small screens.
### Changed
- **Admin chapter force-close:** `Close without merged text` for TBWNN now allows locking any open chapter immediately, so admins can proceed to upload the next chapter without moderation gating.
- **Admin guidance copy:** Updated chapter management instructions and confirmation text to clearly describe force-close behavior and next-step upload flow.

## Version 1.9.47 - Vote warning scales by participation
### Changed
- **Vote warning behavior:** On `vote.index`, the large restriction hero now appears only for users with no edit and no vote history; users who already submitted an edit or already voted now always see a compact status message instead.
- **Participation-aware context:** Added controller view flags for `hasEverSubmittedEdit` / `hasEverVoted` so the vote page can keep warning size consistent after first participation.

## Version 1.9.46 - Dark mode readability + environment-safe social auth
### Fixed
- **Dark mode contrast guard:** Added a base CSS safeguard so elements that keep a white background in dark mode cannot render low-contrast light-amber text.
- **Cross-environment OAuth redirects:** Social login buttons and auth redirects are now gated by host matching, so dev/staging won't surface providers configured with production redirect URLs.
- **PayPal checkout env handling:** Donation/checkout proxy-env wrapper now safely handles hosts where `putenv`/`getenv` are unavailable, preventing runtime checkout failures.
### Changed
- **Environment-scoped password policy:** `Password::defaults()` now enforces strict rules only in `staging` and `production`, while keeping development/local requirements lightweight.
- **Reader heading formatting:** Special sections (cold open/prolog/epilog) now render title-only headings when a custom title exists, avoiding repeated labels like “Cold open: …”.

## Version 1.9.45 - MySQL: paragraph_reactions unique index name
### Fixed
- **`paragraph_reactions` migration:** Gave the composite unique index an explicit short name (`para_react_user_ch_idx_type_unq`) so MySQL no longer rejects the migration with **identifier name too long** (error 1059).

## Version 1.9.42 - P4-4 / P4-5 reader + dark mode; Tier A public profile follow-ups
## Version 1.9.43 - Release B completion + Cloudways deploy runbook
## Version 1.9.44 - Legal policy hardening, branding sweep, and deploy identity envs
### Added
- **Legal identity config:** Added `config/legal.php` and new env-driven legal identity fields so Terms/Privacy can render registered entity details consistently.
- **Unified doc exports:** Added refreshed `docs/latest-markdown-step-by-step.md` and `docs/latest-markdown-step-by-step.docx` with current deployment and legal updates.
### Changed
- **Legal pages:** Expanded Terms, Privacy, Refunds, Community Guidelines, and Cookie Policy with clearer compliance language (retention windows, dispute notice, appeals, cookie details, and withdrawal notes).
- **Deploy runbooks:** Added required post-deploy legal env steps (`LEGAL_ENTITY_*`, jurisdiction, dispute notice days) and explicit verification checks for legal pages.
- **Project docs branding:** Replaced scaffolded README framework boilerplate with WhatsMyBookName project-specific setup/deploy guidance.

## Version 1.9.43 - Release B completion + Cloudways deploy runbook
### Added
- **Cloudways deployment runbook:** Added `docs/cloudways-deploy-readiness-and-runbook.md` with step-by-step staging/production setup, webhook setup, smoke tests, and server command checklist.
- **Deploy scripts:** Added `scripts/deploy/server_post_deploy.sh` (server post-pull automation) and `scripts/deploy/webhook_test_local.sh` (local webhook simulation helper).
### Changed
- **Environment template hardening:** Removed real PayPal credentials from `.env.example`; documented `PAYPAL_WEBHOOK_ID` and `PAYPAL_WEBHOOK_TOKEN`.
- **Cloud env docs:** `docs/cloud-environment-setup.md` now includes PayPal webhook signature/fallback env variables.

## Version 1.9.42 - P4-4 / P4-5 reader + dark mode; Tier A public profile follow-ups
### Added
- **Reader themes & focus (P4-4):** Chapter toolbar (**cream / paper / sepia / night**) with **`localStorage`**; **focus mode** hides app chrome and suggest column (**`sessionStorage`**); CSS in **`app.css`**; Alpine **`novelChapterReader`** in **`app.js`**.
- **Site-wide dark mode (P4-5):** Tailwind **`darkMode: 'class'`**; **`novel-theme.js`** cycles **system / light / dark**; **`theme-boot`** inline script + **`theme-toggle`** in app, guest, and welcome layouts; shell/sidebar/nav/dropdown dark styles.
- **Public profile abuse & privacy (Tier A):** **`user_blocks`**, **`profile_reports`**; **`leaderboard_visible`**, **`profile_indexable`** on **`users`**; **`PublicProfileAbuseController`** (**report**, **block**, **unblock**, **`unblockByUser`**); profile **Safety** UI (**Report** / **Block**); **blocked contributors** on **`profile/edit`**; **`noindex`** when indexing off; leaderboard + top contributor respect opt-out.
### Changed
- **`privacy`**: public profiles, reporting/blocks, choices copy.
- **`PublicProfileController`**, **`LeaderboardController`**, **`AppServiceProvider`** (top leader cache key **`layout.top_leader.v2`**), **`PublicProfileSettingsRequest`**, **`ProfileController::edit`**, **`User`** relationships/helpers.
### Tests
- **`PublicProfileTest`**, **`LeaderboardTest`** extended for reports, blocks, toggles, noindex.

## Version 1.9.41 - Avatar upload: replace Laravel generic “failed to upload”
### Fixed
- **Avatar / `validation.uploaded`:** When PHP rejects an upload before the controller runs, users no longer only see **“The avatar failed to upload.”** — **`ProfileUpdateRequest::withValidator`** replaces it with **`UPLOAD_ERR_*`** detail via **`App\Support\UploadFailureMessage`**; **`ProfileController`** uses the same helper.

## Version 1.9.40 - Profile photo errors + public profile 404 clarity
### Changed
- **Avatar upload:** Specific **PHP upload error** messages (size, partial, tmp dir, etc.), **try/catch** around **`Storage::store`** with storage/link + permissions hint; **`ProfileUpdateRequest`** friendly **mimes/size** copy (incl. **HEIC** note).
- **Profile edit:** Red **error summary** lists all validation messages at the top of settings.
- **`x-input-error`:** Handles **MessageBag** / empty values; **bold** + **`role="alert"`**.
- **Public profile settings:** Callout explaining **404** until checkbox + **Save public profile**; shows **live URL** when enabled.

## Version 1.9.39 - P4-2 public profiles + P4-3 email verification (Tier A)
### Added
- **Public contributor profiles (P4-2):** opt-in **`GET /people/{slug}`** (**`profile.public`**); fields **`public_profile_enabled`**, **`public_slug`**, **`profile_bio`**; **`PublicProfileController`**, **`profile/public`** (guest layout + meta); **`PATCH /profile/public-settings`** (**`PublicProfileSettingsRequest`**); **`ReservedPublicProfileSlugs`**; settings block on **`profile/edit`**; **`User::publicProfileUrl()`**; leaderboard contributor names link when public.
- **Email verification UX (P4-3):** **`User`** implements **`MustVerifyEmail`**; **`profile/partials/email-verification-badge`** on **`profile/show`** and edit form; unverified banner on **dashboard** with resend + link to settings.
### Changed
- **`User`** **`$fillable`** / casts; **`UserFactory::withPublicProfile()`**.
### Tests
- **`PublicProfileTest`**, **`LeaderboardTest`** public profile link case.

## Version 1.9.38 - OAuth redirect_uri_mismatch doc + .env.example note
### Changed
- **`docs/oauth-google-apple-setup.md`**: **`redirect_uri_mismatch`** troubleshooting (port, localhost vs 127.0.0.1, **`config:clear`**).
- **`.env.example`**: Stronger **`APP_URL`** comment for **`php artisan serve`** and Google redirect alignment.

## Version 1.9.37 - Google sign-in visibility (UX + config trim)
### Changed
- **OAuth on auth screens**: **Google** / **Apple** buttons moved **above** email/password on **`auth/modals`**, **`auth/login`**, **`auth/register`** with divider **Or use email & password** so the options are visible without scrolling past the form.
- **`SocialAuthController`**: **`googleConfigured()`** trims **client id/secret** so stray whitespace in **`.env`** does not hide the button.
### Docs
- **`docs/local-development.md`**: **Google button missing?** troubleshooting.

## Version 1.9.36 - Defer Sign in with Apple (flag + remarks)
### Added
- **`APPLE_SIGN_IN_ENABLED`** (**`config/services.php`**, **`.env.example`**): default **false**; Apple button and `/auth/apple/*` stay inactive until **true** and Apple credentials are set.
### Changed
- **`SocialAuthController`**, **`auth` routes**, **`bootstrap/app.php`**, **`AppServiceProvider`**, **`social-login-buttons`**, **`profile` disconnect route**, **`docs/oauth-google-apple-setup.md`**, **`docs/cloud-environment-setup.md`**, **`docs/local-development.md`**: comments documenting defer / later enable without removing code.
### Tests
- **`SocialAuthTest`**: Apple redirect **404** when flag off despite placeholder credentials.

## Version 1.9.35 - Cloud environment checklist doc
### Added
- **`docs/cloud-environment-setup.md`**: Living checklist for **dev / staging / production** (app core, DB, sessions, mail, queue, storage, Google/Apple OAuth, PayPal, optional integrations, post-deploy commands, external consoles); blank **`.env`** template for copying per host. **README** links to it.

## Version 1.9.34 - OAuth doc: rotated Google JSON / secret
### Changed
- **`docs/oauth-google-apple-setup.md`**: **`client_secret_2_*.json`** naming after secret reset; clarify Laravel reads **`.env`** only—copy **`web.client_secret`** into **`GOOGLE_CLIENT_SECRET`** on every environment; checklist item after rotation.

## Version 1.9.33 - OAuth doc: `.env` and git
### Changed
- **`docs/oauth-google-apple-setup.md`**: Explain that **`.env`** is not updated via git; staging/production/CI use host or CI secrets with the same variable names as **`.env.example`**.

## Version 1.9.32 - Ignore Google `client_secret*.json`
### Added
- **`.gitignore`**: **`client_secret*.json`** so Google OAuth Web client downloads are not committed.
### Changed
- **`docs/oauth-google-apple-setup.md`**: Note on the optional **`client_secret_*.json`** file and mapping **`web.*`** fields to **`.env`**.

## Version 1.9.31 - OAuth doc: shared Google client ID
### Changed
- **`docs/oauth-google-apple-setup.md`**: Document **Novel App** **`GOOGLE_CLIENT_ID`** shared across dev/staging/production and clarify registering all origins/redirects on one Web client.

## Version 1.9.30 - README and About legal links
### Added
- **README**: Novel App section linking **`docs/local-development.md`**, **`docs/oauth-google-apple-setup.md`**, and **`docs/legal/README.md`** (and noting **`/legal`** in the running app).
### Changed
- **About** page “Policies & feedback” nav: **Legal hub** link first, then Privacy, Terms, Feedback.

## Version 1.9.29 - Legal hub and OAuth setup doc
### Added
- **Legal hub** at **`/legal`** (**`legal.index`**) with links to Terms, Privacy, **Refunds & cancellation**, **Community guidelines**, and **Cookie policy** (**`legal/*`** views); routes registered in **`web.php`**.
- **OAuth guide** (**`docs/oauth-google-apple-setup.md`**): Google Cloud and Apple Developer steps, redirect URLs per environment, keys, and **`GOOGLE_*` / `APPLE_*`** **`.env`** reference; **`docs/local-development.md`** links to it.
- **`docs/legal/README.md`** and moved proposal markdown into **`docs/legal/`** (**`hub-update-proposal.md`**, **`document-gap-analysis.md`**).
### Changed
- **Privacy** and **Terms** Blade copy: PayPal payments, GDPR/CCPA summary, retention, cookies pointer, contribution **license**, liability cap, refunds cross-links; footers (**`guest`**, **`welcome`**, **`app`**) include **Legal** plus Privacy/Terms where shown before.
### Removed
- Duplicate **`privacy.blade.php`** and **`terms.blade.php`** at repo root (canonical views remain under **`resources/views/`**).
### Tests
- **`LegalPagesTest`**: legal routes and meta descriptions.

## Version 1.9.28 - Social login (P4-1)
### Added
- **Google & Apple sign-in**: **`laravel/socialite`** + **`socialiteproviders/apple`**; routes **`GET auth/{provider}/redirect`** and **`GET|POST auth/{provider}/callback`** (**`SocialAuthController`**); **`social_accounts`** table and nullable **`users.password`** for OAuth-only users.
- **Auth UI**: **Continue with Google / Apple** on **`auth/modals`**, **`auth/login`**, **`auth/register`** when credentials are configured; flash errors on **`social_login_error`**; Apple callback excluded from CSRF in **`bootstrap/app.php`**.
- **Profile**: **Connected sign-in** list with **Disconnect** (**`DELETE /profile/social/{provider}`**); OAuth-only users can **set a first password** without “current password” (**`PasswordController`** + **`update-password-form`**).
### Changed
- **Privacy policy** (`privacy`): **Sign in with Google or Apple** bullet under what we collect.
### Docs
- **`.env.example`** OAuth placeholders; **`docs/local-development.md`** — redirects, Apple POST callback, **client secret / key rotation**.

## Version 1.9.27 - P3 roadmap (#21–#29) and reader/profile polish
### Added
- **Edit outcome notifications**: **`EditOutcomeNotifier`** creates **`Notification`** rows and sends mail on chapter approve/reject (**`ModerationController`**, **`EditApprovalController`**) with badges for **`edit_rejected`**, **`paragraph_accepted`**, **`paragraph_rejected`** on **`notifications/index`**.
- **Payment & vote history**: **`GET /profile/payments`** (**`PaymentHistoryController`**, **`profile/payments`**) and sidebar **Payments & votes**.
- **Draft autosave**: **`localStorage`** for whole-chapter and paragraph suggest text (respects server pending draft when present); scripts in **`whole-edit-draft-preview-script`** and **`chapters/show`**.
- **Diff before submit**: **`POST /edits/preview-diff`** (**`EditDiffPreviewController`**) with **Preview changes** on suggest panel and **`edits/create`**.
- **Profile submissions**: **`profile.show`** with **`?tab=submissions`** lists chapter and paragraph submissions; **My submissions** tab.
- **Profile photo**: **`users.avatar_path`** migration; upload with invalid-file handling and old file cleanup (**`ProfileController`**); **`User::avatarUrl()`**; **`php artisan storage:link`** for public URLs.
- **RSS**: **`GET /feed/chapters.xml`** (**`feed.chapters`**, **`RssFeedController`**, **`feeds/tbw-chapters`**).
- **TBWNN version nav**: **`tbw-version-nav`** partial on chapter show; **`Chapter`** helpers (**`tbwArchiveSiblingsForReader`**, **`tbwLiveManuscriptForSameSlot`**, **`tbwOtherArchiveSiblingsForReader`**, **`manuscriptListSectionKey`**).
- **Checkout rate limit**: **`RateLimiter`** on checkout paths (**25 / 60s** per user) with friendly flash.
- **Docs**: **`docs/P3-manual-testing.md`**, **`docs/P4-priority-detailed.md`**; roadmap maintenance for P3/P4.
### Changed
- **Account menus**: **Profile** opens **`profile.show`**; **Edit profile** opens **`profile.edit`**; **`nav-account-menu`** and **`navigation`** show the user photo when **`avatar_path`** is set.
- **Edit profile form**: current photo preview, selected filename + live preview for a newly picked image.
- **Chapter read**: header uses **`Chapter::readerHeadingLine()`**; blank titles no longer show **Untitled** in reader copy (**`displayTitle()`**).
- **Next chapter**: header link includes **`#chapter-suggest-edit-sidebar`**; locked-chapter sidebar **Read next chapter** goes to **`$nextChapter`** (same hash) instead of **`/chapters`**, with **Browse chapters** when there is no next row.
- **Reader tests / diff**: incremental coverage (**`ChapterReaderHeadingTest`**, **`TextDiffLineDiffTest`**); admin copy touch-ups (deadlines, table headers).
### Fixed
- **Profile avatar upload**: clearer errors for oversize / failed uploads; **`ProfileUpdateRequest`** accepts **`avatar`** without **`dimensions`** blocking saves.
### Tests
- **`P3EnhancementsTest`**, **`ProfileTest`** (avatar persistence), **`ReaderTbwP1EnhancementsTest`** (locked sidebar next vs browse).

## Version 1.9.26 - Vote diff readability
### Changed
- **Peter Trull vote comparison**: **What changed between A and B?** uses **color-coded line rows** (rose / emerald / **unchanged on white over a neutral track**) via **`TextDiff::linesForDisplay()`** instead of a monospace unified-diff terminal block.
- **Vote diff context rows**: **neutral/sand panel** behind the list so **unchanged** lines read as **white strips** with a **neutral left bar** (replacing low-contrast gray-on-white).

## Version 1.9.25 - P2 roadmap remainder (#14–#20)
### Added
- **Leaderboard time scope**: **`GET /leaderboard?period=30d`** ranks by points from **paid** full-chapter approvals (**`edits.points_awarded`**, `updated_at` in window) plus **paid** paragraph approvals (**`inline_edits`** + **`payments`**); **All-time** unchanged; UI pills + **Points (30d)** column; **`App\Support\LeaderboardScoring`**, **`LeaderboardController`** `Request` + **`$period`**.
- **Peter Trull A/B diff**: collapsible **unified diff** under each vote pair (**`sebastian/diff`**, **`App\Support\TextDiff`**); skips diff when combined text exceeds a byte cap.
- **Feedback types**: **accessibility**, **account**, **payment**, **content_issue**; **`Feedback::typeLabel()`**; admin list shows readable labels.
- **Docs**: **`docs/P2-manual-testing.md`** (step-by-step QA for this batch).
### Changed
- **Reader chapter body** (**`#chapter-content`**): serif stack, size/leading/tracking, **`novel-reader-body`** kerning in **`resources/css/app.css`**; paragraph edit control **darker + focus-visible**.
- **WCAG-oriented contrast**: leaderboard table/header copy, vote meta, insights summary/empty panels, admin feedback timestamps; **Top contributor** header chip (**app** / **guest**) is a **link** to **`leaderboard`** with clearer copy; **navigation** top line links the same.
- **Insights**: empty voting/contribution panels use **icon + heading + CTA** instead of italic one-liners.
### Tests
- **`LeaderboardTest`**: **last 30 days** uses recent **`Edit`** **`points_awarded`**.

## Version 1.9.24 - Reading progress, achievements clarity, onboarding, leaderboard rank, admin mail
### Added
- **Dashboard onboarding**: dismissible **Welcome — get started** card with links to chapters, live chapter, leaderboard, vote; **`users.onboarding_completed_at`** migration and **`POST /onboarding/dismiss`**; factory support.
- **Achievements (P2-12)**: **`AchievementUnlock::ensureDefinitionsExist()`** before listing so an empty catalog hydrates from **`config/achievements.php`**; **`Achievement::requirementLabel()`** and **`currentProgressToward()`**-backed progress on **`/achievements`**; **`achievements/index`** uses **`x-dynamic-component`** layout (fixes Blade compile error from split app/guest layout).
- **Dashboard achievements**: **How it works** modal (requirement, progress bar, link to full page); **earned vs not** at a glance (**amber** vs **grayed `opacity-40`**, hover to clarify).
- **Chapter list reading progress**: authed **scroll** posts **`read_percent`** to **`track-progress`**; **`ChapterController::trackProgress`** accepts **`read_percent`** (ratio encoding with **`scroll_extent_max = 1000`**) and merges monotonically with pixel scroll; list card bars use **absolute** fill.
- **Chapter show reading bar**: **sticky** strip; **session peak** % so UI does not drop when focus/scroll jumps (e.g. suggest UI); **restore scroll** when progress stored as ratio; **`flushProgress`** on **visibility** / **pagehide**; fill via **absolute width** + **`min-width`** when &gt; 0.
- **Leaderboard**: **`LeaderboardController`** computes **your rank** and points for logged-in users; **`leaderboard`** view shows **Your rank** when present.
- **Admin mail**: **`AdminNotifier`** resolves recipient from **`AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL`** then **`ADMIN_EMAIL`**; paid-suggestion email uses **reader chapter label** (**`headingPrefix()`** + title) plus **row id**, not only numeric id as “chapter number.”
- **Artisan** **`mail:test`** (**`SendTestMailCommand`**) for smoke-testing the configured mailer.
- **Tests**: **`OnboardingDismissTest`**, **`MailTestCommandTest`**, **`AdminNotifierRecipientTest`**, **`AchievementRequirementLabelTest`**, **`ReadingProgressDisplayTest`**, **`ReaderTbwP1EnhancementsTest`** **`read_percent`** case; **`AchievementShowTest`** empty-catalog hydration; **`LeaderboardTest`** rank; **`RegistrationTest`** always dashboard after register.
- **`.cursor/rules/manual-test-directions.mdc`** for manual QA notes.

### Changed
- **Registration**: always **redirect to dashboard** after signup (ignore modal **`redirect_to`**); removed hidden **`redirect_to`** from auth modals.
- **`ReadingProgress`**: integer casts; **`displayProgressPercent()`** when extent missing; **`trackProgress`** monotonic merge for pixel and ratio saves.
- **`PaymentController`**: admin notification line for new paid edits uses **`Chapter`** display fields.
- **`.env.example`**: admin notification / mail hints where applicable.
- **`docs/enhancement-roadmap-prioritized.md`**: minor roadmap touch-ups.

### Fixed
- **Achievements index 500**: invalid Blade around conditional **`<x-app-layout>`** / **`<x-guest-layout>`** pairs.

### Repository
- **Snapshot tag**: **`snapshot-20260403-v1924`** (annotated) on the **Development** branch commit for this batch.

## Version 1.9.23 - P1 reader UX, landing & auth polish, SEO, leaderboard & insights copy
### Added
- **TBWNN chapter read**: **Previous / Next** navigation (`ChapterController::adjacentTbwChapters`, manuscript and archive order); **in-header “Reading this page”** progress strip with server **`track-progress`** (`scroll_extent_max` on **`reading_progress`**); **furthest-scroll** persistence (position never decreases).
- **Chapter list (`/chapters`)**: per-card **word count** and **~reading time**; logged-in **Your progress** card (percent from **`ReadingProgress::scrollProgressPercent()`**); migration **`scroll_extent_max`** on **`reading_progress`**.
- **`GET /prizes`** (**`route('prizes')`**) and **`resources/views/prizes.blade.php`**; **leaderboard** grand-prize chip and **landing fund goal** figure link there; default **fund goal** display **`$50k`** (**`config/marketing.php`**, **`LANDING_PRIZE_POOL_DISPLAY`**).
- **Vote hub**: when voting is restricted, secondary CTA **Open the live TBWNN chapter** via **`ChapterLifecycle::latestOpenTbwChapter()`** (**`VoteController`**, **`vote/index`**).
- **`config/seo.php`**, **`layouts/partials/seo-head.blade.php`**, **`page-title` / `meta-description`** on **guest** and **app** layouts; **welcome** head meta; static pages **about**, **privacy**, **terms**, **prizes**.
- **`x-password-reveal-field`** (show/hide password): auth **modals**, **login** / **register** / **reset** / **confirm**, **profile** update & delete flows.
- **Tests**: **`ReaderTbwP1EnhancementsTest`**; **`LegalPagesTest`** meta check; **`WelcomeLandingTest`** quiet strip + OG; **`LeaderboardTest`** guest empty state.
- **`docs/enhancement-roadmap-prioritized.md`** (priority plan from source reports).

### Changed
- **Landing**: **“Fund goal”** label (was prize goal); **quiet stats strip** when contributors, accepted edits, and live chapters are all zero (**`LANDING_SOFT_STATS_WHEN_EMPTY`**); **`.env.example`** marketing/SEO hints.
- **Auth modals**: branded header, amber overlay, **`x-modal`** rounded panel; password toggle **beside** the field (no overlap with **Remember me**).
- **Leaderboard**: **guests** see **Sign in** / **Create account** instead of direct **chapters** links (empty state and bottom CTA); **logged-in** users unchanged.
- **Community insights (`/analytics`)**: chapter labels use **`Chapter::insightDisplayLabel()`** (title or **Chapter N**, not cold open / prolog type); **Peter Trull** group headings match; manuscript rows simplified.
- **`.env.example`**: **`LANDING_PRIZE_POOL_DISPLAY="$50k"`**, soft-stats and SEO vars.

### Fixed
- **Reading progress**: initial **`load`** sync when document already **`complete`**; short-page extent handling; index no longer overwrites per-chapter progress with whole-page scroll.

### Repository
- **Snapshot tag**: **`snapshot-20260402-v1923`** (annotated) on the **Development** branch commit for this batch.

## Version 1.9.22 - Chapter lifecycle, TBWNN admin workflow, merge preview fix, stats and profile UX
### Added
- **`ChapterLifecycle`** and related gates for TBWNN suggestions, publish/close-without-merge, and Peter Trull pair handling; **`AdminNotifier`** for admin-facing email when the editing window ends with pending work.
- **`AppSetting`** model, admin **Settings** UI (**`SettingsController`**, **`resources/views/admin/settings`**), migrations **`chapter_lifecycle_and_app_settings`** and **`locked_at`** on **`chapters`**, plus **`backfill_peter_trull_voting_deadlines`**.
- **`TbwRevisionMergePreview`** for merged-text preview on Manage Chapters (accepted edits highlighted in green).
- **Artisan** **`chapter:editing-deadline-reminders`** (**`SendChapterEditingDeadlineReminders`**) scheduled daily in **`bootstrap/app.php`**.
- **Tests**: **`TbwnnChapterLockAndUploadDeadlineTest`**, **`TbwnnPublishDuplicateGuardTest`**, **`ChapterLogicalReaderPieceCountTest`**, **`TbwRevisionMergePreviewTest`**; **Profile** test for locked reading-progress badge.
- **`docs/chapter-lifecycle-spec.md`**; backlog note in **`docs/app-improvement-backlog.md`**.

### Changed
- **Admin Manage Chapters**: TBWNN story upload / **Publish integrated revision & lock** / **Close without merged text** / extend editing close date; Peter Trull pair upload and archive behavior aligned with lifecycle rules.
- **Reader manuscript list (`chapters/index`)** and **chapter show**: paid-edit window copy, locked summaries, inline suggest flow; **Suggest an edit** jump strip visible on all breakpoints (not only **`lg+`**).
- **`Chapter::logicalReaderPieceCount()`**: reader-facing totals (TBWNN A stream, Peter Trull one per voting slot); **admin dashboard** purple tile shows that count plus raw **`Chapter::count()`** with clearer contrast; **landing** “Chapters live” uses the same logical count as **`/chapters`** (caption **“On the chapter list”**). TBWNN main stream treats **`version`** case-insensitively as **A** (also **`ChapterController@index`**).
- **Profile → Reading progress**: locked chapters show **Locked** instead of **In Progress** when **`completed`** is still false; link label **Read** vs **Continue**.
- **Payment / moderation / vote / analytics / archive / leaderboard / sidebar**: use **`Book::NAME_*`** constants and lifecycle-aware checks where applicable.

### Fixed
- **Merge preview highlighting**: **`markersToHtml`** closing delimiter used **RS + E** (not **US + E**), so green **`<mark>`** replaces raw **`Si4` / `Ei4`** markers.

### Repository
- **Snapshot tag**: **`snapshot-20260331-v1922`** (annotated) on the **Development** branch commit for this batch.

## Version 1.9.21 - Insights accuracy, activity feed removal, chapter suggest UX, inline-edit moderation parity
### Added
- **`moderation_outcome`** on **`inline_edits`** (migration) for full vs partial accept tracking aligned with chapter edits.
- **Tests**: **`InlineEditPartialApproveTest`**, **`UserEditDashboardStatsTest`** for paragraph moderation and dashboard edit stats.
- **Guest nav**: **`nav-account-menu`** partial and layout wiring so logged-in readers on guest layout see account/unread counts consistently.

### Changed
- **Community insights (`/analytics`)**: **Pending edits** count matches the real moderator queue (full-chapter pending excluding `inline_edit` stubs **plus** pending **`InlineEdit`** rows). **Manuscript by chapter** uses **`chapter_statistics`** (paid / accepted / rejected) plus live **in-queue** badges instead of raw row counts that did not reflect moderation.
- **Activity stream removed**: **`/activity-feed`** route, **`ActivityFeedController`**, and **`activity-feed`** view deleted; insights page no longer shows recent activity or “feed events (7d)”. **`EditController`** no longer writes **`activity_feed`** rows.
- **Chapter read (`chapters/show`)**: Suggest sidebar **sticky** with nav-aware **`top`** and scrollable max height on **`lg+`**; **mobile** column order puts the suggest / sign-in panel **first** so it is visible on load; **FAB** and **`scrollToSuggestEditSidebar`** for jump-to-form; bottom “Suggest an edit” strip on **desktop only** after long reads.
- **Admin Review Suggestions**: **Paragraph** and **full-chapter** cards show **Accept full / partial / Reject** actions **above** the diff as well as below. **Inline moderation** page layout aligned; **Peter Trull** upload validation and admin chapter list messaging tightened.
- **`ModerationController`**: Paragraph approve/reject updates **`ChapterStatistic`** and points in line with chapter-level moderation; **`PaymentController`** / **`InlineEdit`** model updates for the paid paragraph flow and queue hygiene.
- **`User`**: Dashboard / stats helpers avoid double-counting paragraph stubs and align accepted counts with chapter + inline moderation outcomes.
- **`AnalyticsInsightsTest`**: Assertions updated for the insights page without the activity block.

### Fixed
- **Payment / inline checkout** edge cases and payload handling where noted in **`PaymentController`** for **`InlineEdit`** creation.

### Repository
- **Snapshot tag**: **`snapshot-20260331-development`** (annotated) on the **Development** branch commit for this batch.

## Version 1.9.20 - Admin-only seed, achievements auto-heal, voting/payment fixes, shell UX
### Changed
- **`DatabaseSeeder`**: Seeds **only** the admin user (`admin@example.com` / `password`). No demo books, chapters, **`test@example.com`**, reading progress, or achievement rows. Use Admin to create content; run **`php artisan db:reset-app-data --force`** to truncate app tables and re-seed admin via **`AdminOnlySeeder`**.
- **Achievement unlocks**: Single evaluator **`App\Support\AchievementUnlock`**. Achievement **definitions** live in **`config/achievements.php`** and are written to the DB **only when the `achievements` table is empty** (covers migrate-without-seed). Sync runs from dashboard, achievements index/show, vote index, after casting a vote, payment success, moderation/admin approve paths, and when a **new** reading-progress row is created.
- **Chapter list auto-lock**: Applies **only** to **The Book With No Name** — **Peter Trull** pairs are no longer locked by visiting **`/chapters`**.
- **Vote hub (`/vote`)**: **Version A / B** vote buttons respect **each row’s `is_locked`** (pair badge “Locked” only if **both** locked). Header “vote credits” chip uses **high-contrast** emerald styling.
- **Landing**: Prize stat footnote **“Announced fund · not a live balance”**; **`config/marketing.php`** documents the configurable **`LANDING_PRIZE_POOL_DISPLAY`** line.

### Fixed
- **`Payment::vote()`**: **`HasOne`** uses foreign key **`votes.payment_id` → `payments.id`** (not the PayPal order id stored in **`payments.payment_id`**), so vote credits count correctly.
- **Peter Trull pair matching**: **`VoteController`** treats **`list_section` null** like **`chapter`** when resolving A/B pairs for “already voted” / payment consumption.
- **Inline / PayPal flow**: **`QueryException`** on checkout and success paths caught with clear **migrate** / session guidance; success flash notes **Peter Trull** vote credit.
- **Chapters index**: Text-selection “Suggest edit” passes the correct **paragraph index** via **`data-paragraph-index`**.

### Added
- **`config/achievements.php`**, **`database/seeders/AdminOnlySeeder.php`**, **`app/Console/Commands/ResetAppDataCommand`** (`db:reset-app-data`).
- **Migration**: **`list_section`** on **`chapters`** (ordering for cold open / prolog / chapter / epilog).
- **Tests**: **`PaymentVoteCreditScopeTest`**, **`AnalyticsInsightsTest`**, **`NotificationsPageTest`** (and related coverage updates).
- **`docs/brand-naming.md`**.

### Repository
- **Snapshot tag**: **`snapshot-20260330-development`** (annotated) on the **Development** branch commit for this batch.

## Version 1.9.19 - Notifications view fix, dashboard achievement icon, seed data
### Fixed
- **Notifications page**: Removed invalid Blade pattern (splitting `<x-app-layout>` / `<x-guest-layout>` with `@if` around slots), which caused **“syntax error, unexpected token else”**. Page is **auth-only** and now uses a single `<x-app-layout>` wrapper.
- **Dashboard**: Achievement tiles use **`icon_emoji`** (the real column name) instead of a non-existent **`icon`** attribute.

### Changed
- **`DatabaseSeeder`**: Seeds four achievements and a **`test@example.com`** user (`password` / `password`) with **reading progress** on chapter 1 and **“First steps”** unlocked for dashboard testing.

### Added
- **Test**: `NotificationsPageTest`.

## Version 1.9.18 - P3-4 through P3-8 (insights, notifications, a11y, progress bar, brand doc)
### Changed
- **Insights hub (`/analytics`)**: MVP summary metrics (total votes, pending edits, activity-feed entries last 7 days), **recent activity** preview with link to full stream; sidebar label **Analytics** → **Insights**. `AnalyticsController`, `analytics/index.blade.php`.
- **Activity stream** (`/activity-feed`): Page `h1`, link back to insights; dashboard quick link points to **insights** with updated copy.
- **App header**: **Notifications** bell with **unread count** badge; `layouts.app` view composer (`unreadNotificationCount`). User menu trigger gets **focus-visible** ring.
- **Reading progress** (chapter index + show): Bar sits **below** sticky top nav (`--app-shell-nav-h`), lower z-index, non-interactive strip to avoid clash with chrome (P3-7).
- **Vote hub**: Primary page title promoted to **`h1`**; “Exclusive voting hub” and status chips use text + `aria-hidden` emoji where helpful.
- **Guest nav**: Leader strip **sr-only** prefix; Sign In **focus-visible** ring.

### Added
- **Accessibility**: Global **`:focus-visible`** outline via `resources/css/app.css` (Tailwind `@layer base`).
- **Brand doc**: `docs/brand-naming.md` (product vs books vs handles) for P3-8.

### Added (tests)
- `AnalyticsInsightsTest`.

## Version 1.9.17 - Paid-only points + PayPal abandon draft retention
### Changed
- **Leaderboard points**: Admin approve paths (`ModerationController`, `EditApprovalController`) increment points only when a **completed** `Payment` is linked to that suggestion (`payments.edit_id` for full/phrase edits; `inline_edits.payment_id` for paragraph edits). Copy on chapter pages states points apply **after** successful payment.
- **Paragraph edits**: No more free JSON submit — modal POSTs to **`payment.checkout`** with `type=inline_edit`; payload stored on `edits.inline_edit_payload` so returning from PayPal still works. `InlineEditController@store` returns **422** with guidance.
- **Checkout**: Reuses one **`pending_payment`** draft per user/chapter (updates text instead of stacking rows). PayPal **`cancel_url`** → **`payment.cancel`** with a **warning** flash; failed `createOrder` / exceptions **no longer** mark the edit `cancelled` (draft stays `pending_payment`).
- **Chapter page**: Flash **success** / **error** / **warning**; **Resume PayPal checkout** when a draft exists; main form prefills from unpaid draft.

### Added
- **Migration**: `edits.inline_edit_payload` (JSON text), `inline_edits.payment_id` (FK). **Routes**: `payment.cancel`. Tests: `EditApprovalPaymentTest`, `PaymentCancelFlowTest`.

### Repository
- **Development** (2026-03-29): batch **1.9.15–1.9.17** pushed to **`origin/Development`**. **Snapshot tag:** **`snapshot-20260329-development-batch`** (annotated). Run **`php artisan migrate`** on each environment that has not yet applied `votes.payment_id` and inline/payload migrations.

## Version 1.9.16 - P3-1 PayPal UX + P3-3 About refresh
### Changed
- **PaymentController**: PayPal **client ID and secret** are checked for the active **mode** (`sandbox` vs `live`) **before** creating a `pending_payment` edit, so misconfiguration does not strand cancelled edits. User-facing message points users to try later / contact admin; details stay in logs.
- **Checkout / capture**: Clearer flash copy for bad return parameters, invalid edit session, non-array capture responses, and API failures; `summarizePayPalResponse()` surfaces PayPal `message` / `error` / `details` (truncated) instead of only a generic line.
- **About page**: Aligned with current product ($2 checkout, points, one vote credit per completed payment, Peter Trull rules), added **Privacy**, **Terms**, and **Feedback** links plus CTAs to chapters and the vote hub.

### Added
- **Tests**: `PaymentCheckoutConfigurationTest`; `LegalPagesTest` covers `/about`.

## Version 1.9.15 - Peter Trull votes require paid edit credits
### Changed
- **Vote eligibility**: Casting a vote consumes one **completed** `$2` **Payment** (`votes.payment_id`, unique). Accepted edits alone no longer unlock voting; each successful checkout adds one vote credit for Peter Trull.
- **Copy**: Vote hub, dashboard quick link, about, leaderboard explainer, landing “How it works” / Part 2 card, and chapter suggest-edit blurb aligned with payment-based credits.

### Fixed
- **PayPal success redirect**: Success response now uses `redirect()->to(route(...) . '#edit-submission-box')->with(...)` instead of invalid string concatenation on the redirect object.

### Added
- **Migration**: `votes.payment_id` nullable FK to `payments`, unique when set. Tests: `VotePaymentTest`.

## Version 1.9.14 - Main rail inset via layout CSS (not Tailwind arbitrary)
### Fixed
- **Inset still missing**: `md:pl-[var(--app-shell-rail-w)]` was not reliably generated by Tailwind’s scanner/build for arbitrary `var()`. Added **`.app-shell__main-with-rail`** with a normal **`@media (min-width: 768px)`** rule in **`layouts/app.blade.php`** and **`layouts/guest.blade.php`** so **`padding-left: var(--app-shell-rail-w)`** always applies.

### Verified (2026-03-29)
- **App shell**: Fixed left rail (`position: fixed` + `--app-shell-nav-h` / `--app-shell-rail-w`) plus **`.app-shell__main-with-rail`** padding confirmed in browser; main content no longer sits under the sidebar on `md+`, document scroll remains usable.

## Version 1.9.13 - Main column inset for fixed rail
### Fixed
- **Content hidden under fixed sidebar**: `w-full` plus **`margin-left: 18rem`** made the main column wider than the viewport, so the left strip sat under the rail. Switched to **`padding-left: var(--app-shell-rail-w)`** on `md+` and added **`--app-shell-rail-w: 18rem`** next to **`--app-shell-nav-h`**; sidebar width uses the same variable (`layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/sidebar.blade.php`).

## Version 1.9.12 - Fixed desktop sidebar (no sticky/flex fight)
### Changed
- **Left navigation rail**: On `md+`, the sidebar uses **`position: fixed`** with **`top` / `height` from `--app-shell-nav-h`** (default `4.5rem`, aligned with the sticky top bar). Main content uses **`md:ml-[18rem]`** when the rail is shown so text is not covered. This avoids `position: sticky` + flex ancestor edge cases that broke pinning for guests and logged-in users alike (`layouts/sidebar.blade.php`, `layouts/app.blade.php`, `layouts/guest.blade.php`).

## Version 1.9.11 - Sidebar visible again (restore md:flex)
### Fixed
- **Left sidebar missing**: Replaced **`hidden md:block`** + nested wrapper with a single **`<aside class="hidden md:flex md:flex-col …">`** so the rail matches the original display model. The wrapper/block variant could collapse or stay `display: none` depending on flex/Tailwind interaction (`layouts/sidebar.blade.php`). **`items-stretch`** on the app/guest content row is unchanged for sticky behavior.

## Version 1.9.10 - Sticky sidebar: flex stretch + wrapper
### Fixed
- **Sidebar not sticking when logged in (and long pages)**: The content row used **`items-start`**, so the sidebar flex item was only as tall as the menu. `position: sticky` is limited to that short box, so it “unsticks” as soon as that box leaves the viewport — unlike short guest pages where it was less obvious. Switched to **`items-stretch`** and a **stretching wrapper** around the sidebar with an **inner** `sticky` + `max-h` + `overflow-y-auto` rail (`layouts/sidebar.blade.php`, `layouts/app.blade.php`, `layouts/guest.blade.php`).

## Version 1.9.9 - App shell: document scroll (fix frozen pages)
### Fixed
- **Pages still not scrolling after login**: Removed the **`h-screen overflow-hidden`** + nested flex scroll model entirely. The app shell now uses **normal document (window) scrolling** with a **sticky top nav** and, on `md+`, a **sticky left sidebar** (`top-[4.5rem]`, `max-h-[calc(100dvh-4.5rem)]`, internal scroll if the menu is tall). This avoids browser flex/overflow edge cases that left main content with no scroll (`layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/sidebar.blade.php`).

## Version 1.9.8 - App shell main scroll fix
### Fixed
- **Logged-in / guest app pages not scrolling**: Scrolling moved from the outer content column to **`<main class="flex-1 min-h-0 overflow-y-auto">`**, with **`shrink-0`** on the page header. The previous `flex-grow` + `overflow-y-auto` on the parent broke nested flex `min-height: auto` behavior in common browsers, so content looked “frozen” while the shell used `h-screen overflow-hidden` (`layouts/app.blade.php`, `layouts/guest.blade.php`).

## Version 1.9.7 - P2-3 through P2-5 (leaderboard, points copy, dashboard vote link)
### Added
- **Leaderboard empty state**: When no non-admin users appear on the board, a message, points explainer, and primary CTA replace the empty table (`leaderboard.blade.php`). Tests: `LeaderboardTest`.
- **Dashboard quick link**: **Peter Trull · Vote** in Quick Links, with eligibility messaging; `canVote` computed in the dashboard route (same rules as `VoteController`).

### Changed
- **Points copy (landing-aligned)**: About page Part 1, chapter suggest-edit blurb, chapter JS alerts, dashboard “Your Points” subline, and leaderboard explainer blocks now spell out **2 / 1 / 0** points and first-accepted-edit voting unlock where relevant.

## Version 1.9.6 - App shell height + guest footer parity
### Fixed
- **Sidebar “sticky” behavior**: The shell used `min-h-screen`, so the flex column grew with content and the whole page scrolled. The wrapper is now **`h-screen min-h-0 overflow-hidden`** so only the main column scrolls and the left rail stays fixed on desktop (`layouts/app.blade.php`, `layouts/guest.blade.php`). Top nav is `shrink-0` (always visible; sticky removed as redundant).

### Changed
- **Guest footer**: Matches landing **·** separators between Privacy / Terms / Feedback, copyright order, and link styling (`layouts/guest.blade.php`).

## Version 1.9.5 - Sticky sidebar / landing hero / P2-1 & P2-2
### Fixed
- **App shell layout**: Added `min-h-0` on the main flex row and content column so long pages (e.g. chapters) scroll inside the main area while the **left sidebar stays visible** on desktop (`layouts/app.blade.php`, `layouts/guest.blade.php`, `layouts/sidebar.blade.php`).
- **Landing hero**: Removed the typewriter **caret line** (`border-right` + blinking animation) next to “Where your edits shape the narrative…”; optional typing animation remains where enabled (`welcome.blade.php`).

### Changed (P2)
- **Top leader in header**: View composer limited to `layouts.app` and `layouts.guest` (not every view); result cached **60 seconds** as `layout.top_leader` (`AppServiceProvider`).
- **Guest layout footer**: Links to **Privacy Policy**, **Terms of Service**, and **Feedback** (same routes as the landing page).

## Version 1.9.4 - Profile page reading progress fix
### Fixed
- **Profile (`/profile`)**: Crash when a `reading_progress` row had a null `last_read_at` (the view called `diffForHumans()` on null). The page now shows “Not recorded yet” and `ReadingProgress` casts `last_read_at` to datetime.

## Version 1.9.3 - P1 backlog (mobile nav, vote counts, achievements, admin password reset)
### Added
- **Mobile navigation (logged-in app shell)**: Hamburger button and slide-out drawer on small screens; nav links shared via `layouts/partials/sidebar-inner.blade.php`.
- **Admin user password reset**: On **Admin → User Management → Edit user**, optional new password + confirmation (same validation as registration). Leaving both blank keeps the existing password.
- **Tests**: `AdminUserPasswordResetTest` (admin can reset; non-admin forbidden).

### Changed
- **Vote hub performance**: `VoteController@index` aggregates vote counts in one query; vote page no longer runs `Vote::count()` per chapter pair in the view.
- **Achievements progress**: “Accepted Edits” count includes `accepted`, `accepted_full`, and `accepted_partial`, aligned with voting eligibility.

## Version 1.9.2 - P0 backlog fixes (admin feedback + achievements show)
### Fixed
- **Admin dashboard recent feedback**: Uses `Feedback` model fields correctly — `user` / `email` / `content` / `type` (with `user` eager-loaded); removed invalid `sender_name` / `message` references (`dashboard.blade.php`).

### Added
- **`achievements.show` route**: `GET /achievements/{achievement}` wired to `AchievementController@show` so dashboard achievement tiles no longer 404.
- **Achievement detail view**: Replaced placeholder `achievements/show.blade.php` with a real detail page (requirement, unlock state for signed-in users, sign-in CTA for guests, back link to index).
- **Tests**: `AchievementShowTest` for the show route.

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

## Version 1.9.1 - Landing journey clarity & legal pages
### Added
- **Privacy & Terms**: Public routes `privacy` and `terms` with `privacy` / `terms` Blade views (guest layout); footer links on the landing page point to real URLs instead of `#`.
- **How it works (3 steps)**: Scannable step strip above the two journey cards—read & contribute, earn points / unlock voting, vote on Peter Trull—with `#landing-how-steps` and a visible “How it works” heading.
- **Peter Trull card callout**: Inline note that voting requires at least one accepted edit in *The Book With No Name*, plus a link to chapters.
- **Tests**: `LegalPagesTest` for Privacy and Terms; extended `WelcomeLandingTest` for the new landing hooks and footer routes.
