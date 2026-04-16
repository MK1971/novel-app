@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">Editor Hall of Fame</h2>
            <p class="text-amber-800/80 font-bold">Top 50 contributors by total accepted replacements (full chapter + paragraph), updated automatically.</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <a href="{{ route('leaderboard') }}" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white px-4 py-2 text-sm font-black text-amber-900 hover:bg-amber-50">
                    Back to leaderboard
                </a>
                <a href="{{ route('prizes') }}" class="inline-flex items-center rounded-2xl border border-amber-200 bg-white px-4 py-2 text-sm font-black text-amber-900 hover:bg-amber-50">
                    View prize rules
                </a>
            </div>

            @if($hallOfFameUsers->isEmpty())
                <div class="rounded-[2rem] border border-amber-200 bg-white p-8 text-center">
                    <p class="text-lg font-extrabold text-amber-900">No contributors have accepted replacements yet.</p>
                </div>
            @else
                <div class="rounded-[2rem] border border-amber-200 bg-white shadow-sm overflow-hidden">
                    <table class="min-w-full divide-y divide-amber-100">
                        <thead class="bg-amber-50/60">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-amber-800/70">Rank</th>
                                <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-[0.2em] text-amber-800/70">Contributor</th>
                                <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-[0.2em] text-amber-800/70">Accepted replacements</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @foreach($hallOfFameUsers as $idx => $user)
                                <tr class="hover:bg-amber-50/40">
                                    <td class="px-6 py-4 text-sm font-black text-amber-900">#{{ $idx + 1 }}</td>
                                    <td class="px-6 py-4 text-sm font-extrabold text-amber-900">
                                        @if ($user->public_profile_enabled && filled($user->public_slug))
                                            <a href="{{ route('profile.public', ['slug' => $user->public_slug]) }}" class="hover:underline">
                                                {{ $user->name }}
                                            </a>
                                        @else
                                            {{ $user->name }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-amber-900">{{ number_format((int) ($user->accepted_total ?? 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
