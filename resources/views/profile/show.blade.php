<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">
            {{ __('User Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Header -->
            <div class="p-8 bg-white/80 backdrop-blur-sm shadow-xl sm:rounded-2xl border border-amber-100 flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 text-4xl font-bold border-4 border-amber-200 shadow-inner">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-amber-900 mb-2">{{ $user->name }}</h1>
                    <p class="text-amber-700 mb-4">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <div class="bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                            <span class="text-sm text-amber-600 block">Total Points</span>
                            <span class="text-xl font-bold text-amber-900">{{ $user->points ?? 0 }}</span>
                        </div>
                        <div class="bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                            <span class="text-sm text-amber-600 block">Member Since</span>
                            <span class="text-xl font-bold text-amber-900">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-6 py-3 bg-amber-600 border border-transparent rounded-full font-bold text-white hover:bg-amber-700 transition ease-in-out duration-150 shadow-md">
                        Edit Profile
                    </a>
                </div>
            </div>

            <!-- Reading Progress -->
            <div class="p-8 bg-white/80 backdrop-blur-sm shadow-xl sm:rounded-2xl border border-amber-100">
                <h3 class="text-2xl font-bold text-amber-900 mb-6">Reading Progress</h3>
                @if($readingProgress->count() > 0)
                    <div class="grid gap-4">
                        @foreach($readingProgress as $progress)
                            <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-100">
                                <div>
                                    <h4 class="font-bold text-amber-900">{{ $progress->chapter->displayTitle() }}</h4>
                                    <p class="text-sm text-amber-600">Last read: {{ $progress->last_read_at?->diffForHumans() ?? __('Not recorded yet') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($progress->completed)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase tracking-wider">Completed</span>
                                    @elseif($progress->chapter && $progress->chapter->is_locked)
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full uppercase tracking-wider">Locked</span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">In Progress</span>
                                    @endif
                                    <a href="{{ route('chapters.show', $progress->chapter) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ ($progress->chapter && $progress->chapter->is_locked) ? __('Read') : __('Continue') }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-amber-700 mb-6 italic">You haven't started reading any chapters yet.</p>
                        <a href="{{ route('chapters.index') }}" class="text-amber-600 hover:text-amber-800 font-bold underline">Browse Chapters</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
