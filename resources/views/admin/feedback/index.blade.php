<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">
            {{ __('Review Feedback') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-3xl font-bold text-amber-900 mb-8">Community Feedback</h1>
                    
                    @if($feedback->count() > 0)
                        <div class="grid gap-6">
                            @foreach($feedback as $item)
                                <div class="bg-amber-50 p-6 rounded-xl border border-amber-100 shadow-sm">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full tracking-wide mr-2">
                                                {{ $item->typeLabel() }}
                                            </span>
                                            <span class="text-sm text-amber-800">
                                                From: {{ $item->user ? $item->user->name : ($item->email ?? 'Anonymous') }}
                                            </span>
                                        </div>
                                        <span class="text-xs text-amber-800">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-amber-900 leading-relaxed">{{ $item->content }}</p>
                                    @if($item->chapter)
                                        <div class="mt-4 pt-4 border-t border-amber-100">
                                            <span class="text-xs font-bold text-amber-600 uppercase">Related Chapter:</span>
                                            <a href="{{ route('chapters.show', $item->chapter) }}" class="text-sm text-amber-900 hover:underline ml-1">{{ $item->chapter->displayTitle() }}</a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-8">
                            {{ $feedback->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-amber-700 italic">No feedback received yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
