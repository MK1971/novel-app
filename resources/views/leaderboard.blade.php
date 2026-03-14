@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    @auth
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-amber-900 leading-tight">Leaderboard</h2>
        </x-slot>
    @else
        <div class="max-w-5xl mx-auto px-6 pt-12">
            <h2 class="text-center text-4xl font-extrabold text-amber-900 mb-2">Leaderboard</h2>
            <p class="text-center text-amber-800/70 text-xl font-medium mb-12">The top contributors shaping the narrative.</p>
        </div>
    @endauth

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl overflow-hidden">
                <table class="min-w-full divide-y divide-amber-100">
                    <thead class="bg-amber-50">
                        <tr>
                            <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Rank</th>
                            <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Contributor</th>
                            <th class="px-8 py-4 text-right text-sm font-bold text-amber-900 uppercase tracking-wider">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50">
                        @foreach($users as $index => $user)
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        @if($index === 0)
                                            <span class="text-2xl mr-2">🥇</span>
                                        @elseif($index === 1)
                                            <span class="text-2xl mr-2">🥈</span>
                                        @elseif($index === 2)
                                            <span class="text-2xl mr-2">🥉</span>
                                        @else
                                            <span class="text-lg font-bold text-amber-900/40 w-8">#{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-lg font-bold text-amber-900">{{ $user->name }}</div>
                                    <div class="text-sm text-amber-800/50">Member since {{ $user->created_at->format('M Y') }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="inline-flex items-center px-4 py-1 bg-amber-100 text-amber-800 text-lg font-bold rounded-full">
                                        {{ $user->points }} pts
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-12 text-center">
                <p class="text-amber-800/70 text-lg mb-8">Want to see your name here? Start contributing to the story!</p>
                <a href="{{ route('chapters.index') }}" class="px-10 py-4 bg-amber-500 text-black text-lg font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/25">
                    Start Your Adventure
                </a>
            </div>
        </div>
    </div>

</x-dynamic-component>
