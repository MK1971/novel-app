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
