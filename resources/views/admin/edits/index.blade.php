<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Admin - Review Suggestions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm">{{ session('success') }}</div>
            @endif

            <div class="flex gap-4 mb-8">
                <a href="{{ route('admin.chapters.index') }}" class="px-6 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">
                    Upload Chapters →
                </a>
            </div>

            <div class="grid gap-8">
                @forelse($edits as $edit)
                    <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8">
                        <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-amber-900">Suggestion for {{ $edit->chapter->title }}</h3>
                                <p class="text-amber-800/70 font-medium">By {{ $edit->user->name }} • {{ $edit->type }}</p>
                            </div>
                            <span class="px-4 py-1 bg-amber-100 text-amber-800 text-sm font-bold rounded-full">Pending Review</span>
                        </div>

                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                                <h4 class="text-sm font-bold text-amber-900/50 uppercase tracking-wider mb-3">Original Text</h4>
                                <p class="text-amber-900/80 leading-relaxed">{{ $edit->original_text }}</p>
                            </div>
                            <div class="bg-green-50 rounded-2xl p-6 border border-green-100">
                                <h4 class="text-sm font-bold text-green-900/50 uppercase tracking-wider mb-3">Suggested Edit</h4>
                                <p class="text-green-900 leading-relaxed font-medium">{{ $edit->edited_text }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 pt-6 border-t border-amber-50">
                            <form action="{{ route('admin.edits.approve', $edit) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="accepted_full">
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white font-bold rounded-full hover:bg-green-700 transition-colors shadow-lg shadow-green-600/20">
                                    Accept Full (2 pts)
                                </button>
                            </form>
                            <form action="{{ route('admin.edits.approve', $edit) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="accepted_partial">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                                    Accept Partial (1 pt)
                                </button>
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
                    <div class="text-center py-20 bg-white border border-amber-100 rounded-3xl">
                        <p class="text-amber-900/50 text-xl font-medium">No pending edits to review. Good job!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
