@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="max-w-4xl">
            <h1 class="font-extrabold text-3xl text-amber-900 leading-tight">
                Community insights
            </h1>
            <p class="text-amber-800/70 font-bold mt-2">
                At-a-glance metrics for <span class="text-amber-900">Peter Trull Solitary Detective</span> voting, manuscript activity, and recent community actions.
                <a href="{{ route('activity-feed.index') }}" class="text-amber-700 underline decoration-amber-300 hover:text-amber-900 font-extrabold">Open full activity stream →</a>
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- MVP summary strip --}}
        <div class="grid sm:grid-cols-3 gap-6 mb-12">
            <div class="bg-white border border-amber-100 rounded-3xl p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-800/50 mb-1">Votes cast</p>
                <p class="text-3xl font-black text-amber-900">{{ number_format($insightSummary['total_votes']) }}</p>
                <p class="text-xs font-bold text-amber-800/50 mt-1">All Peter Trull Solitary Detective ballots</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-3xl p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-800/50 mb-1">Edits in review</p>
                <p class="text-3xl font-black text-amber-900">{{ number_format($insightSummary['pending_edits']) }}</p>
                <p class="text-xs font-bold text-amber-800/50 mt-1">Pending moderator queue</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-3xl p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-800/50 mb-1">Feed events (7d)</p>
                <p class="text-3xl font-black text-amber-900">{{ number_format($insightSummary['activities_7d']) }}</p>
                <p class="text-xs font-bold text-amber-800/50 mt-1">Public activity log entries</p>
            </div>
        </div>

        {{-- Recent activity preview (P3-4) --}}
        <div class="mb-14 max-w-4xl">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
                <h2 class="text-xl font-extrabold text-amber-900">Recent activity</h2>
                <a href="{{ route('activity-feed.index') }}" class="text-sm font-extrabold text-amber-700 hover:text-amber-900 underline decoration-amber-300">View all →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                    <div class="flex gap-4 items-start bg-amber-50/80 border border-amber-100 rounded-2xl px-5 py-4">
                        <div class="w-10 h-10 shrink-0 bg-amber-200 text-amber-900 rounded-full flex items-center justify-center text-sm font-black" aria-hidden="true">
                            {{ $activity->user ? strtoupper(substr($activity->user->name, 0, 1)) : '?' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-amber-900">
                                <span class="text-amber-800/70">{{ $activity->user?->name ?? 'Community' }}</span>
                                <span class="mx-1 text-amber-800/40">·</span>
                                {{ $activity->description }}
                            </p>
                            <p class="text-xs font-bold text-amber-800/50 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-amber-800/50 font-bold text-sm py-8 text-center border border-dashed border-amber-200 rounded-2xl">No activity entries yet.</p>
                @endforelse
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-8 flex items-center gap-3">
                    <svg class="w-8 h-8 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Peter Trull Solitary Detective — voting trends
                </h2>

                @if($voteStats->count() > 0)
                    <div class="space-y-8">
                        @foreach($voteStats->groupBy('chapter_group_key') as $groupKey => $stats)
                            <div class="bg-amber-50/50 p-8 rounded-[2rem] border border-amber-100">
                                <h3 class="text-lg font-black text-amber-900 mb-6 uppercase tracking-widest">{{ $stats->first()->chapter_heading }}</h3>
                                <div class="space-y-6">
                                    @php
                                        $totalVotes = $stats->sum('total');
                                    @endphp
                                    @foreach($stats as $stat)
                                        @php
                                            $percentage = $totalVotes > 0 ? ($stat->total / $totalVotes) * 100 : 0;
                                        @endphp
                                        <div>
                                            <div class="flex justify-between text-sm font-black text-amber-800 mb-2">
                                                <span>Version {{ $stat->version_chosen }}</span>
                                                <span>{{ round($percentage) }}% ({{ $stat->total }} votes)</span>
                                            </div>
                                            <div class="w-full bg-amber-100 rounded-full h-3">
                                                <div class="bg-amber-600 h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-amber-50/50 rounded-[2rem] border border-dashed border-amber-200">
                        <p class="text-amber-800/40 font-bold italic">No voting data available yet.</p>
                    </div>
                @endif
            </div>

            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-8 flex items-center gap-3">
                    <svg class="w-8 h-8 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Manuscript contributions by chapter
                </h2>

                @if($chapterStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($chapterStats as $chapter)
                            <div class="flex items-center justify-between p-6 bg-amber-50/50 rounded-2xl border border-amber-100 hover:bg-amber-50 transition-all">
                                <div class="flex flex-col min-w-0">
                                    <span class="font-black text-amber-900">{{ $chapter->headingPrefix() }}</span>
                                    <span class="text-xs font-bold text-amber-800/40 truncate">{{ $chapter->title }}</span>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="px-4 py-1 bg-amber-100 text-amber-700 text-xs font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->edits_count }} edits
                                    </span>
                                    <span class="px-4 py-1 bg-amber-900 text-white text-xs font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->inline_edits_count }} inline
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-20 bg-amber-50/50 rounded-[2rem] border border-dashed border-amber-200">
                        <p class="text-amber-800/40 font-bold italic">No contribution data available yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
