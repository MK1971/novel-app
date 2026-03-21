<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Chapter Archive</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8">Chapter Archive</h1>
                    
                    @if($archivedChapters->count() > 0)
                        <div class="grid gap-8">
                            @foreach($archivedChapters->groupBy('round_number') as $round => $chapters)
                                <div class="bg-amber-50 p-8 rounded-2xl border border-amber-100 shadow-inner">
                                    <h2 class="text-2xl font-bold text-amber-900 mb-6 flex items-center">
                                        <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Round {{ $round }}
                                    </h2>
                                    <div class="grid md:grid-cols-2 gap-6">
                                        @foreach($chapters as $chapter)
                                            <div class="bg-white p-6 rounded-xl border border-amber-100 shadow-sm hover:shadow-md transition duration-150">
                                                <h3 class="text-xl font-bold text-amber-900 mb-2">{{ $chapter->title }}</h3>
                                                <p class="text-amber-700 text-sm mb-4 line-clamp-3">{{ Str::limit($chapter->content, 150) }}</p>
                                                <div class="flex items-center justify-between">
                                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">
                                                        {{ $chapter->version_label ?? 'Final Version' }}
                                                    </span>
                                                    <a href="{{ route('chapters.show', $chapter) }}" class="text-amber-600 hover:text-amber-800 font-bold underline">Read Full</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-24">
                            <p class="text-amber-700 italic text-xl mb-8">No chapters have been archived yet.</p>
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-8 py-4 bg-amber-600 border border-transparent rounded-full font-bold text-lg text-white hover:bg-amber-700 transition ease-in-out duration-150 shadow-lg">
                                Browse Current Chapters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
