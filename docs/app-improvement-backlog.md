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
