<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Project Analytics
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Real-time data on voting trends and community contributions.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                {{-- Voting Trends --}}
                <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                    <div class="p-10 border-b border-amber-50 flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-amber-900">Peter Trull Voting</h3>
                        <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-black rounded-full uppercase tracking-widest">Live Trends</span>
                    </div>
                    <div class="p-10 space-y-8">
                        @forelse($voteStats->groupBy('chapter_id') as $chapterId => $stats)
                            <div class="bg-amber-50/50 p-8 rounded-[2rem] border border-amber-100">
                                <h4 class="text-lg font-extrabold text-amber-900 mb-6">Chapter #{{ $chapterId }}</h4>
                                <div class="space-y-6">
                                    @php $totalVotes = $stats->sum('total'); @endphp
                                    @foreach($stats as $stat)
                                        @php $percentage = ($stat->total / $totalVotes) * 100; @endphp
                                        <div>
                                            <div class="flex justify-between text-xs font-black text-amber-900/40 uppercase tracking-widest mb-2">
                                                <span>Version {{ $stat->version_chosen }}</span>
                                                <span>{{ round($percentage) }}% ({{ $stat->total }} votes)</span>
                                            </div>
                                            <div class="w-full bg-amber-100 rounded-full h-3 overflow-hidden">
                                                <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-amber-900/40 font-bold">No voting data available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Contribution Activity --}}
                <div class="bg-amber-900 rounded-[3rem] p-10 text-amber-50 shadow-xl shadow-amber-900/20">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-2xl font-extrabold">Contribution Activity</h3>
                        <span class="px-4 py-1 bg-white/10 text-amber-400 text-xs font-black rounded-full uppercase tracking-widest">The Book With No Name</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($chapterStats as $chapter)
                            <div class="flex items-center justify-between p-6 bg-white/5 rounded-3xl border border-white/10 hover:bg-white/10 transition-all group">
                                <span class="font-bold text-lg group-hover:text-amber-400 transition-colors">{{ $chapter->title }}</span>
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 bg-amber-500 text-black text-[10px] font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->edits_count }} Edits
                                    </span>
                                    <span class="px-3 py-1 bg-white/10 text-amber-400 text-[10px] font-black rounded-full uppercase tracking-widest">
                                        {{ $chapter->inline_edits_count }} Inline
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <p class="text-amber-50/40 font-bold">No contribution data available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
