<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Suggest Edit: {{ $chapter->title }}
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Refine the story and earn points for your contributions.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
            @endif

            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12">
                <div class="mb-12 p-8 bg-amber-50/50 rounded-[2rem] border border-amber-100">
                    <h3 class="text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Original Content Preview</h3>
                    <p class="text-amber-900/60 font-medium leading-relaxed italic">"{{ Str::limit($chapter->content, 300) }}..."</p>
                </div>

                <form action="{{ route('edits.store') }}" method="POST" class="space-y-10">
                    @csrf
                    <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">

                    <div class="grid md:grid-cols-2 gap-10">
                        <div>
                            <label class="block text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Edit Type</label>
                            <select name="type" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all appearance-none" required>
                                <option value="writing">Writing Edit (Full Chapter)</option>
                                <option value="phrase">Phrase Edit (Specific Section)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Your Suggested Version</label>
                        <textarea name="edited_text" rows="15" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-[2.5rem] px-8 py-8 text-amber-900 font-medium focus:border-amber-500 focus:ring-0 transition-all" placeholder="Rewrite the chapter or section here..." required>{{ old('edited_text', $chapter->content) }}</textarea>
                        @error('edited_text')
                            <p class="mt-4 text-sm text-red-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4 pt-8 border-t border-amber-50">
                        <button type="submit" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                            Submit Suggestion
                        </button>
                        <a href="{{ route('chapters.show', $chapter) }}" class="px-10 py-4 bg-amber-50 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-100 transition-all">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
