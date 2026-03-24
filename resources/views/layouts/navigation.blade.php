<nav x-data="{ open: false }" class="w-full border-b border-amber-200/60 bg-white/80 backdrop-blur-sm sticky !top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-extrabold text-amber-800 hover:text-amber-600 transition-colors">
                        What's My Book Name
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if($topLeader)
                        <div class="inline-flex items-center px-1 pt-1 text-sm font-bold text-amber-700">
                            🏆 Leader: {{ $topLeader->name }} ({{ $topLeader->points }} pts)
                        </div>
                    @endif
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('chapters.index')" :active="request()->routeIs('chapters.*')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                        Chapters
                    </x-nav-link>
                    <x-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                        Leaderboard
                    </x-nav-link>
                    <x-nav-link :href="route('vote.index')" :active="request()->routeIs('vote.*')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                        Vote
                    </x-nav-link>
                    @can('admin')
                        <x-nav-link :href="route('admin.edits.index')" :active="request()->routeIs('admin.edits.*')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                            Review Suggestions
                        </x-nav-link>
                        <x-nav-link :href="route('admin.chapters.index')" :active="request()->routeIs('admin.chapters.*')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                            Upload Chapters
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">
                            Users
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-full text-amber-900 bg-amber-100 hover:bg-amber-200 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-amber-900 hover:bg-amber-50">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-amber-900 hover:bg-amber-50">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-amber-800 hover:text-amber-600 hover:bg-amber-50 focus:outline-none focus:bg-amber-50 focus:text-amber-600 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-amber-900 font-semibold">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chapters.index')" :active="request()->routeIs('chapters.*')" class="text-amber-900 font-semibold">
                Chapters
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('leaderboard')" :active="request()->routeIs('leaderboard')" class="text-amber-900 font-semibold">
                Leaderboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('vote.index')" :active="request()->routeIs('vote.*')" class="text-amber-900 font-semibold">
                Vote
            </x-responsive-nav-link>
            @can('admin')
                <x-responsive-nav-link :href="route('admin.edits.index')" :active="request()->routeIs('admin.edits.*')" class="text-amber-900 font-semibold">
                    Review Suggestions
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.chapters.index')" :active="request()->routeIs('admin.chapters.*')" class="text-amber-900 font-semibold">
                    Upload Chapters
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-amber-900 font-semibold">
                    Users
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-amber-200">
            <div class="px-4">
                <div class="font-bold text-base text-amber-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-amber-800/70">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-amber-900">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-amber-900">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
