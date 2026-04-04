<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">
                @can('admin') ⚙️ Admin Dashboard @else 📊 Dashboard @endcan
            </h2>
            <p class="text-amber-800/60 font-bold">Welcome, {{ auth()->user()->name }}!</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @can('admin')
                {{-- ADMIN DASHBOARD --}}
                
                {{-- Key Metrics --}}
                <div class="grid md:grid-cols-4 gap-6 mb-12">
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 rounded-[2rem] p-8" title="Count of registered users minus one (the configured admin account is excluded from this total).">
                        <div class="text-4xl font-extrabold text-amber-600 mb-2">{{ \App\Models\User::count() - 1 }}</div>
                        <p class="text-amber-800/60 font-bold">Total Users</p>
                        <p class="text-xs text-amber-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Excluding admin.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-amber-300 text-[10px] font-black text-amber-800" title="Count of registered users minus one (the configured admin account is excluded from this total)." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-[2rem] p-8" title="Full-chapter pending queue plus paragraph-level pending. PayPal inline_edit stub rows are excluded from the chapter count.">
                        @php
                            $adminPendingChapter = \App\Models\Edit::where('status', 'pending')->where('type', '!=', 'inline_edit')->count();
                            $adminPendingParagraph = \App\Models\InlineEdit::where('status', 'pending')->count();
                        @endphp
                        <div class="text-4xl font-extrabold text-blue-600 mb-2">{{ $adminPendingChapter + $adminPendingParagraph }}</div>
                        <p class="text-blue-800/60 font-bold">Pending Edits</p>
                        <p class="text-xs text-blue-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Chapter + paragraph queues.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-blue-300 text-[10px] font-black text-blue-900" title="Full-chapter pending queue plus paragraph-level pending. PayPal inline_edit stub rows are excluded from the chapter count." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-[2rem] p-8" title="Edits with status accepted, accepted_full, or accepted_partial — merged or approved into the manuscript.">
                        <div class="text-4xl font-extrabold text-green-600 mb-2">{{ \App\Models\Edit::whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count() }}</div>
                        <p class="text-green-800/60 font-bold">Accepted Edits</p>
                        <p class="text-xs text-green-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Integrated into the novel.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-green-300 text-[10px] font-black text-green-900" title="Edits with status accepted, accepted_full, or accepted_partial — merged or approved into the manuscript." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-[2rem] p-8" title="Top: logical reader-facing pieces (TBWNN main A stream; Peter Trull one slot per voting pair). Bottom: raw chapter rows in the database.">
                        @php
                            $adminReaderChapters = \App\Models\Chapter::logicalReaderPieceCount();
                            $adminChapterRows = \App\Models\Chapter::count();
                        @endphp
                        <div class="text-4xl font-black tabular-nums text-purple-950 mb-1">{{ $adminReaderChapters }}</div>
                        <p class="text-purple-800/60 font-bold">Reader-facing pieces</p>
                        <p class="text-sm font-bold text-purple-900/80 mt-2 tabular-nums">{{ $adminChapterRows }} DB rows</p>
                        <p class="text-xs text-purple-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>List vs raw row count.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-purple-300 text-[10px] font-black text-purple-900" title="Top: logical reader-facing pieces (TBWNN main A stream; Peter Trull one slot per voting pair). Bottom: raw chapter rows in the database." aria-hidden="true">?</span>
                        </p>
                    </div>
                </div>

                {{-- Pending Edits for Review --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 mb-12">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">📝 Pending Edits for Review</h3>
                    
                    @php
                        $pendingEdits = \App\Models\Edit::where('status', 'pending')
                            ->where('type', '!=', 'inline_edit')
                            ->with('user', 'chapter')
                            ->orderByDesc('created_at')
                            ->limit(10)
                            ->get();
                        $pendingParagraphCount = \App\Models\InlineEdit::where('status', 'pending')->count();
                    @endphp
                    
                    @if($pendingParagraphCount > 0)
                        <p class="text-sm font-bold text-amber-800/70 mb-4">
                            <span class="text-amber-900 font-extrabold">{{ $pendingParagraphCount }}</span> paragraph-level suggestion(s) pending —
                            <a href="{{ route('admin.inline-edits.index') }}" class="text-amber-900 underline font-extrabold hover:text-amber-600">open Paragraph edits</a>.
                        </p>
                    @endif
                    @if($pendingEdits->count() > 0)
                        <div class="space-y-4">
                            @foreach($pendingEdits as $edit)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 hover:shadow-lg transition-all">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span class="font-extrabold text-amber-900">{{ $edit->user->name }}</span>
                                                <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded">{{ ucfirst($edit->type) }}</span>
                                            </div>
                                            <p class="text-sm text-amber-900/60 font-bold mb-2">{{ $edit->chapter->readerHeadingLine() }}</p>
                                            <p class="text-xs text-amber-800/60 font-bold">{{ $edit->created_at->diffForHumans() }}</p>
                                        </div>
                                        <a href="{{ route('admin.edits.index') }}" class="px-4 py-2 bg-amber-600 text-white font-bold rounded-lg hover:bg-amber-700 transition-colors text-sm flex-shrink-0">
                                            Review
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($pendingEdits->isEmpty() && $pendingParagraphCount === 0)
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No pending suggestions in either queue.</p>
                        </div>
                    @endif
                </div>

                {{-- Recent Feedback --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 mb-12">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">💬 Recent Feedback</h3>
                    
                    @php
                        $recentFeedback = \App\Models\Feedback::with('user')
                            ->orderByDesc('created_at')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($recentFeedback->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentFeedback as $feedback)
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-6">
                                    <div class="flex items-start justify-between gap-4 mb-2 flex-wrap">
                                        <div class="flex flex-col gap-1">
                                            <span class="font-extrabold text-amber-900">{{ $feedback->user?->name ?? $feedback->email ?? 'Anonymous' }}</span>
                                            <span class="text-xs font-bold text-amber-700/80 uppercase tracking-wider">{{ str_replace('_', ' ', $feedback->type) }}</span>
                                        </div>
                                        <p class="text-xs text-amber-800/60 font-bold">{{ $feedback->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm text-amber-900/80 font-bold">{{ Str::limit($feedback->content, 150) }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No feedback received yet</p>
                        </div>
                    @endif
                </div>

                {{-- Top Contributors --}}
                <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-6">🏆 Top Contributors</h3>
                    
                    @php
                        $topContributors = \App\Models\User::where('is_admin', false)
                            ->orderByDesc('points')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($topContributors->count() > 0)
                        <div class="space-y-3">
                            @foreach($topContributors as $index => $contributor)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                    <div class="flex items-center gap-4">
                                        <span class="text-2xl font-extrabold text-amber-600">{{ $index + 1 }}</span>
                                        <div>
                                            <p class="font-extrabold text-amber-900">{{ $contributor->name }}</p>
                                            <p class="text-xs text-amber-800/60 font-bold">{{ $contributor->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-2xl font-extrabold text-amber-600">{{ $contributor->points }}</p>
                                        <p class="text-xs text-amber-800/60 font-bold">points</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-amber-50 rounded-xl border border-amber-100">
                            <p class="text-amber-800/60 font-bold">No contributors yet</p>
                        </div>
                    @endif
                </div>

            @else
                {{-- USER DASHBOARD --}}
                @if(! auth()->user()->onboarding_completed_at)
                    <div class="mb-10 rounded-[2rem] border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-amber-100/80 p-8 shadow-md shadow-amber-200/30" role="region" aria-labelledby="onboarding-heading">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <h3 id="onboarding-heading" class="text-xl font-extrabold text-amber-950">Welcome — get started</h3>
                                <p class="mt-2 text-sm font-bold text-amber-900/75">Pick a step below. You can dismiss this card anytime.</p>
                                <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                                    <li>
                                        <a href="{{ route('chapters.index') }}" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-white/90 px-4 py-3 text-sm font-extrabold text-amber-900 shadow-sm hover:bg-amber-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500">
                                            <span class="text-xl" aria-hidden="true">📚</span>
                                            Browse chapters
                                        </a>
                                    </li>
                                    @if($firstOpenTbwChapter ?? null)
                                        <li>
                                            <a href="{{ route('chapters.show', $firstOpenTbwChapter) }}" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-white/90 px-4 py-3 text-sm font-extrabold text-amber-900 shadow-sm hover:bg-amber-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500">
                                                <span class="text-xl" aria-hidden="true">📖</span>
                                                Open the live chapter
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('leaderboard') }}" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-white/90 px-4 py-3 text-sm font-extrabold text-amber-900 shadow-sm hover:bg-amber-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500">
                                            <span class="text-xl" aria-hidden="true">🏆</span>
                                            See the leaderboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('vote.index') }}" class="flex items-center gap-3 rounded-xl border border-amber-200 bg-white/90 px-4 py-3 text-sm font-extrabold text-amber-900 shadow-sm hover:bg-amber-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500">
                                            <span class="text-xl" aria-hidden="true">🗳️</span>
                                            Peter Trull vote hub
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <form method="post" action="{{ route('onboarding.dismiss') }}" class="shrink-0">
                                @csrf
                                <button type="submit" class="w-full rounded-xl border-2 border-amber-800/20 bg-white px-5 py-3 text-sm font-extrabold text-amber-900 hover:bg-amber-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600 lg:w-auto">
                                    Dismiss
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="grid md:grid-cols-4 gap-6 mb-12">
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border-2 border-amber-200 rounded-[2rem] p-8" title="Chapter and paragraph suggestions both use 2 / 1 / 0 points (full accept / partial / reject) when paid and moderated.">
                        <div class="text-4xl font-extrabold text-amber-600 mb-2">{{ auth()->user()->points }}</div>
                        <p class="text-amber-800/60 font-bold">Your Points</p>
                        <p class="text-xs text-amber-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Paid edits: up to 2 / 1 / 0 pts.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-amber-300 text-[10px] font-black text-amber-800" title="Chapter and paragraph suggestions both use 2 / 1 / 0 points (full accept / partial / reject) when paid and moderated." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-[2rem] p-8" title="Each paid suggestion counts once. Full-chapter and paragraph submissions are separate; internal payment stub rows are not double-counted.">
                        <div class="text-4xl font-extrabold text-blue-600 mb-2">{{ auth()->user()->editSuggestionsSubmittedCount() }}</div>
                        <p class="text-blue-800/60 font-bold">Your Edits</p>
                        <p class="text-xs text-blue-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Paid suggestions submitted.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-blue-300 text-[10px] font-black text-blue-900" title="Each paid suggestion counts once. Full-chapter and paragraph submissions are separate; internal payment stub rows are not double-counted." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-[2rem] p-8" title="Includes partial chapter accepts and approved paragraph suggestions. Each counts once toward this total.">
                        <div class="text-4xl font-extrabold text-green-600 mb-2">{{ auth()->user()->acceptedChapterAndParagraphEditCount() }}</div>
                        <p class="text-green-800/60 font-bold">Accepted</p>
                        <p class="text-xs text-green-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Moderator-approved edits.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-green-300 text-[10px] font-black text-green-900" title="Includes partial chapter accepts and approved paragraph suggestions. Each counts once toward this total." aria-hidden="true">?</span>
                        </p>
                    </div>

                    <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-200 rounded-[2rem] p-8" title="Chapter or paragraph suggestions that moderators declined.">
                        <div class="text-4xl font-extrabold text-red-600 mb-2">{{ auth()->user()->rejectedChapterAndParagraphEditCount() }}</div>
                        <p class="text-red-800/60 font-bold">Rejected</p>
                        <p class="text-xs text-red-800/45 font-bold mt-2 inline-flex flex-wrap items-center gap-1">
                            <span>Declined suggestions.</span>
                            <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-red-300 text-[10px] font-black text-red-900" title="Chapter or paragraph suggestions that moderators declined." aria-hidden="true">?</span>
                        </p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8">
                        <h3 class="text-2xl font-extrabold text-amber-900 mb-6">📚 Quick Links</h3>
                        <div class="grid gap-4">
                            <a href="{{ route('chapters.index') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">📖 Read Chapters</p>
                                <p class="text-sm text-amber-800/60 font-bold">Explore and edit the story</p>
                            </a>
                            <a href="{{ route('vote.index') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">🗳️ Peter Trull Solitary Detective · Vote</p>
                                @if($canVote ?? false)
                                    <p class="text-sm text-amber-800/60 font-bold">Compare versions and cast your vote</p>
                                @else
                                    <p class="text-sm text-amber-800/60 font-bold">You need at least one <span class="text-amber-900">unused paid edit</span> (completed $2 checkout) for a vote credit. Each payment adds one vote for Peter Trull Solitary Detective. You can still open the page without credits.</p>
                                @endif
                            </a>
                            <a href="{{ route('leaderboard') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all">
                                <p class="font-extrabold text-amber-900 mb-1">🏆 Leaderboard</p>
                                <p class="text-sm text-amber-800/60 font-bold">See top contributors</p>
                            </a>
                            <a href="{{ route('analytics.index') }}" class="p-6 bg-amber-50 border border-amber-200 rounded-xl hover:shadow-lg transition-all focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                                <p class="font-extrabold text-amber-900 mb-1"><span aria-hidden="true">📊</span> Community insights</p>
                                <p class="text-sm text-amber-800/60 font-bold">Stats, voting trends, and recent activity — plus link to the full feed</p>
                            </a>
                        </div>
                    </div>

                    <div
                        class="bg-white border-2 border-amber-100 rounded-[2rem] p-8"
                        x-data="{
                            open: false,
                            d: {},
                            openDetail(payload) {
                                this.d = payload;
                                this.open = true;
                                document.body.classList.add('overflow-y-hidden');
                            },
                            closeDetail() {
                                this.open = false;
                                document.body.classList.remove('overflow-y-hidden');
                            },
                        }"
                        @keydown.escape.window="open && closeDetail()"
                    >
                        <h3 class="text-2xl font-extrabold text-amber-900 mb-2">🎖️ Your Achievements</h3>
                        <p class="text-sm font-bold text-amber-800/65 mb-6">Earned badges look normal; not-yet-earned ones stay grayed out (hover a faded tile to see it clearly). Use <span class="text-amber-900">How it works</span> for requirements and progress.</p>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach($achievements as $achievement)
                                @php
                                    $isUnlocked = in_array($achievement->id, $userAchievements);
                                    $target = max(1, (int) $achievement->requirement_value);
                                    $current = (int) ($progressByAchievementId[$achievement->id] ?? 0);
                                    $barPct = $isUnlocked ? 100 : min(100, (int) round(($current / $target) * 100));
                                    $achievementPopup = [
                                        'name' => $achievement->name,
                                        'emoji' => $achievement->icon_emoji,
                                        'requirement' => $achievement->requirementLabel(),
                                        'description' => $achievement->description,
                                        'current' => $current,
                                        'target' => $target,
                                        'barPct' => $barPct,
                                        'unlocked' => $isUnlocked,
                                        'url' => route('achievements.show', $achievement),
                                    ];
                                @endphp
                                <div class="flex flex-col items-center rounded-2xl border p-3 text-center transition-all {{ $isUnlocked ? 'border-amber-200 bg-amber-50 hover:shadow-md' : 'border-gray-200 bg-gray-50 opacity-40 hover:opacity-100' }}">
                                    <a href="{{ route('achievements.show', $achievement) }}" class="flex w-full flex-col items-center rounded-xl px-1 py-2 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2">
                                        <span class="text-3xl leading-none" aria-hidden="true">{{ $achievement->icon_emoji }}</span>
                                        <span class="mt-2 text-xs font-extrabold text-amber-900 leading-tight">{{ $achievement->name }}</span>
                                    </a>
                                    <button
                                        type="button"
                                        class="mt-2 w-full rounded-lg border border-amber-200/80 bg-white px-2 py-1.5 text-[10px] font-black uppercase tracking-wide text-amber-800 hover:bg-amber-50 focus-visible:outline focus-visible:ring-2 focus-visible:ring-amber-500"
                                        @click="openDetail({{ \Illuminate\Support\Js::from($achievementPopup) }})"
                                    >
                                        How it works
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        {{-- Popup: requirement + progress (dashboard only) --}}
                        <div
                            x-show="open"
                            x-cloak
                            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                            style="display: none;"
                            role="dialog"
                            aria-modal="true"
                            aria-labelledby="dash-ach-popup-title"
                        >
                            <div class="absolute inset-0 bg-amber-950/55 backdrop-blur-[1px]" @click="closeDetail()" aria-hidden="true"></div>
                            <div
                                class="relative z-10 w-full max-w-md rounded-[1.75rem] border-2 border-amber-100 bg-white p-8 shadow-xl shadow-amber-900/15"
                                @click.stop
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex min-w-0 items-start gap-3">
                                        <span class="text-4xl leading-none shrink-0" aria-hidden="true" x-text="d.emoji"></span>
                                        <div class="min-w-0">
                                            <h4 id="dash-ach-popup-title" class="text-lg font-extrabold text-amber-950 leading-tight" x-text="d.name"></h4>
                                            <p class="mt-2 text-sm font-bold text-amber-800/80 leading-relaxed" x-text="d.description"></p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        class="shrink-0 rounded-xl border border-amber-200 px-3 py-1.5 text-xs font-extrabold text-amber-900 hover:bg-amber-50"
                                        @click="closeDetail()"
                                    >
                                        Close
                                    </button>
                                </div>

                                <div class="mt-6 rounded-xl border border-amber-100 bg-amber-50/80 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/55">Requirement</p>
                                    <p class="mt-1 text-sm font-extrabold text-amber-950" x-text="d.requirement"></p>
                                </div>

                                <template x-if="d.unlocked">
                                    <p class="mt-4 text-center text-sm font-extrabold text-green-700">Unlocked — you’ve earned this badge.</p>
                                </template>
                                <template x-if="! d.unlocked">
                                    <div class="mt-4">
                                        <div class="flex items-center justify-between gap-2 text-xs font-extrabold text-amber-900">
                                            <span>Your progress</span>
                                            <span class="tabular-nums"><span x-text="d.current"></span> / <span x-text="d.target"></span> · <span x-text="d.barPct"></span>%</span>
                                        </div>
                                        <div class="relative mt-2 h-2.5 w-full overflow-hidden rounded-full bg-amber-200/80">
                                            <div class="absolute inset-y-0 left-0 rounded-full bg-amber-500 transition-all duration-300" :style="'width: ' + (d.barPct ?? 0) + '%'"></div>
                                        </div>
                                    </div>
                                </template>

                                <a
                                    :href="d.url"
                                    class="mt-6 flex w-full items-center justify-center rounded-2xl bg-amber-500 px-4 py-3 text-sm font-extrabold text-black hover:bg-amber-600"
                                >
                                    Open full achievement page
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
