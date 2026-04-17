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
                        {{ $chapter->readerHeadingLine() }}
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
                                <a href="{{ route('chapters.show', $nextChapter) }}#chapter-suggest-edit-sidebar" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-100 text-amber-900 font-extrabold text-sm border border-amber-200 hover:bg-amber-200 transition-colors">
                                    Next chapter
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                    @if(! $isPeterTrullBook && $chapter->isPilotManuscriptChapter())
                        @if($chapter->is_locked)
                            @php $mClosed = $chapter->lockedAtForDisplay(); @endphp
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                @if($mClosed)
                                    Pilot round closed on {{ $mClosed->timezone(config('app.timezone'))->format('M j, Y') }}.
                                @else
                                    Paid editing is closed for this chapter.
                                @endif
                            </p>
                        @elseif($chapter->manuscriptPaidEditsOpen())
                            <p class="text-sm font-black text-amber-700 mt-2">
                                <span class="font-black text-amber-900">Pilot chapter</span> — paid edits until
                                <strong>{{ config('tbwnn.pilot.close_after_accepted_edits', 50) }}</strong> accepted suggestions
                                ({{ $chapter->pilotAcceptedEditsTotal() }} / {{ config('tbwnn.pilot.close_after_accepted_edits', 50) }}).
                            </p>
                        @else
                            <p class="text-sm font-bold text-amber-800/80 mt-2">Pilot round complete — editing closed for this release.</p>
                        @endif
                    @elseif(! $isPeterTrullBook && ($editingWindowEndsAt ?? null))
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
                    @elseif($isPeterTrullBook && $chapter->isPilotPeterTrullChapter())
                        @php
                            $pilotVoteCap = max(1, (int) config('peter_trull.pilot.close_after_votes', 50));
                            $pilotVotes = $chapter->pilotPeterTrullVotesTotal();
                        @endphp
                        @if($chapter->is_locked)
                            @php $vClosedPilot = $chapter->lockedAtForDisplay(); @endphp
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                @if($vClosedPilot)
                                    Pilot voting closed on {{ $vClosedPilot->timezone(config('app.timezone'))->format('M j, Y') }}.
                                @else
                                    Pilot voting is closed for this chapter.
                                @endif
                            </p>
                        @elseif(! $chapter->isPastEditingWindow())
                            <p class="text-sm font-black text-amber-700 mt-2">
                                Pilot voting closes at {{ $pilotVoteCap }} total votes (A+B). Current: {{ $pilotVotes }}/{{ $pilotVoteCap }}.
                            </p>
                        @else
                            <p class="text-sm font-bold text-amber-800/80 mt-2">
                                Pilot voting cap reached ({{ $pilotVotes }}/{{ $pilotVoteCap }} votes).
                            </p>
                        @endif
                        <p class="text-xs font-bold text-amber-800/60 mt-1">No contribution submissions on this book. Vote credits come from completed $2 contributions in The Book With No Name. <a href="{{ route('vote.index') }}" class="underline font-black">Go to the vote page</a>.</p>
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
                        <p class="text-xs font-bold text-amber-800/60 mt-1">No contribution submissions on this book. Vote credits come from completed $2 contributions in The Book With No Name. <a href="{{ route('vote.index') }}" class="underline font-black">Go to the vote page</a>.</p>
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
                        <p class="text-sm font-bold text-amber-800/70 mt-2 max-w-2xl">Voting only — no contribution submissions. Vote credits come from completed $2 contributions in The Book With No Name. <a href="{{ route('vote.index') }}" class="underline font-black">Vote here</a>.</p>
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

    @if(filled($chapter->reader_blurb))
        <div class="max-w-5xl mx-auto px-4 sm:px-8 pt-4 pb-2">
            <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-white px-5 py-4 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/60 mb-2">
                    {{ $isPeterTrullBook ? 'Peter Trull — note' : 'About this chapter' }}
                </p>
                <div class="text-sm md:text-base font-bold text-amber-950/90 leading-relaxed whitespace-pre-wrap">{{ $chapter->reader_blurb }}</div>
            </div>
        </div>
    @endif

    @if($chapterPrizeLeader)
        <div class="max-w-5xl mx-auto px-4 sm:px-8 pt-2 pb-2">
            <div class="rounded-2xl border border-amber-200 bg-white px-5 py-4 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/60 mb-2">Chapter sponsor note (current leader)</p>
                <p class="text-sm md:text-base font-bold text-amber-950/90 leading-relaxed">
                    This chapter's highest-impact contributor is
                    @if ($chapterPrizeLeader->public_profile_enabled && filled($chapterPrizeLeader->public_slug))
                        <a href="{{ route('profile.public', ['slug' => $chapterPrizeLeader->public_slug]) }}" class="underline decoration-amber-300 hover:text-amber-700">
                            {{ $chapterPrizeLeader->name }}
                        </a>
                    @else
                        <strong>{{ $chapterPrizeLeader->name }}</strong>
                    @endif
                    with {{ number_format((int) ($chapterPrizeLeader->accepted_total ?? 0)) }} accepted replacements in this chapter.
                </p>
            </div>
        </div>
    @endif

    @auth
        {{-- Sticky under top nav: stays visible while reading (header scrolls away) --}}
        <div
            class="novel-chapter-read-progress sticky z-30 -mx-8 mb-6 border-b border-amber-200/60 bg-amber-50/95 backdrop-blur-sm px-8 py-3 shadow-sm supports-[backdrop-filter]:bg-amber-50/85"
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
            $queuedCount = (int) (($queuedPendingEdits ?? collect())->count());
            $queuedTotalDisplay = number_format($queuedCount * 2, 2);
            $payNowTotalDisplay = number_format(($queuedCount + 1) * 2, 2);
            $submittedSuggestions = auth()->check() ? (int) auth()->user()->editSuggestionsSubmittedCount() : 0;
            $acceptedSuggestions = auth()->check() ? (int) auth()->user()->acceptedChapterAndParagraphEditCount() : 0;
            $showContributionMission = auth()->check() && ! $isPeterTrullBook && $submittedSuggestions < 3;
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
        <div
            id="novel-chapter-reader"
            class="novel-chapter-reader-root"
            data-reader-theme="cream"
            x-data="novelChapterReader"
        >
            <div
                class="novel-reader-toolbar novel-reader-focus-bar -mx-8 mb-6 flex flex-wrap items-center gap-3 border-b border-amber-200/60 dark:border-stone-700 bg-amber-50/90 dark:bg-stone-900/80 px-8 py-3 text-xs font-bold text-amber-900 dark:text-amber-100 shadow-sm backdrop-blur-sm supports-[backdrop-filter]:bg-amber-50/80 dark:supports-[backdrop-filter]:bg-stone-900/75"
            >
                <span class="font-black uppercase tracking-widest text-amber-800/50 dark:text-amber-200/55">{{ __('Reader') }}</span>
                <label class="sr-only" for="novel-reader-theme-select">{{ __('Reading theme') }}</label>
                <select
                    id="novel-reader-theme-select"
                    x-model="theme"
                    class="rounded-xl border-amber-200 dark:border-stone-600 bg-white dark:bg-stone-800 text-xs font-bold text-amber-900 dark:text-amber-50 py-2 pl-3 pr-8 max-w-[11rem] sm:max-w-none"
                >
                    <option value="cream">{{ __('Cream (default)') }}</option>
                    <option value="paper">{{ __('Paper') }}</option>
                    <option value="sepia">{{ __('Sepia') }}</option>
                    <option value="night">{{ __('Night (reader)') }}</option>
                </select>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-amber-300 dark:border-stone-600 bg-white dark:bg-stone-800 px-4 py-2 text-xs font-black uppercase tracking-wider text-amber-900 dark:text-amber-50 hover:bg-amber-100 dark:hover:bg-stone-700"
                    @click="toggleFocus()"
                >
                    <span x-show="!focus">{{ __('Focus mode') }}</span>
                    <span x-show="focus" x-cloak>{{ __('Exit focus') }}</span>
                </button>
            </div>
        {{-- Below lg: suggest panel first so it is visible on load without scrolling past the chapter --}}
        <div class="grid gap-12" :class="focus ? 'lg:grid-cols-1' : 'lg:grid-cols-3'">
            {{-- Chapter Content --}}
            <div class="order-2 lg:order-1" :class="focus ? 'lg:col-span-1 max-w-3xl mx-auto w-full' : 'lg:col-span-2'">
                <div class="novel-reader-surface bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden">
                    {{-- Decorative background number --}}
                    <div class="novel-read-decor-num absolute -top-10 -right-10 text-[15rem] font-black select-none leading-none">
                        {{ $chapter->listSectionDecorativeMarker() }}
                    </div>
                    
                    <div class="relative z-10">
                        @include('chapters.partials.tbw-version-nav', [
                            'chapter' => $chapter,
                            'tbwArchiveSiblings' => $tbwArchiveSiblings ?? collect(),
                            'tbwLiveForNav' => $tbwLiveForNav ?? null,
                            'tbwOtherArchiveSiblings' => $tbwOtherArchiveSiblings ?? collect(),
                        ])
                        @if($chapter->is_locked)
                            <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-[0.04] select-none overflow-hidden">
                                <div class="text-[20rem] font-black rotate-[-35deg] whitespace-nowrap">LOCKED</div>
                            </div>
                        @endif
                        <div id="chapter-content" class="novel-reader-body max-w-none text-[1.0625rem] sm:text-lg leading-[1.88] tracking-[0.01em] font-serif font-normal text-left {{ $chapter->is_locked ? 'opacity-80 grayscale-[0.2]' : '' }}">
                            @auth
                                @if(! $chapter->is_locked && $manuscriptEditsAllowed)
                                    <p class="mb-5 md:hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-xs font-bold text-amber-900/80">
                                        Tap the pencil beside a paragraph to replace that line with your version.
                                    </p>
                                @endif
                            @endauth
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
                                                    class="absolute -right-8 top-0 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity p-2 text-amber-700 hover:text-amber-900 focus-visible:opacity-100 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-500 rounded-lg bg-amber-50/80 md:bg-transparent"
                                                    title="Replace this line ($2 contribution): submit your version for this paragraph only. Whole-chapter replacement uses the sidebar form."
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
                                        Replace a line
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Suggest Edit (sticky on large screens; first on small screens so form is on-screen immediately) --}}
            <div class="order-1 lg:order-2 lg:col-span-1" x-show="!focus">
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
                                @if($nextChapter ?? null)
                                    <a href="{{ route('chapters.show', $nextChapter) }}" class="w-full inline-block text-center py-5 bg-amber-900 text-white text-lg font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-1">
                                        Read next chapter
                                    </a>
                                @else
                                    <a href="{{ route('chapters.index') }}" class="w-full inline-block text-center py-5 bg-amber-900 text-white text-lg font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-1">
                                        Browse chapters
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif(! $manuscriptEditsAllowed && ! $chapter->is_locked)
                        <div class="bg-amber-100 border-2 border-amber-200 rounded-[3rem] p-10 text-amber-900 shadow-sm">
                            <h3 class="text-2xl font-extrabold mb-4">Editing window closed</h3>
                            <p class="text-amber-800/80 font-bold leading-relaxed">The 30-day period for paid suggestions on this chapter has ended.</p>
                        </div>
                    @else
                        @auth
                            @if($showContributionMission)
                                <div class="mb-6 rounded-[2rem] border border-amber-300/70 bg-gradient-to-br from-amber-50 to-white px-6 py-5 shadow-sm">
                                    <p class="text-[11px] font-black uppercase tracking-widest text-amber-800/60">Starter mission</p>
                                    <h3 class="mt-2 text-lg font-extrabold text-amber-950">Build momentum with 3 submissions</h3>
                                    <p class="mt-2 text-sm font-bold text-amber-900/75">Progress this session: <strong>{{ $submittedSuggestions }}/3 submitted</strong>, <strong>{{ $acceptedSuggestions }} accepted</strong>. Refine wording before checkout, then submit when ready.</p>
                                    <div class="mt-4 h-2.5 w-full rounded-full bg-amber-100">
                                        <div class="h-full rounded-full bg-amber-600 transition-all" style="width: {{ min(100, (int) round(($submittedSuggestions / 3) * 100)) }}%"></div>
                                    </div>
                                </div>
                            @endif
                            @if(($queuedPendingEdits ?? collect())->isNotEmpty())
                                <div class="mb-6 p-6 rounded-[2rem] border-2 border-amber-400/60 bg-amber-50 text-amber-900 shadow-sm">
                                    <p class="text-sm font-black uppercase tracking-widest text-amber-800/70 mb-2">Queued for checkout</p>
                                    <p class="text-sm font-bold text-amber-900/80 mb-4 leading-relaxed">
                                        You currently have <strong>{{ $queuedPendingEdits->count() }}</strong> queued suggestion(s). Total at checkout: <strong>${{ number_format($queuedPendingEdits->count() * 2, 2) }}</strong>.
                                    </p>
                                    <ul class="mb-4 max-h-64 overflow-y-auto pr-2 text-xs space-y-2 text-amber-900/80">
                                        @foreach($queuedPendingEdits as $queued)
                                            <li class="flex items-center justify-between gap-2">
                                                <span>
                                                    • {{ $queued->chapter?->readerHeadingLine() ?? 'Chapter removed' }} — {{ $queued->type === 'inline_edit' ? 'Paragraph edit' : ucfirst($queued->type).' edit' }}
                                                </span>
                                                <form method="POST" action="{{ route('payment.queue.remove', $queued) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 rounded-lg border border-amber-300 bg-white text-[10px] font-black uppercase text-amber-900 hover:bg-amber-100">
                                                        Remove
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <form id="queued-checkout-form" action="{{ route('payment.checkout') }}" method="POST" class="inline-flex flex-wrap gap-3">
                                        @csrf
                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                        <input type="hidden" name="resume_edit_id" value="{{ $queuedPendingEdits->first()->id }}">
                                        <button type="submit" data-checkout-intent="1" data-intent-kind="queue" class="inline-flex items-center px-6 py-3 bg-amber-900 text-white text-sm font-extrabold rounded-2xl hover:bg-black transition-all shadow-lg">
                                            Submit queued edits & Pay (${{ $queuedTotalDisplay }})
                                        </button>
                                    </form>
                                </div>
                            @endif
                            <div id="edit-submission-box" class="bg-amber-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-amber-900/20 relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-2xl font-extrabold mb-6">Submit your version of the text</h3>
                                    <div class="mb-6 p-4 rounded-2xl bg-white/10 border border-white/20 text-amber-100/90 text-sm font-bold leading-relaxed">
                                        <p class="mb-2"><span class="text-white font-extrabold">Two ways to challenge the text</span> (each uses a <span class="text-white">$2</span> contribution checkout):</p>
                                        <ul class="list-disc list-inside space-y-1.5 text-amber-100/85">
                                            <li><span class="text-white">Paragraph</span> — Hover a paragraph and tap the pencil to replace <em>that line only</em>. Moderators use the same scale as full-chapter edits: <span class="text-white">2 pts</span> full accept, <span class="text-white">1 pt</span> partial, <span class="text-white">0</span> if rejected.</li>
                                            <li><span class="text-white">Writing / Phrase</span> (below) — Replace the <em>entire chapter text</em>. Same <span class="text-white">2 / 1 / 0</span> scale: full accept, partial accept, reject.</li>
                                        </ul>
                                    </div>
                                    <p class="text-amber-100/70 text-sm font-bold mb-8 leading-relaxed">
                                        After a successful <strong class="text-white">$2</strong> contribution, your submission is queued for moderation review (acceptance is not guaranteed). Most submissions are not accepted. Only completed contribution submissions can earn leaderboard points. Rejected suggestions earn <strong class="text-white">0</strong>. Each completed contribution also adds <strong class="text-white">one vote credit</strong> for <strong class="text-white">Peter Trull Solitary Detective</strong>.
                                    </p>
                                    
                                    <form id="chapter-full-edit-form" action="{{ route('payment.checkout') }}" method="POST" class="space-y-6">
                                        @csrf
                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                        
                                        <div>
                                            <label class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Replacement type (full chapter)</label>
                                            <select name="type" class="w-full bg-white/10 border-white/20 rounded-2xl text-white focus:ring-amber-500 focus:border-amber-500 font-bold p-4 outline-none transition-all" required>
                                                <option value="writing" class="bg-amber-900">Writing Edit — full replacement text</option>
                                                <option value="phrase" class="bg-amber-900">Phrase Edit — full replacement text</option>
                                            </select>
                                            <p class="text-[11px] font-bold text-amber-200/60 mt-2 leading-relaxed">For a single-paragraph change, use the pencil on a paragraph in the chapter, not this dropdown.</p>
                                        </div>

                                        <div>
                                            <label for="edited_text" class="block text-xs font-black uppercase tracking-widest text-amber-400 mb-3">Write your version</label>
                                            <textarea 
                                                name="edited_text" 
                                                id="edited_text" 
                                                rows="10" 
                                                class="w-full bg-white/10 border-white/20 rounded-2xl text-white placeholder-white/30 focus:ring-amber-500 focus:border-amber-500 font-bold p-4"
                                                placeholder="Enter your suggested edit..."
                                                required
                                            >{{ old('edited_text', '') }}</textarea>
                                            <p id="full-edit-ownership-note" class="hidden mt-3 text-xs font-black text-amber-100">Your draft is ready. Submit when you want this version reviewed.</p>
                                            @error('edited_text')
                                                <p class="text-red-400 text-xs mt-2 font-bold">{{ $message }}</p>
                                            @enderror
                                            <div class="mt-4 flex flex-wrap gap-3">
                                                <button type="button" id="novel-btn-preview-edit-diff" class="px-4 py-2 rounded-xl bg-white/15 border border-white/30 text-white text-sm font-black hover:bg-white/20 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-300">
                                                    Preview changes vs published
                                                </button>
                                                <button type="button" id="novel-btn-clear-edit-draft" class="px-4 py-2 rounded-xl bg-transparent border border-white/20 text-amber-200 text-sm font-bold hover:bg-white/10">
                                                    Discard local draft
                                                </button>
                                            </div>
                                            <div id="novel-edit-diff-preview" class="hidden mt-4 rounded-2xl border-2 border-white/20 bg-black/30 p-4 text-left flex flex-col gap-3" aria-live="polite"></div>
                                        </div>

                                        <div class="rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                                            <input type="hidden" name="show_in_public_feed" value="0">
                                            <label class="inline-flex items-start gap-3 text-sm text-amber-100">
                                                <input
                                                    type="checkbox"
                                                    name="show_in_public_feed"
                                                    value="1"
                                                    class="mt-0.5 rounded border-white/40 bg-white/10 text-amber-500 focus:ring-amber-400"
                                                    {{ old('show_in_public_feed', '1') ? 'checked' : '' }}
                                                >
                                                <span>
                                                    <span class="font-extrabold text-white">Show this suggestion in the public edits feed</span>
                                                    <span class="block text-xs text-amber-100/80 mt-1">You can change this later from My Profile → My submissions.</span>
                                                </span>
                                            </label>
                                        </div>
                                        
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <button type="submit" data-checkout-intent="1" data-intent-kind="full" class="w-full py-5 bg-amber-500 text-black text-lg font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                                Submit for review - $2
                                            </button>
                                            <button type="submit" name="queue_only" value="1" class="w-full py-5 bg-white/15 border border-white/30 text-white text-lg font-extrabold rounded-2xl hover:bg-white/20 transition-all">
                                                Add another edit
                                            </button>
                                        </div>
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
                                    <h3 class="text-2xl font-extrabold mb-6">Want to challenge the manuscript?</h3>
                                    <p class="text-black/60 text-lg font-bold mb-10 leading-relaxed">
                                        Read first, then submit your version when you are ready. Accepted replacements improve your rank.
                                    </p>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="w-full py-5 bg-black text-white text-lg font-extrabold rounded-2xl hover:bg-amber-900 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Become a Contributor
                                    </button>
                                    <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="w-full mt-4 py-4 bg-transparent border-2 border-black/10 text-black text-lg font-extrabold rounded-2xl hover:bg-black/5 transition-all">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        @endauth
                    @endif

                    <div class="p-8 bg-white border border-amber-100 rounded-[2.5rem] shadow-sm">
                        <h4 class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-6">Chapter stats</h4>
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
        </div>

        @if($chapterSuggestFabVisible)
            <a
                href="#chapter-suggest-edit-sidebar"
                class="novel-reader-focus-hide lg:hidden fixed bottom-5 right-5 z-40 inline-flex items-center gap-2 rounded-2xl bg-amber-900 text-white px-5 py-3.5 text-sm font-extrabold shadow-xl shadow-amber-900/30 ring-2 ring-white/40 hover:bg-black transition-colors max-w-[calc(100vw-2.5rem)]"
                onclick="event.preventDefault(); window.scrollToSuggestEditSidebar();"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Replace a line</span>
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
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-start justify-center p-3 sm:p-6 overflow-y-auto overscroll-contain">
        <div class="relative my-2 sm:my-4 bg-white rounded-[2rem] sm:rounded-[3rem] w-full max-w-2xl p-6 sm:p-12 shadow-2xl max-h-[calc(100vh-1.5rem)] sm:max-h-[calc(100vh-3rem)] overflow-y-auto overscroll-contain">
            <div class="mb-3 flex justify-end">
                <button
                    type="button"
                    onclick="closeInlineEdit()"
                    class="rounded-xl bg-amber-100 px-3 py-2 text-xs font-black uppercase tracking-wider text-amber-900 hover:bg-amber-200 transition-colors"
                    aria-label="Close paragraph edit dialog"
                >
                    Close
                </button>
            </div>
            <h3 class="text-2xl font-extrabold text-amber-900 mb-2">Replace this line</h3>
            <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">You are replacing <strong class="text-amber-900">one paragraph</strong> only (sidebar &ldquo;Writing / Phrase&rdquo; is for full chapter replacement). Free to read; payment applies only when you submit. Moderators review every submission (acceptance is not guaranteed).</p>
            <form id="inline-edit-form" method="POST" action="{{ route('payment.checkout') }}" class="space-y-8">
                @csrf
                <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                <input type="hidden" name="type" value="inline_edit">
                <input type="hidden" name="show_in_public_feed" value="0">
                <input type="hidden" id="paragraph-number" name="paragraph_number">
                <input type="hidden" id="original-text-input" name="original_text">
                
                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Original line</label>
                    <div id="original-text-display" class="p-6 bg-amber-50 rounded-2xl text-amber-900/60 italic text-sm border border-amber-100"></div>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Write your version</label>
                    <textarea id="suggested-text" name="suggested_text" rows="4" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required></textarea>
                    <p id="inline-ownership-note" class="hidden mt-3 text-xs font-black text-amber-900">Your version is ready to replace the original line.</p>
                </div>

                <div>
                    <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-4">Reason (Optional)</label>
                    <input type="text" id="edit-reason" name="reason" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all">
                </div>

                <label class="inline-flex items-start gap-3 text-sm text-amber-900/90">
                    <input
                        type="checkbox"
                        name="show_in_public_feed"
                        value="1"
                        checked
                        class="mt-0.5 rounded border-amber-300 bg-white text-amber-700 focus:ring-amber-400"
                    >
                    <span>
                        <span class="font-extrabold text-amber-900">Show this paragraph edit in the public feed</span>
                        <span class="block text-xs text-amber-800/70 mt-1">You can change this later from My Profile → My submissions.</span>
                    </span>
                </label>

                <p class="text-xs font-bold text-amber-800/70 leading-relaxed">Only accepted replacements are integrated into the manuscript. Each completed contribution also grants one vote credit in Peter Trull Solitary Detective.</p>
                <div class="flex flex-wrap items-center gap-4 pt-4">
                    <button type="submit" data-checkout-intent="1" data-intent-kind="inline" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                        Submit your version
                    </button>
                    <button type="submit" name="queue_only" value="1" class="px-10 py-4 bg-amber-100 border-2 border-amber-200 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-200 transition-all">
                        Add another edit
                    </button>
                    <button type="button" onclick="discardInlineParagraphDraft()" class="px-10 py-4 bg-white border-2 border-amber-200 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-50 transition-all">
                        Discard saved draft
                    </button>
                    <button type="button" onclick="closeInlineEdit()" class="px-10 py-4 bg-amber-50 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-100 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="checkout-intent-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[9999] hidden items-center justify-center p-4 pointer-events-auto">
        <div id="checkout-intent-panel" class="relative z-[10000] w-full max-w-2xl max-h-[calc(100vh-2rem)] overflow-y-auto rounded-[2rem] border border-amber-200 bg-white p-6 sm:p-8 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-700/70">Finalize contribution</p>
                    <h4 id="checkout-intent-title" class="mt-1 text-2xl font-extrabold text-amber-900">Submit your version</h4>
                </div>
                <button type="button" id="checkout-intent-close" class="rounded-xl bg-amber-100 px-3 py-2 text-sm font-black text-amber-900 hover:bg-amber-200">Close</button>
            </div>
            <p class="mt-4 text-sm font-bold leading-relaxed text-amber-900/80">
                You are about to start a <strong class="text-amber-950">$2 contribution checkout</strong>. Your version will compete with other submissions, and only selected versions become part of the manuscript.
            </p>
            <ul class="mt-4 space-y-2 rounded-2xl border border-amber-100 bg-amber-50/70 p-4 text-sm font-bold text-amber-900/85">
                <li>• Every submission is reviewed individually; acceptance is not guaranteed.</li>
                <li>• Only accepted replacements earn leaderboard points and become canon text.</li>
                <li>• Every completed contribution adds one Peter Trull vote credit.</li>
            </ul>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-amber-100 bg-amber-50/50 p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-amber-800/70">Published text</p>
                    <p id="checkout-intent-original" class="mt-2 text-sm font-bold text-amber-900/80"></p>
                </div>
                <div class="rounded-2xl border border-amber-300 bg-amber-100/60 p-4">
                    <p class="text-xs font-black uppercase tracking-widest text-amber-800/70">Your version</p>
                    <p id="checkout-intent-suggested" class="mt-2 text-sm font-bold text-amber-900"></p>
                </div>
            </div>
            <p id="checkout-intent-hesitation" class="mt-4 text-xs font-bold text-amber-800/80"></p>
            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" id="checkout-intent-confirm" class="inline-flex items-center rounded-2xl bg-amber-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black">
                    Submit for review - ${{ $payNowTotalDisplay }}
                </button>
                <button type="button" id="checkout-intent-cancel" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white px-6 py-3 text-sm font-extrabold text-amber-900 hover:bg-amber-50">
                    Continue with this edit
                </button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('inline-edit-modal');
        const inlineEditPanel = modal ? modal.querySelector(':scope > div') : null;
        const form = document.getElementById('inline-edit-form');
        const chapterIdForInline = {{ $chapter->id }};
        const chapterOriginalForCheckout = @json((string) $chapter->content);
        const fullEditForm = document.getElementById('chapter-full-edit-form');
        const queueCheckoutForm = document.getElementById('queued-checkout-form');
        const fullEditTextarea = document.getElementById('edited_text');
        const fullEditOwnership = document.getElementById('full-edit-ownership-note');
        const checkoutIntentModal = document.getElementById('checkout-intent-modal');
        const checkoutIntentTitle = document.getElementById('checkout-intent-title');
        const checkoutIntentOriginal = document.getElementById('checkout-intent-original');
        const checkoutIntentSuggested = document.getElementById('checkout-intent-suggested');
        const checkoutIntentHesitation = document.getElementById('checkout-intent-hesitation');
        const checkoutIntentPanel = document.getElementById('checkout-intent-panel');
        const checkoutIntentConfirm = document.getElementById('checkout-intent-confirm');
        const checkoutIntentCancel = document.getElementById('checkout-intent-cancel');
        const checkoutIntentClose = document.getElementById('checkout-intent-close');
        const queueCountForCheckout = {{ (int) $queuedCount }};
        const queueTotalForCheckout = @json($queuedTotalDisplay);
        let pendingCheckoutSubmit = null;
        let pendingCheckoutKind = 'unknown';
        let inlineHiddenByIntent = false;
        let lastIntentSubmitter = null;
        let inlineDraftTimer;
        let intentInteractionGuardEnabled = false;

        if (checkoutIntentModal && checkoutIntentModal.parentElement !== document.body) {
            document.body.appendChild(checkoutIntentModal);
        }

        function isCheckoutIntentOpen() {
            return !!(checkoutIntentModal && ! checkoutIntentModal.classList.contains('hidden'));
        }

        function trackChapterEvent(eventName, context) {
            if (! eventName || typeof window.trackLandingEvent !== 'function') {
                return;
            }
            window.trackLandingEvent(eventName, Object.assign({
                path: window.location.pathname,
                chapter_id: chapterIdForInline,
            }, context || {}));
        }

        function interactionGuard(event) {
            if (! isCheckoutIntentOpen()) return;
            if (! checkoutIntentPanel) return;
            if (checkoutIntentPanel.contains(event.target)) return;
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation?.();
        }

        function enableIntentInteractionGuard() {
            if (intentInteractionGuardEnabled) return;
            intentInteractionGuardEnabled = true;
            document.addEventListener('pointerdown', interactionGuard, true);
            document.addEventListener('click', interactionGuard, true);
            document.addEventListener('mousedown', interactionGuard, true);
            document.addEventListener('touchstart', interactionGuard, true);
        }

        function disableIntentInteractionGuard() {
            if (! intentInteractionGuardEnabled) return;
            intentInteractionGuardEnabled = false;
            document.removeEventListener('pointerdown', interactionGuard, true);
            document.removeEventListener('click', interactionGuard, true);
            document.removeEventListener('mousedown', interactionGuard, true);
            document.removeEventListener('touchstart', interactionGuard, true);
        }

        function openInlineEdit(number, text) {
            document.getElementById('paragraph-number').value = number;
            document.getElementById('original-text-input').value = text;
            document.getElementById('original-text-display').innerText = text;
            const st = document.getElementById('suggested-text');
            const ikey = 'novel-inline-draft-' + chapterIdForInline + '-' + number;
            let saved = null;
            try { saved = localStorage.getItem(ikey); } catch (e) {}
            st.value = (saved !== null && saved.length > 0) ? saved : text;
            const ownership = document.getElementById('inline-ownership-note');
            if (ownership) {
                if ((st.value || '').trim().length > 0) {
                    ownership.classList.remove('hidden');
                } else {
                    ownership.classList.add('hidden');
                }
            }
            modal.classList.remove('hidden');
            modal.scrollTop = 0;
            if (inlineEditPanel) {
                inlineEditPanel.scrollTop = 0;
            }
            document.body.style.overflow = 'hidden';
            trackChapterEvent('inline_edit_modal_open', { source: 'chapter_show' });
        }

        function closeInlineEdit() {
            modal.classList.add('hidden');
            if (! checkoutIntentModal || checkoutIntentModal.classList.contains('hidden')) {
                document.body.style.overflow = 'auto';
            }
        }

        function summarizeForPreview(text) {
            const normalized = String(text || '').replace(/\s+/g, ' ').trim();
            if (normalized.length <= 160) {
                return normalized || '(empty)';
            }
            return normalized.slice(0, 160) + '...';
        }

        function openCheckoutIntent(payload) {
            if (! checkoutIntentModal) return;
            if (payload.hideInlineModal && modal && ! modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
                modal.style.pointerEvents = 'none';
                inlineHiddenByIntent = true;
            }
            checkoutIntentModal.style.position = 'fixed';
            checkoutIntentModal.style.inset = '0';
            checkoutIntentModal.style.zIndex = '2147483646';
            checkoutIntentModal.style.pointerEvents = 'auto';
            if (checkoutIntentPanel) {
                checkoutIntentPanel.style.position = 'relative';
                checkoutIntentPanel.style.zIndex = '2147483647';
                checkoutIntentPanel.style.pointerEvents = 'auto';
            }
            checkoutIntentTitle.textContent = payload.title;
            checkoutIntentOriginal.textContent = summarizeForPreview(payload.originalText);
            checkoutIntentSuggested.textContent = summarizeForPreview(payload.suggestedText);
            checkoutIntentHesitation.textContent = payload.hesitationCopy;
            pendingCheckoutSubmit = payload.onConfirm;
            pendingCheckoutKind = payload.analyticsKind || 'unknown';
            checkoutIntentModal.classList.remove('hidden');
            checkoutIntentModal.classList.add('flex');
            enableIntentInteractionGuard();
            document.body.style.overflow = 'hidden';
            trackChapterEvent('checkout_intent_open', { kind: pendingCheckoutKind });
        }

        function closeCheckoutIntent(reopenInlineModal) {
            if (! checkoutIntentModal) return;
            const shouldReopenInline = reopenInlineModal !== false;
            checkoutIntentModal.classList.add('hidden');
            checkoutIntentModal.classList.remove('flex');
            pendingCheckoutSubmit = null;
            pendingCheckoutKind = 'unknown';
            disableIntentInteractionGuard();
            if (inlineHiddenByIntent && modal) {
                if (shouldReopenInline) {
                    modal.classList.remove('hidden');
                } else {
                    modal.classList.add('hidden');
                }
                modal.style.pointerEvents = '';
                inlineHiddenByIntent = false;
            }
            if (modal && ! modal.classList.contains('hidden')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = 'auto';
            }
        }

        function submitterFromEvent(event, ownerForm) {
            const nativeSubmitter = event ? event.submitter : null;
            if (nativeSubmitter && nativeSubmitter.form === ownerForm) {
                return nativeSubmitter;
            }
            const active = document.activeElement;
            if (active && active.form === ownerForm) {
                return active;
            }
            if (lastIntentSubmitter && lastIntentSubmitter.form === ownerForm) {
                return lastIntentSubmitter;
            }
            return null;
        }

        document.querySelectorAll('button[data-checkout-intent="1"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                lastIntentSubmitter = btn;
            });
        });

        checkoutIntentConfirm?.addEventListener('click', function () {
            if (typeof pendingCheckoutSubmit === 'function') {
                const submitAction = pendingCheckoutSubmit;
                trackChapterEvent('checkout_intent_confirm', { kind: pendingCheckoutKind });
                closeCheckoutIntent(false);
                submitAction();
            }
        });
        checkoutIntentCancel?.addEventListener('click', function () {
            trackChapterEvent('checkout_intent_continue_edit', { kind: pendingCheckoutKind });
            closeCheckoutIntent();
        });
        checkoutIntentClose?.addEventListener('click', function () {
            trackChapterEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'close_button' });
            closeCheckoutIntent();
        });
        checkoutIntentModal?.addEventListener('click', function (event) {
            if (event.target === checkoutIntentModal) {
                trackChapterEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'backdrop' });
                closeCheckoutIntent();
            }
        });
        checkoutIntentPanel?.addEventListener('click', function (event) {
            event.stopPropagation();
        });
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isCheckoutIntentOpen()) {
                event.preventDefault();
                trackChapterEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'escape_key' });
                closeCheckoutIntent();
            }
        });

        function discardInlineParagraphDraft() {
            const num = document.getElementById('paragraph-number')?.value;
            const origEl = document.getElementById('original-text-input');
            const st = document.getElementById('suggested-text');
            const orig = origEl ? origEl.value : '';
            if (num !== undefined && num !== '') {
                try {
                    localStorage.removeItem('novel-inline-draft-' + chapterIdForInline + '-' + num);
                } catch (e) {}
            }
            if (st) {
                st.value = orig;
            }
        }

        document.getElementById('suggested-text')?.addEventListener('input', function () {
            const num = document.getElementById('paragraph-number')?.value;
            if (num === undefined || num === '') return;
            const ownership = document.getElementById('inline-ownership-note');
            if (ownership) {
                if ((this.value || '').trim().length > 0) {
                    ownership.classList.remove('hidden');
                } else {
                    ownership.classList.add('hidden');
                }
            }
            clearTimeout(inlineDraftTimer);
            inlineDraftTimer = setTimeout(function () {
                try {
                    localStorage.setItem('novel-inline-draft-' + chapterIdForInline + '-' + num, document.getElementById('suggested-text').value);
                } catch (e) {}
            }, 800);
        });

        fullEditTextarea?.addEventListener('input', function () {
            if (! fullEditOwnership) return;
            if ((this.value || '').trim().length > 0) {
                fullEditOwnership.classList.remove('hidden');
            } else {
                fullEditOwnership.classList.add('hidden');
            }
        });
        if (fullEditTextarea && fullEditOwnership && (fullEditTextarea.value || '').trim().length > 0) {
            fullEditOwnership.classList.remove('hidden');
        }

        fullEditForm?.addEventListener('submit', function (event) {
            if (fullEditForm.dataset.intentConfirmed === '1') {
                delete fullEditForm.dataset.intentConfirmed;
                return;
            }
            const submitter = submitterFromEvent(event, fullEditForm);
            if (! submitter || submitter.dataset.checkoutIntent !== '1') return;
            event.preventDefault();
            openCheckoutIntent({
                title: 'Submit full chapter version',
                originalText: chapterOriginalForCheckout,
                suggestedText: fullEditTextarea ? fullEditTextarea.value : '',
                hesitationCopy: 'Your version will compete against other submissions. Continue only if this is the wording you want judged.',
                analyticsKind: 'full_chapter',
                onConfirm: function () {
                    fullEditForm.dataset.intentConfirmed = '1';
                    fullEditForm.submit();
                },
            });
        });

        form?.addEventListener('submit', function (event) {
            if (form.dataset.intentConfirmed === '1') {
                delete form.dataset.intentConfirmed;
                return;
            }
            const submitter = submitterFromEvent(event, form);
            if (! submitter || submitter.dataset.checkoutIntent !== '1') {
                return;
            }
            event.preventDefault();
            const suggested = document.getElementById('suggested-text');
            const original = document.getElementById('original-text-input');
            openCheckoutIntent({
                title: 'Submit single-line replacement',
                originalText: original ? original.value : '',
                suggestedText: suggested ? suggested.value : '',
                hesitationCopy: 'Your version will compete against other submissions. Continue only if this is the wording you want judged.',
                hideInlineModal: true,
                analyticsKind: 'inline_edit',
                onConfirm: function () {
                    const num = document.getElementById('paragraph-number')?.value;
                    if (num !== undefined && num !== '') {
                        try { localStorage.removeItem('novel-inline-draft-' + chapterIdForInline + '-' + num); } catch (e) {}
                    }
                    form.dataset.intentConfirmed = '1';
                    form.submit();
                },
            });
        });

        queueCheckoutForm?.addEventListener('submit', function (event) {
            if (queueCheckoutForm.dataset.intentConfirmed === '1') {
                delete queueCheckoutForm.dataset.intentConfirmed;
                return;
            }
            const submitter = submitterFromEvent(event, queueCheckoutForm);
            if (! submitter || submitter.dataset.checkoutIntent !== '1') return;
            event.preventDefault();
            openCheckoutIntent({
                title: 'Submit queued suggestions',
                originalText: queueCountForCheckout + ' queued suggestion(s)',
                suggestedText: 'Checkout total: $' + queueTotalForCheckout,
                hesitationCopy: 'These versions will compete against other submissions. Remove any you do not want judged yet.',
                analyticsKind: 'queued_checkout',
                onConfirm: function () {
                    queueCheckoutForm.dataset.intentConfirmed = '1';
                    queueCheckoutForm.submit();
                },
            });
        });

        form?.addEventListener('submit', function () {
            const num = document.getElementById('paragraph-number')?.value;
            if (num !== undefined && num !== '') {
                try { localStorage.removeItem('novel-inline-draft-' + chapterIdForInline + '-' + num); } catch (e) {}
            }
        });
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
                    // Avoid persisting false 100% when layout briefly reports no scroll range.
                    stripPct = 0;
                    positionForSave = 0;
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

    @auth
        @php
            $preferServerDraft = ($pendingPaymentEdit ?? null)
                && ($pendingPaymentEdit->type ?? '') !== 'inline_edit'
                && strlen(trim((string) ($pendingPaymentEdit->edited_text ?? ''))) > 0;
            $wholeChapterEditedDefault = (($pendingPaymentEdit ?? null) && ($pendingPaymentEdit->type ?? '') !== 'inline_edit')
                ? (string) ($pendingPaymentEdit->edited_text ?? '')
                : '';
            $editedTextBaseline = old('edited_text', $wholeChapterEditedDefault);
        @endphp
        @if($chapterSuggestFabVisible)
            @include('chapters.partials.whole-edit-draft-preview-script', [
                'chapter' => $chapter,
                'preferServerDraft' => $preferServerDraft,
                'editedTextBaseline' => $editedTextBaseline,
            ])
        @endif
    @endauth
</x-dynamic-component>
