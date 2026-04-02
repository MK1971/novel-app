@props([
    'pageTitle' => null,
    'metaDescription' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace("_", "-", app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle ?? config("app.name", "What's My Book Name") }}</title>
        @include('layouts.partials.seo-head', ['pageTitle' => $pageTitle, 'metaDescription' => $metaDescription])
        {{ $headMeta ?? '' }}

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,600,700,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(["resources/css/app.css", "resources/js/app.js"])
        
        <style>
            [x-cloak] { display: none !important; }
            :root {
                --app-shell-nav-h: 4.5rem;
                --app-shell-rail-w: 18rem;
            }
            /* Inset main column for fixed rail — do not rely on Tailwind arbitrary var() (often not emitted in build) */
            @media (min-width: 768px) {
                .app-shell__main-with-rail {
                    padding-left: var(--app-shell-rail-w, 18rem);
                }
            }
        </style>
    </head>
    <body class="min-h-screen antialiased bg-[#fff9f0] text-[#2c2419]" style="font-family: 'Nunito', sans-serif;">
        @php $showSidebarNav = !isset($hideSidebar) || !$hideSidebar; @endphp
        <div
            class="min-h-screen flex flex-col"
            x-data="{ mobileNavOpen: false }"
            @keydown.escape.window="mobileNavOpen = false"
        >
            {{-- Top Navigation (sticky: document scrolls; no h-screen / overflow-hidden trap) --}}
            <nav class="sticky top-0 z-40 border-b border-amber-200/60 bg-white/80 backdrop-blur-sm">
                <div class="max-w-full mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3 md:gap-8 min-w-0">
                        @if ($showSidebarNav)
                            <button
                                type="button"
                                class="md:hidden shrink-0 inline-flex items-center justify-center w-11 h-11 rounded-2xl border border-amber-200 bg-white text-amber-900 shadow-sm hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-400"
                                @click="mobileNavOpen = true"
                                aria-label="Open main menu"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        @endif
                        <a href="{{ url("/") }}" class="text-xl sm:text-2xl font-extrabold text-amber-800 tracking-tight truncate">
                            What's My Book Name
                        </a>
                        @if($topLeader)
                            <div class="hidden lg:flex items-center px-4 py-1.5 bg-amber-100 text-amber-900 text-sm font-bold rounded-full border border-amber-200/50">
                                <span class="mr-2" aria-hidden="true">🏆</span>
                                <span class="sr-only">Top leaderboard: </span>
                                <span class="opacity-60 mr-1">Leader:</span>
                                <span>{{ $topLeader->name }}</span>
                                <span class="mx-2 opacity-20" aria-hidden="true">|</span>
                                <span>{{ $topLeader->points }} pts</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center gap-2 sm:gap-4">
                        @include('layouts.partials.nav-account-menu')
                    </div>
                </div>
            </nav>

            @if ($showSidebarNav)
                {{-- Mobile drawer: same links as desktop sidebar (md:hidden) --}}
                <div
                    x-show="mobileNavOpen"
                    x-cloak
                    x-transition.opacity
                    class="md:hidden fixed inset-0 z-50"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Main menu"
                >
                    <div class="absolute inset-0 bg-black/40" @click="mobileNavOpen = false"></div>
                    <aside
                        class="absolute inset-y-0 left-0 z-10 w-[min(100vw,18rem)] min-w-[16rem] max-w-full bg-white/95 backdrop-blur-md border-r border-amber-200/60 shadow-xl overflow-y-auto"
                        @click.outside="mobileNavOpen = false"
                    >
                        <div class="flex items-center justify-between p-4 border-b border-amber-100">
                            <span class="text-sm font-extrabold text-amber-900">Menu</span>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-amber-200 text-amber-900 hover:bg-amber-50"
                                @click="mobileNavOpen = false"
                                aria-label="Close menu"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        <div
                            @click.capture="
                                const a = $event.target.closest('a[href]');
                                if (a) { const h = a.getAttribute('href'); if (h && h.indexOf('#') !== 0) mobileNavOpen = false; }
                                if ($event.target.closest('button[type=submit]')) mobileNavOpen = false;
                            "
                        >
                            @include('layouts.partials.sidebar-inner')
                        </div>
                    </aside>
                </div>
            @endif

            {{-- Sidebar is fixed on md+; .app-shell__main-with-rail adds padding-left (see <style> in head) --}}
            <div class="w-full">
                @if($showSidebarNav)
                    @include("layouts.sidebar")
                @endif

                <div class="min-w-0 w-full {{ $showSidebarNav ? 'app-shell__main-with-rail' : '' }}">
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
