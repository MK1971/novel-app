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
                <p class="text-2xl font-extrabold text-amber-900">{{ $achievement->requirementLabel() }}</p>
            </div>

            @auth
                @php
                    $userAchievement = auth()->user()->achievements()->where('achievements.id', $achievement->id)->first();
                    $targetShow = max(1, (int) $achievement->requirement_value);
                    $barPctShow = $userAchievement ? 100 : ($currentProgress !== null ? min(100, (int) round(((int) $currentProgress / $targetShow) * 100)) : 0);
                @endphp
                @if($userAchievement)
                    <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-8 text-center">
                        <p class="text-green-800 font-extrabold text-lg">Unlocked</p>
                        @if($userAchievement->pivot->unlocked_at)
                            <p class="text-green-700/80 font-bold text-sm mt-2">{{ \Carbon\Carbon::parse($userAchievement->pivot->unlocked_at)->toFormattedDateString() }}</p>
                        @endif
                    </div>
                @elseif($currentProgress !== null)
                    <div class="rounded-2xl border-2 border-amber-200 bg-amber-50/80 p-8 text-center">
                        <p class="text-sm font-extrabold text-amber-900">Progress: {{ (int) $currentProgress }} / {{ $targetShow }}</p>
                        <div class="relative mx-auto mt-4 max-w-md h-3 w-full overflow-hidden rounded-full bg-amber-100" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $barPctShow }}">
                            <div class="absolute inset-y-0 left-0 rounded-full bg-amber-500 transition-all duration-300" style="width: {{ $barPctShow }}%"></div>
                        </div>
                        <p class="mt-6 text-amber-900 font-extrabold">Locked — keep contributing to unlock this badge.</p>
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
