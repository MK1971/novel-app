@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
    $isPeterTrullBook = $chapter->book && $chapter->book->name === \App\Models\Book::NAME_PETER_TRULL;
    $manuscriptEditsAllowed = ! $isPeterTrullBook && ! ($suggestionsClosed ?? false);
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('chapters.index') }}" class="w-12 h-12 bg-white border border-amber-200 rounded-2xl flex items-center justify-center text-amber-900 hover:bg-amber-50 transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                        {{ $chapter->headingPrefix() }}: {{ $chapter->displayTitle() }}
                    </h2>
                    <p class="text-amber-800/60 font-bold mt-1">Version {{ $chapter->version }} • Published {{ ($chapter->published_at ?? $chapter->created_at)->timezone(config('app.timezone'))->format('M j, Y') }}</p>
                    @if(! $isPeterTrullBook)
                        <p class="text-sm font-bold text-amber-800/70 mt-1">{{ number_format($chapter->wordCount()) }} words · ~{{ $chapter->estimatedReadingMinutes() }} min read</p>
                    @endif
                    @if(! $isPeterTrullBook && (($prevChapter ?? null) || ($nextChapter ?? null)))
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            @if($prevChapter ?? null)
                                <a href="{{ route('chapters.show', $prevChapter) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-100 text-amber-900 font-extrabold text-sm border border-amber-200 hover:bg-amber-200 transition-colors">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    Previous chapter
                                </a>
                            @endif
                            @if($nextChapter ?? null)
                                <a href="{{ route('chapters.show', $nextChapter) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-100 text-amber-900 font-extrabold text-sm border border-amber-200 hover:bg-amber-200 transition-colors">
                                    Next chapter
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                    @if(! $isPeterTrullBook && ($editingWindowEndsAt ?? null))
                        @php $editingEndLocal = $editingWindowEndsAt->timezone(config('app.timezone')); @endphp
                        @if($chapter->is_locked)
                            @php $mClosed = $chapter->lockedAtForDisplay(); @endphp
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                @if($mClosed)
                                    Paid editing closed on {{ $mClosed->timezone(config('app.timezone'))->format('M j, Y') }}.
                                @else
                                    Paid editing is closed for this chapter.
                                @endif
                            </p>
                        @elseif($chapter->manuscriptPaidEditsOpen())
                            <p class="text-sm font-black text-amber-700 mt-2">
                                Paid edits open until {{ $editingEndLocal->format('M j, Y') }}
                                ({{ $editingWindowEndsAt->diffForHumans() }}).
                            </p>
                        @else
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                Paid editing for this release ended {{ $editingEndLocal->format('M j, Y') }}.
                            </p>
                        @endif
                    @elseif($isPeterTrullBook && ($editingWindowEndsAt ?? null))
                        @php $votingEndLocal = $editingWindowEndsAt->timezone(config('app.timezone')); @endphp
                        @if($chapter->is_locked)
                            @php $vClosed = $chapter->lockedAtForDisplay(); @endphp
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                @if($vClosed)
                                    Voting closed on {{ $vClosed->timezone(config('app.timezone'))->format('M j, Y') }}.
                                @else
                                    Voting is closed for this chapter.
                                @endif
                            </p>
                        @elseif(! $chapter->isPastEditingWindow())
                            <p class="text-sm font-black text-amber-700 mt-2">
                                Voting open until {{ $votingEndLocal->format('M j, Y') }}
                                ({{ $editingWindowEndsAt->diffForHumans() }}).
                            </p>
                        @else
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                Voting period ended {{ $votingEndLocal->format('M j, Y') }}.
                            </p>
                        @endif
                        <p class="text-xs font-bold text-amber-800/60 mt-1">No paid edits on this book. Vote credits come from completed $2 checkouts in The Book With No Name. <a href="{{ route('vote.index') }}" class="underline font-black">Go to the vote page</a>.</p>
                    @elseif($isPeterTrullBook)
                        @if($chapter->is_locked)
                            @php $vClosedNoDeadline = $chapter->lockedAtForDisplay(); @endphp
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                @if($vClosedNoDeadline)
                                    Voting closed on {{ $vClosedNoDeadline->timezone(config('app.timezone'))->format('M j, Y') }}.
                                @else
                                    Voting is closed for this chapter.
                                @endif
                            </p>
                        @endif
                        <p class="text-sm font-bold text-amber-800/70 mt-2 max-w-2xl">Voting only — no paid edits. Vote credits come from $2 checkouts in The Book With No Name. <a href="{{ route('vote.index') }}" class="underline font-black">Vote here</a>.</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-4">
                @if($chapter->is_locked && ! $isPeterTrullBook)
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest bg-white/50 px-4 py-2 rounded-xl border border-amber-100">
                            <span class="text-green-600">{{ $stats->accepted_edits ?? 0 }} Accepted</span>
                            <span class="text-amber-600">{{ $stats->total_edits ?? 0 }} Total</span>
                            <span class="text-red-600">{{ $stats->rejected_edits ?? 0 }} Rejected</span>
                        </div>
                        <div class="px-6 py-2 bg-red-100 rounded-2xl border border-red-200 text-red-700 text-sm font-black uppercase tracking-widest">
                            🔒 Locked
                        </div>
                    </div>
                @elseif(! $isPeterTrullBook)
                    <div class="px-4 py-2 bg-amber-100 rounded-2xl border border-amber-200/50 text-amber-900 text-sm font-bold">
                        <span class="opacity-60 mr-1">Points if accepted (paid only):</span>
                        <span class="text-amber-600">0–2</span>
                        <span class="opacity-60 ml-1 font-extrabold">(2 full · 1 partial)</span>
                    </div>
                @else
                    <div class="px-4 py-2 bg-emerald-100 rounded-2xl border border-emerald-200/50 text-emerald-900 text-sm font-bold">
                        Peter Trull — voting only (no paid edits on this chapter)
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    @auth
        {{-- Sticky under top nav: stays visible while reading (header scrolls away) --}}
        <div
            class="sticky z-30 -mx-8 mb-6 border-b border-amber-200/60 bg-amber-50/95 backdrop-blur-sm px-8 py-3 shadow-sm supports-[backdrop-filter]:bg-amber-50/85"
            style="top: var(--app-shell-nav-h, 4.5rem)"
            aria-live="polite"
        >
            <div class="flex flex-wrap items-baseline justify-between gap-2 mb-2 max-w-3xl">
                <span class="text-[10px] font-black uppercase tracking-widest text-amber-800/50">Reading progress</span>
                <span id="chapter-read-pct-label" class="text-xs font-black text-amber-900 tabular-nums">{{ $progressBarPercent !== null ? $progressBarPercent : 0 }}%</span>
            </div>
            <div class="relative h-2.5 w-full max-w-3xl min-w-0 overflow-hidden rounded-full bg-amber-200/80">
                <div
                    id="chapter-read-inline-progress"
                    class="chapter-read-progress-fill absolute inset-y-0 left-0 z-10 rounded-full bg-amber-600 transition-all duration-300 ease-out"
                    style="width: {{ max(0, min(100, (int) ($progressBarPercent ?? 0))) }}%;"
                ></div>
            </div>
        </div>
    @endauth

    <div class="py-12">
        @php
            $chapterSuggestFabVisible = ! $chapter->is_locked && $manuscriptEditsAllowed && ! $isPeterTrullBook;
        @endphp
        <div class="max-w-7xl mx-auto mb-8 space-y-3">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-2xl border border-green-200 font-bold">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded-2xl border border-red-200 font-bold">{{ session('error') }}</div>
            @endif
            @if (session('warning'))
                <div class="p-4 bg-amber-100 text-amber-900 rounded-2xl border border-amber-200 font-bold">{{ session('warning') }}</div>
            @endif
        </div>
        {{-- Below lg: suggest panel first so it is visible on load without scrolling past the chapter --}}
        <div class="grid lg:grid-cols-3 gap-12">
            {{-- Chapter Content --}}
            <div class="order-2 lg:order-1 lg:col-span-2">
                <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden">
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[15rem] font-black text-amber-500/5 select-none leading-none">
                        {{ $chapter->listSectionDecorativeMarker() }}
                    </div>
                    
                    <div class="relative z-10">
                        @if($chapter->is_locked)
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-[0.04] select-none overflow-hidden">
                                <div class="text-[20rem] font-black rotate-[-35deg] whitespace-nowrap">LOCKED</div>
                            </div>
                        @endif
                        <div id="chapter-content" class="novel-reader-body max-w-none text-amber-950 text-[1.0625rem] sm:text-lg leading-[1.88] tracking-[0.01em] font-serif font-normal text-left {{ $chapter->is_locked ? 'opacity-80 grayscale-[0.2]' : '' }}">
                            @php
                                $paragraphs = explode("\n", $chapter->content);
                            @endphp
                            @foreach($paragraphs as $index => $paragraph)
                                @if(trim($paragraph))
                                    <p class="mb-7 last:mb-0 relative group">
                                        {{ $paragraph }}
                                        @auth
                                            @if(! $chapter->is_locked && $manuscriptEditsAllowed)
                                                <button 
                                                    type="button"
                                                    onclick="openInlineEdit({{ $index }}, '{{ addslashes(trim($paragraph)) }}')"
                                                    class="absolute -right-8 top-0 opacity-0 group-hover:opacity-100 transition-opacity p-2 text-amber-700 hover:text-amber-900 focus-visible:opacity-100 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-500 rounded-lg"
                                                    title="Paragraph edit ($2): suggest a change to this paragraph only. Whole-chapter rewrites use the sidebar form."
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                            @endif
                                        @endauth
                                    </p>
                                @endif
                            @endforeach
                        </div>
                        @if($chapterSuggestFabVisible)
                            <div class="mt-12 pt-10 border-t border-amber-100">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-2xl bg-amber-50/80 border border-amber-100 px-6 py-5">
                                    <p class="text-sm font-bold text-amber-800/90 text-left">
                                        Whole-chapter suggestion or account signup lives in the panel to the side — jump there without scrolling back up.
                                    </p>
                                    <a
                                        href="#chapter-suggest-edit-sidebar"
                                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-2xl bg-amber-900 text-white px-6 py-3.5 text-sm font-extrabold shadow-lg shadow-amber-900/20 hover:bg-black transition-colors whitespace-nowrap"
                                        onclick="event.preventDefault(); window.scrollToSuggestEditSidebar();"
                                    >
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Suggest an edit
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Suggest Edit (sticky on large screens; first on small screens so form is on-screen immediately) --}}
            <div class="order-1 lg:order-2 lg:col-span-1">
                <div id="chapter-suggest-edit-sidebar" class="scroll-mt-28 space-y-8 lg:sticky lg:top-[calc(var(--app-shell-nav-h,4.5rem)+0.375rem+0.75rem)] lg:max-h-[calc(100vh-var(--app-shell-nav-h,4.5rem)-2rem)] lg:overflow-y-auto lg:overscroll-y-contain lg:self-start">
                    @if($isPeterTrullBook)
                        <div class="bg-emerald-50 border-2 border-emerald-100 rounded-[3rem] p-10 text-emerald-900 shadow-sm relative overflow-hidden">
                            <div class="relative z-10">
                                <h3 class="text-2xl font-extrabold mb-4">Voting — not paid edits</h3>
                                <p class="text-emerald-800/80 font-bold mb-8 leading-relaxed">
                                    Peter Trull Solitary Detective uses community votes between two versions. Open the vote hub to pick A or B.
                                </p>
                                <a href="{{ route('vote.index') }}" class="w-full inline-block text-center py-5 bg-emerald-800 text-white text-lg font-extrabold rounded-2xl hover:bg-emerald-950 transition-all shadow-xl">
                                    Go to vote hub
                                </a>
                            </div>
                        </div>
                    @elseif($chapter->is_locked && ! $isPeterTrullBook)
                        <div class="bg-amber-50 border-2 border-amber-100 rounded-[3rem] p-10 text-amber-900 shadow-sm relative overflow-hidden">
                            <div class="relative z-10">
                                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-8">
                                    <span class="text-3xl">🔒</span>
                                </div>
                                    <h3 class="text-2xl font-extrabold mb-6">Chapter Locked for Edits</h3>
                                <p class="text-amber-800/60 text-lg font-bold mb-10 leading-relaxed">
                                    This chapter is now part of the permanent record. Suggestions are closed, but you can still read and enjoy the community-shaped narrative.
                                </p>
                                <a href="{{ route('chapters.index') }}" class="w-full inline-block text-center py-5 bg-amber-900 text-white text-lg font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-1">
                                    Read Next Chapter
                                </a>
                            </div>
                        </div>
                    @elseif(! $manuscriptEditsAllowed && ! $chapter->is_locked)
                        <div class="bg-amber-100 border-2 border-amber-200 rounded-[3rem] p-10 text-amber-900 shadow-sm">
                            <h3 class="text-2xl font-extrabold mb-4">Editing window closed</h3>
                            <p class="text-amber-800/80 font-bold leading-relaxed">The 30-day period for paid suggestions on this chapter has ended.</p>
                        </div>
                    @else
                        @auth
                            @if($pendingPaymentEdit ?? null)
                                <div class="mb-6 p-6 rounded-[2rem] border-2 border-amber-400/60 bg-amber-50 text-amber-900 shadow-sm">
                                    <p class="text-sm font-black uppercase tracking-widest text-amber-800/70 mb-2">Checkout not finished</p>
                                    <p class="text-sm font-bold text-amber-900/80 mb-4 leading-relaxed">
                                        @if($pendingPaymentEdit->type === 'inline_edit')
                                            Your paragraph suggestion is saved. You were not charged until PayPal completes successfully.
                                        @else
                                            Your draft is saved. You were not charged until PayPal completes successfully.
                                        @endif
                                    </p>
                                    <form action="{{ route('payment.checkout') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                        <input type="hidden" name="resume_edit_id" value="{{ $pendingPaymentEdit->id }}">
                                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-amber-900 text-white text-sm font-extrabold rounded-2xl hover:bg-black transition-all shadow-lg">
                                            Resume PayPal checkout ($2)
                                        </button>
                                    </form>
                                </div>
                            @endif
                            <div id="edit-submission-box" class="bg-amber-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-amber-900/20 relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-extrabold mb-6">Suggest an Edit</h3>
                                    <div class="mb-6 p-4 rounded-2xl bg-white/10 border border-white/20 text-amber-100/90 text-sm font-bold leading-relaxed">
                                        <p class="mb-2"><span class="text-white font-extrabold">Two ways to suggest</span> (each uses a <span class="text-white">$2</span> PayPal checkout):</p>
                                        <ul class="list-disc list-inside space-y-1.5 text-amber-100/85">
                                            <li><span class="text-white">Paragraph</span> — Hover a paragraph and tap the pencil to suggest a change to <em>that paragraph only</em>. Moderators use the same scale as full-chapter edits: <span class="text-white">2 pts</span> full accept, <span class="text-white">1 pt</span> partial, <span class="text-white">0</span> if rejected.</li>
                                            <li><span class="text-white">Writing / Phrase</span> (below) — Replace the <em>entire chapter text</em>. Same <span class="text-white">2 / 1 / 0</span> scale: full accept, partial accept, reject.</li>
                                        </ul>
                                    </div>
                                    <p class="text-amber-100/70 text-sm font-bold mb-8 leading-relaxed">
                                        After a successful <strong class="text-white">$2</strong> payment, your suggestion is queued for review. <strong class="text-white">Only paid submissions</strong> earn leaderboard points. Rejected suggestions earn <strong class="text-white">0</strong>. Each completed payment also adds <strong class="text-white">one vote</strong> for <strong class="text-white">Peter Trull Solitary Detective</strong>.
                                    </p>
                                    
                                    <form action="{{ route('payment.checkout') }}" method="POST" class="space-y-6">
                                        @csrf
                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                        
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Edit type (full chapter)</label>
                                            <select name="type" class="w-full bg-white/10 border-white/20 rounded-2xl text-white focus:ring-amber-500 focus:border-amber-500 font-bold p-4 outline-none transition-all" required>
                                                <option value="writing" class="bg-amber-900">Writing Edit — full replacement text</option>
                                                <option value="phrase" class="bg-amber-900">Phrase Edit — full replacement text</option>
                                            </select>
                                            <p class="text-[11px] font-bold text-amber-200/60 mt-2 leading-relaxed">For a single-paragraph change, use the pencil on a paragraph in the chapter, not this dropdown.</p>
                                        </div>

                                        <div>
                                            <label for="edited_text" class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Your Edited Text</label>
                                            <textarea 
                                                name="edited_text" 
                                                id="edited_text" 
                                                rows="10" 
                                                class="w-full bg-white/10 border-white/20 rounded-2xl text-white placeholder-white/30 focus:ring-amber-500 focus:border-amber-500 font-bold p-4"
                                                placeholder="Enter your suggested edit..."
                                                required
                                            >{{ old('edited_text', ($pendingPaymentEdit ?? null) && ($pendingPaymentEdit->type ?? '') !== 'inline_edit' ? $pendingPaymentEdit->edited_text : '') }}</textarea>
                                            @error('edited_text')
                                                <p class="text-red-400 text-xs mt-2 font-bold">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <button type="submit" class="w-full py-5 bg-amber-500 text-black text-lg font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                            Submit & Pay $2
                                        </button>
                                    </form>
                                </div>
                                {{-- Decorative circles --}}
                                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                            </div>
                        @else
                            <div class="bg-amber-500 rounded-[3rem] p-10 text-black shadow-2xl shadow-amber-500/20 relative overflow-hidden">
                                <div class="relative z-10">
                                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mb-8">
                                        <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </div>
                                    <h3 class="text-2xl font-extrabold mb-6">Want to suggest an edit?</h3>
                                    <p class="text-black/60 text-lg font-bold mb-10 leading-relaxed">
                                        Join our community of contributors to shape the story and earn your place on the leaderboard.
                                    </p>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="w-full py-5 bg-black text-white text-lg font-extrabold rounded-2xl hover:bg-amber-900 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Create Account
                                    </button>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="w-full mt-4 py-4 bg-transparent border-2 border-black/10 text-black text-lg font-extrabold rounded-2xl hover:bg-black/5 transition-all">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        @endauth
                    @endif

                    <div class="p-8 bg-white border border-amber-100 rounded-[2.5rem] shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-6">Chapter Stats</h4>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">📖 Total Reads</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_reads ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">✏️ Total Edits</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">✅ Accepted</span>
                                <span class="text-amber-900 font-black text-green-600">{{ $stats->accepted_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">❌ Rejected</span>
                                <span class="text-amber-900 font-black text-red-600">{{ $stats->rejected_edits ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-amber-900/60 font-bold">🗳️ Votes</span>
                                <span class="text-amber-900 font-black">{{ $stats->total_votes ?? 0 }}</span>
                            </div>
                            <div class="pt-6 border-t border-amber-50">
                                <p class="text-xs text-amber-800/40 font-bold leading-relaxed">
                                    @if($chapter->is_locked && $chapter->book->name !== 'Peter Trull Solitary Detective')
                                        This chapter is locked. Final stats are preserved above.
                                    @else
                                        Accepted edits are permanently integrated into the final version of the novel.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Feedback Form --}}
                    <div class="p-8 bg-amber-50 border border-amber-100 rounded-[2.5rem] shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-6">Chapter Feedback</h4>
                        <p class="text-sm text-amber-900/60 font-bold mb-6">Have thoughts on this chapter? Share them with us!</p>
                        
                        <form action="{{ route('feedback.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                            <input type="hidden" name="type" value="chapter">
                            
                            @guest
                                <div>
                                    <input type="email" name="email" placeholder="Your email (optional)" class="w-full bg-white border border-amber-200 rounded-xl px-4 py-2 text-sm text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                                </div>
                            @endguest
                            
                            <div>
                                <textarea name="content" rows="4" placeholder="Your feedback..." class="w-full bg-white border border-amber-200 rounded-xl px-4 py-2 text-sm text-amber-900 focus:ring-2 focus:ring-amber-500 outline-none transition-all" required></textarea>
                            </div>
                            
                            <button type="submit" class="w-full py-3 bg-amber-900 text-white text-sm font-black rounded-xl hover:bg-black transition-all shadow-lg shadow-amber-900/10">
                                Send Feedback
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($chapterSuggestFabVisible)
            <a
                href="#chapter-suggest-edit-sidebar"
                class="lg:hidden fixed bottom-5 right-5 z-40 inline-flex items-center gap-2 rounded-2xl bg-amber-900 text-white px-5 py-3.5 text-sm font-extrabold shadow-xl shadow-amber-900/30 ring-2 ring-white/40 hover:bg-black transition-colors max-w-[calc(100vw-2.5rem)]"
                onclick="event.preventDefault(); window.scrollToSuggestEditSidebar();"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Suggest edit</span>
            </a>
        @endif

        <script>
            window.scrollToSuggestEditSidebar = function () {
                document.getElementById('chapter-suggest-edit-sidebar')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };
        </script>
    </div>

    {{-- Inline Edit Modal --}}
    @auth
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-[3rem] w-full max-w-2xl p-12 shadow-2xl">
            <h3 class="text-2xl font-extrabold text-amber-900 mb-2">Suggest paragraph edit</h3>
            <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">You are editing <strong class="text-amber-900">one paragraph</strong> only (sidebar &ldquo;Writing / Phrase&rdquo; is for the whole chapter). Same <strong class="text-amber-900">$2</strong> checkout. Points match chapter suggestions: up to <strong class="text-amber-900">2</strong> for a full accept, <strong class="text-amber-900">1</strong> for partial, <strong class="text-amber-900">0</strong> if rejected.</p>
            <form id="inline-edit-form" method="POST" action="{{ route('payment.checkout') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
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

                <p class="text-xs font-bold text-amber-800/70 leading-relaxed">Paragraph suggestions use the same <strong class="text-amber-900">$2</strong> PayPal checkout. Points apply only after payment succeeds and a moderator accepts your edit.</p>
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

    <script>
        const modal = document.getElementById('inline-edit-modal');
        const form = document.getElementById('inline-edit-form');

        function openInlineEdit(number, text) {
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
    </script>
    @endauth

    <script>
        (function () {
            const progressFill = document.getElementById('chapter-read-inline-progress');
            const progressLabel = document.getElementById('chapter-read-pct-label');
            const chapterId = {{ $chapter->id }};
            const canTrackProgress = @json(auth()->check());
            let lastSavedScrollTop = null;
            const initialServerPct = {{ (int) ($progressBarPercent ?? 0) }};
            let sessionPeakStripPct = Math.min(100, Math.max(0, initialServerPct));

            function readMetrics() {
                const root = document.scrollingElement || document.documentElement;
                const scrollTop = window.scrollY;
                const maxScroll = Math.max(0, root.scrollHeight - window.innerHeight);
                const extentForSave = Math.max(1, maxScroll);
                let stripPct;
                let positionForSave;
                if (maxScroll <= 0) {
                    stripPct = 100;
                    positionForSave = 1;
                } else {
                    stripPct = (scrollTop / maxScroll) * 100;
                    positionForSave = scrollTop;
                }
                return {
                    scrollTop,
                    maxScroll,
                    extentForSave,
                    stripPct: Math.min(100, Math.max(0, stripPct)),
                    positionForSave,
                };
            }

            function paintStrip(m) {
                if (! progressFill) return;
                const raw = Math.min(100, Math.max(0, m.stripPct));
                sessionPeakStripPct = Math.max(sessionPeakStripPct, raw);
                const w = Math.round(sessionPeakStripPct * 100) / 100;
                progressFill.style.setProperty('width', w + '%', 'important');
                if (w > 0) {
                    progressFill.style.setProperty('min-width', '4px', 'important');
                } else {
                    progressFill.style.removeProperty('min-width');
                }
                if (progressLabel) {
                    progressLabel.textContent = Math.round(sessionPeakStripPct) + '%';
                }
            }

            function saveProgress(m, options) {
                if (! canTrackProgress) return;
                const tokenEl = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenEl ? tokenEl.getAttribute('content') : '';
                if (! csrf) return;
                const opts = options || {};
                const payload = JSON.stringify({
                    scroll_position: m.positionForSave,
                    scroll_extent_max: m.extentForSave,
                });
                if (opts.keepalive) {
                    fetch(`/chapters/${chapterId}/track-progress`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: payload,
                        keepalive: true,
                    }).catch(function () {});
                    return;
                }
                fetch(`/chapters/${chapterId}/track-progress`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    body: payload,
                }).catch(function () {});
            }

            function flushProgress() {
                if (! canTrackProgress) return;
                const m = readMetrics();
                saveProgress(m, { keepalive: true });
            }

            const savedProgress = {{ (int) ($progress ?? 0) }};
            const progressExtentMax = {{ (int) ($progressExtentMax ?? 0) }};

            function applySavedScroll() {
                if (savedProgress <= 0) {
                    return;
                }
                const m = readMetrics();
                if (progressExtentMax === 1000) {
                    const pct = Math.min(100, savedProgress / 10);
                    window.scrollTo(0, (pct / 100) * m.maxScroll);
                } else {
                    window.scrollTo(0, savedProgress);
                }
            }

            function syncAfterLayout() {
                const m = readMetrics();
                paintStrip(m);
                saveProgress(m);
                lastSavedScrollTop = m.maxScroll <= 0 ? 0 : m.scrollTop;
            }

            function scheduleInitialSync() {
                function run() {
                    requestAnimationFrame(function () {
                        applySavedScroll();
                        requestAnimationFrame(syncAfterLayout);
                    });
                }
                if (document.readyState === 'complete') {
                    run();
                } else {
                    window.addEventListener('load', run);
                }
            }
            scheduleInitialSync();

            window.addEventListener('scroll', function () {
                const m = readMetrics();
                paintStrip(m);
                if (! canTrackProgress || lastSavedScrollTop === null) return;
                const delta = Math.abs(m.scrollTop - lastSavedScrollTop);
                if (delta >= 48 || m.stripPct >= 98) {
                    saveProgress(m);
                    lastSavedScrollTop = m.maxScroll <= 0 ? 0 : m.scrollTop;
                }
            }, { passive: true });

            document.addEventListener('visibilitychange', function () {
                if (document.visibilityState === 'hidden') {
                    flushProgress();
                }
            });
            window.addEventListener('pagehide', flushProgress);
        })();
    </script>
</x-dynamic-component>
