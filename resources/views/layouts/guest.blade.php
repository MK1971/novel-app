@props([
    'pageTitle' => null,
    'metaDescription' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('layouts.partials.theme-boot')

        <title>{{ $pageTitle ?? config('app.name', 'What\'s My Book Name') }}</title>
        @include('layouts.partials.seo-head', ['pageTitle' => $pageTitle, 'metaDescription' => $metaDescription])
        {{ $headMeta ?? '' }}

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
    <body class="min-h-screen antialiased bg-[#fff9f0] dark:bg-stone-950 text-[#2c2419] dark:text-amber-50 transition-colors duration-200" style="font-family: 'Nunito', sans-serif;">
        @php $guestShowSidebar = !isset($hideSidebar) || !$hideSidebar; @endphp
        <div class="min-h-screen flex flex-col">
            {{-- Top Navigation --}}
            <nav class="novel-reader-focus-hide sticky top-0 z-40 border-b border-amber-200/60 dark:border-stone-700 bg-white/80 dark:bg-stone-900/85 backdrop-blur-sm">
                <div class="max-w-full mx-auto px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-8">
                        <a href="{{ url('/') }}" class="text-2xl font-extrabold text-amber-800 dark:text-amber-200 tracking-tight">
                            What's My Book Name
                        </a>
                        @if($topLeader)
                            <a href="{{ route('leaderboard') }}" class="hidden lg:inline-flex items-center px-4 py-1.5 bg-amber-100 text-amber-950 text-sm font-bold rounded-full border border-amber-200/50 hover:bg-amber-200/80 transition-colors" title="Open full leaderboard">
                                <span class="mr-2" aria-hidden="true">🏆</span>
                                <span class="sr-only">Top contributor — open leaderboard: </span>
                                <span class="text-amber-900/85 mr-1">Top contributor:</span>
                                <span>{{ $topLeader->name }}</span>
                                <span class="mx-2 text-amber-900/35" aria-hidden="true">|</span>
                                <span>{{ $topLeader->points }} pts</span>
                            </a>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-4">
                        @include('layouts.partials.theme-toggle')
                        @auth
                            @include('layouts.partials.nav-account-menu')
                        @else
                            <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="px-6 py-2 bg-amber-500 dark:bg-amber-400 text-black font-bold rounded-full hover:bg-amber-600 dark:hover:bg-amber-300 transition-all shadow-md shadow-amber-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-600 focus-visible:ring-offset-2 focus-visible:ring-offset-[#fff9f0] dark:focus-visible:ring-offset-stone-950">Sign In</button>
                        @endauth
                    </div>
                </div>
            </nav>

            <div class="w-full">
                @if($guestShowSidebar)
                    @include('layouts.sidebar')
                @endif

                <div class="min-w-0 w-full {{ $guestShowSidebar ? 'app-shell__main-with-rail' : '' }}">
                    @isset($header)
                        <header class="novel-reader-focus-hide bg-white/50 dark:bg-stone-900/40 border-b border-amber-100 dark:border-stone-700 py-8 px-8">
                            <div class="max-w-7xl mx-auto">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="novel-app-main p-8">
                        <div class="max-w-7xl mx-auto">
                            {{ $slot }}
                        </div>
                    </main>

                    <footer class="novel-reader-focus-hide py-8 px-8 border-t border-amber-100 dark:border-stone-800 text-center">
                        <p class="text-amber-900/55 dark:text-stone-500 font-bold mb-6 text-sm">© {{ date('Y') }} What's My Book Name. All rights reserved.</p>
                        <nav class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-sm font-bold text-amber-900/70 dark:text-stone-400" aria-label="Legal">
                            <a href="{{ route('legal.index') }}" class="whitespace-nowrap transition-colors hover:text-amber-900 dark:hover:text-amber-200">Legal</a>
                            <span class="text-amber-300 dark:text-stone-600 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('privacy') }}" class="whitespace-nowrap transition-colors hover:text-amber-900 dark:hover:text-amber-200">Privacy</a>
                            <span class="text-amber-300 dark:text-stone-600 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('terms') }}" class="whitespace-nowrap transition-colors hover:text-amber-900 dark:hover:text-amber-200">Terms</a>
                            <span class="text-amber-300 dark:text-stone-600 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('feedback.index') }}" class="whitespace-nowrap transition-colors hover:text-amber-900 dark:hover:text-amber-200">Feedback</a>
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
