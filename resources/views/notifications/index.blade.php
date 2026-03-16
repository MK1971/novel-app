@if(auth()->check())
    <x-app-layout>
@else
    <x-guest-layout>
@endif
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">🔔 Notifications</h2>
            <span class="bg-amber-500 text-white font-extrabold px-4 py-2 rounded-full text-sm">{{ $unreadCount }} Unread</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    <div class="bg-white border-2 {{ $notification->read_at ? 'border-amber-100' : 'border-amber-400 shadow-lg shadow-amber-400/20' }} rounded-[2rem] p-6 hover:shadow-lg transition-all {{ !$notification->read_at ? 'bg-amber-50' : '' }}">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="font-extrabold text-amber-900">{{ $notification->title }}</h3>
                                    <span class="text-xs font-bold text-white bg-amber-600 px-3 py-1 rounded-full">
                                        @switch($notification->type)
                                            @case('edit_accepted')
                                                ✅ Edit Accepted
                                                @break
                                            @case('achievement_unlocked')
                                                🏆 Achievement
                                                @break
                                            @case('comment')
                                                💬 Comment
                                                @break
                                            @case('vote')
                                                🗳️ Vote
                                                @break
                                            @default
                                                📢 {{ ucfirst($notification->type) }}
                                        @endswitch
                                    </span>
                                </div>
                                <p class="text-amber-900 font-bold mb-2">{{ $notification->message }}</p>
                                <p class="text-xs text-amber-800/60 font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.read', $notification) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition-colors text-sm">
                                        Mark Read
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-amber-800/60 font-bold px-4 py-2 bg-amber-50 rounded-xl">Read</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-amber-50 rounded-[2rem] border-2 border-amber-100">
                        <p class="text-amber-800/60 font-bold text-lg">No notifications yet</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->count() > 0)
                <div class="mt-8 text-center">
                    <p class="text-amber-800/60 font-bold text-sm">Showing {{ $notifications->count() }} notifications</p>
                </div>
            @endif
        </div>
    </div>
@if(auth()->check())
    </x-app-layout>
@else
    </x-guest-layout>
@endif
