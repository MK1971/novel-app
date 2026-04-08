<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Admin - Review Suggestions</h2>
        <p class="text-amber-800/60 text-sm font-bold mt-2 max-w-3xl">
            Paid <strong class="text-amber-900">paragraph</strong> and <strong class="text-amber-900">full-chapter</strong> suggestions both appear on this page. The dedicated
            <a href="{{ route('admin.inline-edits.index') }}" class="text-amber-900 underline font-extrabold hover:text-amber-600">Paragraph edits</a>
            screen is the same queue with pagination.
        </p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="space-y-14">
                <section aria-labelledby="heading-paragraph-edits">
                    <h3 id="heading-paragraph-edits" class="text-lg font-extrabold text-amber-900 mb-2">Paragraph-level (paid checkout)</h3>
                    <p class="text-amber-800/50 text-sm font-bold mb-6">Same <strong class="text-amber-900">2 / 1 / 0</strong> point scale as full-chapter suggestions. When you accept, you can <strong class="text-amber-900">edit the merge text</strong> below (typos, house style) before it is stored — the green preview on Manage Chapters uses that final wording.</p>
                    <div class="grid gap-8">
                        @forelse($pendingInlineEdits ?? [] as $inlineEdit)
                            <div class="bg-white border-2 border-emerald-100 shadow-sm rounded-3xl p-8">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-amber-900">{{ $inlineEdit->chapter->readerHeadingLine() }}</h3>
                                        <p class="text-amber-800/70 font-medium">By {{ $inlineEdit->user->name }} • Paragraph #{{ $inlineEdit->paragraph_number }} • {{ $inlineEdit->created_at->diffForHumans() }}</p>
                                    </div>
                                    <span class="px-4 py-1 bg-emerald-100 text-emerald-900 text-xs font-black rounded-full uppercase tracking-widest">Paragraph</span>
                                </div>
                                <div class="grid md:grid-cols-2 gap-8 mb-8">
                                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                                        <h4 class="text-sm font-bold text-amber-900/50 uppercase tracking-wider mb-3">Original</h4>
                                        <p class="text-amber-900/80 leading-relaxed whitespace-pre-wrap">{{ $inlineEdit->original_text }}</p>
                                    </div>
                                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                                        <h4 class="text-sm font-bold text-green-900/50 uppercase tracking-wider mb-3">Reader suggested</h4>
                                        <p class="text-green-900 leading-relaxed font-medium whitespace-pre-wrap">{{ $inlineEdit->suggested_text }}</p>
                                    </div>
                                </div>
                                @if($inlineEdit->reason)
                                    <div class="mb-8 p-6 bg-blue-50/50 border border-blue-100 rounded-2xl">
                                        <h4 class="text-xs font-black text-blue-900/40 uppercase tracking-widest mb-2">Reason</h4>
                                        <p class="text-blue-900 font-bold text-sm">{{ $inlineEdit->reason }}</p>
                                    </div>
                                @endif
                                <div class="space-y-4 pt-6 border-t border-amber-100">
                                    <form action="{{ route('admin.inline-edits.approve', $inlineEdit) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-extrabold text-amber-900 mb-1">Text that will merge into the chapter</label>
                                            <p class="text-xs font-bold text-amber-700/70 mb-2">Pre-filled with the reader’s suggestion. Change it here before accepting if you want different wording in the manuscript and in the green merge preview.</p>
                                            <textarea name="merged_text" rows="6" class="w-full rounded-2xl border-2 border-emerald-200 bg-white px-4 py-3 text-amber-900 font-medium whitespace-pre-wrap">{{ $inlineEdit->suggested_text }}</textarea>
                                        </div>
                                        <div class="flex flex-wrap gap-4">
                                            <button type="submit" class="px-6 py-2 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                                                Accept full (2 pts)
                                            </button>
                                            <button type="submit" name="partial" value="1" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                                Accept partial (1 pt)
                                            </button>
                                        </div>
                                    </form>
                                    <form action="{{ route('admin.inline-edits.reject', $inlineEdit) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 bg-red-600 text-white font-bold rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">
                                            Reject (0 pts)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-amber-800/60 text-sm font-bold">No pending paragraph suggestions.</p>
                        @endforelse
                    </div>
                </section>

                <section aria-labelledby="heading-full-chapter">
                    <h3 id="heading-full-chapter" class="text-lg font-extrabold text-amber-900 mb-2">Full-chapter / phrase (paid checkout)</h3>
                    <p class="text-amber-800/50 text-sm font-bold mb-6">Whole-chapter / phrase suggestions from the $2 flow. You can <strong class="text-amber-900">edit the merge text</strong> before accepting so the stored replacement matches house style; the green preview on Manage Chapters uses that text.</p>
                    <div class="grid gap-8">
                        @forelse($edits as $edit)
                            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8">
                                <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-amber-900">Suggestion for {{ $edit->chapter->readerHeadingLine() }}</h3>
                                        <p class="text-amber-800/70 font-medium">By {{ $edit->user->name }} • {{ $edit->type }}</p>
                                    </div>
                                    <span class="px-4 py-1 bg-amber-100 text-amber-800 text-sm font-bold rounded-full">Pending Review</span>
                                </div>

                                <div class="grid md:grid-cols-2 gap-8 mb-8">
                                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                                        <h4 class="text-sm font-bold text-amber-900/50 uppercase tracking-wider mb-3">Original text</h4>
                                        <p class="text-amber-900/80 leading-relaxed whitespace-pre-wrap">{{ $edit->original_text }}</p>
                                    </div>
                                    <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                                        <h4 class="text-sm font-bold text-green-900/50 uppercase tracking-wider mb-3">Reader suggested</h4>
                                        <p class="text-green-900 leading-relaxed font-medium whitespace-pre-wrap">{{ $edit->edited_text }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4 pt-6 border-t border-amber-100">
                                    <form action="{{ route('admin.edits.approve', $edit) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label class="block text-sm font-extrabold text-amber-900 mb-1">Text that will replace the original span</label>
                                            <p class="text-xs font-bold text-amber-700/70 mb-2">Pre-filled with the reader’s suggestion. Edit before accepting if needed (must still replace the same passage in the chapter when you publish).</p>
                                            <textarea name="merged_text" rows="8" class="w-full rounded-2xl border-2 border-amber-200 bg-white px-4 py-3 text-amber-900 font-medium whitespace-pre-wrap">{{ $edit->edited_text }}</textarea>
                                        </div>
                                        <div class="flex flex-wrap gap-4">
                                            <button type="submit" name="status" value="accepted_full" class="px-6 py-2 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                                                Accept full (2 pts)
                                            </button>
                                            <button type="submit" name="status" value="accepted_partial" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                                Accept partial (1 pt)
                                            </button>
                                        </div>
                                    </form>
                                    <form action="{{ route('admin.edits.reject', $edit) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-6 py-2 bg-red-600 text-white font-bold rounded-full hover:bg-red-700 transition-colors shadow-lg shadow-red-600/20">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="text-amber-800/60 text-sm font-bold">No pending full-chapter suggestions.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
