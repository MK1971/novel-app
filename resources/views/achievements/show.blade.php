@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <a href="{{ route('chapters.index') }}" class="w-12 h-12 bg-white border border-amber-200 rounded-2xl flex items-center justify-center text-amber-900 hover:bg-amber-50 transition-all shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-amber-900">Your Achievements</h1>
                    <p class="text-amber-600 font-bold">Unlock badges by contributing to the story</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 py-12">
        @auth
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $achievements = [
                        [
                            'id' => 1,
                            'name' => 'First Contributor',
                            'emoji' => '✏️',
                            'description' => 'Suggest your first edit',
                            'color' => 'from-blue-400 to-blue-600'
                        ],
                        [
                            'id' => 2,
                            'name' => 'Prolific Editor',
                            'emoji' => '📝',
                            'description' => 'Get 5 edits accepted',
                            'color' => 'from-purple-400 to-purple-600'
                        ],
                        [
                            'id' => 3,
                            'name' => 'Voting Champion',
                            'emoji' => '🗳️',
                            'description' => 'Cast 10 votes',
                            'color' => 'from-green-400 to-green-600'
                        ],
                        [
                            'id' => 4,
                            'name' => 'Rising Star',
                            'emoji' => '⭐',
                            'description' => 'Earn 100 points',
                            'color' => 'from-yellow-400 to-yellow-600'
                        ],
                        [
                            'id' => 5,
                            'name' => 'Bookworm',
                            'emoji' => '📚',
                            'description' => 'Read 5 chapters',
                            'color' => 'from-red-400 to-red-600'
                        ],
                    ];
                @endphp

                @foreach($achievements as $achievement)
                    @php
                        $userHasAchievement = auth()->user()->achievements()->where('achievement_id', $achievement['id'])->exists();
                    @endphp
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-r {{ $achievement['color'] }} rounded-3xl blur opacity-75 group-hover:opacity-100 transition duration-1000 {{ !$userHasAchievement ? 'opacity-0' : '' }}"></div>
                        <div class="relative bg-white rounded-3xl p-8 shadow-lg {{ !$userHasAchievement ? 'opacity-50 grayscale' : '' }}">
                            <div class="text-6xl mb-4 text-center">{{ $achievement['emoji'] }}</div>
                            <h3 class="text-2xl font-extrabold text-amber-900 text-center mb-2">{{ $achievement['name'] }}</h3>
                            <p class="text-amber-600 text-center font-bold mb-4">{{ $achievement['description'] }}</p>
                            
                            @if($userHasAchievement)
                                <div class="flex items-center justify-center gap-2 text-green-600 font-bold">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    Unlocked
                                </div>
                            @else
                                <div class="text-amber-600 text-center font-bold text-sm">Locked</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 bg-amber-50 border-2 border-amber-200 rounded-3xl p-8">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-4">Your Progress</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                        <div class="text-3xl font-extrabold text-amber-900">{{ auth()->user()->achievements()->count() }}</div>
                        <div class="text-amber-600 font-bold text-sm mt-2">Achievements Unlocked</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                        <div class="text-3xl font-extrabold text-amber-900">{{ auth()->user()->edits()->count() }}</div>
                        <div class="text-amber-600 font-bold text-sm mt-2">Edits Submitted</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                        <div class="text-3xl font-extrabold text-amber-900">{{ auth()->user()->votes()->count() }}</div>
                        <div class="text-amber-600 font-bold text-sm mt-2">Votes Cast</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 text-center shadow-sm">
                        <div class="text-3xl font-extrabold text-amber-900">{{ auth()->user()->points ?? 0 }}</div>
                        <div class="text-amber-600 font-bold text-sm mt-2">Total Points</div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <h2 class="text-2xl font-extrabold text-amber-900 mb-4">Sign in to view your achievements</h2>
                <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="px-8 py-4 bg-amber-900 text-white font-bold rounded-2xl hover:bg-black transition-all">
                    Sign In
                </button>
            </div>
        @endauth
    </div>
</x-dynamic-component>
