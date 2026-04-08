@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">🏆 Achievements</h2>
            <p class="text-amber-800/60 font-bold">Unlock badges by contributing to the community</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($achievements as $achievement)
                    @php
                        $unlocked = in_array($achievement->id, $userAchievements);
                        $target = max(1, (int) $achievement->requirement_value);
                        $current = auth()->check() ? (int) ($progressByAchievementId[$achievement->id] ?? 0) : 0;
                        $barPct = $unlocked ? 100 : min(100, (int) round(($current / $target) * 100));
                    @endphp
                    <article tabindex="0" class="outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 rounded-[2rem] bg-white border-2 {{ $unlocked ? 'border-amber-500 shadow-lg shadow-amber-500/20' : 'border-amber-100' }} p-8 text-center transition-all hover:shadow-lg {{ $unlocked ? 'bg-amber-50' : '' }}">
                        <div class="text-6xl mb-4" aria-hidden="true">{{ $achievement->icon_emoji }}</div>
                        <h3 class="text-xl font-extrabold text-amber-900 mb-2">{{ $achievement->name }}</h3>
                        <p class="text-amber-800/60 font-bold text-sm mb-4">{{ $achievement->description }}</p>

                        <div class="bg-amber-50 rounded-xl p-4 mb-3 text-left">
                            <p class="text-xs text-amber-900/60 font-bold uppercase tracking-widest mb-2">Requirement</p>
                            <p class="text-amber-900 font-extrabold">{{ $achievement->requirementLabel() }}</p>
                        </div>

                        @if(auth()->check())
                            <div class="mb-4 text-left">
                                <p class="text-xs font-bold text-amber-800/70 mb-1">Your progress</p>
                                <div class="flex items-center justify-between gap-2 text-sm font-extrabold text-amber-900">
                                    <span>{{ $current }} / {{ $target }}</span>
                                    <span>{{ $barPct }}%</span>
                                </div>
                                <div class="relative mt-2 h-2.5 w-full overflow-hidden rounded-full bg-amber-200/80" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $barPct }}" aria-label="Progress toward {{ $achievement->name }}">
                                    <div class="absolute inset-y-0 left-0 rounded-full bg-amber-500 transition-all duration-300" style="width: {{ $barPct }}%"></div>
                                </div>
                            </div>
                        @else
                            <p class="mb-4 text-left text-xs font-bold text-amber-800/60">Sign in to see your progress on this badge.</p>
                        @endif

                        <details class="mb-4 rounded-xl border border-amber-100 bg-white/80 p-3 text-left text-sm focus-within:ring-2 focus-within:ring-amber-400">
                            <summary class="cursor-pointer font-extrabold text-amber-900 outline-none">How to earn</summary>
                            <p class="mt-2 font-bold text-amber-800/80 leading-relaxed">{{ $achievement->description }}</p>
                        </details>

                        @if($unlocked)
                            <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-white font-extrabold py-3 px-4 rounded-xl">
                                ✓ Unlocked
                            </div>
                        @else
                            <div class="bg-amber-100 text-amber-900 font-bold py-3 px-4 rounded-xl opacity-60">
                                Locked
                            </div>
                        @endif
                    </article>
                @empty
                    <div class="col-span-full text-center py-16">
                        <p class="text-amber-800/60 font-bold text-lg">No achievements available yet.</p>
                    </div>
                @endforelse
            </div>

            @if(auth()->check())
                <div class="mt-12 bg-amber-50 border-2 border-amber-200 rounded-[2rem] p-8">
                    <h3 class="text-xl font-extrabold text-amber-900 mb-4">Your Progress</h3>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="bg-white rounded-xl p-6 text-center border border-amber-100">
                            <p class="text-3xl font-extrabold text-amber-600">{{ auth()->user()->edits()->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count() }}</p>
                            <p class="text-amber-800/60 font-bold text-sm mt-2">Accepted Edits</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center border border-amber-100">
                            <p class="text-3xl font-extrabold text-amber-600">{{ auth()->user()->votes()->count() }}</p>
                            <p class="text-amber-800/60 font-bold text-sm mt-2">Votes Cast</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center border border-amber-100">
                            <p class="text-3xl font-extrabold text-amber-600">{{ auth()->user()->points }}</p>
                            <p class="text-amber-800/60 font-bold text-sm mt-2">Total Points</p>
                        </div>
                        <div class="bg-white rounded-xl p-6 text-center border border-amber-100">
                            <p class="text-3xl font-extrabold text-amber-600">{{ $userAchievements ? count($userAchievements) : 0 }}</p>
                            <p class="text-amber-800/60 font-bold text-sm mt-2">Achievements</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
