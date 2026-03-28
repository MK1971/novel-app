<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config("app.name", "What's My Book Name") }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
        
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
                        <a href="{{ url("/") }}" class="text-2xl font-extrabold text-amber-800 tracking-tight">
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
                                @can('admin')
                                    <x-dropdown-link :href="route('admin.inline-edits.index')" class="text-amber-600 font-bold hover:bg-amber-50">
                                        {{ __('Moderation') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('admin.users.index')" class="text-amber-600 font-bold hover:bg-amber-50">
                                        {{ __('User Management') }}
                                    </x-dropdown-link>
                                @endcan
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

            <div class="flex flex-1">
                {{-- Sidebar --}}
                @if(!isset($hideSidebar) || !$hideSidebar)
                    @include("layouts.sidebar")
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
                        <p class="text-amber-900/30 text-sm font-bold">© {{ date("Y") }} What's My Book Name. All rights reserved.</p>
                    </footer>
                </div>
            </div>
        </div>

        {{-- Auth modals for guests --}}
        @if (!auth()->check())
            @include("auth.modals")
        @endif
    </body>
</html>
