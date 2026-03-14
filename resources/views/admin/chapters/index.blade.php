<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Admin - Upload Chapters</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-6 space-y-12">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="flex gap-4 mb-8">
                <a href="{{ route('admin.edits.index') }}" class="px-6 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                    Review Suggestions →
                </a>
            </div>

            {{-- Upload chapter for The Book With No Name --}}
            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8">
                <h3 class="text-2xl font-bold text-amber-900 mb-4">Upload Chapter: The Book With No Name</h3>
                <p class="text-amber-800/70 mb-8">Add a new chapter to the story where users can suggest edits.</p>
                
                <form action="{{ route('admin.chapters.store-story') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" placeholder="Chapter 2" required>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Chapter Number</label>
                            <input type="number" name="number" value="{{ old('number', $storyChapters->count() + 1) }}" min="1" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-amber-900 font-bold mb-2">Content</label>
                        <textarea name="content" rows="8" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">Upload Chapter</button>
                </form>

                @if($storyChapters->isNotEmpty())
                    <div class="mt-10 pt-8 border-t border-amber-50">
                        <h4 class="text-lg font-bold text-amber-900 mb-4">Existing Chapters</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($storyChapters as $ch)
                                <div class="bg-amber-50 rounded-xl p-3 text-sm text-amber-900 font-medium border border-amber-100">
                                    {{ $ch->number }}. {{ $ch->title }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Upload chapter for Peter Trull Solitary Detective --}}
            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8">
                <h3 class="text-2xl font-bold text-amber-900 mb-4">Upload Chapter: Peter Trull Solitary Detective</h3>
                <p class="text-amber-800/70 mb-8">Add a chapter pair (Version A and B) for users to vote on.</p>
                
                <form action="{{ route('admin.chapters.store-peter-trull') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" placeholder="Chapter 2" required>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Chapter Number</label>
                            @php
                                $maxNum = $peterChapters->isEmpty() ? 1 : (int) $peterChapters->groupBy('number')->keys()->max() + 1;
                            @endphp
                            <input type="number" name="number" value="{{ old('number', $maxNum) }}" min="1" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Version A Content</label>
                            <textarea name="content_a" rows="8" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>{{ old('content_a') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Version B Content</label>
                            <textarea name="content_b" rows="8" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>{{ old('content_b') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">Upload Chapter Pair</button>
                </form>

                @if($peterChapters->isNotEmpty())
                    <div class="mt-10 pt-8 border-t border-amber-50">
                        <h4 class="text-lg font-bold text-amber-900 mb-4">Existing Chapter Pairs</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($peterChapters->groupBy('number') as $num => $vers)
                                <div class="bg-amber-50 rounded-xl p-3 text-sm text-amber-900 font-medium border border-amber-100">
                                    Ch. {{ $num }}: A & B
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
