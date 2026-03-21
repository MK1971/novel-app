@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp
<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    The Book With No Name
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Part 1: Suggest edits and shape the narrative.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 bg-amber-100 rounded-2xl border border-amber-200/50 text-amber-900 text-sm font-bold">
                    <span class="opacity-60 mr-1">Status:</span>
                    <span class="text-green-600">Open for Edits</span>
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
        <div class="grid gap-10">
            @forelse($chapters ?? [] as $chapter)
                <div class="bg-white border border-amber-100 shadow-sm rounded-[2.5rem] p-10 hover:shadow-xl transition-all group relative overflow-hidden">
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[12rem] font-black text-amber-500/5 select-none group-hover:text-amber-500/10 transition-colors">
                        {{ $chapter->number }}
                    </div>
                    <div class="relative z-10">
                        <div class="flex flex-wrap items-center gap-4 mb-6">
                            <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Chapter {{ $chapter->number }}</span>
                            <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">Version {{ $chapter->version }}</span>
                        </div>
                        
                        <h3 class="text-3xl font-extrabold text-amber-900 mb-6 group-hover:text-amber-600 transition-colors">{{ $chapter->title }}</h3>
                        
                        <p class="text-amber-900/70 text-lg leading-relaxed mb-10 max-w-3xl">
                            {{ Str::limit($chapter->content, 350) }}
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-6">
                            <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-8 py-4 bg-amber-500 text-black font-extrabold rounded-full hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20 transform hover:-translate-y-0.5">
                                Read & Suggest Edit
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                            
                            <div class="flex items-center text-amber-900/40 text-sm font-bold">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Updated {{ $chapter->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-32 bg-white border border-amber-100 rounded-[3rem] shadow-sm">
                    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">The ink is still drying...</h3>
                    <p class="text-amber-800/50 text-lg font-bold">No chapters have been published yet. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</x-dynamic-component>
