<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8">Community Analytics & Insights</h1>
                    
                    <div class="grid md:grid-cols-2 gap-12">
                        <!-- Vote Analytics -->
                        <div class="bg-amber-50 p-8 rounded-2xl border border-amber-100 shadow-inner">
                            <h2 class="text-2xl font-bold text-amber-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Peter Trull Voting Trends
                            </h2>
                            @if($voteStats->count() > 0)
                                <div class="space-y-6">
                                    @foreach($voteStats->groupBy('round_number') as $round => $stats)
                                        <div class="bg-white p-6 rounded-xl border border-amber-100 shadow-sm">
                                            <h3 class="text-lg font-bold text-amber-900 mb-4">Round {{ $round }}</h3>
                                            <div class="space-y-4">
                                                @php
                                                    $totalVotes = $stats->sum('total');
                                                @endphp
                                                @foreach($stats as $stat)
                                                    @php
                                                        $percentage = ($stat->total / $totalVotes) * 100;
                                                    @endphp
                                                    <div>
                                                        <div class="flex justify-between text-sm font-bold text-amber-800 mb-1">
                                                            <span>Version {{ $stat->version_voted }}</span>
                                                            <span>{{ round($percentage) }}% ({{ $stat->total }} votes)</span>
                                                        </div>
                                                        <div class="w-full bg-amber-100 rounded-full h-2.5">
                                                            <div class="bg-amber-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-amber-700 italic text-center py-12">No voting data available yet.</p>
                            @endif
                        </div>

                        <!-- Chapter Analytics -->
                        <div class="bg-amber-50 p-8 rounded-2xl border border-amber-100 shadow-inner">
                            <h2 class="text-2xl font-bold text-amber-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Contribution Activity
                            </h2>
                            @if($chapterStats->count() > 0)
                                <div class="space-y-4">
                                    @foreach($chapterStats as $chapter)
                                        <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-amber-100 shadow-sm">
                                            <span class="font-bold text-amber-900">{{ $chapter->title }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">
                                                    {{ $chapter->edits_count }} Edits
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-amber-700 italic text-center py-12">No contribution data available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
