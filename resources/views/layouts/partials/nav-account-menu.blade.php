{{-- Logged-in account area: notifications + profile dropdown (shared by app + guest layouts) --}}
<div class="flex items-center gap-2 sm:gap-4">
    <a
        href="{{ route('notifications.index') }}"
        class="relative inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-amber-200 dark:border-stone-600 bg-white dark:bg-stone-800 text-amber-900 dark:text-amber-100 shadow-sm hover:bg-amber-50 dark:hover:bg-stone-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:focus-visible:ring-amber-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#fff9f0] dark:focus-visible:ring-offset-stone-950"
        aria-label="Notifications{{ ($unreadNotificationCount ?? 0) > 0 ? ', '.$unreadNotificationCount.' unread' : '' }}"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if(($unreadNotificationCount ?? 0) > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[1.125rem] h-[1.125rem] px-1 flex items-center justify-center rounded-full bg-red-600 text-[10px] font-black text-white leading-none">
                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
            </span>
        @endif
    </a>
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-amber-200 dark:border-stone-600 text-sm font-bold rounded-full text-amber-900 dark:text-amber-50 bg-white dark:bg-stone-800 hover:bg-amber-50 dark:hover:bg-stone-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:focus-visible:ring-amber-400 focus-visible:ring-offset-2 focus-visible:ring-offset-[#fff9f0] dark:focus-visible:ring-offset-stone-950 transition-all shadow-sm">
                @if(Auth::user()->avatarUrl())
                    <img src="{{ Auth::user()->avatarUrl() }}" alt="" class="w-6 h-6 rounded-full mr-2 object-cover border border-amber-200 dark:border-stone-600 shrink-0" width="24" height="24" />
                @else
                    <span class="w-6 h-6 bg-amber-500 rounded-full mr-2 flex items-center justify-center text-[10px] text-black shrink-0" aria-hidden="true">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </span>
                @endif
                <div>{{ Auth::user()->name }}</div>
                <svg class="ms-2 h-4 w-4 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </x-slot>

        <x-slot name="content">
            <x-dropdown-link :href="route('profile.show')" class="text-amber-900 dark:text-amber-50 font-bold hover:bg-amber-50 dark:hover:bg-stone-700">
                {{ __('Profile') }}
            </x-dropdown-link>
            <x-dropdown-link :href="route('profile.edit')" class="text-amber-900 dark:text-amber-50 font-bold hover:bg-amber-50 dark:hover:bg-stone-700">
                {{ __('Edit profile') }}
            </x-dropdown-link>
            @can('admin')
                <x-dropdown-link :href="route('admin.inline-edits.index')" class="text-amber-600 dark:text-amber-300 font-bold hover:bg-amber-50 dark:hover:bg-stone-700">
                    {{ __('Moderation') }}
                </x-dropdown-link>
                <x-dropdown-link :href="route('admin.users.index')" class="text-amber-600 dark:text-amber-300 font-bold hover:bg-amber-50 dark:hover:bg-stone-700">
                    {{ __('User Management') }}
                </x-dropdown-link>
            @endcan
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 dark:text-red-400 font-bold hover:bg-red-50 dark:hover:bg-red-950/40">
                    {{ __('Log Out') }}
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>
</div>
