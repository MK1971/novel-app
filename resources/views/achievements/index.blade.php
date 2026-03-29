@if(auth()->check())
    <x-app-layout>
@else
    <x-guest-layout>
@endif
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
                    <div class="bg-white border-2 {{ in_array($achievement->id, $userAchievements) ? 'border-amber-500 shadow-lg shadow-amber-500/20' : 'border-amber-100' }} rounded-[2rem] p-8 text-center transition-all hover:shadow-lg {{ in_array($achievement->id, $userAchievements) ? 'bg-amber-50' : '' }}">
                        <div class="text-6xl mb-4">{{ $achievement->icon_emoji }}</div>
                        <h3 class="text-xl font-extrabold text-amber-900 mb-2">{{ $achievement->name }}</h3>
                        <p class="text-amber-800/60 font-bold text-sm mb-4">{{ $achievement->description }}</p>
                        
                        <div class="bg-amber-50 rounded-xl p-4 mb-4">
                            <p class="text-xs text-amber-900/60 font-bold uppercase tracking-widest mb-2">Requirement</p>
                            <p class="text-amber-900 font-extrabold">
                                @switch($achievement->requirement_type)
                                    @case('edits_accepted')
                                        {{ $achievement->requirement_value }} Accepted Edits
                                        @break
                                    @case('votes_cast')
                                        {{ $achievement->requirement_value }} Votes Cast
                                        @break
                                    @case('points_earned')
                                        {{ $achievement->requirement_value }} Points
                                        @break
                                    @case('chapters_read')
                                        {{ $achievement->requirement_value }} Chapters Read
                                        @break
                                @endswitch
                            </p>
                        </div>

                        @if(in_array($achievement->id, $userAchievements))
                            <div class="bg-gradient-to-r from-amber-400 to-amber-500 text-white font-extrabold py-3 px-4 rounded-xl">
                                ✓ Unlocked
                            </div>
                        @else
                            <div class="bg-amber-100 text-amber-900 font-bold py-3 px-4 rounded-xl opacity-60">
                                Locked
                            </div>
                        @endif
                    </div>
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
@if(auth()->check())
    </x-app-layout>
@else
    </x-guest-layout>
@endif
