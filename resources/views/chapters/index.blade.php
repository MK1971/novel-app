@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    The Book With No Name
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Read the story and shape the narrative.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        @if (session('success'))
            <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-8 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 shadow-sm font-bold">{{ session('error') }}</div>
        @endif

        <div class="max-w-5xl mx-auto space-y-16">
            @php
                $tbwnnPrimaryMaxId = ($chapters ?? collect())->max('id');
            @endphp
            @forelse($chapters ?? [] as $chapter)
                @php
                    $isLatestTbwSlot = $tbwnnPrimaryMaxId !== null && (int) $chapter->id === (int) $tbwnnPrimaryMaxId;
                @endphp
                <div 
                    id="chapter-{{ $chapter->id }}" 
                    class="chapter-container bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden transition-all duration-500"
                    data-chapter-id="{{ $chapter->id }}"
                    data-paid-edits-open="{{ $chapter->manuscriptPaidEditsOpen() ? '1' : '0' }}"
                >
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[15rem] font-black text-amber-500/5 select-none leading-none">
                        {{ $chapter->listSectionDecorativeMarker() }}
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">{{ $chapter->listSectionBadge() }}</span>
                                <span class="text-amber-800/60 font-bold">Version {{ $chapter->version }}</span>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                @if($chapter->is_locked)
                                    <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-widest bg-amber-50/50 px-6 py-3 rounded-2xl border border-amber-100/50">
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-900 text-lg mb-0.5">{{ $chapter->statistics->total_reads ?? 0 }}</span>
                                            <span class="text-amber-900/40">Reads</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-green-600 text-lg mb-0.5">{{ $chapter->statistics->accepted_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Accepted</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-amber-600 text-lg mb-0.5">{{ $chapter->statistics->total_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Total Edits</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-red-600 text-lg mb-0.5">{{ $chapter->statistics->rejected_edits ?? 0 }}</span>
                                            <span class="text-amber-900/40">Rejected</span>
                                        </div>
                                        <div class="w-px h-8 bg-amber-200/50"></div>
                                        <div class="flex flex-col items-center">
                                            <span class="text-red-700 text-lg mb-0.5">🔒</span>
                                            <span class="text-red-700">Locked</span>
                                        </div>
                                    </div>
                                    <button 
                                        type="button"
                                        onclick="toggleChapter({{ $chapter->id }})"
                                        class="p-3 bg-amber-50 rounded-2xl text-amber-900 hover:bg-amber-100 transition-all"
                                        id="toggle-btn-{{ $chapter->id }}"
                                        aria-expanded="{{ $isLatestTbwSlot ? 'true' : 'false' }}"
                                        aria-label="{{ $isLatestTbwSlot ? 'Collapse chapter summary' : 'Expand chapter summary' }}"
                                    >
                                        <svg class="w-6 h-6 transform transition-transform duration-300 {{ $isLatestTbwSlot ? 'rotate-180' : '' }}" id="icon-{{ $chapter->id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                @else
                                    @if($chapter->manuscriptPaidEditsOpen())
                                        <div class="px-6 py-3 bg-green-100 rounded-2xl border border-green-200 text-green-700 text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                            Open for Edits
                                        </div>
                                    @else
                                        <div class="px-6 py-3 bg-amber-100 rounded-2xl border border-amber-200 text-amber-800 text-xs font-black uppercase tracking-widest">
                                            Editing window closed
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <h3 class="text-4xl font-extrabold text-amber-900 {{ $chapter->is_locked ? 'mb-3' : 'mb-4' }}">{{ $chapter->displayTitle() }}</h3>
                        @php
                            $cardWordCount = $chapter->wordCount();
                            $cardReadMins = $chapter->estimatedReadingMinutes();
                            $cardProgress = auth()->check() ? $readingProgressByChapter->get($chapter->id) : null;
                            $cardReadPct = $cardProgress?->displayProgressPercent();
                        @endphp
                        <div class="mb-6 space-y-3">
                            <p class="text-sm font-bold text-amber-800/70 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span>{{ number_format($cardWordCount) }} words</span>
                                <span class="text-amber-800/40" aria-hidden="true">·</span>
                                <span>~{{ $cardReadMins }} min read</span>
                            </p>
                            @auth
                                <div class="w-full max-w-lg rounded-2xl border border-amber-100 bg-amber-50/50 px-4 py-3">
                                    <div class="flex flex-wrap items-baseline justify-between gap-2 mb-2">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-800/50">Your progress</span>
                                        @if($cardReadPct !== null)
                                            <span class="text-xs font-black text-amber-900 tabular-nums">{{ $cardReadPct }}%</span>
                                        @else
                                            <span class="text-xs font-bold text-amber-800/60">Not started</span>
                                        @endif
                                    </div>
                                    @if($cardReadPct !== null)
                                        <span class="sr-only">{{ $cardReadPct }} percent read on this chapter</span>
                                    @endif
                                    <div class="relative h-2 w-full min-w-0 overflow-hidden rounded-full bg-amber-200/80" aria-hidden="true">
                                        <div
                                            class="absolute inset-y-0 left-0 rounded-full bg-amber-600 transition-all duration-300 ease-out"
                                            style="width: {{ max(0, min(100, (int) ($cardReadPct ?? 0))) }}%;"
                                        ></div>
                                    </div>
                                    <p class="mt-2 text-[10px] font-bold text-amber-800/50">Updates when you scroll this list or the full chapter page.</p>
                                </div>
                            @endauth
                        </div>

                        @if($chapter->is_locked)
                            <div
                                id="content-{{ $chapter->id }}"
                                class="mb-12 {{ $isLatestTbwSlot ? '' : 'hidden' }}"
                            >
                                <div class="bg-amber-50/30 rounded-[2rem] p-10 border-2 border-dashed border-amber-200/50 text-center group-hover:border-amber-300/50 transition-all">
                                    <div class="w-16 h-16 bg-amber-100/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                        <span class="text-2xl">🔒</span>
                                    </div>
                                    <p class="text-amber-900/40 font-extrabold text-lg mb-2 uppercase tracking-widest">Chapter Locked</p>
                                    <p class="text-amber-800/30 font-bold italic">This chapter is now part of the permanent record. Click below to read the final version.</p>
                                </div>
                            </div>
                        @else
                            <div
                                id="content-{{ $chapter->id }}"
                                class="chapter-content max-w-none text-amber-900/80 leading-[2.2] font-medium text-left mb-12"
                            >
                                @php
                                    $paragraphs = explode("\n", $chapter->content);
                                @endphp
                                @foreach($paragraphs as $index => $paragraph)
                                    @if(trim($paragraph))
                                        <p class="mb-6 relative group" data-paragraph-index="{{ $index }}">
                                            {{ $paragraph }}
                                            @auth
                                                @if($chapter->manuscriptPaidEditsOpen())
                                                    <button
                                                        onclick="openInlineEdit({{ $chapter->id }}, {{ $index }}, '{{ addslashes(trim($paragraph)) }}')"
                                                        class="absolute -right-8 top-0 opacity-0 group-hover:opacity-100 transition-opacity p-2 text-amber-400 hover:text-amber-600"
                                                        title="Suggest edit for this paragraph"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </button>
                                                @endif
                                            @endauth
                                        </p>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-8 border-t border-amber-100 gap-6 flex-wrap">
                            <div class="text-amber-800/40 text-sm font-bold space-y-1 min-w-0">
                                <div>Published {{ ($chapter->published_at ?? $chapter->created_at)->timezone(config('app.timezone'))->format('M j, Y') }}</div>
                                @if($chapter->is_locked)
                                    @php $paidClosedOn = $chapter->lockedAtForDisplay(); @endphp
                                    <div class="text-amber-800/80 font-bold">
                                        @if($paidClosedOn)
                                            Paid editing closed on {{ $paidClosedOn->timezone(config('app.timezone'))->format('M j, Y') }}.
                                        @else
                                            Paid editing is closed for this chapter.
                                        @endif
                                    </div>
                                @elseif($chapter->editing_closes_at)
                                    @if($chapter->manuscriptPaidEditsOpen())
                                        <div class="text-amber-800 font-bold">Paid edits open until {{ $chapter->editing_closes_at->timezone(config('app.timezone'))->format('M j, Y') }} ({{ $chapter->editing_closes_at->diffForHumans() }}).</div>
                                    @else
                                        <div class="text-amber-800/80 font-bold">Paid editing for this round ended {{ $chapter->editing_closes_at->timezone(config('app.timezone'))->format('M j, Y') }}.</div>
                                    @endif
                                @endif
                            </div>
                            
                            @if(!$chapter->is_locked && $chapter->manuscriptPaidEditsOpen())
                                <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-10 py-5 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                    Suggest an Edit
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            @elseif(!$chapter->is_locked)
                                <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-10 py-5 bg-amber-100 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-200 transition-all border border-amber-200 shadow-sm transform hover:-translate-y-1">
                                    View chapter
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            @else
                                <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-10 py-5 bg-amber-100 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-200 transition-all border border-amber-200 shadow-sm transform hover:-translate-y-1">
                                    View Final Version
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-32 bg-white border border-amber-100 rounded-[3rem] shadow-sm">
                    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">The ink is still drying...</h3>
                    <p class="text-amber-800/50 text-lg font-bold">No chapters have been published yet. Check back soon!</p>
                </div>
            @endforelse

            @if(isset($archiveChapters) && $archiveChapters->isNotEmpty())
                <div class="border-t-2 border-amber-200/60 pt-16">
                    <h2 class="text-2xl font-extrabold text-amber-900 mb-2">Previous published versions</h2>
                    <p class="text-amber-800/60 font-bold mb-8 text-sm">Final closed text from earlier editing rounds (read-only).</p>
                    <ul class="space-y-3">
                        @foreach($archiveChapters as $arch)
                            <li>
                                <a href="{{ route('chapters.show', $arch) }}" class="inline-flex items-center gap-2 text-amber-800 font-extrabold hover:text-amber-950 underline decoration-amber-300">
                                    {{ $arch->headingPrefix() }}: {{ $arch->displayTitle() }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Inline Edit Modal --}}
    @auth
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-2xl p-12 shadow-2xl">
            <h3 class="text-2xl font-extrabold text-amber-900 mb-2">Suggest paragraph edit</h3>
            <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">Change <strong class="text-amber-900">one paragraph</strong> in this chapter. For the <strong class="text-amber-900">entire chapter</strong>, use the sidebar on the chapter page (Writing / Phrase). Same <strong class="text-amber-900">$2</strong> checkout. Points match chapter edits: <strong class="text-amber-900">2</strong> full / <strong class="text-amber-900">1</strong> partial / <strong class="text-amber-900">0</strong> rejected.</p>
            <form id="inline-edit-form" method="POST" action="{{ route('payment.checkout') }}" class="space-y-8">
                @csrf
                <input type="hidden" id="edit-chapter-id" name="chapter_id">
                <input type="hidden" name="type" value="inline_edit">
                <input type="hidden" id="paragraph-number" name="paragraph_number">
                <input type="hidden" id="original-text-input" name="original_text">
                
                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Original Paragraph</label>
                    <div id="original-text-display" class="p-6 bg-amber-50 rounded-2xl text-amber-900/60 italic text-sm border border-amber-100"></div>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Your Suggestion</label>
                    <textarea id="suggested-text" name="suggested_text" rows="4" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required></textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Reason (Optional)</label>
                    <input type="text" id="edit-reason" name="reason" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all">
                </div>

                <p class="text-xs font-bold text-amber-800/70 leading-relaxed">Uses the <strong class="text-amber-900">$2</strong> PayPal checkout. Leaderboard points apply only after payment succeeds and your edit is accepted.</p>
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                        Continue to PayPal ($2)
                    </button>
                    <button type="button" onclick="closeInlineEdit()" class="px-10 py-4 bg-amber-50 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-100 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <script>
        let selectionButton = null;

        // Text Selection Logic
        document.addEventListener('mouseup', function(e) {
            const selection = window.getSelection();
            const selectedText = selection.toString().trim();
            
            if (selectionButton) {
                selectionButton.remove();
                selectionButton = null;
            }

            if (selectedText.length > 0) {
                const range = selection.getRangeAt(0);
                const container = range.commonAncestorContainer;
                
                // Ensure the selection is within an unlocked chapter content
                let chapterContent = container.nodeType === 3 ? container.parentElement : container;
                while (chapterContent && !chapterContent.classList.contains('chapter-content')) {
                    chapterContent = chapterContent.parentElement;
                }

                if (chapterContent && !chapterContent.classList.contains('hidden')) {
                    const chWrap = chapterContent.closest('.chapter-container');
                    if (! chWrap || chWrap.getAttribute('data-paid-edits-open') !== '1') {
                        return;
                    }
                    const chapterId = chapterContent.id.replace('content-', '');
                    
                    selectionButton = document.createElement('button');
                    selectionButton.innerHTML = '✨ Suggest Edit';
                    selectionButton.className = 'fixed z-[200] px-4 py-2 bg-amber-900 text-white text-xs font-black rounded-full shadow-2xl hover:bg-black transition-all transform -translate-x-1/2 -translate-y-full mt-[-10px]';
                    
                    const rect = range.getBoundingClientRect();
                    selectionButton.style.left = (rect.left + rect.width / 2) + 'px';
                    selectionButton.style.top = rect.top + window.scrollY + 'px';
                    
                    selectionButton.onclick = function() {
                        let paragraphIndex = 0;
                        let node = range.commonAncestorContainer;
                        if (node.nodeType === 3) {
                            node = node.parentElement;
                        }
                        while (node && node !== document.body) {
                            if (node.dataset && node.dataset.paragraphIndex !== undefined) {
                                paragraphIndex = parseInt(node.dataset.paragraphIndex, 10) || 0;
                                break;
                            }
                            node = node.parentElement;
                        }
                        openInlineEdit(chapterId, paragraphIndex, selectedText);
                        selection.removeAllRanges();
                        selectionButton.remove();
                        selectionButton = null;
                    };
                    
                    document.body.appendChild(selectionButton);
                }
            }
        });

        function toggleChapter(id) {
            const content = document.getElementById('content-' + id);
            const icon = document.getElementById('icon-' + id);
            const btn = document.getElementById('toggle-btn-' + id);
            if (!content || !icon) return;
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
                if (btn) {
                    btn.setAttribute('aria-expanded', 'true');
                    btn.setAttribute('aria-label', 'Collapse chapter summary');
                }
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.setAttribute('aria-label', 'Expand chapter summary');
                }
            }
        }

        @auth
        const modal = document.getElementById('inline-edit-modal');
        const form = document.getElementById('inline-edit-form');

        function openInlineEdit(chapterId, number, text) {
            document.getElementById('edit-chapter-id').value = chapterId;
            document.getElementById('paragraph-number').value = number;
            document.getElementById('original-text-input').value = text;
            document.getElementById('original-text-display').innerText = text;
            document.getElementById('suggested-text').value = text;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeInlineEdit() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        @endauth

        @auth
        (function () {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (! csrf) {
                return;
            }
            let timer = null;
            function percentReadThrough(el) {
                const rect = el.getBoundingClientRect();
                const h = el.offsetHeight;
                if (h < 1) {
                    return 0;
                }
                const viewportBottom = window.scrollY + window.innerHeight;
                const blockTop = rect.top + window.scrollY;
                const readThrough = Math.min(Math.max(viewportBottom - blockTop, 0), h);
                return Math.min(100, Math.round((100 * readThrough) / h));
            }
            function flush() {
                document.querySelectorAll('.chapter-container[data-chapter-id]').forEach(function (wrap) {
                    const id = wrap.getAttribute('data-chapter-id');
                    const content = document.getElementById('content-' + id);
                    if (! content || content.classList.contains('hidden')) {
                        return;
                    }
                    const pct = percentReadThrough(content);
                    if (pct < 1) {
                        return;
                    }
                    fetch('/chapters/' + encodeURIComponent(id) + '/track-progress', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ read_percent: pct }),
                        keepalive: true,
                    }).catch(function () {});
                });
            }
            function scheduleFlush() {
                window.clearTimeout(timer);
                timer = window.setTimeout(flush, 450);
            }
            window.addEventListener('scroll', scheduleFlush, { passive: true });
            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    flush();
                }
            });
            window.addEventListener('pagehide', flush);
        })();
        @endauth
    </script>
</x-dynamic-component>
