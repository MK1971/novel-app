<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Inline Moderation
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Review and approve paragraph-level edit suggestions.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($inlineEdits->count() > 0)
                <div class="grid gap-8">
                    @foreach($inlineEdits as $edit)
                        <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10 hover:shadow-lg transition-all">
                            <div class="flex flex-wrap justify-between items-start gap-4 mb-8">
                                <div>
                                    <h3 class="text-2xl font-extrabold text-amber-900">Suggestion for {{ $edit->chapter->title }}</h3>
                                    <p class="text-amber-800/60 font-bold mt-1">By {{ $edit->user->name }} • Paragraph #{{ $edit->paragraph_number }} • {{ $edit->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-black rounded-full uppercase tracking-widest">Pending Review</span>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8 mb-10">
                                <div class="bg-amber-50/50 rounded-[2rem] p-8 border border-amber-100">
                                    <h4 class="text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Original Text</h4>
                                    <div class="text-amber-900/80 leading-relaxed whitespace-pre-wrap text-sm italic">"{{ $edit->original_text }}"</div>
                                </div>
                                <div class="bg-green-50/30 rounded-[2rem] p-8 border border-green-100">
                                    <h4 class="text-xs font-black text-green-900/30 uppercase tracking-[0.2em] mb-4">Suggested Edit</h4>
                                    <div class="text-green-900 leading-relaxed font-medium whitespace-pre-wrap text-sm italic">"{{ $edit->suggested_text }}"</div>
                                </div>
                            </div>

                            @if($edit->reason)
                                <div class="mb-10 p-8 bg-blue-50/30 border border-blue-100 rounded-[2rem]">
                                    <h4 class="text-xs font-black text-blue-900/30 uppercase tracking-[0.2em] mb-4">Reason for Suggestion</h4>
                                    <p class="text-blue-900 font-medium leading-relaxed">{{ $edit->reason }}</p>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-4 pt-8 border-t border-amber-50">
                                <button 
                                    onclick="approveInlineEdit({{ $edit->id }})"
                                    class="px-10 py-4 bg-green-600 text-white font-extrabold rounded-2xl hover:bg-green-700 transition-all shadow-xl shadow-green-600/20 transform hover:-translate-y-0.5"
                                >
                                    Approve (1 pt)
                                </button>
                                <button 
                                    onclick="rejectInlineEdit({{ $edit->id }})"
                                    class="px-10 py-4 bg-red-600 text-white font-extrabold rounded-2xl hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 transform hover:-translate-y-0.5"
                                >
                                    Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12">
                    {{ $inlineEdits->links() }}
                </div>
            @else
                <div class="text-center py-32 bg-white border border-amber-100 rounded-[3rem] shadow-sm">
                    <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">No pending inline edits</h3>
                    <p class="text-amber-800/50 text-lg font-bold">All paragraph suggestions have been processed. Good job!</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function approveInlineEdit(id) {
            if (confirm('Approve this inline edit suggestion?')) {
                fetch(`/admin/inline-edits/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function rejectInlineEdit(id) {
            if (confirm('Reject this inline edit suggestion?')) {
                fetch(`/admin/inline-edits/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</x-app-layout>
