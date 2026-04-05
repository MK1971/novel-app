@php
    $publicPageTitle = $profileUser->name.' — Contributor — '.config('app.name');
    $publicMetaDescription = 'Public contributor profile for '.$profileUser->name.' on '.config('app.name').'. Points and suggestion stats only; email is not shown.';
@endphp
<x-guest-layout :page-title="$publicPageTitle" :meta-description="$publicMetaDescription">
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/50 mb-3">Contributor</p>
                    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                        <div class="w-24 h-24 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 text-3xl font-black border-2 border-amber-200 shrink-0 overflow-hidden">
                            @if ($profileUser->avatarUrl())
                                <img src="{{ $profileUser->avatarUrl() }}" alt="" class="w-full h-full object-cover" width="96" height="96" />
                            @else
                                {{ substr($profileUser->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-3xl font-black text-amber-900 leading-tight">{{ $profileUser->name }}</h1>
                            <p class="text-sm font-bold text-amber-800/60 mt-1">Member since {{ $profileUser->created_at->format('F Y') }}</p>
                            @if (filled($profileUser->profile_bio))
                                <div class="mt-4 prose prose-amber prose-sm max-w-none text-amber-900 font-bold leading-relaxed">
                                    {!! nl2br(e($profileUser->profile_bio)) !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55">Points</p>
                            <p class="text-2xl font-black text-amber-950 tabular-nums">{{ number_format($stats['points']) }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55">Suggestions submitted</p>
                            <p class="text-2xl font-black text-amber-950 tabular-nums">{{ number_format($stats['submitted']) }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-100 bg-amber-50/60 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55">Accepted by moderators</p>
                            <p class="text-2xl font-black text-amber-950 tabular-nums">{{ number_format($stats['accepted']) }}</p>
                        </div>
                    </div>

                    <p class="mt-10 text-xs font-bold text-amber-800/55 leading-relaxed">
                        This is an optional public page. Email and account details are not shown here.
                        <a href="{{ route('leaderboard') }}" class="text-amber-700 underline hover:text-amber-900">Leaderboard</a>
                        ·
                        <a href="{{ route('chapters.index') }}" class="text-amber-700 underline hover:text-amber-900">Read chapters</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
