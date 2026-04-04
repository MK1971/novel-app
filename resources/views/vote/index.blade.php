@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Peter Trull Solitary Detective
                </h1>
                <p class="text-amber-800/60 font-bold mt-1">Part 2: Vote on the final versions of each chapter.</p>
                <p class="text-amber-800/50 text-sm font-bold mt-2 max-w-2xl leading-relaxed">
                    Each pair has a voting deadline (same 30-day calendar as paid edits on The Book With No Name). Vote credits come from completed $2 edits there. The author can still lock a version early.
                </p>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    @if($canVote ?? false)
                        <div class="px-4 py-2 bg-emerald-100 text-emerald-950 border-2 border-emerald-600/80 text-sm font-black rounded-2xl shadow-sm flex items-center gap-2">
                            <span aria-hidden="true">🗳️</span>
                            <span>You have vote credits</span>
                        </div>
                    @else
                        <div class="px-4 py-2 bg-amber-900 text-white text-sm font-black rounded-2xl shadow-lg shadow-amber-900/20 flex items-center gap-2">
                            <span aria-hidden="true">ℹ️</span>
                            <span>Ballots from $2 edit checkouts</span>
                        </div>
                    @endif
                @else
                    <div class="px-4 py-2 bg-amber-900 text-white text-sm font-black rounded-2xl shadow-lg shadow-amber-900/20 flex items-center gap-2">
                        <span aria-hidden="true">👋</span>
                        <span>Sign in to use vote credits</span>
                    </div>
                @endauth
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

        @if (!($canVote ?? false))
            <div class="mb-16 p-12 bg-amber-900 rounded-[3rem] text-center text-white shadow-2xl shadow-amber-900/20 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-amber-500 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-extrabold mb-4">Voting is restricted</h2>
                    <p class="text-amber-100/70 text-xl font-bold mb-10 max-w-2xl mx-auto leading-relaxed"><span class="text-white font-black">Peter Trull Solitary Detective</span> uses <strong>paid edit credits</strong> only: each completed <strong>$2</strong> edit payment in <strong>The Book With No Name</strong> gives <strong>one vote</strong> here. There are no free votes based on accepted edits alone.</p>
                    <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-12 py-5 bg-amber-500 text-black text-xl font-extrabold rounded-full hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                        Start Contributing Now
                        <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    @if($firstOpenTbwChapter ?? null)
                        <div class="mt-6">
                            <a href="{{ route('chapters.show', $firstOpenTbwChapter) }}" class="inline-flex items-center px-10 py-4 bg-white/10 text-white text-lg font-extrabold rounded-full border-2 border-white/30 hover:bg-white/15 transition-all">
                                Open the live TBWNN chapter
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </a>
                        </div>
                    @endif
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-black/10 rounded-full blur-3xl"></div>
            </div>
        @else
            <div class="mb-16 text-center max-w-3xl mx-auto">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-4">Compare and decide</h2>
                @if (($voteCreditsRemaining ?? 0) > 0)
                    <p class="mb-4 text-amber-900 font-black text-sm uppercase tracking-widest">Unused vote credits: {{ $voteCreditsRemaining }}</p>
                @endif
                <p class="text-amber-800/85 text-lg font-bold leading-relaxed">Review Version A and Version B of each chapter. Casting a vote uses one paid edit credit. You can vote only once per chapter pair.</p>
            </div>
        @endif

        <div class="space-y-16">
            @forelse($chapters ?? [] as $chapterNum => $versions)
                @php
                    $versionA = $versions->firstWhere('version', 'A');
                    $versionB = $versions->firstWhere('version', 'B');
                @endphp
                @if($versionA && $versionB)
                    @php
                        $lockA = (bool) $versionA->is_locked;
                        $lockB = (bool) $versionB->is_locked;
                        $pairFullyClosed = $lockA && $lockB;
                        $userHasVoted = in_array($versionA->id, $hasVoted ?? []) || in_array($versionB->id, $hasVoted ?? []);
                        $votesA = (int) ($voteCounts[$versionA->id] ?? 0);
                        $votesB = (int) ($voteCounts[$versionB->id] ?? 0);
                        $totalVotes = $votesA + $votesB;
                        $votePeriodEnded = $versionA->editing_closes_at && $versionA->isPastEditingWindow();
                        $voteADisabled = $userHasVoted || $lockA || $votePeriodEnded;
                        $voteBDisabled = $userHasVoted || $lockB || $votePeriodEnded;
                        $caLocked = $versionA->lockedAtForDisplay();
                        $cbLocked = $versionB->lockedAtForDisplay();
                        $pairClosedAt = $pairFullyClosed
                            ? (($caLocked && $cbLocked) ? ($caLocked->greaterThan($cbLocked) ? $caLocked : $cbLocked) : ($caLocked ?? $cbLocked))
                            : null;
                    @endphp
                    <details
                        @if($chapterNum === ($latestPtVotePairKey ?? null)) open @endif
                        class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden group"
                    >
                        <summary class="list-none cursor-pointer [&::-webkit-details-marker]:hidden">
                            <div class="bg-amber-50/50 px-10 py-6 border-b border-amber-100 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div class="min-w-0 text-left">
                                    <h3 class="text-2xl font-extrabold text-amber-900">{{ $versionA->readerHeadingLine() }}</h3>
                                    @if($pairFullyClosed)
                                        @if($pairClosedAt)
                                            <p class="text-sm font-bold text-amber-800/80 mt-2">Voting closed on {{ $pairClosedAt->timezone(config('app.timezone'))->format('M j, Y') }}.</p>
                                        @else
                                            <p class="text-sm font-bold text-amber-800/80 mt-2">Voting is closed for this pair.</p>
                                        @endif
                                    @elseif($versionA->editing_closes_at)
                                        @if($votePeriodEnded)
                                            <p class="text-sm font-bold text-amber-800/80 mt-2">Voting period ended {{ $versionA->editing_closes_at->timezone(config('app.timezone'))->format('M j, Y') }}.</p>
                                        @else
                                            <p class="text-sm font-black text-amber-700 mt-2">Voting open until {{ $versionA->editing_closes_at->timezone(config('app.timezone'))->format('M j, Y') }} ({{ $versionA->editing_closes_at->diffForHumans() }}).</p>
                                        @endif
                                    @endif
                                    <p class="text-xs font-bold text-amber-700/70 mt-2 md:hidden">Tap header to expand or collapse this pair.</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-4 shrink-0">
                                    @if($chapterNum !== ($latestPtVotePairKey ?? null))
                                        <span class="px-3 py-1 bg-amber-100/80 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest group-open:hidden">Older round — tap to open</span>
                                        <span class="px-3 py-1 bg-amber-100/80 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest hidden group-open:inline">Older round</span>
                                    @endif
                                    @if($pairFullyClosed)
                                        <span class="px-4 py-1 bg-red-100 text-red-700 text-xs font-black rounded-full uppercase tracking-widest"><span aria-hidden="true">🔒</span> Locked</span>
                                    @elseif($votePeriodEnded)
                                        <span class="px-4 py-1 bg-amber-200 text-amber-900 text-xs font-black rounded-full uppercase tracking-widest">Period ended</span>
                                    @elseif($userHasVoted)
                                        <span class="px-4 py-1 bg-green-100 text-green-700 text-xs font-black rounded-full uppercase tracking-widest"><span aria-hidden="true">✓</span> Voted</span>
                                    @else
                                        <span class="px-4 py-1 bg-amber-100 text-amber-700 text-xs font-black rounded-full uppercase tracking-widest animate-pulse">Voting open</span>
                                    @endif
                                    <div class="text-sm font-bold text-amber-800">
                                        Total Votes: <span class="text-amber-950">{{ $totalVotes }}</span>
                                    </div>
                                    <svg class="w-6 h-6 text-amber-800/50 shrink-0 transition-transform group-open:rotate-180 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </summary>
                        <div class="grid md:grid-cols-2 divide-x divide-amber-50">
                            <div class="p-10 flex flex-col">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-extrabold text-amber-900">Version A</h4>
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-black text-amber-900">{{ $votesA }} votes</span>
                                        <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-black rounded-full uppercase tracking-widest">Option 1</span>
                                    </div>
                                </div>
                                <p class="text-amber-900 text-lg leading-relaxed mb-12 flex-grow whitespace-pre-wrap">{{ $versionA->content }}</p>
                                @if($canVote ?? false)
                                    <form action="{{ route('vote.store', $versionA) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-8 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-900 disabled:hover:translate-y-0" {{ $voteADisabled ? 'disabled' : '' }}>
                                            @if($lockA) 🔒 Voting closed for A @elseif($votePeriodEnded) ⏱ Voting period ended @elseif($userHasVoted) ✓ Already Voted @else Vote for Version A @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="p-10 flex flex-col bg-amber-50/20">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-extrabold text-amber-900">Version B</h4>
                                    <div class="flex items-center gap-3">
                                        <span class="text-lg font-black text-amber-900">{{ $votesB }} votes</span>
                                        <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Option 2</span>
                                    </div>
                                </div>
                                <p class="text-amber-900 text-lg leading-relaxed mb-12 flex-grow whitespace-pre-wrap">{{ $versionB->content }}</p>
                                @if($canVote ?? false)
                                    <form action="{{ route('vote.store', $versionB) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-8 py-4 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-500 disabled:hover:translate-y-0" {{ $voteBDisabled ? 'disabled' : '' }}>
                                            @if($lockB) 🔒 Voting closed for B @elseif($votePeriodEnded) ⏱ Voting period ended @elseif($userHasVoted) ✓ Already Voted @else Vote for Version B @endif
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @if($versionA->content !== $versionB->content)
                            @php
                                $pairDiffUi = \App\Support\TextDiff::linesForDisplay($versionA->content, $versionB->content);
                            @endphp
                            <div class="border-t border-amber-100 px-6 sm:px-10 py-5 bg-slate-50/60">
                                @if($pairDiffUi !== null)
                                    <details class="group">
                                        <summary class="cursor-pointer text-sm font-black text-amber-950 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-500 rounded-lg px-1 -mx-1">
                                            What changed between A and B?
                                        </summary>
                                        <p class="mt-3 text-xs font-bold text-amber-900/90 leading-relaxed max-w-3xl">
                                            <span class="inline-flex items-center gap-1.5 mr-3"><span class="inline-block w-3 h-3 rounded-sm bg-rose-200 border border-rose-400" aria-hidden="true"></span> Removed from A</span>
                                            <span class="inline-flex items-center gap-1.5"><span class="inline-block w-3 h-3 rounded-sm bg-emerald-200 border border-emerald-500" aria-hidden="true"></span> Added in B</span>
                                            <span class="block sm:inline sm:ml-3 mt-1 sm:mt-0 text-amber-800/90 font-semibold">Pale <strong class="text-neutral-800">white</strong> rows (on the sand track) = unchanged context.</span>
                                        </p>
                                        <div class="mt-3 max-h-96 overflow-auto rounded-2xl border-2 border-neutral-300/80 bg-neutral-200/90 shadow-inner" role="region" aria-label="Text differences">
                                            @foreach($pairDiffUi['lines'] as $row)
                                                <div @class([
                                                    'px-3 py-1.5 whitespace-pre-wrap break-words text-sm leading-relaxed border-b border-neutral-300/60 last:border-b-0',
                                                    'bg-rose-100 text-rose-950 border-l-4 border-l-rose-600 pl-2' => $row['kind'] === 'removed',
                                                    'bg-emerald-100 text-emerald-950 border-l-4 border-l-emerald-600 pl-2' => $row['kind'] === 'added',
                                                    'bg-white text-neutral-900 border-l-4 border-l-neutral-500 pl-2 shadow-[inset_0_1px_0_rgba(0,0,0,0.04)]' => $row['kind'] === 'same',
                                                    'bg-amber-100 text-amber-950 font-bold text-xs py-2 border-l-4 border-l-amber-500' => $row['kind'] === 'warning',
                                                ])>{{ $row['text'] }}</div>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <p class="text-sm font-bold text-amber-900">These versions differ; the comparison view is omitted because the combined text is very long.</p>
                                @endif
                            </div>
                        @endif
                    </details>
                @endif
            @empty
                <div class="text-center py-32 bg-white border border-amber-100 rounded-[3rem] shadow-sm">
                    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">The voting booth is closed...</h3>
                    <p class="text-amber-800/50 text-lg font-bold">No chapter pairs have been uploaded for voting yet. Check back soon!</p>
                </div>
            @endforelse

            @if(isset($archiveChapters) && $archiveChapters->isNotEmpty())
                <details class="border-t-2 border-amber-200/60 pt-16 mt-8 group">
                    <summary class="list-none cursor-pointer [&::-webkit-details-marker]:hidden flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-extrabold text-amber-900 mb-2">Previous voting rounds</h2>
                            <p class="text-amber-800/60 font-bold text-sm max-w-2xl">Read-only archived A/B text from earlier rounds.</p>
                        </div>
                        <svg class="w-8 h-8 text-amber-700/40 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </summary>
                    <ul class="space-y-3 mt-8">
                        @foreach($archiveChapters as $arch)
                            <li>
                                <a href="{{ route('chapters.show', $arch) }}" class="inline-flex items-center gap-2 text-amber-800 font-extrabold hover:text-amber-950 underline decoration-amber-300">
                                    {{ $arch->readerHeadingLine() }} ({{ $arch->version }})
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    </div>
</x-dynamic-component>
