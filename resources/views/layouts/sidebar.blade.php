<aside class="w-80 bg-white border-r border-amber-100 min-h-screen p-10 hidden lg:block">
    <div class="space-y-12">
        {{-- Logo Section --}}
        <div class="flex items-center gap-4 mb-12">
            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-black font-black text-2xl shadow-lg shadow-amber-500/20">
                M
            </div>
            <div>
                <h2 class="text-xl font-black text-amber-900 tracking-tighter">MWBN<span class="text-amber-500">.</span></h2>
                <p class="text-[10px] font-black text-amber-900/30 uppercase tracking-[0.2em]">Collaborative Story</p>
            </div>
        </div>

        {{-- Main Navigation --}}
        <div>
            <h3 class="text-xs font-extrabold text-amber-900/30 uppercase tracking-[0.2em] mb-6 px-4">Main Menu</h3>
            <nav class="space-y-2">
                <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home">
                    Dashboard
                </x-sidebar-link>
                <x-sidebar-link :href="route('chapters.index')" :active="request()->routeIs('chapters.*')" icon="book">
                    The Story
                </x-sidebar-link>
                <x-sidebar-link :href="route('vote.index')" :active="request()->routeIs('vote.*')" icon="vote">
                    Peter Trull
                </x-sidebar-link>
                <x-sidebar-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')" icon="trophy">
                    Leaderboard
                </x-sidebar-link>
            </nav>
        </div>

        {{-- Explore Section --}}
        <div>
            <h3 class="text-xs font-extrabold text-amber-900/30 uppercase tracking-[0.2em] mb-6 px-4">Explore</h3>
            <nav class="space-y-2">
                <x-sidebar-link :href="route('archive.chapters')" :active="request()->routeIs('archive.*')" icon="archive">
                    Archives
                </x-sidebar-link>
                <x-sidebar-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')" icon="analytics">
                    Analytics
                </x-sidebar-link>
                <x-sidebar-link :href="route('feedback.index')" :active="request()->routeIs('feedback.index')" icon="feedback">
                    Feedback
                </x-sidebar-link>
                @auth
                <x-sidebar-link :href="route('achievements.index')" :active="request()->routeIs('achievements.*')" icon="trophy">
                    Achievements
                </x-sidebar-link>
                @endauth
            </nav>
        </div>

        {{-- Admin Section --}}
        @can('admin')
            <div>
                <h3 class="text-xs font-extrabold text-amber-900/30 uppercase tracking-[0.2em] mb-6 px-4">Administration</h3>
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('admin.inline-edits.index')" :active="request()->routeIs('admin.inline-edits.*')" icon="edit">
                        Moderation
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
                <h3 class="text-xs font-extrabold text-amber-900/30 uppercase tracking-[0.2em] mb-6 px-4">Account</h3>
                <nav class="space-y-2">
                    <x-sidebar-link :href="route('profile.show')" :active="request()->routeIs('profile.show')" icon="user">
                        My Profile
                    </x-sidebar-link>
                    <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')" icon="settings">
                        Settings
                    </x-sidebar-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-3 text-sm font-bold text-amber-900/60 hover:text-red-600 hover:bg-red-50 rounded-2xl transition-all group">
                            <svg class="w-5 h-5 mr-3 opacity-40 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Log Out
                        </button>
                    </form>
                </nav>
            </div>
        @endauth
        
        {{-- CTA for Guests --}}
        @guest
            <div class="p-6 bg-amber-500/10 rounded-3xl border border-amber-500/20">
                <p class="text-sm font-bold text-amber-900 mb-4">Ready to shape the story?</p>
                <a href="{{ route('register') }}" class="w-full block text-center py-3 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                    Join Now
                </a>
            </div>
        @endguest
    </div>
</aside>
