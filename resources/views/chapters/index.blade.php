@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    The manuscript opens chapter by chapter.
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Each chapter is released in a limited contribution window. Early accepted replacements set tone, rhythm, and authority for what follows.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ ready: false }" x-init="setTimeout(() => ready = true, 180)">
        @if (session('success'))
            <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-8 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 shadow-sm font-bold">{{ session('error') }}</div>
        @endif

        <div x-show="!ready" x-cloak class="max-w-5xl mx-auto space-y-6" aria-hidden="true">
            <div class="h-36 rounded-[2rem] bg-amber-100/70 animate-pulse"></div>
            <div class="h-36 rounded-[2rem] bg-amber-100/70 animate-pulse"></div>
            <div class="h-36 rounded-[2rem] bg-amber-100/70 animate-pulse"></div>
        </div>

        <div class="max-w-5xl mx-auto space-y-16" x-show="ready" x-cloak>
            @auth
                @php
                    $submittedSuggestions = (int) auth()->user()->editSuggestionsSubmittedCount();
                    $acceptedSuggestions = (int) auth()->user()->acceptedChapterAndParagraphEditCount();
                    $showContributorMission = $submittedSuggestions < 3;
                    $firstOpenChapter = ($chapters ?? collect())->first(function ($c) {
                        return ! $c->is_locked && $c->manuscriptPaidEditsOpen();
                    });
                @endphp
                @if($showContributorMission)
                    <div class="rounded-[2rem] border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-white px-6 py-6 shadow-sm">
                        <p class="text-xs font-black uppercase tracking-widest text-amber-800/60">Starter mission</p>
                        <h3 class="mt-2 text-2xl font-extrabold text-amber-950">Make your first 3 contribution attempts</h3>
                        <p class="mt-2 text-sm font-bold text-amber-900/75">Each approved replacement changes the manuscript and moves you up the leaderboard. Progress: <strong>{{ $submittedSuggestions }}/3 submitted</strong>, <strong>{{ $acceptedSuggestions }} accepted</strong>.</p>
                        <div class="mt-4 h-2.5 w-full rounded-full bg-amber-100">
                            <div class="h-full rounded-full bg-amber-600 transition-all" style="width: {{ min(100, (int) round(($submittedSuggestions / 3) * 100)) }}%"></div>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-3">
                            @if($firstOpenChapter)
                                <a href="{{ route('chapters.show', $firstOpenChapter) }}" data-track-event="onboarding_mission_primary_click" data-track-label="chapters_index_mission_open_chapter" class="inline-flex items-center rounded-2xl bg-amber-900 px-5 py-3 text-sm font-extrabold text-white hover:bg-black">
                                    Open active chapter
                                </a>
                            @endif
                            <a href="{{ route('leaderboard') }}" data-track-event="onboarding_mission_secondary_click" data-track-label="chapters_index_mission_leaderboard" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white px-5 py-3 text-sm font-extrabold text-amber-900 hover:bg-amber-50">
                                See how ranking works
                            </a>
                        </div>
                    </div>
                @endif
            @endauth
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
                                            Open for Replacements
                                        </div>
                                    @else
                                        <div class="relative group px-6 py-3 bg-amber-100 rounded-2xl border border-amber-200 text-amber-800 text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                            Editing window closed
                                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-amber-300 text-[10px]">i</span>
                                            <span class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 hidden -translate-x-1/2 whitespace-normal w-64 rounded-xl bg-amber-950 px-3 py-2 text-[10px] normal-case tracking-normal leading-relaxed text-amber-100 shadow-lg group-hover:block">
                                                This chapter is now read-only because the paid editing window ended.
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <h3 class="text-4xl font-extrabold text-amber-900 {{ $chapter->is_locked ? 'mb-3' : 'mb-4' }}">{{ $chapter->readerHeadingLine() }}</h3>
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
                                        <div class="relative group w-16 h-16 bg-amber-100/50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                        <span class="text-2xl">🔒</span>
                                        <span class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 hidden -translate-x-1/2 whitespace-normal w-64 rounded-xl bg-amber-950 px-3 py-2 text-[10px] normal-case tracking-normal leading-relaxed text-amber-100 shadow-lg group-hover:block">
                                            Locked chapters are finalized text and no longer accept paid edits.
                                        </span>
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
                                                        title="Replace this line ($2 contribution): submit your version for this paragraph."
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
                                @elseif($chapter->isPilotManuscriptChapter() && $chapter->manuscriptPaidEditsOpen())
                                    <div class="text-amber-800 font-bold">
                                        <span class="text-amber-950 font-black">Pilot chapter</span> — paid edits until we reach
                                        <strong>{{ config('tbwnn.pilot.close_after_accepted_edits', 50) }}</strong> accepted suggestions
                                        ({{ $chapter->pilotAcceptedEditsTotal() }} so far).
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
                                    Challenge this chapter
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
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">The ink is not dry...</h3>
                    <p class="text-amber-800/70 text-lg font-bold max-w-2xl mx-auto">The first chapter has not been released yet. When it opens, the earliest contributors will have the strongest influence on the manuscript voice.</p>
                    <a href="{{ route('home') }}#landing-updates-signup" class="mt-8 inline-flex items-center px-8 py-4 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/25">
                        Get notified when Chapter 1 opens
                    </a>
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
                                    {{ $arch->readerHeadingLine() }}
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
    <div id="inline-edit-modal" class="fixed inset-0 bg-amber-900/80 backdrop-blur-sm z-[100] hidden flex items-start sm:items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="relative my-2 sm:my-4 bg-white rounded-[2rem] sm:rounded-[3rem] w-full max-w-2xl p-6 sm:p-12 shadow-2xl max-h-[calc(100vh-1rem)] sm:max-h-[calc(100vh-2rem)] overflow-y-auto overscroll-contain">
            <button
                type="button"
                onclick="closeInlineEdit()"
                class="absolute top-3 right-3 sm:top-4 sm:right-4 w-10 h-10 rounded-xl bg-amber-100 text-amber-900 font-black hover:bg-amber-200 transition-colors"
                aria-label="Close paragraph edit dialog"
            >
                ×
            </button>
            <h3 class="text-2xl font-extrabold text-amber-900 mb-2">Replace this line</h3>
            <p class="text-sm font-bold text-amber-800/70 mb-8 leading-relaxed">Challenge <strong class="text-amber-900">one paragraph</strong> with your own version. For full-chapter replacement, use the chapter page sidebar. Submission enters moderation review (acceptance is not guaranteed).</p>
            <form id="inline-edit-form" method="POST" action="{{ route('payment.checkout') }}" class="space-y-8">
                @csrf
                <input type="hidden" id="edit-chapter-id" name="chapter_id">
                <input type="hidden" name="type" value="inline_edit">
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

                <p class="text-xs font-bold text-amber-800/70 leading-relaxed">Free to read. Payment applies only when you submit. Only accepted replacements are integrated into the manuscript.</p>
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" data-checkout-intent="1" data-intent-kind="inline" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                        Submit your version
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
                    <h4 id="checkout-intent-title" class="mt-1 text-2xl font-extrabold text-amber-900">Submit single-line replacement</h4>
                </div>
                <button type="button" id="checkout-intent-close" class="rounded-xl bg-amber-100 px-3 py-2 text-sm font-black text-amber-900 hover:bg-amber-200">Close</button>
            </div>
            <p class="mt-4 text-sm font-bold leading-relaxed text-amber-900/80">
                You are about to start a <strong class="text-amber-950">$2 contribution checkout</strong>. Submissions stay pending until moderation review.
            </p>
            <ul class="mt-4 space-y-2 rounded-2xl border border-amber-100 bg-amber-50/70 p-4 text-sm font-bold text-amber-900/85">
                <li>• Acceptance is not guaranteed, but accepted replacements can earn leaderboard points.</li>
                <li>• Approved submissions permanently reshape this manuscript.</li>
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
            <p id="checkout-intent-hesitation" class="mt-4 text-xs font-bold text-amber-800/80">This line is still challengeable. Continue only if this wording is the one you want judged.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" id="checkout-intent-confirm" class="inline-flex items-center rounded-2xl bg-amber-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-black">
                    Continue to checkout
                </button>
                <button type="button" id="checkout-intent-cancel" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white px-6 py-3 text-sm font-extrabold text-amber-900 hover:bg-amber-50">
                    Continue with this edit
                </button>
            </div>
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
                    selectionButton.innerHTML = 'Replace this line';
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
        const checkoutIntentModal = document.getElementById('checkout-intent-modal');
        const checkoutIntentPanel = document.getElementById('checkout-intent-panel');
        const checkoutIntentOriginal = document.getElementById('checkout-intent-original');
        const checkoutIntentSuggested = document.getElementById('checkout-intent-suggested');
        const checkoutIntentConfirm = document.getElementById('checkout-intent-confirm');
        const checkoutIntentCancel = document.getElementById('checkout-intent-cancel');
        const checkoutIntentClose = document.getElementById('checkout-intent-close');
        let pendingCheckoutSubmit = null;
        let pendingCheckoutKind = 'inline_index';
        let intentInteractionGuardEnabled = false;
        let inlineHiddenByIntent = false;
        let lastIntentSubmitter = null;

        if (checkoutIntentModal && checkoutIntentModal.parentElement !== document.body) {
            document.body.appendChild(checkoutIntentModal);
        }

        function isCheckoutIntentOpen() {
            return !!(checkoutIntentModal && ! checkoutIntentModal.classList.contains('hidden'));
        }

        function trackChapterIndexEvent(eventName, context) {
            if (! eventName || typeof window.trackLandingEvent !== 'function') {
                return;
            }
            window.trackLandingEvent(eventName, Object.assign({
                path: window.location.pathname,
                chapter_id: document.getElementById('edit-chapter-id')?.value || null,
            }, context || {}));
        }

        function summarizeForPreview(text) {
            const normalized = String(text || '').replace(/\s+/g, ' ').trim();
            if (normalized.length <= 160) {
                return normalized || '(empty)';
            }
            return normalized.slice(0, 160) + '...';
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

        function openInlineEdit(chapterId, number, text) {
            document.getElementById('edit-chapter-id').value = chapterId;
            document.getElementById('paragraph-number').value = number;
            document.getElementById('original-text-input').value = text;
            document.getElementById('original-text-display').innerText = text;
            document.getElementById('suggested-text').value = text;
            const ownership = document.getElementById('inline-ownership-note');
            if (ownership) {
                ownership.classList.remove('hidden');
            }
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            trackChapterIndexEvent('inline_edit_modal_open', { source: 'chapters_index' });
        }

        function closeInlineEdit() {
            modal.classList.add('hidden');
            if (! checkoutIntentModal || checkoutIntentModal.classList.contains('hidden')) {
                document.body.style.overflow = 'auto';
            }
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
            checkoutIntentOriginal.textContent = summarizeForPreview(payload.originalText);
            checkoutIntentSuggested.textContent = summarizeForPreview(payload.suggestedText);
            pendingCheckoutSubmit = payload.onConfirm;
            pendingCheckoutKind = payload.analyticsKind || 'inline_index';
            checkoutIntentModal.classList.remove('hidden');
            checkoutIntentModal.classList.add('flex');
            enableIntentInteractionGuard();
            document.body.style.overflow = 'hidden';
            trackChapterIndexEvent('checkout_intent_open', { kind: pendingCheckoutKind });
        }

        function closeCheckoutIntent(reopenInlineModal) {
            if (! checkoutIntentModal) return;
            const shouldReopenInline = reopenInlineModal !== false;
            checkoutIntentModal.classList.add('hidden');
            checkoutIntentModal.classList.remove('flex');
            pendingCheckoutSubmit = null;
            pendingCheckoutKind = 'inline_index';
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

        document.getElementById('suggested-text')?.addEventListener('input', function (e) {
            const ownership = document.getElementById('inline-ownership-note');
            if (! ownership) return;
            if ((e.target.value || '').trim().length > 0) {
                ownership.classList.remove('hidden');
            } else {
                ownership.classList.add('hidden');
            }
        });

        document.querySelectorAll('button[data-checkout-intent="1"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                lastIntentSubmitter = btn;
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
                originalText: original ? original.value : '',
                suggestedText: suggested ? suggested.value : '',
                hideInlineModal: true,
                analyticsKind: 'inline_index',
                onConfirm: function () {
                    form.dataset.intentConfirmed = '1';
                    form.submit();
                },
            });
        });

        checkoutIntentConfirm?.addEventListener('click', function () {
            if (typeof pendingCheckoutSubmit === 'function') {
                const submitAction = pendingCheckoutSubmit;
                trackChapterIndexEvent('checkout_intent_confirm', { kind: pendingCheckoutKind });
                closeCheckoutIntent(false);
                submitAction();
            }
        });
        checkoutIntentCancel?.addEventListener('click', function () {
            trackChapterIndexEvent('checkout_intent_continue_edit', { kind: pendingCheckoutKind });
            closeCheckoutIntent();
        });
        checkoutIntentClose?.addEventListener('click', function () {
            trackChapterIndexEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'close_button' });
            closeCheckoutIntent();
        });
        checkoutIntentModal?.addEventListener('click', function (event) {
            if (event.target === checkoutIntentModal) {
                trackChapterIndexEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'backdrop' });
                closeCheckoutIntent();
            }
        });
        checkoutIntentPanel?.addEventListener('click', function (event) {
            event.stopPropagation();
        });
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isCheckoutIntentOpen()) {
                event.preventDefault();
                trackChapterIndexEvent('checkout_intent_close', { kind: pendingCheckoutKind, source: 'escape_key' });
                closeCheckoutIntent();
            }
        });

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
                    // Locked chapters only show a short placeholder here; real progress is saved on the full chapter page.
                    if (! content.classList.contains('chapter-content')) {
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
