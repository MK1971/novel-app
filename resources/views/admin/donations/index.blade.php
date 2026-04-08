<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <nav class="mb-2 text-xs font-bold text-amber-800/70">
                    <a href="{{ route('dashboard') }}" class="underline">Dashboard</a> / Admin / Donations
                </nav>
                <h2 class="font-extrabold text-3xl text-amber-900">Donations report</h2>
            </div>
            <a href="{{ route('admin.donations.export') }}" class="px-4 py-2 rounded-full border border-amber-300 bg-white text-amber-900 font-black text-sm hover:bg-amber-50">Export CSV</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-5 rounded-2xl border border-amber-200 bg-amber-50">
                <p class="text-xs font-black uppercase tracking-widest text-amber-700/70">Total donated</p>
                <p class="text-3xl font-black text-amber-900">${{ number_format($totalCents / 100, 2) }}</p>
                <div class="mt-3">
                    @if($usesSignatureVerification)
                        <span class="inline-flex items-center gap-2 rounded-full border border-green-300 bg-green-50 px-3 py-1 text-xs font-black text-green-800">
                            <span aria-hidden="true">✅</span>
                            Webhook auth: Signature verification enabled
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 rounded-full border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-black text-amber-900">
                            <span aria-hidden="true">⚠️</span>
                            Webhook auth: Token fallback mode
                        </span>
                    @endif
                </div>
            </div>
            <div class="bg-white dark:bg-stone-900 border border-amber-100 dark:border-stone-700 rounded-2xl overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-amber-50 dark:bg-stone-800 text-amber-900 dark:text-stone-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-black">User</th>
                            <th class="px-4 py-3 text-left font-black">Email</th>
                            <th class="px-4 py-3 text-left font-black">Amount</th>
                            <th class="px-4 py-3 text-left font-black">PayPal order</th>
                            <th class="px-4 py-3 text-left font-black">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-stone-900 dark:text-stone-100">
                        @forelse($rows as $row)
                            <tr class="border-t border-amber-100 dark:border-stone-700">
                                <td class="px-4 py-3">{{ $row->user?->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3">{{ $row->user?->email ?? '' }}</td>
                                <td class="px-4 py-3 font-bold">${{ number_format($row->amount_cents / 100, 2) }}</td>
                                <td class="px-4 py-3 break-all">{{ $row->payment_id }}</td>
                                <td class="px-4 py-3">{{ $row->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-amber-800/70 font-bold">No completed donations yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
