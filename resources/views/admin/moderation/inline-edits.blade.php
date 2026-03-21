@php
    $layout = 'app-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-3xl font-extrabold text-amber-900">Inline Edit Suggestions</h2>
            <span class="px-4 py-2 bg-amber-100 rounded-full text-amber-900 font-bold text-sm">
                {{ $inlineEdits->total() }} pending
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            @if($inlineEdits->count() > 0)
                <div class="space-y-6">
                    @foreach($inlineEdits as $edit)
                        <div class="bg-white border border-amber-100 rounded-2xl p-8 shadow-sm hover:shadow-md transition-all">
                            <div class="flex items-start justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-amber-900">
                                        {{ $edit->user->name }} - {{ $edit->chapter->title }}
                                    </h3>
                                    <p class="text-sm text-amber-600 font-bold mt-1">
                                        {{ $edit->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">
                                    Pending Review
                                </span>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-amber-600 mb-2">Original Text</p>
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-900 font-medium text-sm leading-relaxed">
                                        {{ $edit->original_text }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-amber-600 mb-2">Suggested Text</p>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-900 font-medium text-sm leading-relaxed">
                                        {{ $edit->suggested_text }}
                                    </div>
                                </div>
                            </div>

                            @if($edit->reason)
                                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-xs font-black uppercase tracking-widest text-blue-600 mb-2">Reason</p>
                                    <p class="text-blue-900 font-medium">{{ $edit->reason }}</p>
                                </div>
                            @endif

                            <div class="flex gap-4">
                                <button 
                                    onclick="approveInlineEdit({{ $edit->id }})"
                                    class="flex-1 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-all"
                                >
                                    ✓ Approve
                                </button>
                                <button 
                                    onclick="rejectInlineEdit({{ $edit->id }})"
                                    class="flex-1 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-all"
                                >
                                    ✗ Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $inlineEdits->links() }}
                </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-12 text-center">
                    <p class="text-amber-900 font-bold text-lg">No pending inline edit suggestions</p>
                    <p class="text-amber-600 mt-2">All suggestions have been reviewed!</p>
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
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Inline edit approved! User earned 1 point.');
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
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Inline edit rejected.');
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</x-dynamic-component>
