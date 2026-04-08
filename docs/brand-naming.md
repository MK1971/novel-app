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
