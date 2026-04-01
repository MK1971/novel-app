<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suggest Edit - {{ $chapter->displayTitle() }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <p class="mb-4 text-gray-600">Original: {{ Str::limit($chapter->content, 100) }}...</p>
                <form action="{{ route('edits.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                    <div class="mb-4">
                        <label class="block font-medium mb-2">Edit Type</label>
                        <select name="type" class="border rounded px-3 py-2 w-full" required>
                            <option value="writing">Writing Edit</option>
                            <option value="phrase">Phrase Edit</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium mb-2">Your Edited Text</label>
                        <textarea name="edited_text" rows="10" class="border rounded px-3 py-2 w-full" required>{{ old('edited_text', $chapter->content) }}</textarea>
                        @error('edited_text')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit Edit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
