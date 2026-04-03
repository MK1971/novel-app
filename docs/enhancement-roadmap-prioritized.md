# Enhancement roadmap (prioritized)

Source documents (repo root):

- `Comprehensive_Application_Enhancement_Report_What's_My_Book_Name_(v2).docx`
- `WhatsMyBookName_UX_Enhancement_Report.docx`

This file turns those suggestions into a **priority-ordered plan** with **how we intend to implement** each item. Scope assumes the current Laravel stack (Blade, Tailwind, PayPal for paid edits, existing chapters/votes/moderation).

---

## Priority 1 — Highest impact, incremental effort

These fix obvious reader/contributor friction without large new systems.

| # | Enhancement | Why now | Development plan |
|---|-------------|---------|------------------|
| 1 | **Next / Previous chapter** on chapter read view | Expected book-like navigation; reduces list ping-pong. | Pass ordered TBWNN chapter IDs (or `list_section` + `number`) from `ChapterController@show`; render two links in `chapters/show.blade.php`. Respect locked/archived rules same as index. |
| 2 | **Reading progress on chapter list cards** | Users already have scroll progress; surfacing it aids resume. | Reuse `ReadingProgress` for logged-in users: on `ChapterController@index`, eager-load or batch-query progress per chapter; show thin bar or % on each card in `chapters/index.blade.php`. |
| 3 | **Estimated reading time** (± word count) per chapter | Sets expectations; cheap. | Helper on `Chapter` (e.g. word count from `strip_tags` + `str_word_count`, minutes at ~200 wpm); show on index cards and/or show header. |
| 4 | **Vote hub: CTA when voting is restricted** | Turns a dead-end into the next paid path. | In `vote/index.blade.php`, when user lacks credit, add primary button to `chapters.index` or first open TBWNN chapter (`ChapterLifecycle::latestOpenTbwChapter()` or similar). |
| 5 | **Grand prize banner → rules page** | Reduces confusion and support. | Add route + Blade `prizes` or `campaign-rules` (copy from marketing/legal); link from leaderboard banner and landing if present. |
| 6 | **Landing: soften or hide zero stats** | Avoids “empty project” first impression. | In home route, if contributors/edits/chapters below thresholds, hide the stats strip or show copy like “Be among the first…” (`config/marketing.php` flags). |
| 7 | **Auth: password show/hide** | Small accessibility/usability win. | Alpine or vanilla JS toggle `type` on password inputs in auth modals/views; ensure labels/ARIA. |
| 8 | **Auth modals: stronger branding** | Aligns login with site identity. | Reuse landing colors/logo in `auth/modals` partials; no backend change. |
| 9 | **SEO: meta description + Open Graph** on public pages | Better shares and search snippets. | Per-route or layout `@section('meta')`; `og:title`, `og:description`, `og:image` from `config` or env. |

---

## Priority 2 — Strong value, moderate scope

| # | Enhancement | Why | Development plan |
|---|-------------|-----|------------------|
| 10 | **Post-signup onboarding checklist** | Guides first session without full “tour” product. | After registration, one dashboard card or `/welcome-steps`: links to `chapters.index`, first open chapter, leaderboard; dismissible; optional `users.onboarding_completed_at`. |
| 11 | **Dashboard stat cards: shorter copy + “Details”** | Reduces visual noise; keeps depth. | Trim subtitles in `dashboard.blade.php`; add `title` tooltips or small “?” popover with current long text. |
| 12 | **Achievements: clearer progress / how to earn** | Badges exist; discovery is weak. | On `achievements` views, ensure each tile shows requirement + progress (query existing unlock rules); hover/focus panels for keyboard users. |
| 13 | **Leaderboard: “Your rank” highlight** | Scales when list is long. | In `LeaderboardController`, compute current user’s position; render sticky row or banner; style with existing palette. |
| 14 | **Leaderboard: time scope** (week / month / all-time) | Makes board feel fresh. | Requires **defining** score source (e.g. points events by `updated_at` on accepted edits only). May add materialized sums or query `edits`/`inline_edits` by `approved_at`. Start with all-time + “last 30 days” if simpler. |
| 15 | **Peter Trull: text diff between A and B** | Core to “compare versions.” | Server-side diff (e.g. `sebastian/diff` already in ecosystem) or JS diff of plain text; render in `vote/index.blade.php` below or beside columns; guard length for performance. |
| 16 | **Reader typography polish** (spacing, optional serif for body) | Literary feel without full theme engine. | Scoped CSS for `#chapter-content` / prose class; optional user toggle in `localStorage` later (see P4). |
| 17 | **Feedback form: more categories** | Better triage for admin. | Extend `feedback.type` validation + migration if enum-like; update `FeedbackController` + form options. |
| 18 | **WCAG contrast pass** on muted text | Accessibility + readability. | Audit tokens in Tailwind usage; darken `text-amber-800/40` etc. on cream backgrounds; re-check focus rings. |
| 19 | **Insights: empty states** | Less “broken chart” feeling. | `analytics/index.blade.php`: placeholders, illustration or short copy when series empty. |
| 20 | **Top leader strip: clearer label + link** | “Leader: …” confusion. | `layouts` composer: copy “Top contributor” + `route('leaderboard')`; optional tooltip. |

---

## Priority 3 — Larger features, still aligned

| # | Enhancement | Development plan |
|---|-------------|------------------|
| 21 | **Email + in-app when edit is accepted/rejected/partial** | Verify all moderation paths fire `Notification` (or mail) consistently; add tests; template copy for each outcome. |
| 22 | **User payment / vote credit history** | New `Payment`-centric index for authed user: list completed checkouts, linked vote if any, chapter; reuse PayPal metadata; no Stripe unless product changes. |
| 23 | **Draft autosave for edit textareas** | `localStorage` key per `chapter_id` + type; restore on load; clear on successful submit. Server drafts optional later. |
| 24 | **Diff preview before submitting full-chapter edit** | Client-side or server compare `original` vs textarea; modal or collapsible; paragraph flow unchanged. |
| 25 | **Profile: “My submissions”** | Tab on profile: queries on `Edit` + `InlineEdit` for user; statuses, links to chapters; pagination. |
| 26 | **Profile photo upload** | `users.avatar_path`, storage disk, validation, resize (Intervention or Laravel image); default to initials if null. |
| 27 | **RSS feed for new TBWNN chapters** | `Route::get('/feed/chapters.xml')`; query latest non-archived TBWNN rows; `response()->view` with `Content-Type: application/rss+xml`. |
| 28 | **Legacy / history inside chapter UX** | Optional tab: “Current” vs “Previous versions” using `is_reader_archive_link` / archive links; may reduce reliance on standalone archive page over time. |
| 29 | **Rate limiting paid edit checkouts** | `RateLimiter` in `PaymentController` by `user_id` + sliding window; flash friendly message. |

---

## Priority 4 — Major initiatives (separate approval)

These need product, budget, and often legal review before engineering estimates.

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
| **Social login (Google / Apple)** | OAuth apps, policies, fallback email. |

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

- **Shipped (P2 #10–13):** Post-signup onboarding card on the reader dashboard (dismiss + `users.onboarding_completed_at`, existing users backfilled on migration); reader and admin stat tiles shortened with `title` + “?” tooltips; achievements index/show show numeric progress, clearer requirement copy, `<details>` “How to earn”, focus-visible rings on tiles; leaderboard uses `LeaderboardController` with **Your rank** banner, **You** row highlight, and note when outside top 20.
- **Shipped (P2 #14–20):** Leaderboard **`?period=30d`** (points from **`edits.points_awarded`** + paid **inline** approvals in window via **`LeaderboardScoring`**); Peter Trull vote pairs — collapsible **unified text diff** (**`sebastian/diff`**, **`App\Support\TextDiff`**); reader **`#chapter-content`** typography + paragraph-edit contrast/focus; feedback types **accessibility, account, payment, content_issue** + admin **`typeLabel()`**; contrast bumps on leaderboard/vote/insights/admin feedback; insights **empty states** with CTAs; header **Top contributor** strip links to **`route('leaderboard')`**. Manual checklist: **`docs/P2-manual-testing.md`**.
