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
