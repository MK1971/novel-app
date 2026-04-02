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
                At-a-glance metrics for <span class="text-amber-900">Peter Trull Solitary Detective</span> voting and <span class="text-amber-900">The Book With No Name</span> manuscript contributions.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- MVP summary strip --}}
        <div class="grid sm:grid-cols-2 gap-6 mb-12 max-w-3xl">
            <div class="bg-white border border-amber-100 rounded-3xl p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-800/50 mb-1">Votes cast</p>
                <p class="text-3xl font-black text-amber-900">{{ number_format($insightSummary['total_votes']) }}</p>
                <p class="text-xs font-bold text-amber-800/50 mt-1">All Peter Trull Solitary Detective ballots</p>
            </div>
            <div class="bg-white border border-amber-100 rounded-3xl p-6 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-800/50 mb-1">Edits in review</p>
                <p class="text-3xl font-black text-amber-900">{{ number_format($insightSummary['pending_edits']) }}</p>
                <p class="text-xs font-bold text-amber-800/50 mt-1">Full-chapter + paragraph suggestions awaiting moderation</p>
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
                                <h3 class="text-lg font-black text-amber-900 mb-6">{{ $stats->first()->chapter_heading }}</h3>
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
                <p class="text-sm font-bold text-amber-800/50 mb-6 -mt-4">Counts come from chapter statistics (updated when payments complete and moderators act) plus live pending queue.</p>

                @if($chapterStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($chapterStats as $chapter)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 bg-amber-50/50 rounded-2xl border border-amber-100 hover:bg-amber-50 transition-all">
                                <div class="flex flex-col min-w-0">
                                    <span class="font-black text-amber-900 truncate">{{ $chapter->insightDisplayLabel() }}</span>
                                    @if(filled(trim((string) ($chapter->title ?? ''))))
                                        <span class="text-xs font-bold text-amber-800/40">Chapter {{ $chapter->number }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-col items-stretch sm:items-end gap-2 shrink-0">
                                    @if($chapter->insight_pending > 0)
                                        <span class="px-4 py-1 bg-amber-200 text-amber-900 text-xs font-black rounded-full uppercase tracking-widest text-center sm:text-right">
                                            {{ $chapter->insight_pending }} in queue
                                        </span>
                                    @endif
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-[10px] font-black rounded-full uppercase tracking-widest" title="Suggestions accepted by moderators">
                                            {{ $chapter->insight_accepted }} accepted
                                        </span>
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-[10px] font-black rounded-full uppercase tracking-widest" title="Suggestions rejected">
                                            {{ $chapter->insight_rejected }} rejected
                                        </span>
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest" title="Paid submissions (chapter + paragraph)">
                                            {{ $chapter->insight_submitted }} paid
                                        </span>
                                    </div>
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
