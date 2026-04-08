# Novel App Unified Step-by-Step Guide

This document consolidates the latest Markdown files in this repository.
Order: most recently updated first.

## Included sources

1. `docs/P2-manual-testing.md`
2. `docs/development/reset-database-for-testing.md`
3. `docs/legal/README.md`
4. `PROJECT_STATUS.md`
5. `README.md`
6. `SETUP_GUIDE_TODAY.md`
7. `docs/P4-priority-detailed.md`
8. `docs/brand-naming.md`
9. `docs/chapter-lifecycle-spec.md`
10. `docs/oauth-google-apple-setup.md`
11. `docs/enhancement-roadmap-prioritized.md`
12. `docs/local-development.md`
13. `docs/legal/hub-update-proposal.md`
14. `DEPLOYMENT.md`
15. `docs/app-improvement-backlog.md`
16. `docs/cloudways-deploy-readiness-and-runbook.md`
17. `docs/P3-manual-testing.md`
18. `docs/legal/document-gap-analysis.md`
19. `docs/cloud-environment-setup.md`
20. `CHANGELOG.md`



---

## Step 1: docs/P2-manual-testing.md

# Manual testing — P2 remainder (post #10–#13)

Use a **local** stack with **`php artisan serve`**, **`APP_URL`** matching the browser origin, and **`npm run build`** (or **`npm run dev`**) so CSS/JS match Blade. Sign in as a **non-admin** user unless the step says otherwise.

---

## 1. Leaderboard — time scope (P2 #14)

1. Open **`/leaderboard`**. Confirm **All-time** is selected (amber pill) and the table matches cumulative **`users.points`** (familiar behavior).
2. Click **Last 30 days**. Confirm the subtitle explains **paid suggestions approved** in the window.
3. If you have **no** recent approvals, expect the **empty state** with **View all-time leaderboard**.
4. (Optional, with DB access) Approve a paid edit so **`edits.points_awarded` > 0** and **`updated_at`** is recent; reload **Last 30 days** and confirm that user appears with **Points (30d)** matching summed awards (full-chapter + paragraph paths both feed this — see **`LeaderboardScoring`**).

---

## 2. Peter Trull vote — A/B diff (P2 #15)

1. Open **`/vote`** (logged in with vote credits if you need expanded pairs).
2. Expand a chapter pair where **Version A** and **Version B** body text **differ**.
3. Scroll below the two columns; open **What changed between A and B?** Confirm **color-coded rows** (rose = removed from A, green = added in B, **white rows on a sand/neutral track** = unchanged context), not terminal-style `+`/`-` text.
4. If A and B are **identical**, confirm **no** diff section appears.
5. (Stress) If texts are huge, confirm the **“diff is omitted”** message instead of a lock-up.

---

## 3. Reader typography (P2 #16)

1. Open any **The Book With No Name** chapter **`/chapters/{id}`** with real paragraph body text.
2. Confirm body uses **serif**, slightly **larger** size on sm+ breakpoints, comfortable **line-height**, and **darker** text (**amber-950**) for reading.
3. Hover a paragraph (logged in, unlocked chapter): confirm the **paragraph pencil** is **darker amber** and shows **focus ring** when tabbing to the button.

---

## 4. Feedback categories (P2 #17)

1. Open **`/feedback`** (or any page embedding the feedback form).
2. Open **Feedback Type** and confirm new options: **Accessibility**, **Account / login**, **Payment / PayPal**, **Content / typo**.
3. Submit a short message with one new type; expect success flash.
4. As **admin**, open **`/admin/feedback`** (**`admin.feedback.index`**) and confirm the row shows a **human-readable** label (not only the raw slug).

---

## 5. WCAG / contrast (P2 #18)

1. Spot-check **leaderboard** table headers, **vote** page meta lines, **insights** summary subtitles, and **admin feedback** metadata — muted text should stay **readable** on cream/amber backgrounds (no ultra-faint `/40` labels on critical copy).
2. Tab through **vote** diff **`<summary>`** and **chapter** paragraph buttons — **focus rings** visible.

---

## 6. Insights empty states (P2 #19)

1. Open **`/analytics`** (**`route('analytics.index')`** — Community insights) on a **fresh** DB or dataset with **no** votes / **no** contribution rows.
2. Confirm **two** empty panels show **icons**, **headings**, **short copy**, and **CTAs** (**Open the vote page**, **Browse chapters**) instead of a single italic line.

---

## 7. Top contributor strip (P2 #20)

1. As **guest** and as **logged-in** user, view **landing** or any **app/guest** layout with the header **top contributor** chip.
2. Confirm label reads **Top contributor:** (not only “Leader:”).
3. **Click** the chip; expect navigation to **`/leaderboard`**.
4. On **Breeze** dashboard nav (if visible), confirm the **Top contributor** line is a **link** to the leaderboard.

---

## 8. Regression quick pass

- **Dashboard** onboarding card (if not dismissed) still works.
- **`/achievements`** loads; tiles show progress where applicable.
- **`/vote`** restricted state still shows TBWNN CTAs when configured.

---

## Reading progress / local vs hosted (context)

If **track-progress** or list **progress bars** behave on **Manus** but not locally, treat **`APP_URL`**, **cookie `Secure`**, **`SESSION_*`**, and **same host (localhost vs 127.0.0.1)** as the **first** checks before changing Blade/JS. See **`docs/local-development.md`**.



---

## Step 2: docs/development/reset-database-for-testing.md

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



---

## Step 3: docs/legal/README.md

# Legal documentation

- **Live policies** are rendered from Blade views under `resources/views/` (`privacy`, `terms`, `legal/*`).
- **Planning / gap analysis** (not legal advice): [hub-update-proposal.md](hub-update-proposal.md), [document-gap-analysis.md](document-gap-analysis.md).



---

## Step 4: PROJECT_STATUS.md

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



---

## Step 5: README.md

# WhatsMyBookName

Collaborative story platform where readers submit paid edits, earn points, and unlock voting on chapter variants.

## Core docs

- **[Local development](docs/local-development.md)** — run the app locally, cookies/sessions, troubleshooting.
- **[OAuth setup](docs/oauth-google-apple-setup.md)** — Google/Apple sign-in configuration by environment.
- **[Cloud environment setup](docs/cloud-environment-setup.md)** — env variable checklist for staging/production.
- **[Cloudways runbook](docs/cloudways-deploy-readiness-and-runbook.md)** — step-by-step deploy and post-deploy checks.
- **[Legal docs index](docs/legal/README.md)** — legal page map (in-app hub is `/legal`).

## Quick start

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Deployment

1. Configure environment variables on host (never commit secrets).
2. Pull latest code.
3. Run:

```bash
bash scripts/deploy/server_post_deploy.sh
```

4. Verify:
   - login and chapter read/edit flow
   - donation checkout and webhook
   - admin donations report and CSV export
   - legal pages (`/legal`, `/terms`, `/privacy`)



---

## Step 6: SETUP_GUIDE_TODAY.md

# Novel App Setup Guide – Steps Completed Today

**Project:** Crowdsourced Novel App – "The Book With No Name"  
**Stack:** WhatsMyBookName app, PHP 8.3, MySQL, PayPal, Blade

---

## Phase 1: Initial Setup (Previously Completed)

- Installed app authentication stack (Breeze)
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



---

## Step 7: docs/P4-priority-detailed.md

# Priority 4 (P4) — detailed priority list

This document **ranks** the “major initiatives” and closely related deferred items from `docs/enhancement-roadmap-prioritized.md` and the source reports (`Comprehensive_Application_Enhancement_Report_What's_My_Book_Name_(v2).docx`, `WhatsMyBookName_UX_Enhancement_Report.docx`). **Nothing here is approved work** until product, budget, and (where relevant) legal sign-off.

## How to read this list

- **Order** = recommended **sequencing if you pursue P4 at all** (dependencies, risk, and leverage), not effort hours.
- **Tier** groups items that can be **scoped or killed together** in planning.
- Items marked **Gate** need a written spec (abuse model, data retention, or rights) before engineering estimates are meaningful.

---

## Pass handoff: “next N” items (AI + engineers)

When the user asks for the **next two** (or next N) **numbered** P4 items in **one pass** (e.g. **P4-4 and P4-5** after Tier A shipped), that request means **do the work that earlier passes explicitly deferred**—**not** to repeat a “not in this pass” disclaimer instead of building it.

### 1) Tier A backlog (ship with the next P4 pair unless scoped out)

Prior identity/profile work called out **block/report**, **privacy beyond a single public on/off toggle**, and **privacy-policy text updates** as future. **Implement these in the same pass** as the next numbered P4 items **unless** the user explicitly narrows scope (e.g. “P4-4/5 only, skip reporting” or “no legal copy this sprint”):

- **Block / report** — minimal abuse path on public contributor surfaces (e.g. **report** on **`/people/{slug}`**, and block/hide behavior per a short product spec).
- **Advanced privacy toggles** — beyond **`public_profile_enabled`** only, where product agrees (e.g. leaderboard visibility, indexed-by-search—exact fields TBD in spec).
- **Privacy policy** — update **`/privacy`** (and linked legal pages if needed) for **OAuth**, **public profiles**, and any new data collection from the above.

If legal or product blocks a line item, ship what is approved and **document what was cut** in the pass summary—do **not** treat the backlog as permanently optional boilerplate.

### 2) The numbered items they asked for

Then deliver the requested **P4-`*`** work (e.g. reader themes + dark mode). Ordering within the milestone is flexible (backlog first vs parallel), but **default expectation** = Tier A deferrals **plus** the next two ranked items in one engagement.

### 3) Browser validation — end of pass

Finish with **concrete browser checks** (local or staging), not only PHPUnit:

1. **Run the app** — e.g. `php artisan serve` and open the site at the URL that matches **`APP_URL`** in `.env` (OAuth and absolute URLs care about `localhost` vs `127.0.0.1`).
2. **Walk the surfaces that changed** in the pass, for example:
   - **`/`** — home loads.
   - **`/login`**, **`/register`** — auth; social login when configured.
   - **`/dashboard`** (signed in) — loads; verification banner if relevant.
   - **`/profile`**, **`/profile/edit`** — profile + settings (avatar, public profile, social accounts, **new privacy toggles** if shipped).
   - **`/people/{slug}`** — public profile; **report/block UX** if shipped.
   - **`/leaderboard`** — links and visibility rules if privacy shipped.
   - **`/privacy`**, **`/legal`** — **read updated copy** when policy work shipped.
3. **Optional CLI check** — `php artisan test` for the narrowest filters that cover the pass.

Adapt paths to the actual features shipped—**always** name routes or URLs so validation is repeatable.

---

## Tier A — Identity, access, and trust (usually first among P4)

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-1** | **Social login (Google / Apple)** | ~~Removes signup friction; does not require other P4 systems.~~ **Shipped in app (configure OAuth consoles + `.env`).** | **Optional later:** “unlink” UX polish, legal review of privacy wording. | **Done:** Socialite, **`social_accounts`**, linking, **`privacy`** OAuth section, **Apple rotation** notes in **`docs/local-development.md`**, **disconnect** on profile + **set password** without current password for OAuth-only users, tests. |
| **P4-2** | **Public contributor profiles** | ~~Builds on existing profile, leaderboard, and avatars.~~ **Shipped (v1):** opt-in **`/people/{slug}`**, stats + bio. | **Follow-up:** block/report, richer privacy than on/off, privacy-policy updates—**scheduled with the next P4 pair** per **Pass handoff** below (unless the user scopes them out). | **Done:** migration **`public_profile_enabled`**, **`public_slug`**, **`profile_bio`**; **`PublicProfileController`**, **`profile.public`**, **`profile/public`**, **`PATCH profile/public-settings`**, reserved slugs, leaderboard name links when public. |
| **P4-3** | **Email verification indicator (profile/settings)** | ~~Small trust signal.~~ **Shipped:** **`MustVerifyEmail`** on **`User`**, badges on profile + edit, dashboard banner for unverified users. | Marketing copy tweaks only. | **Done:** **`email-verification-badge`** partial, resend from dashboard. |

---

## Tier B — Reader experience overhaul (one big design program)

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-4** | **Full reader themes + focus mode** | Improves core reading loop; touches many pages but is **self-contained** (no new moderation surface). | **Gate:** design tokens, QA matrix (every layout), persistence (`localStorage` vs user column). | CSS variables / Tailwind preset, reader-only focus chrome, save preference API optional. |
| **P4-5** | **Dark mode (site-wide)** | Same token work as P4-4; doing it separately doubles design QA. | **Gate:** same as P4-4; contrast audit for amber/cream brand. | Prefer **one program** with P4-4 unless product insists on reader-only dark. |

*Recommendation:* Treat **P4-4 + P4-5** as a **single epic** with optional phase 1 = reader-only.

---

## Tier C — Community surface area (high moderation + notification load)

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-6** | **Threaded chapter comments + @mentions** | Clear user value; explodes moderation and notification complexity. | **Gate:** reporting, rate limits, mod tools, email/in-app volume, “comments closed” per chapter. | New tables, polymorphic or chapter-scoped threads, mention parsing, sanitization, optional Echo for realtime. |
| **P4-7** | **Community peer review before admin** | Changes **canonical** workflow and moderator SLA. | **Gate:** who qualifies as peer, quorum, tie-break, appeal to admin, abuse of downvotes. | Queue states, reputation thresholds (may overlap points/achievements), admin override always available in v1. |
| **P4-8** | **Peer upvote/downvote on edit suggestions** | Overlaps P4-7; can be **merged into one governance spec** or deferred after comments. | **Gate:** gaming (sockpuppets), visibility of pending edits, voter privacy. | Votes table, UI on suggestion list, thresholds for “promote to mod.” |
| **P4-9** | **Feedback upvoting** (deferred from UX report) | Smaller than comments but still abuse-prone. | **Gate:** same anti-gaming as P4-8. | `feedback_votes` or generic polymorphic votes, IP/user limits. |

---

## Tier D — AI and automation (cost, false positives, canon risk)

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-10** | **AI pre-screening of edits** | May **reduce** mod load if tuned; may **increase** drama if opaque. | **Gate:** model choice, budget cap, moderator “override” UX, logging for disputes. | Async job, score + reasons stored, never auto-merge without explicit product rule. |
| **P4-11** | **“Trusted scribe” auto-approve** | Highest canon risk; should follow audit trail and mod tooling maturity. | **Gate:** legal + editorial policy, rollback story, rate limits. | Role + permissions, apply_edit with automated audit row. |
| **P4-12** | **AI TTS / narrations (+ character voices, community voice voting)** | Large **rights** and **cost** surface; subsumes “character voices” from comprehensive report. | **Gate:** voice licensing, regeneration on text change, storage (S3), queue workers, DMCA. | Generate audio per chapter revision, player UX, optional user preference for voice. |

*Recommendation:* Do **P4-10** before **P4-11** if both are considered. **P4-12** is often a **separate product** (budget line).

---

## Tier E — Analytics, competition, and “battle” mechanics

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-13** | **Voter insights** (e.g. “80% who liked Ch1 voted B”) | Interesting for PT; **privacy and small-n** issues. | **Gate:** minimum sample size, aggregation only, opt-out. | Batch or materialized aggregates, no per-user exposure. |
| **P4-14** | **Live voting countdown** (per chapter / pair) | Smaller scoped UI if `editing_closes_at` (or PT equivalent) is reliable. | Product rule for which books/chapters show it. | Blade + Alpine, timezone copy. |
| **P4-15** | **Battle mode for conflicting paragraph edits** | Niche; complex UX and mod rules. | **Gate:** tie-break policy, merges, author voice. | State machine on `inline_edits` or new conflict entity. |

---

## Tier F — Distribution, marketing, and long-lived content

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-16** | **Physical book / pre-order / name-on-cover tracker** | Commerce + ops; weak dependency on app features. | **Gate:** payments provider, fulfillment, legal (sweepstakes/promo rules). | Landing pages, Stripe or separate store link, CRM export. |
| **P4-17** | **Collaborative wiki / codex** | Ongoing editorial load; overlaps moderation. | **Gate:** notability rules, spam, canon locks. | Wiki engine or lightweight markdown + mod queue. |

---

## Tier G — Realtime and resurrected features

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-18** | **Real-time global activity feed (Laravel Echo)** | You **removed** a prior feed; only revisit with a spec **distinct** from notifications. | **Gate:** event taxonomy, mute, performance, WebSocket ops. | Echo + Redis, curated event types, not a duplicate of `Notification`. |

---

## Tier H — Paid edits & patron support (commerce)

Extends today’s **$2 PayPal checkout** model (`edits`, `inline_edits`, `payments`) without assuming Stripe.

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-19** | **Multiple paid edit submissions ($2 each, one checkout)** | Contributors can submit **several** suggestions ( **paragraph** + **whole chapter** ) in one flow; **pricing is $2 per edit** (e.g. **2 edits → $4**) via a **single** PayPal session / one captured amount—not a separate checkout per suggestion. | **Gate:** cart or “review & pay” step showing line items + total; partial failure (payment OK but one row fails); pending review state per row; abuse caps; moderator queue volume; copy (“$2 per suggestion, total at checkout”). | Line-item amount on checkout; on success, attach **one** `payment` (or equivalent) to **N** `edits` / `inline_edits` rows; **points / vote credit** rules per accepted suggestion (product: still 1 vote credit per $2 unit vs one credit per checkout—spec explicitly). |
| **P4-20** | **Donations (support the book)** | Revenue and goodwill **separate** from vote credit and edit fees. | **Gate:** legal/tax (donation vs tip vs merch), receipt wording, whether donations are **tax-deductible**; PayPal **Donate** vs pay link; fraud/chargebacks; optional **public thank-you** (privacy). | New **`donations`** or generic **`payments`** type; webhook or IPN; thank-you page + email; optional amount presets; admin report export; no automatic vote credit unless product explicitly ties them. |

*Recommendation:* Spec **P4-19** first (reuses edit pipelines); **P4-20** can share PayPal config and ops runbooks but needs **distinct** product/legal sign-off.

---

## Consolidated order (executive short list)

If leadership asks “what order should we **consider** P4?”:

1. **P4-1** Social login  
2. **P4-2** Public profiles (with privacy)  
3. **P4-19 / P4-20** Batched paid edits (**$2 × count**, **one** checkout) + **donations** (if revenue/support are top priority—otherwise after reader polish)  
4. **P4-4 / P4-5** Reader themes + focus (+ dark mode as one program)  
5. **P4-6** Threaded comments + mentions  
6. **P4-7 / P4-8** Peer review + suggestion voting (single governance spec)  
7. **P4-10** AI pre-screen  
8. **P4-11** Trusted scribe (only with strong audit)  
9. **P4-12** TTS / narrations  
10. **P4-13 / P4-14** Voter insights + countdowns  
11. **P4-16 / P4-17** Book / wiki  
12. **P4-15** Battle mode  
13. **P4-18** Echo feed  
14. **P4-9** Feedback upvotes (or fold into P4-8 generic voting)  
15. **P4-3** Email verified badge (can move up if bundled with auth work)

---

## Between P3 and full P4 (not epic-sized)

These are **not** in the numbered P4 list above but are cheaper than Tier B–G. Good for **fill-in sprints** when P4 gates are blocked:

- Landing / hero / footer polish (second CTA, “How it works,” newsletter — content-dependent).  
- Locked chapter: tooltip “why locked” on list cards.  
- Paragraph pencil discoverability on mobile.  
- Loading skeletons on heaviest pages (`/chapters`, vote).  
- Breadcrumbs on deep admin/reader paths.  
- Insights export / share (marketing ask).  
- Reading progress richer visuals on profile.

---

## Maintenance

When an item ships or is rejected, update **`docs/enhancement-roadmap-prioritized.md`** and trim or mark rows here. Re-run a pass on the `.docx` reports when new versions land in the repo.



---

## Step 8: docs/brand-naming.md

# Brand and naming (P3-8)

Use these consistently across UI, marketing, and admin copy.

| Context | Use | Avoid |
|--------|-----|--------|
| **Product / site** (nav, footer, legal, emails) | **What’s My Book Name** (curly apostrophe in prose if available; straight `'` is acceptable in code strings) | “WhatsMyBookName” as a sentence; random casing |
| **Handle / URL / repo** | `whatsmybookname`, `novel-app`, domain as configured | Mixing product title into slugs without a defined rule |
| **Main collaborative manuscript** | **The Book With No Name** | “the main book” without the title |
| **Detective voting book** | **Peter Trull** or **Peter Trull: Solitary Detective** (match `books.name` in DB: `Peter Trull Solitary Detective`) | Inconsistent detective naming |
| **Points / voting** | Describe **$2** paid checkout, **vote credits** from completed payments | Implying free votes from accepted edits alone |

When in doubt, match the strings already used on the **landing** (`welcome.blade.php`) and **about** pages.



---

## Step 9: docs/chapter-lifecycle-spec.md

# Chapter lifecycle, uploads, and archive links — product spec

Consolidated requirements (**2026-04-01**). Update `CHANGELOG.md` when this is implemented.

---

## The Book With No Name (edits + suggestions)

### Edit window (30 days)

- **`editing_closes_at`** (or equivalent) is set from **the day of publication** of the open chapter: default **30 days** from that date.
- Admin can **extend or shorten** the window (per chapter or per policy — decide at build time).
- Readers see a **time remaining** indicator (“open for edits for the next X days”) — see parked item in `docs/app-improvement-backlog.md`.

### Closing without an “edited upload”

- If **all** community suggestions were **rejected** and there are **no pending** suggestions when the **clock runs out**, the admin may **close the chapter** **without** uploading a merged “edited closed” text file.
- After that close, they may proceed to **upload the new chapter** (next slot).

### When a merged upload *is* required

- If there were **any accepted** suggestions (full or partial), admin must **first** upload the **closed / integrated** canonical text for that chapter before publishing the **next** chapter.
- Enforcement: **server-side** gates on admin upload actions.

### Archive link on `/chapters` (and Peter Trull surface)

- Only the **edited closed chapter** (the final locked revision that replaced the open period) is **saved and surfaced as a link** on the chapters page — not every intermediate draft.
- The **pre-edit / pre-close** reader-facing chapter is **replaced** when the closed version is published and locked; the **closed snapshot** appears as an archive link for history.

---

## Peter Trull Solitary Detective (votes only, no edit suggestions)

- **No** community edit-suggestion flow for Peter Trull; **votes** choose between versions (e.g. A vs B).
- When a **new chapter** is uploaded, the system **saves the version that had the most votes** as the archived “closed” record for the previous chapter slot (the one voters decided).
- **Admin can overrule** which version is kept if needed (single “close” step for the pair).
- **One** closed step per chapter slot — votes determine default; admin override optional.

---

## Admin notification email

- **Editable in admin UI** (not only `.env`): destination for operational email, e.g. new suggestions pending moderation, alerts such as edit window ending / time to upload next chapter.
- **Note:** `ADMIN_EMAIL` is used for **Gate / admin identity**. Prefer a **separate** “notifications / operations inbox” in the UI unless product merges both.

---

## Summary table

| Topic | Rule |
|--------|------|
| **30-day clock** | Starts on **publication day** of the open chapter; admin can adjust. |
| **TBWNN — close with no upload** | Allowed when **all rejected** + **no pending** when window ends. |
| **TBWNN — next chapter** | If there were accepts, **closed edited text** upload first; then new chapter. |
| **Archive link** | Surface **only** the **final closed** chapter per slot, for **both** books. |
| **Peter Trull** | **Most votes** wins for archived version; **admin overrule**; one close step per slot. |
| **Notify email** | **Admin UI**-editable. |

---

## Open implementation details (for build phase)

- Schema: `chapters` columns vs `chapter_revisions` table; Peter Trull A/B → one “closed” archived row.
- “Close without upload”: lock with unchanged content vs explicit admin action.
- Mail + queue + scheduler for reminders vs `editing_closes_at`.
- Disable paid edit / paragraph flows on Peter Trull chapters if “votes only” is strict.



---

## Step 10: docs/oauth-google-apple-setup.md

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



---

## Step 11: docs/enhancement-roadmap-prioritized.md

# Enhancement roadmap (prioritized)

Source documents (repo root):

- `Comprehensive_Application_Enhancement_Report_What's_My_Book_Name_(v2).docx`
- `WhatsMyBookName_UX_Enhancement_Report.docx`

This file turns those suggestions into a **priority-ordered plan** with **how we intend to implement** each item. Scope assumes the current Laravel stack (Blade, Tailwind, PayPal for paid edits, existing chapters/votes/moderation).

---

## Priority 1 — Highest impact, incremental effort

**Status:** items **#1–#9** are **shipped** (see **Maintenance** below). The table is kept for traceability to the source reports.

These fix obvious reader/contributor friction without large new systems.

| # | Enhancement | Why now | Development plan |
|---|-------------|---------|------------------|
| 1 | **Next / Previous chapter** on chapter read view | Expected book-like navigation; reduces list ping-pong. | ~~Pass ordered TBWNN chapter IDs (or `list_section` + `number`) from `ChapterController@show`; render two links in `chapters/show.blade.php`. Respect locked/archived rules same as index.~~ **Done:** `ChapterController` adjacent TBWNN chapters + prev/next on **`chapters/show`**. |
| 2 | **Reading progress on chapter list cards** | Users already have scroll progress; surfacing it aids resume. | ~~Reuse `ReadingProgress` for logged-in users: on `ChapterController@index`, eager-load or batch-query progress per chapter; show thin bar or % on each card in `chapters/index.blade.php`.~~ **Done:** List cards show progress; **`track-progress`** supports **`read_percent`** + monotonic merge. |
| 3 | **Estimated reading time** (± word count) per chapter | Sets expectations; cheap. | ~~Helper on `Chapter` (e.g. word count from `strip_tags` + `str_word_count`, minutes at ~200 wpm); show on index cards and/or show header.~~ **Done:** Word count + **~reading time** on index (and related chapter UI). |
| 4 | **Vote hub: CTA when voting is restricted** | Turns a dead-end into the next paid path. | ~~In `vote/index.blade.php`, when user lacks credit, add primary button to `chapters.index` or first open TBWNN chapter (`ChapterLifecycle::latestOpenTbwChapter()` or similar).~~ **Done:** **`VoteController`** / **`vote/index`** CTA to **`ChapterLifecycle::latestOpenTbwChapter()`**. |
| 5 | **Grand prize banner → rules page** | Reduces confusion and support. | ~~Add route + Blade `prizes` or `campaign-rules` (copy from marketing/legal); link from leaderboard banner and landing if present.~~ **Done:** **`GET /prizes`** (**`route('prizes')`**), links from leaderboard + landing (**`config/marketing.php`**). |
| 6 | **Landing: soften or hide zero stats** | Avoids “empty project” first impression. | ~~In home route, if contributors/edits/chapters below thresholds, hide the stats strip or show copy like “Be among the first…” (`config/marketing.php` flags).~~ **Done:** **`LANDING_SOFT_STATS_WHEN_EMPTY`** quiet strip + copy. |
| 7 | **Auth: password show/hide** | Small accessibility/usability win. | ~~Alpine or vanilla JS toggle `type` on password inputs in auth modals/views; ensure labels/ARIA.~~ **Done:** **`x-password-reveal-field`** on auth + profile password flows. |
| 8 | **Auth modals: stronger branding** | Aligns login with site identity. | ~~Reuse landing colors/logo in `auth/modals` partials; no backend change.~~ **Done:** Branded **`auth/modals`** + **`x-modal`** styling. |
| 9 | **SEO: meta description + Open Graph** on public pages | Better shares and search snippets. | ~~Per-route or layout `@section('meta')`; `og:title`, `og:description`, `og:image` from `config` or env.~~ **Done:** **`config/seo.php`**, **`layouts/partials/seo-head`**, guest/app **`meta`**, welcome + static pages. |

---

## Priority 2 — Strong value, moderate scope

**Status:** items **#10–#20** are **shipped** (see **Maintenance** below). The table is kept for traceability to the source reports.

| # | Enhancement | Why | Development plan |
|---|-------------|-----|------------------|
| 10 | **Post-signup onboarding checklist** | Guides first session without full “tour” product. | ~~After registration, one dashboard card or `/welcome-steps`: links to `chapters.index`, first open chapter, leaderboard; dismissible; optional `users.onboarding_completed_at`.~~ **Done:** Dashboard welcome card, **`users.onboarding_completed_at`**, **`POST /onboarding/dismiss`**. |
| 11 | **Dashboard stat cards: shorter copy + “Details”** | Reduces visual noise; keeps depth. | ~~Trim subtitles in `dashboard.blade.php`; add `title` tooltips or small “?” popover with current long text.~~ **Done:** Shorter reader/admin tiles + **`title`** and **“?”** tooltips. |
| 12 | **Achievements: clearer progress / how to earn** | Badges exist; discovery is weak. | ~~On `achievements` views, ensure each tile shows requirement + progress (query existing unlock rules); hover/focus panels for keyboard users.~~ **Done:** **`Achievement::requirementLabel()`**, progress on tiles, **`<details>` How to earn**, focus-visible rings. |
| 13 | **Leaderboard: “Your rank” highlight** | Scales when list is long. | ~~In `LeaderboardController`, compute current user’s position; render sticky row or banner; style with existing palette.~~ **Done:** **Your rank** banner + **You** row (**`LeaderboardController`**). |
| 14 | **Leaderboard: time scope** (week / month / all-time) | Makes board feel fresh. | ~~Requires **defining** score source (e.g. points events by `updated_at` on accepted edits only). May add materialized sums or query `edits`/`inline_edits` by `approved_at`. Start with all-time + “last 30 days” if simpler.~~ **Done:** **`?period=30d`** + **`LeaderboardScoring`** (full + inline paid approvals in window). |
| 15 | **Peter Trull: text diff between A and B** | Core to “compare versions.” | ~~Server-side diff (e.g. `sebastian/diff` already in ecosystem) or JS diff of plain text; render in `vote/index.blade.php` below or beside columns; guard length for performance.~~ **Done:** **`App\Support\TextDiff`** + collapsible diff on **`vote/index`**. |
| 16 | **Reader typography polish** (spacing, optional serif for body) | Literary feel without full theme engine. | ~~Scoped CSS for `#chapter-content` / prose class; optional user toggle in `localStorage` later (see P4).~~ **Done:** **`#chapter-content`** / **`novel-reader-body`** in **`resources/css/app.css`**; paragraph edit contrast/focus. |
| 17 | **Feedback form: more categories** | Better triage for admin. | ~~Extend `feedback.type` validation + migration if enum-like; update `FeedbackController` + form options.~~ **Done:** Types **accessibility, account, payment, content_issue** + **`Feedback::typeLabel()`**. |
| 18 | **WCAG contrast pass** on muted text | Accessibility + readability. | ~~Audit tokens in Tailwind usage; darken `text-amber-800/40` etc. on cream backgrounds; re-check focus rings.~~ **Done:** Contrast pass on leaderboard, vote, insights, admin feedback, diff **`<summary>`**. |
| 19 | **Insights: empty states** | Less “broken chart” feeling. | ~~`analytics/index.blade.php`: placeholders, illustration or short copy when series empty.~~ **Done:** Empty panels with icons, CTAs (**`/analytics`**). |
| 20 | **Top leader strip: clearer label + link** | “Leader: …” confusion. | ~~`layouts` composer: copy “Top contributor” + `route('leaderboard')`; optional tooltip.~~ **Done:** **Top contributor** chip + link **`route('leaderboard')`** (guest + app layouts). |

---

## Priority 3 — Larger features, still aligned

**Status:** items **#21–#29** are **shipped** (see **Maintenance** below). The table is kept for traceability to the source reports.

| # | Enhancement | Development plan |
|---|-------------|------------------|
| 21 | **Email + in-app when edit is accepted/rejected/partial** | ~~Verify all moderation paths fire `Notification` (or mail) consistently; add tests; template copy for each outcome.~~ **Done:** `EditOutcomeNotifier`, moderation + admin edit approval/reject. |
| 22 | **User payment / vote credit history** | ~~New `Payment`-centric index for authed user: list completed checkouts, linked vote if any, chapter; reuse PayPal metadata; no Stripe unless product changes.~~ **Done:** `/profile/payments`, sidebar link. |
| 23 | **Draft autosave for edit textareas** | ~~`localStorage` key per `chapter_id` + type; restore on load; clear on successful submit. Server drafts optional later.~~ **Done:** whole-chapter + paragraph suggest flows. |
| 24 | **Diff preview before submitting full-chapter edit** | ~~Client-side or server compare `original` vs textarea; modal or collapsible; paragraph flow unchanged.~~ **Done:** `POST /edits/preview-diff` + **Preview changes** UI. |
| 25 | **Profile: “My submissions”** | ~~Tab on profile: queries on `Edit` + `InlineEdit` for user; statuses, links to chapters; pagination.~~ **Done:** `?tab=submissions` on profile. |
| 26 | **Profile photo upload** | ~~`users.avatar_path`, storage disk, validation, resize (Intervention or Laravel image); default to initials if null.~~ **Done:** `avatar_path` + upload (no server-side resize yet; initials fallback unchanged). |
| 27 | **RSS feed for new TBWNN chapters** | ~~`Route::get('/feed/chapters.xml')`; query latest non-archived TBWNN rows; `response()->view` with `Content-Type: application/rss+xml`.~~ **Done:** `feed.chapters`. |
| 28 | **Legacy / history inside chapter UX** | ~~Optional tab: “Current” vs “Previous versions” using `is_reader_archive_link` / archive links; may reduce reliance on standalone archive page over time.~~ **Done:** **Versions** nav strip on TBWNN chapter show. |
| 29 | **Rate limiting paid edit checkouts** | ~~`RateLimiter` in `PaymentController` by `user_id` + sliding window; flash friendly message.~~ **Done:** 25 attempts / 60s per user. |

---

## Priority 4 — Major initiatives (separate approval)

These need product, budget, and often legal review before engineering estimates.

**Detailed ranked list (tiers, dependencies, gates):** [`docs/P4-priority-detailed.md`](P4-priority-detailed.md).

| Topic | Notes |
|-------|--------|
| **AI TTS / narrations** | Queue workers, storage (S3), API costs, rights, regeneration on revision. |
| **Full reader themes + focus mode** | Design system + persistence; test every page. |
| **Dark mode** | Same as above; tokenize colors project-wide. |
| **Community peer review before admin** | Governance, abuse, SLA for pending queue. |
| **AI pre-screening edits** | Model cost, false positives, moderator trust. |
| **“Trusted scribe” auto-approve** | Canon risk; start with admin-only tools. |
| **Threaded chapter comments + @mentions** | Moderation load, notifications, reporting. |
| **Public contributor profiles** | Privacy settings, GDPR-style controls, harassment surface. |
| **Physical book / pre-order / codex wiki** | Marketing ops + long-lived content maintenance. |
| **Real-time global activity feed (Echo)** | You previously removed a feed; only revisit with a spec distinct from notifications. |
| **Social login (Google / Apple)** | ~~OAuth apps, policies, fallback email.~~ **Shipped (v1.9.28):** Socialite + **`social_accounts`**; configure **`.env`**; buttons hidden until credentials set. |
| **Multiple $2 edit submissions** | **$2 per suggestion**, **one checkout** for the batch (e.g. **2 edits → $4**)—paragraph and whole-chapter paths; cart/review step, queue, abuse caps. See **`docs/P4-priority-detailed.md`** (**P4-19**). |
| **Donations (support the book)** | Patron flow **separate** from vote credit and edit fees; legal/tax, receipts, PayPal (or provider), thank-you UX. See **`docs/P4-priority-detailed.md`** (**P4-20**). |

---

## Suggestions from the reports **not** placed in Priority 1–3 above

These were either folded into a broader item, deferred to P4, or **intentionally omitted** from the execution roadmap (but listed here for transparency).

### From the UX report (not given their own P1–3 row)

- **Hero: second CTA cut off** — Fixable as a quick layout tweak; fold into a small “landing polish” pass with QA on common breakpoints.
- **Footer: social links, newsletter, “How it works”** — Content/marketing dependent; not specified as a dev epic here.
- **Testimonials / social proof / video** — Creative assets, not engineering-first.
- **Social login** — Listed under P4.
- **Email verification indicator** — Small item; add when touching auth profile/settings.
- **Quick Links: more visual / icon cards** — Polish; can bundle with dashboard P2.
- **Dashboard: full recent activity feed** — Overlaps removed activity feature; defer unless spec differs from notifications.
- **Locked chapter: tooltip “why locked”** — Partially addressed by copy; can add `title` or popover per card.
- **Suggest panel: slide-over vs sticky** — Larger IA change; optional experiment after P1 navigation.
- **Paragraph pencil discoverability** — Some copy exists; could add always-visible small icon on mobile.
- **Generic leaderboard avatars** — Addressed by profile photo (P3) or generated initials avatar (quick win).
- **PT: blurred preview before unlock** — Product risk to paid funnel; omitted from recommended roadmap.
- **PT: visual diagram for pay → vote** — Nice; illustration/copy pass.
- **Insights: export / share stats** — Growth feature; low priority unless marketing asks.
- **Reading progress: richer visuals on profile** — Extend P1/P2 progress work to profile list.
- **Public profile** — P4.
- **Feedback upvoting** — New tables + abuse; defer.
- **Seed example feedback** — Content choice; optional.
- **Mobile bottom nav** — Alternative to hamburger; design pass; defer until mobile analytics justify.
- **Loading skeletons** — Incremental; sprinkle on heaviest pages (chapters, vote).
- **Notification bell badge** — Verify current app already shows count; if missing, fix as bugfix.
- **Breadcrumbs** — Helpful; medium priority polish.
- **Keyboard shortcuts** — Power users; document in help modal; low priority.
- **Stripe + payment history** — Product uses PayPal; history covered under P3 PayPal wording.
- **Chapter commenting (threaded)** — P4 scope.
- **Admin analytics dashboard** — Overlaps insights + DB queries; separate admin epic.
- **Report’s “47 suggestions” rollup** — This doc maps them into priorities; not every line is a unique ticket.

### From the Comprehensive report (not given their own P1–3 row)

- **XP bar / contributor levels (`xp` column)** — Overlaps points + achievements; omitted unless you want a second progression system.
- **Personalized “Next Task” algorithm** — Partially covered by onboarding checklist (P2); full recommender later.
- **Achievement showcase “dedicated grid”** — Largely exists; enhance via P2 achievement clarity.
- **Activity feed + Laravel Echo** — P4 / conflict with past removal decision.
- **Voter insights (“80% who liked Ch1 voted B”)** — Privacy, sample size, stats rigor; P4.
- **Interactive vibe tags / word cloud** — Gamable, noisy; omitted from recommended roadmap.
- **Live voting countdown per chapter** — Useful if `editing_closes_at` is always meaningful for PT; can merge into vote UI as a small P2 item if you want it explicitly.
- **AI character voices + community voice voting** — Subset of TTS P4.
- **Customizable reader (themes, sliders) + focus mode** — P4 reader overhaul.
- **Peer review upvote/downvote on suggestions** — P4 governance.
- **AI content pre-screening** — P4.
- **Trusted scribe auto-approve** — P4 risk.
- **Battle mode for conflicting paragraph edits** — P4 complexity.
- **Mention system** — P4 social.
- **Physical book: name on cover tracker, pre-order bar** — P4 marketing.
- **Collaborative wiki / codex** — P4 content product.

---

## Maintenance

Update this file when you ship or descope items. Re-run a pass on the two `.docx` reports if new versions appear in the repo.

- **Shipped (P1 #1–#9):** TBWNN chapter **prev/next** navigation; chapter list **reading progress** bars + **`track-progress`** (**`read_percent`** / scroll merge); **word count** and **~reading time** on cards; vote hub **CTA** to **live TBWNN chapter** when restricted; **`/prizes`** + marketing links; landing **soft zero-stats** strip (**`LANDING_SOFT_STATS_WHEN_EMPTY`**); **`x-password-reveal-field`**; **branded auth modals**; **`config/seo.php`** + **Open Graph** / meta on public pages.
- **Shipped (P2 #10–13):** Post-signup onboarding card on the reader dashboard (dismiss + `users.onboarding_completed_at`, existing users backfilled on migration); reader and admin stat tiles shortened with `title` + “?” tooltips; achievements index/show show numeric progress, clearer requirement copy, `<details>` “How to earn”, focus-visible rings on tiles; leaderboard uses `LeaderboardController` with **Your rank** banner, **You** row highlight, and note when outside top 20.
- **Shipped (P2 #14–20):** Leaderboard **`?period=30d`** (points from **`edits.points_awarded`** + paid **inline** approvals in window via **`LeaderboardScoring`**); Peter Trull vote pairs — collapsible **unified text diff** (**`sebastian/diff`**, **`App\Support\TextDiff`**); reader **`#chapter-content`** typography + paragraph-edit contrast/focus; feedback types **accessibility, account, payment, content_issue** + admin **`typeLabel()`**; contrast bumps on leaderboard/vote/insights/admin feedback; insights **empty states** with CTAs; header **Top contributor** strip links to **`route('leaderboard')`**. Manual checklist: **`docs/P2-manual-testing.md`**.
- **Shipped (P3 #21–#29):** **`EditOutcomeNotifier`** — in-app **`Notification`** + mail on chapter/paragraph moderation outcomes; **`/profile/payments`** + **Payments & votes** nav; **`localStorage`** draft restore for paid suggest textareas; server **`edits.preview-diff`** + **Preview changes** on suggest UI and **`edits/create`**; profile **`?tab=submissions`** (**My submissions**); **`users.avatar_path`** + profile **Profile photo** (**`storage:link`**); public **`/feed/chapters.xml`**; TBWNN chapter **Versions** nav (**archive / current release**); checkout **`RateLimiter`** (25/min per user, flash). Manual checklist: **`docs/P3-manual-testing.md`**.
- **Shipped (P4-1 social login):** **`laravel/socialite`** + **`socialiteproviders/apple`**; **`SocialAuthController`**; **`social_accounts`** + nullable **`users.password`**; Google/Apple buttons when **`.env`** is set; **`privacy`** OAuth bullet; profile **Disconnect** (**`profile.social.disconnect`**) + **set first password** without current password; tests **`SocialAuthTest`**, **`ProfileSocialDisconnectTest`**, **`PasswordUpdateTest`**. Setup: **`docs/local-development.md`** (OAuth + Apple rotation).



---

## Step 12: docs/local-development.md

# Local development

## URL and sessions

- Pick **one** origin and stick to it: either `http://localhost:8000` or `http://127.0.0.1:8000`. Browsers treat them as different sites; mixing them breaks login and CSRF.
- Set **`APP_URL`** in `.env` to exactly that origin (scheme, host, port).
- For plain **HTTP** locally, keep **`SESSION_SECURE_COOKIE=false`** (see `.env.example`). For **HTTPS** (including production), set **`SESSION_SECURE_COOKIE=true`** so browsers treat the session cookie as secure-only.
- **`SESSION_DOMAIN`** is usually **`null`** locally unless you know you need a custom cookie domain.
- After changing session or URL settings, run **`php artisan config:clear`** and sign in again.

## OAuth (Google / Apple)

For **step-by-step** console registration, redirect URIs per environment, keys, and a full **`.env` checklist**, see **[oauth-google-apple-setup.md](oauth-google-apple-setup.md)**.

- **Redirect URLs** in Google Cloud Console and Apple Developer must match **`APP_URL`** (e.g. `http://127.0.0.1:8000/auth/google/callback` and `.../auth/apple/callback`). Mismatch causes “redirect_uri_mismatch” or Apple `invalid_client`.
- Set **`GOOGLE_CLIENT_ID`** and **`GOOGLE_CLIENT_SECRET`** to show **Continue with Google** on login/register (modals and `/login` / `/register`). If either is unset, the Google button is hidden and the redirect route returns **404**. After editing **`.env`**, run **`php artisan config:clear`** (or avoid **`config:cache`** during local dev) so Laravel picks up new values.
- **Apple** is **off by default**: set **`APPLE_SIGN_IN_ENABLED=true`** when ready, then configure **`APPLE_CLIENT_ID`**, **`APPLE_REDIRECT_URI`**, and either a JWT **`APPLE_CLIENT_SECRET`** or **`APPLE_TEAM_ID`**, **`APPLE_KEY_ID`**, and **`APPLE_PRIVATE_KEY`**. Apple’s callback is a **POST**; CSRF is disabled only for **`auth/apple/callback`** in `bootstrap/app.php`.
- **Account linking:** if a Google/Apple email matches an existing user, the provider is attached and they sign in as that user (same email must be the verified identity from the provider).

### Google button missing?

- The **Continue with Google** control only renders when **`GOOGLE_CLIENT_ID`** and **`GOOGLE_CLIENT_SECRET`** are both non-empty in the running app (see **`SocialAuthController::providerConfigured('google')`**). It appears **above** the email fields on **`/login`**, **`/register`**, and in the **Sign in / Join** modals.
- After editing **`.env`**, run **`php artisan config:clear`**. If you previously ran **`php artisan config:cache`**, run **`config:clear`** again during development or rebuild cache with current env.
- Verify the process sees your vars: **`php artisan tinker`** then `config('services.google.client_id')` (should not be `null`).
- Ensure **`.env`** is in the **project root** (same folder as **`artisan`**), not only a JSON download in the folder.

### Apple client secret rotation

- If you use a **JWT** as **`APPLE_CLIENT_SECRET`**, Apple allows a maximum lifetime of about **six months**. Before it expires, generate a new secret in the Apple Developer portal (or with your usual script), update **`.env`**, then run **`php artisan config:clear`**.
- If you use **`APPLE_TEAM_ID`**, **`APPLE_KEY_ID`**, and **`APPLE_PRIVATE_KEY`** (`.p8` file), the Socialite provider builds a short-lived client secret per request; you still need to **rotate or revoke keys** in Apple Developer if a key is compromised. Replace the `.p8` path or file, update **`APPLE_KEY_ID`** if you create a new key, and redeploy.

## Reading progress / AJAX

- Progress and similar routes need a valid session and CSRF token. In DevTools → Network, **`track-progress`** (or related POSTs) should return **200**. **419** usually means CSRF or session mismatch—check **`APP_URL`**, origin consistency, and that **`npm run build`** (or `npm run dev`) has produced current assets.

## Database

- Copy **`.env.example`** to **`.env`**, set **`APP_KEY`**, run migrations and seeders as documented for this project. Local and deployed environments each use their own database; data does not sync between them.
- To **wipe application data** and keep **only the admin user** (for a clean manual test pass), see **[Reset database for testing](development/reset-database-for-testing.md)**.



---

## Step 13: docs/legal/hub-update-proposal.md

# Legal Hub & Document Update Proposal

This proposal outlines the creation of a centralized **Legal Hub** and provides specific text updates for the **Privacy Policy** and **Terms of Service** to support paid features and community contributions.

---

## 1. The "Legal Hub" Structure
Instead of individual links in the footer, we will create a centralized `/legal` page that acts as a directory for all legal documents.

### **Proposed `/legal` Page (Mockup)**
*   **Terms of Service**: The master agreement governing your use of the platform.
*   **Privacy Policy**: How we collect, use, and protect your personal data.
*   **Refund & Cancellation Policy**: Specific terms for voting credits and paid contributions.
*   **Community Guidelines**: Behavioral expectations for all members.
*   **Cookie Policy**: Details on the technical tracking we use.

---

## 2. Updated Terms of Service (Key Additions)

### **Section: Payments & Voting Credits**
> "All payments for voting credits, contribution fees, or other digital services are processed through our third-party payment provider (e.g., Stripe). By completing a transaction, you agree to their terms. **All sales are final and non-refundable** once the digital service (e.g., a vote or edit submission) has been initiated or delivered."

### **Section: Intellectual Property & Contributions**
> "By submitting an edit, suggestion, or any other content to {{ config('app.name') }}, you irrevocably grant us a perpetual, worldwide, royalty-free, and exclusive license to use, modify, publish, and incorporate that content into the final book project and any related promotional materials. You represent that you own all rights to the content you submit."

### **Section: Limitation of Liability**
> "To the maximum extent permitted by law, our total liability for any claim arising out of these terms or the Service shall not exceed the total amount paid by you to us in the twelve (12) months preceding the claim."

---

## 3. Updated Privacy Policy (Key Additions)

### **Section: Payment Information**
> "We do not store your full credit card details on our servers. Payment information is collected and processed directly by our payment provider (Stripe). We only receive and store metadata related to your transaction (e.g., transaction ID, amount, and status) for order fulfillment and tax purposes."

### **Section: Legal Basis for Processing (GDPR/CCPA)**
> "We process your data based on:
> 1. **Contractual Necessity**: To provide the services you signed up for (e.g., managing your account and votes).
> 2. **Consent**: When you opt-in to marketing or specific data uses.
> 3. **Legitimate Interest**: To maintain security, prevent fraud, and improve our platform."

### **Section: Data Retention**
> "We retain your account information as long as your account is active. Financial records are retained for a minimum of seven (7) years to comply with tax and legal obligations. Contributions (edits/votes) may be retained indefinitely as part of the project's historical record."

---

## 4. Implementation Steps

1.  **Create `resources/views/legal/index.blade.php`**: The hub page.
2.  **Update `resources/views/terms.blade.php`**: Incorporate the new clauses.
3.  **Update `resources/views/privacy.blade.php`**: Incorporate the new clauses.
4.  **Update `routes/web.php`**: Add the `/legal` route.
5.  **Update Footer**: Replace individual links with a single "Legal" link.

---

**Recommendation:** These updates should be reviewed by a legal professional before being finalized for a production environment.



---

## Step 14: DEPLOYMENT.md

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



---

## Step 15: docs/app-improvement-backlog.md

# App improvement backlog (UI/UX + functional)

Prioritized from a codebase walkthrough. Apply in order within each tier unless dependencies say otherwise.

---

## P0 — Fix correctness / broken flows first

**Status: complete** (2026-03-29). No change to `welcome.blade.php` / `_local_backups/welcome-original.blade.php` for this batch — not applicable.

| # | Status | Item | Notes |
|---|--------|------|--------|
| P0-1 | **done** | **Admin dashboard recent feedback** | Fixed: `user?->name` / `email` / `content` / `type`, `Feedback::with('user')`. |
| P0-2 | **done** | **Achievement detail route + view** | Added `achievements.show` route; rebuilt `achievements/show.blade.php` to use real `Achievement` model (was hardcoded mock). |

---

## P1 — High-impact UX and performance

**Status: complete** (2026-03-29). Snapshots: `_local_backups/snapshots-20260328-p1/`.

| # | Status | Item | Notes |
|---|--------|------|--------|
| P1-1 | **done** | **Mobile nav for logged-in users** | Hamburger + slide-out drawer (`md:hidden`) reuses `layouts/partials/sidebar-inner.blade.php`; desktop sidebar unchanged. |
| P1-2 | **done** | **Vote index query load** | `VoteController@index` precomputes per-`chapter_id` vote counts; `vote/index.blade.php` reads from `$voteCounts`. |
| P1-3 | **done** | **Achievements “accepted edits” count** | Progress tile uses `whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])`. |
| — | **done** | **Admin reset user password** | Optional `password` / `password_confirmation` on admin user edit (`UserController@update`); same rules as registration. Tests: `AdminUserPasswordResetTest`. |

---

## P2 — Consistency, discoverability, polish

**Status: complete** (2026-03-29). Snapshot: `_local_backups/snapshots-20260329-p235/`.

| # | Status | Item | Notes |
|---|--------|------|--------|
| P2-1 | **done** | **Scope or cache top-leader view composer** | `View::composer(['layouts.app','layouts.guest'], …)` + `Cache::remember('layout.top_leader', 60, …)`. |
| P2-2 | **done** | **Guest layout footer** | Footer nav: Privacy Policy, Terms of Service, Feedback (same routes as landing). |
| P2-3 | **done** | **Leaderboard empty state** | Empty `$users`: centered message + explainer + CTA; guest sign-in hint. Table only when rows exist. |
| P2-4 | **done** | **Points copy alignment** | About, chapter edit copy, dashboard points subline, leaderboard explainer: 2 full / 1 partial / 0 rejected + voting unlock. |
| P2-5 | **done** | **User dashboard quick links** | Quick link to **Peter Trull · Vote** with gating copy; `canVote` from dashboard route. |

---

## P3 — Deeper product / a11y / nice-to-have

**Status: complete** (2026-03-30). See `CHANGELOG` **1.9.18**.

| # | Item | Notes |
|---|------|--------|
| P3-1 | **PayPal / checkout error UX** (done) | **Shipped (2026-03-29):** Validate sandbox/live client ID + secret before creating `Edit`; friendly flash when misconfigured; richer PayPal error summaries + try/catch on capture; improved invalid-session messages. Tests: `PaymentCheckoutConfigurationTest`. |
| P3-2 | **Voting tied to payments** (done) | **Shipped (2026-03-29):** Peter Trull votes require an unused **completed** `Payment` per vote (`votes.payment_id`, unique). No votes from accepted-edit eligibility alone; `paid_at` on new votes mirrors payment capture time. Dashboard / copy updated. |
| P3-3 | **About page refresh** (done) | **Shipped (2026-03-29):** $2 PayPal flow, points, payment-based vote credits, Peter Trull gating; links to Privacy, Terms, Feedback; CTA to chapters + vote hub. `resources/views/about.blade.php`. |
| P3-4 | **Activity feed / analytics** (done) | **Shipped (2026-03-30):** Single sidebar entry **Insights** → `/analytics`; MVP summary strip (votes, pending edits, 7d feed count); recent activity preview + link to full `activity-feed`. `AnalyticsController`, `analytics/index.blade.php`. Test: `AnalyticsInsightsTest`. |
| P3-5 | **Notifications in header** (done) | **Shipped (2026-03-30):** Bell + unread badge on `layouts/app.blade.php`; `View::composer` supplies `unreadNotificationCount`. |
| P3-6 | **Accessibility pass (app shell)** (done) | **Shipped (2026-03-30):** `:focus-visible` ring in `resources/css/app.css`; `h1`/heading cleanup on vote + activity + analytics; emoji paired with text or `aria-hidden` where decorative; `sr-only` on leader strip (app + guest). |
| P3-7 | **Reading progress bar vs sticky chrome** (done) | **Shipped (2026-03-30):** Progress bar positioned `top: var(--app-shell-nav-h)`, `z-[35]`, `pointer-events-none` on chapter index + show. |
| P3-8 | **Brand string consistency** (done) | **Shipped (2026-03-30):** `docs/brand-naming.md` guidelines; nav/footer already use product title consistently. |

---

## Parked — implement when prioritized

| Saved | Item | Notes |
|-------|------|-------|
| **2026-03-31** | **Chapter edit window (“clock”)** | When a chapter is **open for editing**, show a clear **time remaining** (e.g. open for edits for the next **X** days). **Default when a chapter is opened: 30 days.** **Admin** can **lengthen or shorten** the window based on how many edits are coming in. *User request: save for a future batch — not implemented in the 2026-03-31 release.* Full lifecycle rules (TBWNN vs Peter Trull, archive links, notify email): **`docs/chapter-lifecycle-spec.md`**. |
| **2026-04-01** | **Chapter lifecycle (implementation pending)** | Canonical spec: **`docs/chapter-lifecycle-spec.md`**. Covers: archive = **final closed** revision only; **Peter Trull** = votes pick archived version, admin override, one close step; **TBWNN** = close without merged upload if all rejected + no pending at clock end; merged upload required if any accept; **30 days from publication**; **notify email in admin UI**. |

---

## How to use this doc

- Tick or strike items as you ship them.
- Optional: add PR/commit references next to each row.
- After each batch, update `CHANGELOG.md` (per project rule).



---

## Step 16: docs/cloudways-deploy-readiness-and-runbook.md

# Cloudways deploy readiness and runbook

This is the operator checklist for deploying this repository to **staging** and **production** on Cloudways.

Use this file as your step-by-step script when each environment is first created and for every subsequent deploy.

---

## 1) One-time per environment (Cloudways panel)

1. Create app/server in Cloudways.
2. Set application path to this repo and connect Git deployment.
3. In **Application Settings > Environment Variables**, set all required variables (see section 2).
4. Ensure SSL is enabled for staging/prod domains.
5. Configure PHP version compatible with app (same major as local successful test run).
6. Configure cron for scheduler:
   - `* * * * * cd /home/master/applications/<APP_ID>/public_html && php artisan schedule:run >> /dev/null 2>&1`
7. Configure queue worker (Supervisor or Cloudways process manager) to run:
   - `php artisan queue:work --sleep=3 --tries=3 --timeout=90`

---

## 2) Required env keys by environment

### Common

- `APP_NAME=WhatsMyBookName`
- `APP_ENV=staging` or `production`
- `APP_DEBUG=false`
- `APP_URL=https://<env-domain>`
- `APP_KEY=<generated key>`
- `MAIL_FROM_NAME=WhatsMyBookName`

### Legal identity (new, required for policy pages)

- `LEGAL_ENTITY_NAME=<registered legal entity>`
- `LEGAL_ENTITY_ADDRESS=<registered address>`
- `LEGAL_CONTACT_EMAIL=<legal/privacy contact email>`
- `LEGAL_JURISDICTION=<governing jurisdiction>`
- `LEGAL_DISPUTE_NOTICE_DAYS=30`

### Database/session/cache/queue

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `SESSION_DRIVER=database`
- `SESSION_SECURE_COOKIE=true`
- `CACHE_STORE=database` (or redis if configured)
- `QUEUE_CONNECTION=database` (or redis if configured)

### Mail

- `MAIL_MAILER=smtp` (or provider)
- `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS=whatsmybookname@gmail.com` (or your sender)

### PayPal

- `PAYPAL_MODE=sandbox` on staging; `live` on production
- staging: `PAYPAL_SANDBOX_CLIENT_ID`, `PAYPAL_SANDBOX_CLIENT_SECRET`
- production: `PAYPAL_LIVE_CLIENT_ID`, `PAYPAL_LIVE_CLIENT_SECRET`
- webhook security:
  - `PAYPAL_WEBHOOK_ID=<paypal webhook id>` (recommended, signature verification mode)
  - `PAYPAL_WEBHOOK_TOKEN=<long random secret>` (fallback/local testing)

### OAuth

- `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`
- `GOOGLE_REDIRECT_URI=https://<env-domain>/auth/google/callback`
- Apple keys only if enabled.

---

## 3) Deploy script (run on server after code pull)

From app root:

```bash
bash scripts/deploy/server_post_deploy.sh
```

This script runs:

1. `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan optimize:clear`
4. `php artisan storage:link`
5. `npm ci && npm run build` (if node is available on server)
6. `php artisan config:cache`
7. `php artisan route:cache`
8. `php artisan view:cache`

If node build is done in CI instead, skip server-side npm/build and deploy built assets.

---

## 4) PayPal webhook setup (dashboard)

1. In PayPal Developer Dashboard, open your app webhooks.
2. Add endpoint:
   - `https://<env-domain>/payment/donation/webhook`
3. Subscribe to:
   - `PAYMENT.CAPTURE.COMPLETED`
   - `CHECKOUT.ORDER.COMPLETED`
4. Copy Webhook ID into `PAYPAL_WEBHOOK_ID` env.
5. Redeploy or run:
   - `php artisan optimize:clear`

To verify auth mode in UI:
- open `/admin/donations`
- check badge:
  - `Signature verification enabled` (preferred)
  - `Token fallback mode` (if webhook id missing)

---

## 5) Post-deploy smoke test (browser)

1. Login works.
2. `/chapters/{id}`:
   - queue edit
   - remove queued edit
   - submit current + queued payment
3. `/edits/public`:
   - visibility and feedback behavior
4. `/dashboard` donation checkout:
   - complete donation
   - confirm donor receipt mail + admin donation mail
5. `/admin/donations`:
   - row visible
   - export CSV works
6. webhook:
   - trigger PayPal test webhook
   - one donation row (deduped on repeated event)
7. legal pages:
   - open `/legal`, `/terms`, `/privacy`, `/legal/refunds`, `/legal/community`, `/legal/cookies`
   - verify legal entity name/address/contact/jurisdiction render correctly
   - verify no placeholder copy remains

---

## 6) Commands you may need on server

- Clear caches:
  - `php artisan optimize:clear`
- Reload config after env updates:
  - `php artisan optimize:clear && php artisan config:cache`
- Re-cache config/routes/views:
  - `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- Restart queue worker:
  - `php artisan queue:restart`
- Check failed jobs:
  - `php artisan queue:failed`

---

## 7) Handoff note

Once staging and production environments are up, we can run through this runbook together and execute each step in order.



---

## Step 17: docs/P3-manual-testing.md

# Manual testing — P3 (#21–#29)

Use a **local** stack with **`php artisan serve`**, **`APP_URL`** matching the browser origin, **`npm run build`** (or **`npm run dev`**), and after migrations **`php artisan storage:link`** (for profile photos). Sign in as appropriate for each step (non-admin vs admin).

---

## 1. Edit outcome notifications (P3 #21)

1. As a **non-admin**, submit a **paid full-chapter suggestion** on an open TBWNN chapter (or use an existing **pending** edit tied to your user).
2. As **admin**, open **`/admin/moderation`** (or **`/admin/edits`**) and **accept** (full or partial) or **reject** that suggestion.
3. Sign back in as the **author**; open **`/notifications`**. Confirm a new row with the right **title** (e.g. suggestion accepted / not accepted) and that the **type** reads sensibly in the list (including **paragraph** paths if you test inline approve/reject).
4. (Optional) With **`MAIL_MAILER=log`** (or similar), confirm a **raw email** line is logged for the outcome (failures should not break the request).

---

## 2. Payments & vote history (P3 #22)

1. As a logged-in user who has at least one **completed** payment (or seed data), open **`/profile/payments`** (or use the sidebar **Payments & votes** link).
2. Confirm rows show **chapter context**, **amount/status**, and **vote credit** linkage where applicable.
3. As **guest**, hit **`/profile/payments`** — expect **redirect to login**.

---

## 3. Draft autosave (P3 #23)

1. Open an **open** TBWNN chapter **`/chapters/{id}`** with the **suggest** UI visible (logged in).
2. **Whole-chapter** (sidebar **Writing / Phrase**): type in **Your edited text**; **reload** the page without submitting. Confirm the **draft** returns (unless a **server pending** non–paragraph checkout draft wins).
3. Click **Discard local draft** on that sidebar form: the textarea should return to the same **baseline** as a fresh load (usually **empty** unless you have a **resume checkout** draft), **not** the full published chapter HTML dumped in.
4. **Paragraph** (pencil → modal): type a draft; close with **Cancel** or open again — saved text should **restore** from **`localStorage`**. You typically **cannot** leave the page without closing the modal first, so a “navigate away” reload test is **not** applicable here. Use **Discard saved draft** in the modal to clear the stored paragraph draft and reset the box to the **original paragraph** text.

---

## 4. Diff preview before submit (P3 #24)

**Where it appears (same behavior, same API):**

1. **Chapter read page** — logged-in user, TBWNN chapter with **Suggest an Edit** sidebar: **Preview changes vs published** next to **Your edited text**.
2. **Standalone suggest form** — **`GET /chapters/{chapterId}/edit`** (**`route('edits.create')`**), when your user is allowed (paid-edit gate): same button under the textarea.

**Checks**

1. Change the **whole-chapter** textarea so it **differs** from the published body; click **Preview changes vs published**.
2. Confirm **How to read this** explains rose / green / neutral / amber rows, then a **scrollable** line list where **rose and green rows are visibly tinted** (not plain text only). Long chapters: unchanged HTML is **folded** into an amber “hidden lines” notice so you mostly see **what changed**; very large bodies may hit a server limit and return an error instead of a diff.
3. Repeat on the **other** URL above if both are available to your test account.

---

## 5. Profile — My submissions (P3 #25)

1. Open **`/profile?tab=submissions`** (or **Profile** then **My submissions** tab).
2. Confirm **chapter-level** and **paragraph** submissions appear with **status** and links to chapters where applicable.
3. Switch to other profile tabs; confirm no errors.

---

## 6. Profile photo (P3 #26)

1. Open **Profile** → edit profile form; under **Profile photo**, choose a small **JPEG/PNG** under the size limit.
2. Save; confirm the **header** shows the image (**`/storage/...`**).
3. (Optional) Remove/clear if the form supports it, or re-upload — confirm no 500s.

---

## 7. RSS feed (P3 #27)

1. In the browser (or `curl`), open **`/feed/chapters.xml`**.
2. Expect **`200`**, **`Content-Type`** including **`rss`** / **`xml`**, and **`<rss`**, **`<channel>`**, and **item** titles matching TBWNN manuscript chapters.

---

## 8. TBWNN version nav (P3 #28)

1. Open a TBWNN chapter that has **archive siblings** or a **current release** counterpart (seed or production-like data).
2. Confirm the **Versions** strip: **This release** / **Current release** / **Earlier — …** / **Archived copy** links behave and navigate to the expected **`/chapters/{id}`** pages.

---

## 9. Checkout rate limit (P3 #29)

1. **Normally** you should not hit this in manual QA. To verify: trigger **more than 25** checkout **`POST`s** in **one minute** as the same user (e.g. script or repeated clicks if the UI allows).
2. Expect flash **Too many checkout attempts** and a **wait** hint. After waiting, checkout should work again.

---

## 10. Regression quick pass

- **`/chapters`** and chapter **read** view still load; **suggest** gate respects locked/closed rules.
- **`/vote`** and **PayPal** checkout still reach the same failure/success paths as before when credentials are missing or present.
- **`php artisan test`** passes (includes **`P3EnhancementsTest`**).

---

## Reading progress / local vs hosted (context)

If **track-progress** or cookies misbehave locally, check **`APP_URL`**, **`SESSION_*`**, and **same host** (**`localhost`** vs **`127.0.0.1`**) before blaming new P3 code. See **`docs/local-development.md`** if present.



---

## Step 18: docs/legal/document-gap-analysis.md

# Legal Document Comparison & Gap Analysis

This document compares the current **Privacy Policy** and **Terms of Service** of "What's My Book Name" against industry standards for paid, community-driven platforms.

---

## 1. Terms of Service (ToS) Comparison

| Feature | Current State | Industry Standard | Gap / Recommendation |
| :--- | :--- | :--- | :--- |
| **Payment Terms** | Brief mention of "payment rules apply where stated." | Detailed clauses on billing, currency, taxes, and payment processor (e.g., Stripe). | **High Gap**: Need explicit sections on how voting credits or contribution payments are handled. |
| **Refund Policy** | Not explicitly mentioned. | Clear "No Refund" or "Conditional Refund" policy for digital goods/services. | **Critical Gap**: Must state that digital contributions or votes are non-refundable once processed. |
| **IP Ownership** | Grant of rights to "operate, display, moderate." | Explicit "Contributor License Agreement" (CLA) or "Assignment of Rights." | **Medium Gap**: Clarify if the user retains any rights or if the project owns the final book. |
| **User Conduct** | General "do not misuse" clause. | Specific list of prohibited actions (scraping, botting, commercial use of content). | **Low Gap**: Add specific prohibitions against using AI to mass-generate edits. |
| **Termination** | Mention of suspension for violations. | Detailed process for account closure and data retention post-termination. | **Medium Gap**: Define what happens to a user's votes/edits if their account is deleted. |

---

## 2. Privacy Policy Comparison

| Feature | Current State | Industry Standard | Gap / Recommendation |
| :--- | :--- | :--- | :--- |
| **Data Collection** | Lists account, content, and technical data. | Granular list including payment metadata, device IDs, and location (if applicable). | **Medium Gap**: Explicitly mention payment processors (Stripe/PayPal) as third-party recipients. |
| **Legal Basis** | Not explicitly stated. | Clear "Legal Basis for Processing" (Consent, Contract, Legitimate Interest) for GDPR. | **High Gap**: Required for users in the EU/UK. |
| **Data Retention** | Not mentioned. | Specific retention periods (e.g., "7 years for financial records"). | **Medium Gap**: Define how long edit history is kept. |
| **User Rights** | Mentions access, correction, deletion. | Detailed instructions for exercising rights (DSAR process) and right to portability. | **Medium Gap**: Provide a specific email or form for legal requests. |
| **Cookies** | Brief mention. | Detailed Cookie Policy or link to a preference center. | **Low Gap**: List specific cookies used (session, CSRF, analytics). |

---

## 3. Structural Recommendations

### The "Legal Hub" Concept
Currently, links are scattered in the footer. A centralized **Legal Hub** (e.g., `/legal`) would improve trust and accessibility.

**Proposed Hub Structure:**
1.  **Terms of Service**: The master agreement.
2.  **Privacy Policy**: Data handling details.
3.  **Refund & Cancellation Policy**: Specifics for paid features.
4.  **Community Guidelines**: Behavioral expectations (less formal).
5.  **Cookie Policy**: Technical tracking details.

---

## 4. Key Text Updates Needed

1.  **Monetization Clause**: "Payments for voting credits or contributions are processed via [Processor]. All transactions are final."
2.  **IP Assignment**: "By submitting an edit, you irrevocably assign all copyright and intellectual property rights in that edit to [Project Name] for use in the final publication."
3.  **Liability Cap**: Explicitly limit liability to the amount paid by the user in the last 12 months.



---

## Step 19: docs/cloud-environment-setup.md

# Cloud environments: what to set (dev, staging, production)

Use this as a **living checklist** when you move Novel App to a cloud host (or add staging/production). **Update this file** when you add new `env()` configuration or integrations.

**Rules**

- **Never commit** real secrets (`.env`, `client_secret*.json`, PayPal keys, DB passwords). Keep them in each host’s **environment variables** or **secrets manager**.
- **Each environment** gets its own values: at minimum different **`APP_URL`**, **`APP_ENV`**, database, and usually OAuth redirect registration (same Google *client* can list multiple URLs—see [oauth-google-apple-setup.md](oauth-google-apple-setup.md)).
- After deploy: **`php artisan config:clear`** (or **`config:cache`** in production after verifying env).

Canonical variable **names** also appear in **`.env.example`**.

---

## 1. Quick reference by environment

| Concern | Development | Staging | Production |
|--------|-------------|---------|------------|
| **`APP_ENV`** | `local` | `staging` | `production` |
| **`APP_DEBUG`** | `true` | `false` (recommended) | **`false`** |
| **`APP_URL`** | Your dev origin (one of `http://127.0.0.1:8000` or `http://localhost:8000`) | `https://staging.yourdomain.com` | `https://www.yourdomain.com` (or apex—be consistent) |
| **`APP_KEY`** | `php artisan key:generate` | Unique per env | Unique per env |
| **`SESSION_SECURE_COOKIE`** | `false` if HTTP | `true` (HTTPS) | **`true`** |
| **`SESSION_DOMAIN`** | Usually `null` | `null` or `.yourdomain.com` if sharing cookies across subdomains | Same rule as staging |
| **Database** | Local sqlite or shared dev DB | **Dedicated** staging DB | **Dedicated** production DB |
| **PayPal `PAYPAL_MODE`** | `sandbox` | `sandbox` (typical) | **`live`** |
| **Mail** | `log` or Mailpit | Real SMTP or transactional provider | Real SMTP / Postmark / etc. |
| **OAuth Google** | Same client ID OK if redirect/origins registered | Same | Same |
| **OAuth Apple** | Often tested on staging (HTTPS) | Return URL for staging host | Return URL for prod host |

**Novel App–specific public hosts** (from your Google OAuth registration—keep in sync with Google Cloud Console):

- Local: `http://127.0.0.1:8000/auth/google/callback` (match `APP_URL`)
- Staging: `https://staging.whatsmybookname.com/auth/google/callback`
- Production: `https://www.whatsmybookname.com/auth/google/callback`

If domains change, update **Google** (and **Apple**) consoles **and** this table.

---

## 2. Application core

| Variable | Required | Notes |
|----------|----------|--------|
| `APP_NAME` | Yes | Display name (e.g. site title). |
| `APP_ENV` | Yes | `local` / `staging` / `production`. |
| `APP_KEY` | Yes | `base64:...` from `php artisan key:generate`. |
| `APP_DEBUG` | Yes | **`false`** in staging/prod** except short-lived debugging. |
| `APP_URL` | Yes | Must match browser origin (scheme + host + port). Drives URLs, OAuth defaults, mail, storage URL. |
| `ADMIN_EMAIL` | Recommended | Admin gate + seeders + notifications; see `.env.example`. |
| `LEGAL_ENTITY_NAME`, `LEGAL_ENTITY_ADDRESS`, `LEGAL_CONTACT_EMAIL`, `LEGAL_JURISDICTION` | Recommended | Rendered on legal pages so public policies show your registered business identity and governing jurisdiction. |

---

## 3. Database

| Variable | Required | Notes |
|----------|----------|--------|
| `DB_CONNECTION` | Yes | e.g. `mysql`, `pgsql`, or `sqlite` (dev only). |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | If not sqlite | **Separate database** per staging vs production. |
| `DB_URL` | Optional | Some PaaS provide a single URL instead of discrete vars. |

Run **`php artisan migrate`** (and **`--force`** in production). Use seeders only where appropriate (avoid prod admin seed mistakes).

---

## 4. Session, cookies, HTTPS

| Variable | Required | Notes |
|----------|----------|--------|
| `SESSION_DRIVER` | Yes | Often `database` (this project’s `.env.example`); ensure `sessions` table exists. |
| `SESSION_LIFETIME` | Optional | Minutes (default 120). |
| `SESSION_DOMAIN` | Optional | Usually `null`; set only if you need cross-subdomain cookies. |
| `SESSION_SECURE_COOKIE` | Yes for HTTPS | **`true`** when the site is only served over HTTPS. |
| `SESSION_ENCRYPT` | Optional | Can enable for extra cookie encryption. |

See [local-development.md](local-development.md) for local URL vs cookie pitfalls.

---

## 5. Cache, queue, Redis

| Variable | Required | Notes |
|----------|----------|--------|
| `CACHE_STORE` | Yes | `database` or `redis` in cloud. |
| `QUEUE_CONNECTION` | Yes | `database` or `redis`; run a **queue worker** in cloud. |
| `REDIS_*` | If using Redis | Host, password, port from your provider. |

**Scheduler:** configure the host to run **`php artisan schedule:run`** every minute (cron or platform scheduler).

---

## 6. Mail

| Variable | Required | Notes |
|----------|----------|--------|
| `MAIL_MAILER` | Yes | `smtp`, `log`, `postmark`, etc. |
| `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` | If SMTP | From provider. |
| `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` | Yes | Must be allowed by your mail provider. |

Test: **`php artisan mail:test you@example.com`** (if available in your Laravel version) or trigger a real notification.

---

## 7. Files / storage (optional S3)

| Variable | Required | Notes |
|----------|----------|--------|
| `FILESYSTEM_DISK` | Yes | `local` or `s3` for cloud multi-instance. |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` | If S3 | For uploads and public URLs. |

Post-deploy: **`php artisan storage:link`** when using local `public` disk for user-visible files.

---

## 8. OAuth — Google

| Variable | Required | Notes |
|----------|----------|--------|
| `GOOGLE_CLIENT_ID` | If using Google sign-in | Same ID can span envs if Console lists all origins/redirects. |
| `GOOGLE_CLIENT_SECRET` | If using Google sign-in | **Rotate** in Console updates secret everywhere. |
| `GOOGLE_REDIRECT_URI` | Optional | Defaults to `APP_URL/auth/google/callback`. |

Details: [oauth-google-apple-setup.md](oauth-google-apple-setup.md). Copy from **`client_secret_*.json`** into `.env` only on the server—do not commit JSON.

---

## 9. OAuth — Apple

| Variable | Required | Notes |
|----------|----------|--------|
| `APPLE_SIGN_IN_ENABLED` | To show Apple in UI | Default **`false`** until you intentionally enable Apple; must be **`true`** plus credentials below. |
| `APPLE_CLIENT_ID` | If using Apple | Services ID. |
| `APPLE_REDIRECT_URI` | Optional | Defaults to `APP_URL/auth/apple/callback`. |
| **Either** `APPLE_CLIENT_SECRET` (JWT) **or** `APPLE_TEAM_ID` + `APPLE_KEY_ID` + `APPLE_PRIVATE_KEY` (.p8 path) | If using Apple | Key path must exist on server; not in web root. |

Register **Return URLs** for **each** HTTPS host (staging + production). Apple often cannot use plain `http://localhost`.

---

## 10. PayPal

| Variable | Required | Notes |
|----------|----------|--------|
| `PAYPAL_MODE` | Yes | **`sandbox`** for dev/staging; **`live`** for production. |
| `PAYPAL_SANDBOX_CLIENT_ID`, `PAYPAL_SANDBOX_CLIENT_SECRET` | If sandbox | From [PayPal Developer](https://developer.paypal.com). |
| `PAYPAL_LIVE_CLIENT_ID`, `PAYPAL_LIVE_CLIENT_SECRET` | If live | Production credentials only on production. |
| `PAYPAL_WEBHOOK_ID` | Recommended | Enables PayPal signature verification for webhook authenticity checks. |
| `PAYPAL_WEBHOOK_TOKEN` | Fallback | Shared-secret fallback for local/testing if webhook ID is not configured. |
| `PAYPAL_LIVE_APP_ID` | Optional | If your integration needs it. |

**Do not commit** live keys. Replace any sample keys in local `.env` with your own.

---

## 11. Marketing / SEO (optional)

| Variable | Notes |
|----------|--------|
| `LANDING_PRIZE_POOL_DISPLAY` | Landing strip prize line. |
| `LANDING_SOFT_STATS_WHEN_EMPTY` | `true`/`false`. |
| `SEO_DEFAULT_DESCRIPTION` | Default meta description. |
| `SEO_OG_IMAGE_URL` | Open Graph image URL. |

---

## 12. Vite / front-end

| Variable | Notes |
|----------|--------|
| `VITE_APP_NAME` | Usually `"${APP_NAME}"`. |

Build assets in CI or on deploy: **`npm ci`** && **`npm run build`**.

---

## 13. Optional integrations (if you enable them)

| Variable | Service |
|----------|---------|
| `POSTMARK_API_KEY` | Postmark |
| `RESEND_API_KEY` | Resend |
| `SLACK_BOT_USER_OAUTH_TOKEN`, `SLACK_BOT_USER_DEFAULT_CHANNEL` | Slack notifications |

---

## 14. Post-deploy commands (production-oriented)

Run on the server after code and env are in place (exact order may vary):

1. Install Composer deps: `composer install --no-dev --optimize-autoloader`
2. `php artisan migrate --force`
3. `php artisan storage:link` (if using public disk)
4. `npm ci && npm run build` (or use prebuilt assets from CI)
5. `php artisan config:cache` && `php artisan route:cache` && `php artisan view:cache` (only after env is correct)
6. Restart queue workers / PHP-FPM / Octane as your platform requires

For troubleshooting config: temporarily `php artisan config:clear`.

---

## 15. External consoles (sync with env)

When **`APP_URL`** or domains change, update:

- [ ] **Google Cloud** — Authorized JavaScript origins + redirect URIs for **each** environment URL.
- [ ] **Apple Developer** — Services ID domains + Return URLs for **each** HTTPS host.
- [ ] **PayPal** — App return/cancel URLs if PayPal dashboard requires them for your integration.

---

## 16. Blank template (copy per environment)

Fill one block per host (paste into secrets UI or `.env` on server—**not** into git):

```dotenv
APP_NAME=
APP_ENV=
APP_KEY=
APP_DEBUG=
APP_URL=

ADMIN_EMAIL=

DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=
SESSION_SECURE_COOKIE=
SESSION_DOMAIN=

CACHE_STORE=
QUEUE_CONNECTION=

MAIL_MAILER=
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=

LEGAL_ENTITY_NAME=
LEGAL_ENTITY_ADDRESS=
LEGAL_CONTACT_EMAIL=
LEGAL_JURISDICTION=
LEGAL_DISPUTE_NOTICE_DAYS=30

FILESYSTEM_DISK=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

APPLE_SIGN_IN_ENABLED=false
APPLE_CLIENT_ID=
APPLE_TEAM_ID=
APPLE_KEY_ID=
APPLE_PRIVATE_KEY=
# APPLE_CLIENT_SECRET=

PAYPAL_MODE=
PAYPAL_SANDBOX_CLIENT_ID=
PAYPAL_SANDBOX_CLIENT_SECRET=
PAYPAL_LIVE_CLIENT_ID=
PAYPAL_LIVE_CLIENT_SECRET=

VITE_APP_NAME="${APP_NAME}"
```

Add **`REDIS_*`** if you switch cache/queue to Redis.

---

*Last reviewed: keep in sync with `.env.example` and `config/*.php` when the app gains new environment variables.*



---

## Step 20: CHANGELOG.md

# Changelog: Novel App Development

This document summarizes the key changes and enhancements made to the `novel-app` project during its development.

## Version 1.9.42 - P4-4 / P4-5 reader + dark mode; Tier A public profile follow-ups
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
