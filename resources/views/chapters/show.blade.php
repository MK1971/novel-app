<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $chapter->title }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="font-bold mb-4">Current Chapter Content</h3>
                <p class="whitespace-pre-wrap text-gray-700">{{ $chapter->content }}</p>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="font-bold mb-4">Suggest an Edit ($2)</h3>
                <p class="text-gray-600 mb-4">Pay $2 to submit a writing or phrase edit. If accepted fully: 2 points. Partially: 1 point.</p>
                <form action="{{ route('payment.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                    <div class="mb-4">
                        <label class="block font-medium mb-2">Edit Type</label>
                        <select name="type" class="border rounded px-3 py-2 w-full" required>
                            <option value="writing" {{ old('type') === 'writing' ? 'selected' : '' }}>Writing Edit</option>
                            <option value="phrase" {{ old('type') === 'phrase' ? 'selected' : '' }}>Phrase Edit</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block font-medium mb-2">Your Edited Text</label>
                        <textarea name="edited_text" rows="10" class="border rounded px-3 py-2 w-full" required placeholder="Enter your suggested edit...">{{ old('edited_text', $chapter->content) }}</textarea>
                        @error('edited_text')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-6 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/25">
                        Submit & Pay $2 via PayPal
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
