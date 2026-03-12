<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin - Review Suggestions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif

            <div class="flex gap-4 mb-8">
                <a href="{{ route('admin.chapters.index') }}" class="px-4 py-2 bg-amber-500 text-black font-semibold rounded hover:bg-amber-600">
                    Upload Chapters →
                </a>
            </div>

            @forelse($edits as $edit)
                <div class="bg-white shadow rounded-lg p-6 mb-4">
                    <p><strong>By:</strong> {{ $edit->user->name }}</p>
                    <p><strong>Chapter:</strong> {{ $edit->chapter->title }}</p>
                    <p><strong>Type:</strong> {{ $edit->type }}</p>
                    <p><strong>Original:</strong> {{ Str::limit($edit->original_text, 200) }}</p>
                    <p><strong>Edited:</strong> {{ Str::limit($edit->edited_text, 200) }}</p>
                    <div class="mt-4 flex gap-2">
                        <form action="{{ route('admin.edits.approve', $edit) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="accepted_full">
                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">Accept Full (2 pts)</button>
                        </form>
                        <form action="{{ route('admin.edits.approve', $edit) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="accepted_partial">
                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Accept Partial (1 pt)</button>
                        </form>
                        <form action="{{ route('admin.edits.reject', $edit) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">Reject</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-600">No pending edits.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
