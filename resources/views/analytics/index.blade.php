@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
            Community Analytics & Insights
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Vote Analytics -->
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-8 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Peter Trull Voting Trends
                </h2>
                
                @if($voteStats->count() > 0)
                    <div class="space-y-8">
                        @foreach($voteStats->groupBy('chapter_number') as $chapterNum => $stats)
                            <div class="bg-amber-50/50 p-8 rounded-[2rem] border border-amber-100">
                                <h3 class="text-lg font-black text-amber-900 mb-6 uppercase tracking-widest">Chapter {{ $chapterNum }}</h3>
                                <div class="space-y-6">
                                    @php
                                        $totalVotes = $stats->sum('total');
                                    @endphp
                                    @foreach($stats as $stat)
                                        @php
                                            $percentage = ($stat->total / $totalVotes) * 100;
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

            <!-- Chapter Analytics -->
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-8 flex items-center">
                    <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Contribution Activity
                </h2>
                
                @if($chapterStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($chapterStats as $chapter)
                            <div class="flex items-center justify-between p-6 bg-amber-50/50 rounded-2xl border border-amber-100 hover:bg-amber-50 transition-all">
                                <div class="flex flex-col">
                                    <span class="font-black text-amber-900">Chapter {{ $chapter->number }}</span>
                                    <span class="text-xs font-bold text-amber-800/40">{{ $chapter->title }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="px-4 py-1 bg-amber-100 text-amber-700 text-xs font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->edits_count }} Edits
                                    </span>
                                    <span class="px-4 py-1 bg-amber-900 text-white text-xs font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->inline_edits_count }} Inline
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
