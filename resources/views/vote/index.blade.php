@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    @auth
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-amber-900 leading-tight">Peter Trull Solitary Detective - Vote</h2>
        </x-slot>
    @else
        <nav class="border-b border-amber-200/60 bg-white/80 backdrop-blur-sm sticky top-0 z-40">
            <div class="max-w-5xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="text-xl font-extrabold text-amber-800 hover:text-amber-600 transition-colors">
                    What's My Book Name
                </a>
                <div class="flex items-center gap-6 md:gap-8">
                    @if($topLeader)
                        <div class="hidden sm:block text-amber-700 font-bold">
                            🏆 Leader: {{ $topLeader->name }} ({{ $topLeader->points }} pts)
                        </div>
                    @endif
                    <div class="flex gap-4">
                        <button type="button" x-data @click="$dispatch('open-modal', 'login-modal')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">Sign In</button>
                        <button type="button" x-data @click="$dispatch('open-modal', 'register-modal')" class="px-4 py-2 bg-amber-500 text-black font-semibold rounded-full hover:bg-amber-600 transition-colors shrink-0">
                            Create Account
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <div class="max-w-5xl mx-auto px-6 pt-12">
            <h2 class="text-center text-4xl font-extrabold text-amber-900 mb-2">Peter Trull Solitary Detective</h2>
            <p class="text-center text-amber-800/70 text-xl font-medium mb-12">Vote on the final versions of each chapter.</p>
        </div>
    @endauth

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 shadow-sm">{{ session('error') }}</div>
            @endif

            @if (!($canVote ?? false))
                <div class="mb-12 p-8 bg-amber-100/50 border border-amber-200 rounded-3xl text-center">
                    <h3 class="text-xl font-bold text-amber-900 mb-2">Voting is restricted</h3>
                    <p class="text-amber-800/70 text-lg">Only contributors who have suggested an edit in <strong>The Book With No Name</strong> can vote on these chapters.</p>
                    <a href="{{ route('chapters.index') }}" class="inline-block mt-6 px-8 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                        Start Contributing Now →
                    </a>
                </div>
            @else
                <p class="mb-12 text-center text-amber-800/70 text-lg font-medium">Compare Version A and Version B of each chapter and vote for your favorite.</p>
            @endif

            <div class="space-y-12">
                @forelse($chapters ?? [] as $chapterNum => $versions)
                    @php
                        $versionA = $versions->firstWhere('version', 'A');
                        $versionB = $versions->firstWhere('version', 'B');
                    @endphp
                    @if($versionA && $versionB)
                        <div class="bg-white border border-amber-100 shadow-sm rounded-3xl overflow-hidden">
                            <div class="bg-amber-50 px-8 py-4 border-b border-amber-100">
                                <h3 class="text-xl font-bold text-amber-900">Chapter {{ $chapterNum }}: {{ $versionA->title }}</h3>
                            </div>
                            <div class="grid md:grid-cols-2 divide-x divide-amber-50">
                                <div class="p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-lg font-bold text-amber-900">Version A</h4>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full uppercase">Option 1</span>
                                    </div>
                                    <p class="text-amber-900/80 leading-relaxed mb-8 whitespace-pre-wrap">{{ $versionA->content }}</p>
                                    @if($canVote ?? false)
                                        <form action="{{ route('vote.store', $versionA) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="version_chosen" value="A">
                                            <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                                Vote for Version A
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="p-8">
                                    <div class="flex items-center justify-between mb-6">
                                        <h4 class="text-lg font-bold text-amber-900">Version B</h4>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full uppercase">Option 2</span>
                                    </div>
                                    <p class="text-amber-900/80 leading-relaxed mb-8 whitespace-pre-wrap">{{ $versionB->content }}</p>
                                    @if($canVote ?? false)
                                        <form action="{{ route('vote.store', $versionB) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="version_chosen" value="B">
                                            <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                                                Vote for Version B
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-20 bg-white border border-amber-100 rounded-3xl">
                        <p class="text-amber-900/50 text-xl font-medium">No chapter pairs have been uploaded for voting yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @guest
        @include('auth.modals')
    @endguest
</x-dynamic-component>
