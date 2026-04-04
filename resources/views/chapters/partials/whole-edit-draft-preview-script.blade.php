<script>
(function () {
    const CHAPTER_ID = {{ $chapter->id }};
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const ta = document.getElementById('edited_text');
    const previewBox = document.getElementById('novel-edit-diff-preview');
    const btnPreview = document.getElementById('novel-btn-preview-edit-diff');
    const btnClear = document.getElementById('novel-btn-clear-edit-draft');
    const form = ta?.closest('form');
    if (!ta || !token || !form) return;

    const draftKey = 'novel-whole-edit-draft-' + CHAPTER_ID;
    let draftTimer;
    const preferServerDraft = @json($preferServerDraft ?? false);
    const editedTextBaseline = @json($editedTextBaseline ?? '');

    const saved = localStorage.getItem(draftKey);
    if (!preferServerDraft && saved && saved.length > 0) {
        ta.value = saved;
    }

    ta.addEventListener('input', function () {
        clearTimeout(draftTimer);
        draftTimer = setTimeout(function () {
            try {
                localStorage.setItem(draftKey, ta.value);
            } catch (e) {}
        }, 800);
    });

    form.addEventListener('submit', function () {
        try {
            localStorage.removeItem(draftKey);
        } catch (e) {}
    });

    btnClear?.addEventListener('click', function () {
        try {
            localStorage.removeItem(draftKey);
        } catch (e) {}
        ta.value = editedTextBaseline;
        previewBox?.classList.add('hidden');
        previewBox && (previewBox.innerHTML = '');
    });

    function buildDiffLegend(wasTruncated, collapsedSame) {
        const wrap = document.createElement('div');
        wrap.className = 'space-y-2 text-xs font-bold text-amber-50/95 shrink-0';
        wrap.setAttribute('role', 'note');

        const title = document.createElement('p');
        title.className = 'text-sm font-black text-white';
        title.textContent = 'How to read this';
        wrap.appendChild(title);

        const intro = document.createElement('p');
        intro.className = 'text-amber-100/85 font-semibold leading-relaxed';
        intro.textContent = 'Published text is compared to your draft line by line. Chapter HTML is split at tag boundaries so you see separate rows (not one huge block). Long unchanged sections are folded into an amber notice so the actual edits stay visible — fine for long chapters (e.g. thousands of words).';
        wrap.appendChild(intro);

        function legendRow(swatchClass, label) {
            const row = document.createElement('div');
            row.className = 'flex flex-wrap items-start gap-2';
            const sw = document.createElement('span');
            sw.className = 'novel-diff-preview-swatch ' + swatchClass;
            sw.setAttribute('aria-hidden', 'true');
            const lab = document.createElement('span');
            lab.className = 'leading-relaxed';
            lab.textContent = label;
            row.appendChild(sw);
            row.appendChild(lab);
            return row;
        }

        wrap.appendChild(legendRow('novel-diff-preview-swatch--removed', 'Rose / red — in published, not in your draft (removed or replaced).'));
        wrap.appendChild(legendRow('novel-diff-preview-swatch--added', 'Green — in your draft, not in published (new or changed).'));
        wrap.appendChild(legendRow('novel-diff-preview-swatch--same', 'Dim — unchanged context (only a few lines shown around big unchanged stretches).'));
        wrap.appendChild(legendRow('novel-diff-preview-swatch--warning', 'Amber — notes (hidden unchanged lines, or preview cut off for size).'));

        if (collapsedSame) {
            const c = document.createElement('p');
            c.className = 'text-amber-100/90 font-bold pt-1';
            c.textContent = 'This preview folded long unchanged passages; scroll the list for rose/green rows where text actually changed.';
            wrap.appendChild(c);
        }

        if (wasTruncated) {
            const t = document.createElement('p');
            t.className = 'text-amber-200 font-black pt-1';
            t.textContent = 'This preview ends early; scroll the list below for the last note.';
            wrap.appendChild(t);
        }

        return wrap;
    }

    function renderDiffRows(lines, wasTruncated, collapsedSame) {
        if (!previewBox) return;
        previewBox.innerHTML = '';

        previewBox.appendChild(buildDiffLegend(!! wasTruncated, !! collapsedSame));

        const scroller = document.createElement('div');
        scroller.className = 'max-h-72 overflow-y-auto rounded-xl border-2 border-white/15 bg-black/30 shadow-inner min-h-0';
        scroller.setAttribute('role', 'region');
        scroller.setAttribute('aria-label', 'Line-by-line comparison: published chapter versus your suggestion');

        lines.forEach(function (row) {
            const div = document.createElement('div');
            const kind = row.kind === 'removed' || row.kind === 'added' || row.kind === 'warning' ? row.kind : 'same';
            div.className = 'novel-diff-preview-row novel-diff-preview-row--' + kind;
            div.textContent = row.text;
            scroller.appendChild(div);
        });

        previewBox.appendChild(scroller);
    }

    btnPreview?.addEventListener('click', async function () {
        if (!previewBox) return;
        previewBox.classList.remove('hidden');
        previewBox.innerHTML = '<p class="text-white/70 text-sm font-bold p-2">Loading comparison…</p>';
        try {
            const res = await fetch(@json(route('edits.preview-diff')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ chapter_id: CHAPTER_ID, edited_text: ta.value }),
            });
            const data = await res.json();
            if (!data.ok || !data.diff || !data.diff.lines) {
                previewBox.innerHTML = '<p class="text-amber-200 text-sm font-bold p-2">' + (data.message || 'Could not build preview.') + '</p>';
                return;
            }
            renderDiffRows(data.diff.lines, !! data.diff.truncated, !! data.diff.collapsed_same);
        } catch (e) {
            previewBox.innerHTML = '<p class="text-red-200 text-sm font-bold p-2">Network error. Try again.</p>';
        }
    });
})();
</script>
