@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
    $pointsExplainer = 'Accepted edits earn up to 2 points: 2 for a full accept, 1 for partial, 0 if rejected. Your first accepted edit unlocks voting on Peter Trull.';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Leaderboard
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">The top contributors shaping the narrative.</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="px-4 py-2 bg-amber-500 text-black text-sm font-black rounded-2xl shadow-lg shadow-amber-500/20">
                    🏆 Grand Prize: Name on the Cover
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        @if($users->isEmpty())
            <div class="max-w-xl mx-auto text-center bg-white border border-amber-100 shadow-sm rounded-[3rem] px-10 py-16">
                <div class="w-20 h-20 bg-amber-100 rounded-3xl flex items-center justify-center mx-auto mb-8">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-extrabold text-amber-900 mb-4">No contributors on the board yet</h3>
                <p class="text-amber-800/70 font-bold leading-relaxed mb-8">{{ $pointsExplainer }}</p>
                <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="inline-flex items-center px-10 py-4 bg-amber-500 text-black text-lg font-extrabold rounded-full hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/25">
                    Read chapters &amp; suggest an edit
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
                @guest
                    <p class="mt-6 text-sm text-amber-800/50 font-bold">Have an account? <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="text-amber-700 underline font-extrabold hover:text-amber-900">Sign in</button> to track your points.</p>
                @endguest
            </div>
        @else
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                <table class="min-w-full divide-y divide-amber-100">
                    <thead class="bg-amber-50/50">
                        <tr>
                            <th class="px-10 py-6 text-left text-xs font-extrabold text-amber-900/40 uppercase tracking-[0.2em]">Rank</th>
                            <th class="px-10 py-6 text-left text-xs font-extrabold text-amber-900/40 uppercase tracking-[0.2em]">Contributor</th>
                            <th class="px-10 py-6 text-right text-xs font-extrabold text-amber-900/40 uppercase tracking-[0.2em]">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-50">
                        @foreach($users as $index => $user)
                            <tr class="hover:bg-amber-50/30 transition-all group">
                                <td class="px-10 py-8">
                                    <div class="flex items-center">
                                        @if($index === 0)
                                            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-amber-500/20 transform group-hover:rotate-6 transition-transform">🥇</div>
                                        @elseif($index === 1)
                                            <div class="w-12 h-12 bg-slate-200 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-slate-200/20 transform group-hover:rotate-6 transition-transform">🥈</div>
                                        @elseif($index === 2)
                                            <div class="w-12 h-12 bg-orange-200 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-orange-200/20 transform group-hover:rotate-6 transition-transform">🥉</div>
                                        @else
                                            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-lg font-black text-amber-900/30">#{{ $index + 1 }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <div class="text-xl font-extrabold text-amber-900 group-hover:text-amber-600 transition-colors">{{ $user->name }}</div>
                                    <div class="text-sm text-amber-800/40 font-bold mt-1">Member since {{ $user->created_at->format('M Y') }}</div>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <span class="inline-flex items-center px-6 py-2 bg-amber-100 text-amber-900 text-lg font-black rounded-full border border-amber-200/50">
                                        {{ number_format($user->points) }} <span class="ml-2 text-xs uppercase tracking-widest opacity-40">pts</span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="mt-20 text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 bg-amber-500/10 rounded-3xl flex items-center justify-center mx-auto mb-8">
                <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h3 class="text-3xl font-extrabold text-amber-900 mb-4">Want to see your name here?</h3>
            <p class="text-amber-800/60 text-lg font-bold mb-10 leading-relaxed">{{ $pointsExplainer }} The top contributor will have their name featured on the final book cover.</p>
            <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="inline-flex items-center px-12 py-5 bg-amber-500 text-black text-xl font-extrabold rounded-full hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                Start Contributing Now
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>
    </div>
</x-dynamic-component>
