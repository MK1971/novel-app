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
