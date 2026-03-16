@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Peter Trull Solitary Detective
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Part 2: Vote on the final versions of each chapter.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 bg-amber-900 text-white text-sm font-black rounded-2xl shadow-lg shadow-amber-900/20">
                    🔒 Exclusive Voting Hub
                </div>
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
                    <h3 class="text-3xl font-extrabold mb-4">Voting is restricted</h3>
                    <p class="text-amber-100/70 text-xl font-bold mb-10 max-w-2xl mx-auto leading-relaxed">Only contributors who have suggested an accepted edit in <strong>The Book With No Name</strong> can vote on these chapters.</p>
                    <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-12 py-5 bg-amber-500 text-black text-xl font-extrabold rounded-full hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                        Start Contributing Now
                        <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-black/10 rounded-full blur-3xl"></div>
            </div>
        @else
            <div class="mb-16 text-center max-w-3xl mx-auto">
                <h3 class="text-2xl font-extrabold text-amber-900 mb-4">Compare and Decide</h3>
                <p class="text-amber-800/60 text-lg font-bold leading-relaxed">Review Version A and Version B of each chapter. Your vote will determine which one makes it into the final novel. Each user can vote only once per chapter.</p>
            </div>
        @endif

        <div class="space-y-16">
            @forelse($chapters ?? [] as $chapterNum => $versions)
                @php
                    $versionA = $versions->firstWhere('version', 'A');
                    $versionB = $versions->firstWhere('version', 'B');
                    $userHasVoted = in_array($versionA->id, $hasVoted ?? []) || in_array($versionB->id, $hasVoted ?? []);
                @endphp
                @if($versionA && $versionB)
                    <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                        <div class="bg-amber-50/50 px-10 py-6 border-b border-amber-100 flex items-center justify-between">
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
                                @if($canVote ?? false)
                                    <form action="{{ route('vote.store', $versionA) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="version_chosen" value="A">
                                        <button type="submit" class="w-full px-8 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-900 disabled:hover:translate-y-0" {{ $userHasVoted ? 'disabled' : '' }}>
                                            {{ $userHasVoted ? '✓ Already Voted' : 'Vote for Version A' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                            <div class="p-10 flex flex-col bg-amber-50/20">
                                <div class="flex items-center justify-between mb-8">
                                    <h4 class="text-xl font-extrabold text-amber-900">Version B</h4>
                                    <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Option 2</span>
                                </div>
                                <p class="text-amber-900/70 text-lg leading-relaxed mb-12 flex-grow whitespace-pre-wrap">{{ $versionB->content }}</p>
                                @if($canVote ?? false)
                                    <form action="{{ route('vote.store', $versionB) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="version_chosen" value="B">
                                        <button type="submit" class="w-full px-8 py-4 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/20 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-amber-500 disabled:hover:translate-y-0" {{ $userHasVoted ? 'disabled' : '' }}>
                                            {{ $userHasVoted ? '✓ Already Voted' : 'Vote for Version B' }}
                                        </button>
                                    </form>
                                @endif
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
</x-dynamic-component>
