<aside class="hidden md:flex flex-col w-64 bg-white/50 backdrop-blur-sm border-r border-amber-200/60 sticky top-0 h-screen overflow-y-auto">
    <div class="p-6 space-y-8">
        {{-- Main Navigation --}}
        <div>
            <h3 class="text-xs font-bold text-amber-900/40 uppercase tracking-widest mb-4 px-4">Main Menu</h3>
            <nav class="space-y-1">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="dashboard">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('chapters.index')" :active="request()->routeIs('chapters.*')" icon="book">
                    Chapters
                </x-sidebar-link>
                <x-sidebar-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')" icon="trophy">
                    Leaderboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('vote.index')" :active="request()->routeIs('vote.*')" icon="vote">
                    Peter Trull
                </x-sidebar-link>
            </nav>
        </div>

        {{-- Admin Section --}}
        @can('admin')
            <div>
                <h3 class="text-xs font-bold text-amber-900/40 uppercase tracking-widest mb-4 px-4">Administration</h3>
                <nav class="space-y-1">
                    <x-sidebar-link :href="route('admin.edits.index')" :active="request()->routeIs('admin.edits.*')" icon="review">
                        Review Suggestions
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.chapters.index')" :active="request()->routeIs('admin.chapters.*')" icon="upload">
                        Upload Chapters
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" icon="users">
                        User Management
                    </x-sidebar-link>
                </nav>
            </div>
        @endcan

        {{-- User Profile --}}
        @auth
            <div>
                <h3 class="text-xs font-bold text-amber-900/40 uppercase tracking-widest mb-4 px-4">Account</h3>
                <nav class="space-y-1">
                    <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="user">
                        My Profile
                    </x-sidebar-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2.5 text-sm font-bold text-amber-900/60 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all group">
                            <svg class="w-5 h-5 mr-3 opacity-50 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Log Out
                        </button>
                    </form>
                </nav>
            </div>
        @endauth
    </div>
</aside>
