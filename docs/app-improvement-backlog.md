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

| # | Item | Notes |
|---|------|--------|
| P3-1 | **PayPal / checkout error UX** | User-visible message when credentials missing or PayPal API fails; avoid silent or generic failures. `PaymentController`. |
| P3-2 | **Voting vs `paid_at`** | Confirm product intent: free votes for eligible users vs paid. If free, consider renaming/clarifying `paid_at` in code or docs to avoid confusion. |
| P3-3 | **About page refresh** | Mention $2 edit flow, gated voting, link to Privacy/Terms; match current product. `resources/views/about.blade.php`. |
| P3-4 | **Activity feed / analytics** | If low value, trim sidebar prominence or add content; if strategic, define minimum viable metrics. |
| P3-5 | **Notifications in header** | Optional bell + unread count on `app` layout when notification volume grows. |
| P3-6 | **Accessibility pass (app shell)** | Focus rings, heading order, reduce reliance on emoji-only meaning in critical UI (vote/chapters). Mirror landing patterns where useful. |
| P3-7 | **Reading progress bar vs sticky chrome** | Chapter index: z-index / overlap with sticky nav on small screens; verify tap targets. |
| P3-8 | **Brand string consistency** | Document when to use “What’s My Book Name” vs “WhatsMyBookName” vs book titles; sweep key layouts. |

---

## How to use this doc

- Tick or strike items as you ship them.
- Optional: add PR/commit references next to each row.
- After each batch, update `CHANGELOG.md` (per project rule).
