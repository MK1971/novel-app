<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Community Feedback
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Review and manage feedback from the community.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($feedback->count() > 0)
                <div class="grid gap-8">
                    @foreach($feedback as $item)
                        <div class="bg-white border border-amber-100 shadow-sm rounded-[2.5rem] p-10 hover:shadow-md transition-shadow">
                            <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-8">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-4 py-1 bg-amber-100 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest">
                                        {{ $item->type }}
                                    </span>
                                    <span class="text-sm font-black text-amber-900/40 uppercase tracking-widest">
                                        From: <span class="text-amber-900">{{ $item->user ? $item->user->name : ($item->email ?? 'Anonymous') }}</span>
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-amber-800/40 uppercase tracking-widest">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <div class="bg-amber-50/50 rounded-3xl p-8 border border-amber-100">
                                <p class="text-amber-900 font-medium leading-relaxed text-lg italic">"{{ $item->content }}"</p>
                            </div>

                            @if($item->chapter)
                                <div class="mt-8 pt-8 border-t border-amber-50 flex items-center gap-3">
                                    <span class="text-[10px] font-black text-amber-900/30 uppercase tracking-widest">Related Chapter:</span>
                                    <a href="{{ route('chapters.show', $item->chapter) }}" class="px-4 py-1 bg-amber-900 text-white text-[10px] font-black rounded-full uppercase tracking-widest hover:bg-black transition-colors">
                                        {{ $item->chapter->title }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="mt-12">
                    {{ $feedback->links() }}
                </div>
            @else
                <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-20 text-center">
                    <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">No Feedback Yet</h3>
                    <p class="text-amber-800/60 font-bold">The community hasn't shared any thoughts yet.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
