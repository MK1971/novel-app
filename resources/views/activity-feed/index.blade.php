<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-extrabold text-3xl text-amber-900">Live activity stream</h1>
                <p class="text-amber-800/60 font-bold mt-1">Recent edits, votes, and milestones from the community.</p>
            </div>
            <a href="{{ route('analytics.index') }}" class="text-sm font-extrabold text-amber-700 hover:text-amber-900 underline decoration-amber-300 shrink-0">← Back to insights</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-6">
                @forelse($activities as $activity)
                    <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 hover:shadow-lg transition-all">
                        <div class="flex items-start gap-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-white font-extrabold flex-shrink-0">
                                {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-extrabold text-amber-900">{{ $activity->user->name }}</h3>
                                    <span class="text-xs font-bold text-amber-800/60 bg-amber-50 px-3 py-1 rounded-full">
                                        @switch($activity->activity_type)
                                            @case('edit_suggested')
                                                ✏️ Suggested Edit
                                                @break
                                            @case('edit_accepted')
                                                ✅ Edit Accepted
                                                @break
                                            @case('vote_cast')
                                                🗳️ Voted
                                                @break
                                            @case('chapter_read')
                                                📖 Read Chapter
                                                @break
                                            @case('achievement_unlocked')
                                                🏆 Achievement Unlocked
                                                @break
                                            @default
                                                📢 {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}
                                        @endswitch
                                    </span>
                                </div>
                                <p class="text-amber-900 font-bold mb-3">{{ $activity->description }}</p>
                                @if($activity->chapter)
                                    <a href="{{ route('chapters.show', $activity->chapter) }}" class="text-amber-600 font-bold hover:text-amber-700 transition-colors">
                                        → {{ $activity->chapter->title }}
                                    </a>
                                @endif
                                <p class="text-xs text-amber-800/60 font-bold mt-3">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-amber-50 rounded-[2rem] border-2 border-amber-100">
                        <p class="text-amber-800/60 font-bold text-lg">No activities yet. Be the first to contribute!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
