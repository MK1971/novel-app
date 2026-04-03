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
