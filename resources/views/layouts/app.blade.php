<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'What\'s My Book Name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="min-h-screen antialiased bg-[#fff9f0] text-[#2c2419]" style="font-family: 'Nunito', sans-serif;">
        <div class="min-h-screen flex flex-col">
            {{-- Top Navigation --}}
            <nav class="border-b border-amber-200/60 bg-white/80 backdrop-blur-sm sticky top-0 z-40">
                <div class="max-w-full mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-8">
                        <a href="{{ url('/') }}" class="text-2xl font-extrabold text-amber-800 tracking-tight">
                            What's My Book Name
                        </a>
                        @if($topLeader)
                            <div class="hidden lg:flex items-center px-4 py-1.5 bg-amber-100 text-amber-900 text-sm font-bold rounded-full border border-amber-200/50">
                                <span class="mr-2">🏆</span>
                                <span class="opacity-60 mr-1">Leader:</span>
                                <span>{{ $topLeader->name }}</span>
                                <span class="mx-2 opacity-20">|</span>
                                <span>{{ $topLeader->points }} pts</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-4">
                        {{-- Notification Bell --}}
                        <div class="relative">
                            <button id="notification-bell" class="relative p-2 text-amber-900 hover:bg-amber-50 rounded-full transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                @php
                                    $unreadCount = auth()->user()->notifications()->where('read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            
                            {{-- Notification Dropdown --}}
                            <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-amber-100 z-50 max-h-96 overflow-y-auto">
                                <div class="p-4 border-b border-amber-100">
                                    <h3 class="font-bold text-amber-900">Notifications</h3>
                                </div>
                                <div id="notification-list" class="divide-y divide-amber-100">
                                    @forelse(auth()->user()->notifications()->latest()->limit(10)->get() as $notification)
                                        <div class="p-4 hover:bg-amber-50 transition-all {{ !$notification->read ? 'bg-amber-50' : '' }}">
                                            <p class="text-sm font-bold text-amber-900">{{ $notification->title }}</p>
                                            <p class="text-xs text-amber-600 mt-1">{{ $notification->message }}</p>
                                            <p class="text-xs text-amber-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    @empty
                                        <div class="p-8 text-center">
                                            <p class="text-sm text-amber-600 font-bold">No notifications yet</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-4 py-2 border border-amber-200 text-sm font-bold rounded-full text-amber-900 bg-white hover:bg-amber-50 focus:outline-none transition-all shadow-sm">
                                    <div class="w-6 h-6 bg-amber-500 rounded-full mr-2 flex items-center justify-center text-[10px] text-black">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <div>{{ Auth::user()->name }}</div>
                                    <svg class="ms-2 h-4 w-4 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" class="text-amber-900 font-bold hover:bg-amber-50">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                @if(Auth::user()->email === 'admin@example.com')
                                    <x-dropdown-link href="#" onclick="alert('Admin panel coming soon!')" class="text-amber-600 font-bold hover:bg-amber-50">
                                        {{ __('Admin Panel') }}
                                    </x-dropdown-link>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 font-bold hover:bg-red-50">
                                        {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </nav>

            <div class="flex flex-1 overflow-hidden">
                {{-- Sidebar --}}
                @if(!isset($hideSidebar) || !$hideSidebar)
                    @include('layouts.sidebar')
                @endif

                {{-- Main Content Area --}}
                <div class="flex-1 flex flex-col overflow-y-auto">
                    @isset($header)
                        <header class="bg-white/50 border-b border-amber-100 py-8 px-8">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="flex-grow p-8">
                        <div class="max-w-7xl mx-auto">
                            {{ $slot }}
                        </div>
                    </main>
                    
                    <footer class="py-8 px-8 border-t border-amber-100 text-center">
                        <p class="text-amber-900/30 text-sm font-bold">© {{ date('Y') }} What's My Book Name. All rights reserved.</p>
                    </footer>
                </div>
            </div>
        </div>

        {{-- Auth modals for guests --}}
        @if (!auth()->check())
            @include('auth.modals')
        @endif

        <script>
            // Notification Bell Toggle
            document.getElementById('notification-bell')?.addEventListener('click', function() {
                const dropdown = document.getElementById('notification-dropdown');
                dropdown.classList.toggle('hidden');
            });

            // Close notification dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const bell = document.getElementById('notification-bell');
                const dropdown = document.getElementById('notification-dropdown');
                if (bell && dropdown && !bell.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>
    </body>
</html>
