@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    @auth
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-amber-900 leading-tight">
                The Book With No Name - Chapters
            </h2>
        </x-slot>
    @else
        <div class="max-w-5xl mx-auto px-6 pt-12">
            <h2 class="text-center text-4xl font-extrabold text-amber-900 mb-2">The Book With No Name</h2>
            <p class="text-center text-amber-800/70 text-xl font-medium mb-12">Explore the chapters and shape the narrative.</p>
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

            <div class="grid gap-8">
                @forelse($chapters ?? [] as $chapter)
                    <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-2xl font-bold text-amber-900">Chapter {{ $chapter->number }}: {{ $chapter->title }}</h3>
                            <span class="px-4 py-1 bg-amber-100 text-amber-800 text-sm font-bold rounded-full">Version {{ $chapter->version }}</span>
                        </div>
                        <p class="text-amber-900/80 text-lg leading-relaxed mb-6">{{ Str::limit($chapter->content, 300) }}</p>
                        <div class="flex items-center justify-between">
                            <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-6 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                                Read & Suggest Edit
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white border border-amber-100 rounded-3xl">
                        <p class="text-amber-900/50 text-xl font-medium">No chapters have been written yet. Stay tuned!</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-16 flex flex-wrap justify-center gap-6">
                <a href="{{ route('leaderboard') }}" class="px-8 py-3 bg-white border-2 border-amber-200 text-amber-800 font-bold rounded-full hover:bg-amber-50 transition-colors">
                    View Leaderboard
                </a>
                <a href="{{ route('vote.index') }}" class="px-8 py-3 bg-white border-2 border-amber-200 text-amber-800 font-bold rounded-full hover:bg-amber-50 transition-colors">
                    Vote on Peter Trull
                </a>
            </div>
        </div>
    </div>

</x-dynamic-component>
