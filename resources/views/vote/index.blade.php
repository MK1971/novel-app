<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Peter Trull: Solitary Detective
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Vote on the next chapter of the detective's journey.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 bg-amber-500 text-black text-sm font-black rounded-2xl shadow-lg shadow-amber-500/20">
                    🗳️ Voting Eligibility: {{ $canVote ? 'Active' : 'Inactive' }}
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(!$canVote)
                <div class="bg-amber-50 border border-amber-200 rounded-[2.5rem] p-10 mb-12 text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-amber-900 mb-2">Voting is Locked</h3>
                    <p class="text-amber-800/60 font-bold mb-6">You must have at least one accepted edit to participate in voting.</p>
                    <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-8 py-3 bg-amber-500 text-black font-black rounded-xl hover:bg-amber-600 transition-all">
                        Start Contributing
                    </a>
                </div>
            @endif

            @forelse($chapters->groupBy('number') as $chapterNum => $chapterGroup)
                @php
                    $versionA = $chapterGroup->where('version', 'A')->first();
                    $versionB = $chapterGroup->where('version', 'B')->first();
                    $userHasVoted = in_array($versionA?->id, $userVotes) || in_array($versionB?->id, $userVotes);
                @endphp

                @if($versionA && $versionB)
                    <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden mb-12">
                        <div class="p-10 bg-amber-50/50 border-b border-amber-100 flex items-center justify-between">
                            <h3 class="text-2xl font-extrabold text-amber-900">Chapter {{ $chapterNum }}: {{ $versionA->title }}</h3>
                            <div class="flex items-center gap-2">
                                @if($userHasVoted)
                                    <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                    <span class="text-xs font-black text-green-700 uppercase tracking-widest">✓ Voted</span>
                                @else
                                    <span class="w-3 h-3 bg-amber-500 rounded-full animate-pulse"></span>
                                    <span class="text-xs font-black text-amber-900/40 uppercase tracking-widest">Voting Active</span>
                                @endif
                            </div>
                        </div>
                        <div class="grid md:grid-cols-2 divide-x divide-amber-50">
                            <div class="p-10 flex flex-col">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-extrabold text-amber-900">Version A</h4>
                                    <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-black rounded-full uppercase tracking-widest">Option 1</span>
                                </div>
                                <p class="text-amber-900/70 text-lg leading-relaxed mb-12 flex-grow whitespace-pre-wrap">{{ $versionA->content }}</p>
                                <form action="{{ route('vote.store', $versionA) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="version" value="A">
                                    <button type="submit" class="w-full px-8 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-900 disabled:hover:translate-y-0" {{ ($userHasVoted || !$canVote) ? 'disabled' : '' }}>
                                        {{ $userHasVoted ? '✓ Already Voted' : 'Vote for Version A' }}
                                    </button>
                                </form>
                            </div>
                            <div class="p-10 flex flex-col bg-amber-50/20">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-extrabold text-amber-900">Version B</h4>
                                    <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Option 2</span>
                                </div>
                                <p class="text-amber-900/70 text-lg leading-relaxed mb-12 flex-grow whitespace-pre-wrap">{{ $versionB->content }}</p>
                                <form action="{{ route('vote.store', $versionB) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="version" value="B">
                                    <button type="submit" class="w-full px-8 py-4 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-500 disabled:hover:translate-y-0" {{ ($userHasVoted || !$canVote) ? 'disabled' : '' }}>
                                        {{ $userHasVoted ? '✓ Already Voted' : 'Vote for Version B' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
        </div>
    </div>
</x-app-layout>
