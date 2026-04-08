<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suggest Edit — {{ $chapter->readerHeadingLine() }}</h2>
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
                        <textarea name="edited_text" id="edited_text" rows="10" class="border rounded px-3 py-2 w-full" required>{{ old('edited_text', $chapter->content) }}</textarea>
                        @error('edited_text')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" id="novel-btn-preview-edit-diff" class="px-4 py-2 rounded-lg bg-amber-100 text-amber-900 text-sm font-bold border border-amber-200 hover:bg-amber-200">
                                Preview changes vs published
                            </button>
                            <button type="button" id="novel-btn-clear-edit-draft" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-800 text-sm font-bold border border-gray-200">
                                Discard local draft
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 font-medium">The same preview is available on the chapter page under <strong class="text-gray-700">Suggest an Edit</strong> (sidebar), if you prefer to work there.</p>
                        <div id="novel-edit-diff-preview" class="hidden mt-3 rounded-lg border border-amber-200 bg-slate-900 p-4 text-left flex flex-col gap-3" aria-live="polite"></div>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit Edit</button>
                </form>
            </div>
        </div>
    </div>

    @include('chapters.partials.whole-edit-draft-preview-script', [
        'chapter' => $chapter,
        'preferServerDraft' => false,
        'editedTextBaseline' => old('edited_text', $chapter->content),
    ])
</x-app-layout>
