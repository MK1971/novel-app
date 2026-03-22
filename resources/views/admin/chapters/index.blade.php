<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">
                📚 Manage Chapters
            </h2>
            <p class="text-amber-800/60 font-bold">Upload, lock, and delete chapters</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Upload chapter for The Book With No Name --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <h3 class="text-2xl font-extrabold text-amber-900 mb-2">📖 The Book With No Name</h3>
                <p class="text-amber-800/60 font-bold mb-8">Add a new chapter. Previous chapters will be automatically locked for editing.</p>
                
                <form action="{{ route('admin.chapters.store-story') }}" method="POST" class="space-y-6 mb-12">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" placeholder="Chapter Title" required>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Chapter Number</label>
                            <input type="number" name="number" value="{{ old('number', $storyChapters->count() + 1) }}" min="1" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-amber-900 font-extrabold mb-2">Content</label>
                        <textarea name="content" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-amber-600 text-white font-extrabold rounded-xl hover:bg-amber-700 transition-all shadow-lg shadow-amber-600/20">
                        Upload & Lock Previous
                    </button>
                </form>

                @if($storyChapters->isNotEmpty())
                    <div class="space-y-4">
                        <h4 class="text-lg font-extrabold text-amber-900">Existing Chapters</h4>
                        <div class="grid gap-4">
                            @foreach($storyChapters as $ch)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-100 rounded-xl">
                                    <div class="flex items-center gap-4">
                                        <span class="w-8 h-8 bg-amber-200 rounded-full flex items-center justify-center font-extrabold text-amber-900 text-xs">{{ $ch->number }}</span>
                                        <div>
                                            <p class="font-extrabold text-amber-900">{{ $ch->title }}</p>
                                            <p class="text-xs font-bold {{ $ch->is_locked ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $ch->is_locked ? '🔒 Locked' : '🔓 Open for Edits' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.chapters.toggle-lock', $ch) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="{{ $ch->is_locked ? 'Unlock' : 'Lock' }}">
                                                @if($ch->is_locked) 🔓 @else 🔒 @endif
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.chapters.destroy', $ch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this chapter?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Upload chapter for Peter Trull Solitary Detective --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <h3 class="text-2xl font-extrabold text-amber-900 mb-2">🕵️ Peter Trull Solitary Detective</h3>
                <p class="text-amber-800/60 font-bold mb-8">Add a chapter pair (Version A and B). Previous pairs will be automatically locked.</p>
                
                <form action="{{ route('admin.chapters.store-peter-trull') }}" method="POST" class="space-y-6 mb-12">
                    @csrf
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" placeholder="Chapter Title" required>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Chapter Number</label>
                            @php
                                $maxNum = $peterChapters->isEmpty() ? 1 : (int) $peterChapters->groupBy('number')->keys()->max() + 1;
                            @endphp
                            <input type="number" name="number" value="{{ old('number', $maxNum) }}" min="1" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Version A Content</label>
                            <textarea name="content_a" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content_a') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-amber-900 font-extrabold mb-2">Version B Content</label>
                            <textarea name="content_b" rows="6" class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all font-bold" required>{{ old('content_b') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="px-8 py-3 bg-green-600 text-white font-extrabold rounded-xl hover:bg-green-700 transition-all shadow-lg shadow-green-600/20">
                        Upload Pair & Lock Previous
                    </button>
                </form>

                @if($peterChapters->isNotEmpty())
                    <div class="space-y-4">
                        <h4 class="text-lg font-extrabold text-amber-900">Existing Chapter Pairs</h4>
                        <div class="grid gap-4">
                            @foreach($peterChapters->groupBy('number') as $num => $vers)
                                <div class="flex items-center justify-between p-4 bg-amber-50 border border-amber-100 rounded-xl">
                                    <div class="flex items-center gap-4">
                                        <span class="w-8 h-8 bg-amber-200 rounded-full flex items-center justify-center font-extrabold text-amber-900 text-xs">{{ $num }}</span>
                                        <div>
                                            <p class="font-extrabold text-amber-900">{{ $vers->first()->title }}</p>
                                            <p class="text-xs font-bold {{ $vers->first()->is_locked ? 'text-red-500' : 'text-green-500' }}">
                                                {{ $vers->first()->is_locked ? '🔒 Locked' : '🔓 Open for Voting' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('admin.chapters.toggle-lock', $vers->first()) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 text-amber-600 hover:bg-amber-100 rounded-lg transition-colors" title="{{ $vers->first()->is_locked ? 'Unlock' : 'Lock' }}">
                                                @if($vers->first()->is_locked) 🔓 @else 🔒 @endif
                                            </button>
                                        </form>
                                        @foreach($vers as $v)
                                            <form action="{{ route('admin.chapters.destroy', $v) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this version?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete Version {{ $v->version }}">
                                                    🗑️ ({{ $v->version }})
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
