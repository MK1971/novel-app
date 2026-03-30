@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('achievements.index') }}" class="w-12 h-12 bg-white border border-amber-200 rounded-2xl flex items-center justify-center text-amber-900 hover:bg-amber-50 transition-all shadow-sm shrink-0" aria-label="Back to all achievements">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">{{ $achievement->name }}</h2>
                    <p class="text-amber-800/60 font-bold mt-1">Achievement details</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-10 text-center mb-8 shadow-sm">
                <div class="text-7xl mb-6" aria-hidden="true">{{ $achievement->icon_emoji }}</div>
                <p class="text-amber-800 font-bold text-lg leading-relaxed">{{ $achievement->description }}</p>
            </div>

            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-8 mb-8">
                <p class="text-xs text-amber-900/60 font-bold uppercase tracking-widest mb-3">Requirement</p>
                <p class="text-2xl font-extrabold text-amber-900">
                    @switch($achievement->requirement_type)
                        @case('edits_accepted')
                            {{ $achievement->requirement_value }} accepted edits
                            @break
                        @case('votes_cast')
                            {{ $achievement->requirement_value }} votes cast
                            @break
                        @case('points_earned')
                            {{ $achievement->requirement_value }} points
                            @break
                        @case('chapters_read')
                            {{ $achievement->requirement_value }} chapters read
                            @break
                        @case('completed_payments')
                            {{ $achievement->requirement_value }} completed $2 edit payment(s)
                            @break
                        @default
                            {{ $achievement->requirement_value }} ({{ $achievement->requirement_type }})
                    @endswitch
                </p>
            </div>

            @auth
                @php
                    $userAchievement = auth()->user()->achievements()->where('achievements.id', $achievement->id)->first();
                @endphp
                @if($userAchievement)
                    <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-8 text-center">
                        <p class="text-green-800 font-extrabold text-lg">Unlocked</p>
                        @if($userAchievement->pivot->unlocked_at)
                            <p class="text-green-700/80 font-bold text-sm mt-2">{{ \Carbon\Carbon::parse($userAchievement->pivot->unlocked_at)->toFormattedDateString() }}</p>
                        @endif
                    </div>
                @else
                    <div class="bg-amber-100 border-2 border-amber-200 rounded-2xl p-8 text-center">
                        <p class="text-amber-900 font-extrabold">Locked — keep contributing to unlock this badge.</p>
                    </div>
                @endif
            @else
                <div class="text-center rounded-2xl border-2 border-amber-100 bg-white p-8">
                    <p class="text-amber-800/70 font-bold mb-4">Sign in to track your progress on this achievement.</p>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="px-8 py-3 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-colors">
                        Sign in
                    </button>
                </div>
            @endauth

            <p class="mt-10 text-center">
                <a href="{{ route('achievements.index') }}" class="text-amber-700 font-bold hover:underline">← All achievements</a>
            </p>
        </div>
    </div>
</x-dynamic-component>
