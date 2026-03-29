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
            :root {
                --app-shell-nav-h: 4.5rem;
                --app-shell-rail-w: 18rem;
            }
            @media (min-width: 768px) {
                .app-shell__main-with-rail {
                    padding-left: var(--app-shell-rail-w, 18rem);
                }
            }
        </style>
    </head>
    <body class="min-h-screen antialiased bg-[#fff9f0] text-[#2c2419]" style="font-family: 'Nunito', sans-serif;">
        @php $guestShowSidebar = !isset($hideSidebar) || !$hideSidebar; @endphp
        <div class="min-h-screen flex flex-col">
            {{-- Top Navigation --}}
            <nav class="sticky top-0 z-40 border-b border-amber-200/60 bg-white/80 backdrop-blur-sm">
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
                        <button onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="px-6 py-2 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-all shadow-md shadow-amber-500/20">Sign In</button>
                    </div>
                </div>
            </nav>

            <div class="w-full">
                @if($guestShowSidebar)
                    @include('layouts.sidebar')
                @endif

                <div class="min-w-0 w-full {{ $guestShowSidebar ? 'app-shell__main-with-rail' : '' }}">
                    @isset($header)
                        <header class="bg-white/50 border-b border-amber-100 py-8 px-8">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="p-8">
                        <div class="max-w-7xl mx-auto">
                            {{ $slot }}
                        </div>
                    </main>

                    <footer class="py-8 px-8 border-t border-amber-100 text-center">
                        <p class="text-amber-900/55 font-bold mb-6 text-sm">© {{ date('Y') }} What's My Book Name. All rights reserved.</p>
                        <nav class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-sm font-bold text-amber-900/70" aria-label="Legal">
                            <a href="{{ route('privacy') }}" class="whitespace-nowrap transition-colors hover:text-amber-900">Privacy Policy</a>
                            <span class="text-amber-300 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('terms') }}" class="whitespace-nowrap transition-colors hover:text-amber-900">Terms of Service</a>
                            <span class="text-amber-300 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('feedback.index') }}" class="whitespace-nowrap transition-colors hover:text-amber-900">Feedback</a>
                        </nav>
                    </footer>
                </div>
            </div>
        </div>

        {{-- Auth modals for guests --}}
        @if (!auth()->check())
            @include('auth.modals')
        @endif
    </body>
</html>
