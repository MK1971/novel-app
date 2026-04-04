# Priority 4 (P4) — detailed priority list

This document **ranks** the “major initiatives” and closely related deferred items from `docs/enhancement-roadmap-prioritized.md` and the source reports (`Comprehensive_Application_Enhancement_Report_What's_My_Book_Name_(v2).docx`, `WhatsMyBookName_UX_Enhancement_Report.docx`). **Nothing here is approved work** until product, budget, and (where relevant) legal sign-off.

## How to read this list

- **Order** = recommended **sequencing if you pursue P4 at all** (dependencies, risk, and leverage), not effort hours.
- **Tier** groups items that can be **scoped or killed together** in planning.
- Items marked **Gate** need a written spec (abuse model, data retention, or rights) before engineering estimates are meaningful.

---

## Tier A — Identity, access, and trust (usually first among P4)

| Rank | Initiative | Why this order | Dependencies / gates | Rough engineering themes |
|------|------------|----------------|----------------------|-------------------------|
| **P4-1** | **Social login (Google / Apple)** | Removes signup friction; does not require other P4 systems. | **Gate:** OAuth apps, privacy policy, account linking (email collision), “unlink” path. | Laravel Socialite (or equivalent), user table / `social_accounts`, session + registration flows, tests for merge edge cases. |
| **P4-2** | **Public contributor profiles** | Builds on existing profile, leaderboard, and (if shipped) avatars; increases community recognition. | **Gate:** GDPR-style controls, visibility toggles, block/report, harassment policy. Prefer **after** or **with** P4-1 if social accounts exist. | Public slug, optional bio, “contributions” summary, privacy settings, moderation hooks. |
| **P4-3** | **Email verification indicator (profile/settings)** | Small trust signal; often bundled with P4-1/P4-2 auth work. | Low gate; align copy with marketing. | UI on profile + optional banner after social link. |

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

## Consolidated order (executive short list)

If leadership asks “what order should we **consider** P4?”:

1. **P4-1** Social login  
2. **P4-2** Public profiles (with privacy)  
3. **P4-4 / P4-5** Reader themes + focus (+ dark mode as one program)  
4. **P4-6** Threaded comments + mentions  
5. **P4-7 / P4-8** Peer review + suggestion voting (single governance spec)  
6. **P4-10** AI pre-screen  
7. **P4-11** Trusted scribe (only with strong audit)  
8. **P4-12** TTS / narrations  
9. **P4-13 / P4-14** Voter insights + countdowns  
10. **P4-16 / P4-17** Book / wiki  
11. **P4-15** Battle mode  
12. **P4-18** Echo feed  
13. **P4-9** Feedback upvotes (or fold into P4-8 generic voting)  
14. **P4-3** Email verified badge (can move up if bundled with auth work)

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
