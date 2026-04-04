<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-3xl text-amber-900">Payments &amp; vote credits</h1>
                <p class="text-amber-800/80 font-bold mt-1">Completed $2 checkouts and how vote credits were used.</p>
            </div>
            <a href="{{ route('profile.show') }}" class="text-sm font-black text-amber-700 hover:text-amber-900 underline">Back to profile</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] overflow-hidden shadow-sm">
                <div class="divide-y divide-amber-100">
                    @forelse($payments as $payment)
                        @php
                            $chapter = $payment->edit?->chapter ?? $payment->vote?->chapter;
                            $amount = number_format(($payment->amount_cents ?? 200) / 100, 2);
                        @endphp
                        <div class="p-6 sm:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-amber-800/50">{{ $payment->created_at->format('M j, Y g:i A') }}</p>
                                    <p class="text-lg font-black text-amber-950 mt-1">${{ $amount }} — <span class="capitalize">{{ $payment->status }}</span></p>
                                    @if($chapter)
                                        <p class="text-sm font-bold text-amber-800 mt-2">
                                            Chapter:
                                            <a href="{{ route('chapters.show', $chapter) }}" class="text-amber-700 underline hover:text-amber-950">{{ $chapter->readerHeadingLine() }}</a>
                                        </p>
                                    @endif
                                </div>
                                <div class="shrink-0">
                                    @if($payment->status === 'completed' && $payment->vote)
                                        <span class="inline-flex px-4 py-2 rounded-full bg-emerald-100 text-emerald-900 text-xs font-black uppercase tracking-widest">Vote credit used</span>
                                        @if($payment->vote->chapter)
                                            <p class="text-xs font-bold text-amber-700 mt-2 text-right">
                                                Ballot: <a href="{{ route('vote.index') }}" class="underline">Peter Trull</a>
                                            </p>
                                        @endif
                                    @elseif($payment->status === 'completed')
                                        <span class="inline-flex px-4 py-2 rounded-full bg-amber-100 text-amber-900 text-xs font-black uppercase tracking-widest">Vote credit available</span>
                                    @else
                                        <span class="inline-flex px-4 py-2 rounded-full bg-slate-100 text-slate-800 text-xs font-black uppercase tracking-widest">{{ $payment->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <p class="text-amber-800 font-bold mb-4">No payments recorded yet.</p>
                            <a href="{{ route('chapters.index') }}" class="text-amber-700 font-black underline">Browse chapters to suggest an edit</a>
                        </div>
                    @endforelse
                </div>
                @if($payments->hasPages())
                    <div class="p-6 border-t border-amber-100">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
