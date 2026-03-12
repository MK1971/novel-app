<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin - Upload Chapters</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-12">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="flex gap-4 mb-8">
                <a href="{{ route('admin.edits.index') }}" class="px-4 py-2 bg-amber-500 text-black font-semibold rounded hover:bg-amber-600">
                    Review Suggestions →
                </a>
            </div>

            {{-- Upload chapter for The Book With No Name --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Upload Chapter: The Book With No Name</h3>
                <p class="text-gray-600 mb-4">Add a new chapter to the story where users can suggest edits.</p>
                <form action="{{ route('admin.chapters.store-story') }}" method="POST">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2 mb-4">
                        <div>
                            <label class="block font-medium mb-1">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="border rounded px-3 py-2 w-full" placeholder="Chapter 2" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Chapter Number</label>
                            <input type="number" name="number" value="{{ old('number', $storyChapters->count() + 1) }}" min="1" class="border rounded px-3 py-2 w-full" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium mb-1">Content</label>
                        <textarea name="content" rows="8" class="border rounded px-3 py-2 w-full" required>{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload Chapter</button>
                </form>
                @if($storyChapters->isNotEmpty())
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-medium mb-2">Existing chapters</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach($storyChapters as $ch)
                                <li>{{ $ch->number }}. {{ $ch->title }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Upload chapter for Peter Trull Solitary Detective --}}
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Upload Chapter: Peter Trull Solitary Detective</h3>
                <p class="text-gray-600 mb-4">Add a chapter pair (Version A and B) for users to vote on.</p>
                <form action="{{ route('admin.chapters.store-peter-trull') }}" method="POST">
                    @csrf
                    <div class="grid gap-4 md:grid-cols-2 mb-4">
                        <div>
                            <label class="block font-medium mb-1">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="border rounded px-3 py-2 w-full" placeholder="Chapter 2" required>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Chapter Number</label>
                            @php
                                $maxNum = $peterChapters->isEmpty() ? 1 : (int) $peterChapters->groupBy('number')->keys()->max() + 1;
                            @endphp
                            <input type="number" name="number" value="{{ old('number', $maxNum) }}" min="1" class="border rounded px-3 py-2 w-full" required>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block font-medium mb-1">Version A Content</label>
                            <textarea name="content_a" rows="8" class="border rounded px-3 py-2 w-full" required>{{ old('content_a') }}</textarea>
                        </div>
                        <div>
                            <label class="block font-medium mb-1">Version B Content</label>
                            <textarea name="content_b" rows="8" class="border rounded px-3 py-2 w-full" required>{{ old('content_b') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Upload Chapter Pair</button>
                </form>
                @if($peterChapters->isNotEmpty())
                    <div class="mt-6 pt-6 border-t">
                        <h4 class="font-medium mb-2">Existing chapters</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach($peterChapters->groupBy('number') as $num => $vers)
                                <li>Ch. {{ $num }}: A & B</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
