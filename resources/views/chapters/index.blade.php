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
                <p class="text-amber-800/60 font-bold mt-1">Read the story and shape the narrative.</p>
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

        <div class="max-w-5xl mx-auto space-y-16">
            @forelse($chapters ?? [] as $chapter)
                <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 md:p-16 relative overflow-hidden">
                    {{-- Decorative background number --}}
                    <div class="absolute -top-10 -right-10 text-[15rem] font-black text-amber-500/5 select-none">
                        {{ $chapter->number }}
                    </div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Chapter {{ $chapter->number }}</span>
                                <span class="text-amber-800/60 font-bold">Version {{ $chapter->version }}</span>
                            </div>
                            
                            @if($chapter->is_locked)
                                <div class="flex items-center gap-6 text-[10px] font-black uppercase tracking-widest bg-amber-50/50 px-6 py-3 rounded-2xl border border-amber-100/50">
                                    <div class="flex flex-col items-center">
                                        <span class="text-green-600 text-lg mb-0.5">{{ $chapter->statistics->accepted_edits ?? 0 }}</span>
                                        <span class="text-amber-900/40">Accepted</span>
                                    </div>
                                    <div class="w-px h-8 bg-amber-200/50"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-amber-600 text-lg mb-0.5">{{ $chapter->statistics->total_edits ?? 0 }}</span>
                                        <span class="text-amber-900/40">Total Edits</span>
                                    </div>
                                    <div class="w-px h-8 bg-amber-200/50"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-red-600 text-lg mb-0.5">{{ $chapter->statistics->rejected_edits ?? 0 }}</span>
                                        <span class="text-amber-900/40">Rejected</span>
                                    </div>
                                    <div class="w-px h-8 bg-amber-200/50"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-red-700 text-lg mb-0.5">🔒</span>
                                        <span class="text-red-700">Locked</span>
                                    </div>
                                </div>
                            @else
                                <div class="px-6 py-3 bg-green-100 rounded-2xl border border-green-200 text-green-700 text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    Open for Edits
                                </div>
                            @endif
                        </div>

                        <h3 class="text-4xl font-extrabold text-amber-900 mb-8">{{ $chapter->title }}</h3>
                        
                        <div class="max-w-none text-amber-900/80 leading-[2.2] font-medium text-left mb-12">
                            {!! nl2br(e($chapter->content)) !!}
                        </div>

                        <div class="flex items-center justify-between pt-8 border-t border-amber-100">
                            <div class="text-amber-800/40 text-sm font-bold">
                                Published {{ $chapter->created_at->format('M d, Y') }}
                            </div>
                            
                            @if(!$chapter->is_locked)
                                <a href="{{ route('chapters.show', $chapter) }}" class="inline-flex items-center px-10 py-5 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                                    Suggest an Edit
                                    <svg class="w-6 h-6 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                            @else
                                <div class="text-amber-800/40 font-black uppercase tracking-widest text-xs">
                                    Final Version
                                </div>
                            @endif
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
