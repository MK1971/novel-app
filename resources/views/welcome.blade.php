<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'WhatsMyBookName') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
            /* Off-screen until focused (Safari-friendly vs clip-only hiding) */
            /* Button (not link): Safari’s default Tab includes buttons, not links */
            .skip-to-main {
                position: absolute;
                left: -10000px;
                top: auto;
                width: 1px;
                height: 1px;
                overflow: hidden;
                z-index: 10000;
                margin: 0;
                padding: 0;
                border: none;
                background: transparent;
                font: inherit;
                color: inherit;
                cursor: pointer;
                -webkit-appearance: none;
                appearance: none;
            }
            .skip-to-main:focus {
                position: fixed;
                left: 1rem;
                top: 1rem;
                width: auto;
                height: auto;
                padding: 0.75rem 1.25rem;
                overflow: visible;
                border-radius: 0.75rem;
                background: #0c0a09;
                color: #fef3c7;
                font-weight: 800;
                font-size: 0.875rem;
                text-align: center;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4), 0 0 0 3px #fbbf24;
                outline: 3px solid #fbbf24;
                outline-offset: 2px;
            }
            /* Safari + Tailwind: use :focus with :focus-visible mouse suppression where supported */
            #landing-root nav :is(a, button):focus,
            #landing-root main > header :is(a, button):focus {
                outline: 3px solid #fde68a !important;
                outline-offset: 3px;
                box-shadow: 0 0 0 3px rgba(253, 230, 138, 0.95);
            }
            #landing-root nav .bg-amber-500:focus,
            #landing-root main > header .bg-amber-500:focus {
                outline-color: #1c1917 !important;
                box-shadow: 0 0 0 3px rgba(28, 25, 23, 0.95);
            }
            @supports selector(:focus-visible) {
                #landing-root nav :is(a, button):focus:not(:focus-visible),
                #landing-root main > header :is(a, button):focus:not(:focus-visible) {
                    outline: none !important;
                    box-shadow: none;
                }
                #landing-root nav .bg-amber-500:focus:not(:focus-visible),
                #landing-root main > header .bg-amber-500:focus:not(:focus-visible) {
                    outline: none !important;
                    box-shadow: none;
                }
            }
            #landing-root main > section :is(a, button):focus,
            #landing-root footer :is(a, button):focus {
                outline: 3px solid #b45309 !important;
                outline-offset: 3px;
                box-shadow: 0 0 0 3px rgba(180, 83, 9, 0.35);
            }
            @supports selector(:focus-visible) {
                #landing-root main > section :is(a, button):focus:not(:focus-visible),
                #landing-root footer :is(a, button):focus:not(:focus-visible) {
                    outline: none !important;
                    box-shadow: none;
                }
            }
            /* Scroll on small viewports / touch — fixed is janky on iOS (UX #13) */
            .bg-book-pattern {
                background-image: linear-gradient(rgba(0, 0, 0, 0.68), rgba(0, 0, 0, 0.86)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2256&auto=format&fit=crop');
                background-size: cover;
                background-position: center;
                background-attachment: scroll;
            }
            @media (min-width: 768px) {
                .bg-book-pattern {
                    background-attachment: fixed;
                }
            }
            /* Hero legibility over busy photo (UX #10) */
            .hero-foreground h1,
            .hero-foreground .hero-lead {
                text-shadow: 0 2px 28px rgba(0, 0, 0, 0.75), 0 1px 3px rgba(0, 0, 0, 0.9);
            }
            .hero-foreground .hero-eyebrow {
                text-shadow: 0 1px 12px rgba(0, 0, 0, 0.65);
            }
            .hero-foreground .hero-cta-subline-shadow {
                text-shadow: 0 1px 16px rgba(0, 0, 0, 0.72);
            }
            @keyframes typing {
                from { width: 0 }
                to { width: 100% }
            }
            @keyframes blink-caret {
                from, to { border-color: transparent }
                50% { border-color: #d97706; }
            }
            .typewriter h1 {
                overflow: hidden;
                border-right: .15em solid #d97706;
                white-space: normal;
                margin: 0 auto;
                letter-spacing: -.02em;
                animation:
                    typing 3.5s steps(40, end),
                    blink-caret .75s step-end infinite;
                max-width: 100%;
                word-wrap: break-word;
            }
            /* Typewriter + wrapping fights the caret on small screens (UX #11) */
            @media (max-width: 767px) {
                .typewriter h1 {
                    animation: none !important;
                    border-right-width: 0 !important;
                    border-right-color: transparent !important;
                    overflow: visible;
                }
            }
            .book-mockup {
                perspective: 1000px;
            }
            .book-cover {
                transform: rotateY(-20deg) rotateX(5deg);
                box-shadow: 20px 20px 60px rgba(0,0,0,0.5);
                transition: transform 0.5s ease;
            }
            .book-cover:hover {
                transform: rotateY(-10deg) rotateX(0deg);
            }
            @media (prefers-reduced-motion: reduce) {
                .typewriter h1 {
                    animation: none !important;
                    border-right-width: 0 !important;
                    border-right-color: transparent !important;
                }
                .hero-ping-dot {
                    animation: none !important;
                }
                .book-cover {
                    transition: none;
                }
                .book-cover:hover {
                    transform: rotateY(-20deg) rotateX(5deg);
                }
                .landing-motion-card,
                .landing-motion-card .landing-motion-icon {
                    transition: none !important;
                }
                .landing-motion-card:hover {
                    transform: none !important;
                }
                .landing-motion-card:hover .landing-motion-icon {
                    transform: none !important;
                }
                #landing-root main > header a.transform,
                #landing-root main > header button.transform {
                    transition: none !important;
                }
                #landing-root main > header a:hover,
                #landing-root main > header button:hover {
                    transform: none !important;
                }
            }
        </style>
    </head>
    <body class="antialiased font-['Nunito'] bg-[#fff9f0] text-amber-900 selection:bg-amber-200 selection:text-amber-900" x-data="{ showLoginModal: false, showRegisterModal: false, mobileNavOpen: false }" @keydown.escape.window="mobileNavOpen = false">
        <button type="button" class="skip-to-main" id="skip-to-main-btn" onclick="window.skipToMainContent && window.skipToMainContent()">Skip to main content</button>
        <script>
            window.skipToMainContent = function () {
                var m = document.getElementById('main-content');
                if (!m) return;
                var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                m.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
                requestAnimationFrame(function () { m.focus({ preventScroll: true }); });
            };
        </script>
        <div id="landing-root" class="min-h-screen bg-book-pattern flex flex-col">
            {{-- Solid light bar (opaque) so it stays visible over hero + cream sections; amber only on CTAs like Join Now --}}
            <nav class="sticky top-0 z-50 bg-white border-b border-amber-200/90 shadow-md shadow-amber-900/10 relative" @click.outside="mobileNavOpen = false">
                <div class="max-w-7xl mx-auto px-6 min-h-20 flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 min-h-11 py-2 -my-1 rounded-lg text-amber-900 hover:text-amber-700 transition-colors focus:outline-none">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25 shrink-0" aria-hidden="true">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-xl font-black tracking-tight">WhatsMyBookName</span>
                    </a>

                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Chapters</a>
                        <a href="{{ route('leaderboard') }}" class="inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Leaderboard</a>
                        <a href="{{ route('vote.index') }}" class="inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Peter Trull</a>
                        <a href="{{ route('about') }}" class="inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">About</a>
                    </div>

                    <div class="hidden md:flex items-center gap-4">
                        @auth
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-black uppercase tracking-widest text-amber-800/80">Member</span>
                                    <span class="text-sm font-black text-amber-950">{{ Auth::user()->name }}</span>
                                </div>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center min-h-11 px-6 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25">Dashboard</a>
                            </div>
                        @else
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="inline-flex items-center min-h-11 px-2 -mx-2 text-sm font-bold text-amber-900 hover:text-amber-700 transition-colors rounded-lg">Sign In</button>
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="inline-flex items-center justify-center min-h-11 px-6 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25">Join Now</button>
                        @endauth
                    </div>

                    <div class="md:hidden relative shrink-0">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center min-h-11 min-w-11 rounded-xl text-amber-900 border border-amber-200 bg-amber-50 hover:bg-amber-100 transition-colors"
                            aria-controls="landing-mobile-menu"
                            x-bind:aria-expanded="mobileNavOpen"
                            @click="mobileNavOpen = !mobileNavOpen"
                        >
                            <span class="sr-only">Toggle menu</span>
                            <svg x-show="!mobileNavOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <svg x-show="mobileNavOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div
                            id="landing-mobile-menu"
                            x-cloak
                            x-show="mobileNavOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1 scale-[0.98]"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-1 scale-[0.98]"
                            class="absolute right-0 top-full z-50 pt-1 pb-2"
                        >
                            <div class="w-[10.25rem] max-w-[calc(100vw-2rem)] rounded-2xl border border-white/15 bg-stone-950/92 backdrop-blur-xl shadow-xl shadow-black/40 overflow-hidden max-h-[min(72vh,20rem)] flex flex-col">
                                <nav class="flex flex-col gap-0.5 p-1.5 overflow-y-auto shrink" aria-label="Mobile navigation">
                                    <a href="{{ route('chapters.index', ['resume' => 1]) }}" @click="mobileNavOpen = false" class="flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Chapters</a>
                                    <a href="{{ route('leaderboard') }}" @click="mobileNavOpen = false" class="flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Leaderboard</a>
                                    <a href="{{ route('vote.index') }}" @click="mobileNavOpen = false" class="flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Peter Trull</a>
                                    <a href="{{ route('about') }}" @click="mobileNavOpen = false" class="flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">About</a>
                                </nav>
                                @auth
                                    <div class="p-2 space-y-2 bg-black/20">
                                        <div class="px-2 pt-0.5">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-200/80">Member</p>
                                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                                        </div>
                                        <a href="{{ route('dashboard') }}" @click="mobileNavOpen = false" class="inline-flex items-center justify-center w-full min-h-11 px-4 bg-amber-500 text-black text-xs font-black rounded-xl hover:bg-amber-600 transition-colors shadow-md shadow-amber-500/15">Dashboard</a>
                                    </div>
                                @else
                                    <div class="p-2 flex flex-col gap-1.5 bg-black/20">
                                        <button type="button" @click="mobileNavOpen = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="flex items-center justify-center w-full min-h-11 text-xs font-bold text-white rounded-xl bg-white/5 hover:bg-white/12 transition-colors">Sign In</button>
                                        <button type="button" @click="mobileNavOpen = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="inline-flex items-center justify-center w-full min-h-11 px-4 bg-amber-500 text-black text-xs font-black rounded-xl hover:bg-amber-600 transition-colors">Join Now</button>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main id="main-content" tabindex="-1">
            {{-- Hero Section --}}
            <header class="relative flex-grow flex items-center pt-20 pb-32 overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="text-left hero-foreground">
                            <div class="hero-eyebrow inline-flex items-center gap-2 px-4 py-2 bg-amber-500/20 rounded-full text-amber-400 text-xs font-black uppercase tracking-widest mb-8 border border-amber-500/30 backdrop-blur-md">
                                <span class="relative flex h-2 w-2">
                                    <span class="hero-ping-dot animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                Round 1 is Live
                            </div>
                            
                            <div class="typewriter mb-8">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.2] break-words whitespace-normal">
                                    Where your edits shape the narrative...
                                </h1>
                            </div>
                            
                            <p class="hero-lead text-xl md:text-2xl text-white/95 font-bold mb-12 leading-relaxed max-w-xl">
                                A two-part collaborative journey where your edits shape the narrative and your votes decide the final mystery.
                            </p>

                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                <div class="flex flex-col items-center sm:items-start gap-2 w-full sm:w-auto">
                                    <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="w-full sm:w-auto px-10 py-5 bg-amber-500 text-black text-lg font-black rounded-2xl hover:bg-amber-600 transition-all shadow-2xl shadow-amber-500/30 transform hover:-translate-y-1 text-center">
                                        Start Your Adventure
                                    </a>
                                    <p id="landing-hero-cta-subline" class="text-sm font-bold text-white/90 text-center sm:text-left max-w-xs sm:max-w-sm leading-snug hero-cta-subline-shadow">
                                        Opens the chapter list — read the story and jump in with edits when you’re ready.
                                    </p>
                                </div>
                                @guest
                                    <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="w-full sm:w-auto px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/20 text-white text-lg font-black rounded-2xl hover:bg-white/20 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Join the Community
                                    </button>
                                @else
                                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/20 text-white text-lg font-black rounded-2xl hover:bg-white/20 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Go to Dashboard
                                    </a>
                                @endguest
                            </div>
                        </div>

                        <div class="hidden lg:flex justify-center book-mockup">
                            <div class="relative w-80 h-[500px] book-cover bg-gradient-to-br from-amber-900 to-black rounded-r-2xl overflow-hidden border-l-8 border-amber-950 shadow-2xl">
                                <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/leather.png')]"></div>
                                <div class="relative h-full flex flex-col items-center justify-center p-8 text-center">
                                    <div class="w-20 h-20 bg-amber-500 rounded-2xl mb-8 flex items-center justify-center shadow-2xl">
                                        <svg class="w-12 h-12 text-amber-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <p class="text-3xl font-black text-white mb-4 tracking-tighter uppercase">THE BOOK <br>WITH NO <br>NAME</p>
                                    <div class="w-16 h-1 bg-amber-500 mb-6"></div>
                                    <p class="text-amber-200 font-bold italic">"A collaborative masterpiece in the making"</p>
                                    <div class="mt-auto text-white/55 text-sm font-bold tracking-widest uppercase">Volume I</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- The Two-Part Journey --}}
            <section class="py-32 bg-white border-y border-amber-100">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-20">
                        <h2 class="text-4xl md:text-5xl font-black text-amber-900 mb-6">The Journey</h2>
                        <p class="text-xl text-amber-900/70 font-bold max-w-2xl mx-auto">Two distinct ways to contribute, earn points, and win prizes.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12">
                        {{-- Part 1 --}}
                        <div class="landing-motion-card group bg-[#fff9f0] p-12 rounded-[3rem] border border-amber-100 shadow-sm hover:shadow-2xl hover:shadow-amber-900/5 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="landing-motion-icon w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-600">01</span>
                            </div>
                            <h3 class="text-3xl font-black text-amber-900 mb-6">The Book With No Name</h3>
                            <p class="text-lg text-amber-900/70 font-bold mb-8 leading-relaxed">
                                Read the evolving story and suggest edits. For a small $2 fee, your contribution could become a permanent part of the novel.
                            </p>
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-center gap-3 text-amber-900 font-bold">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Earn 50 points for accepted edits
                                </li>
                                <li class="flex items-center gap-3 text-amber-900 font-bold">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Climb the global leaderboard
                                </li>
                            </ul>
                            <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="inline-flex items-center gap-2 text-amber-600 font-black hover:gap-4 transition-all">
                                Browse Chapters <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>

                        {{-- Part 2 --}}
                        <div class="landing-motion-card group bg-amber-900 p-12 rounded-[3rem] text-white shadow-2xl shadow-amber-900/20 hover:shadow-amber-900/40 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="landing-motion-icon w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-400">02</span>
                            </div>
                            <h3 class="text-3xl font-black mb-6">Peter Trull: Solitary Detective</h3>
                            <p class="text-lg text-amber-100/75 font-bold mb-8 leading-relaxed">
                                Compare two versions of the same chapter and vote for the best one. Your votes decide the final direction of the detective's story.
                            </p>
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-center gap-3 font-bold">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Unlocked after your first edit
                                </li>
                                <li class="flex items-center gap-3 font-bold">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Shape the final mystery
                                </li>
                            </ul>
                            <a href="{{ route('vote.index') }}" class="inline-flex items-center gap-2 text-amber-400 font-black hover:gap-4 transition-all">
                                Start Voting <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Social proof — project voice / tagline (UX #15) --}}
            <section id="landing-social-proof" class="py-16 bg-[#fff9f0] border-y border-amber-100" aria-labelledby="landing-social-proof-heading">
                <h2 id="landing-social-proof-heading" class="sr-only">Community voices</h2>
                <div class="max-w-3xl mx-auto px-6 text-center">
                    <blockquote class="text-2xl md:text-3xl font-black text-amber-900 leading-snug tracking-tight">
                        <p>“A collaborative masterpiece in the making.”</p>
                    </blockquote>
                    <p class="mt-5 text-sm font-bold text-amber-800/75">
                        From the living manuscript —
                        <cite class="not-italic font-black text-amber-900">The Book With No Name</cite>
                    </p>
                    <p class="mt-8 text-xs font-black uppercase tracking-widest text-amber-800/55">Reader-powered fiction</p>
                </div>
            </section>

            {{-- Community Stats — live counts + configurable prize goal (UX #14) --}}
            <section class="py-32" aria-labelledby="landing-stats-heading">
                <h2 id="landing-stats-heading" class="sr-only">Community statistics</h2>
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['contributors'] }}</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Contributors</div>
                            <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">With an accepted edit</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['edits_accepted'] }}</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Edits Accepted</div>
                            <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">Story and inline</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['chapters_live'] }}</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Chapters Live</div>
                            <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">Published</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['prize_pool'] }}</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Prize goal</div>
                            <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">Campaign pool</div>
                        </div>
                    </div>
                    <p id="landing-stats-footnote" class="mt-10 text-center text-xs font-bold text-amber-800/60 max-w-2xl mx-auto leading-relaxed">
                        Community figures update as people contribute. The prize line is the announced campaign goal, not a live tally.
                    </p>
                </div>
            </section>
            </main>

            {{-- Footer --}}
            <footer class="py-20 border-t border-amber-100 bg-white">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <div class="flex items-center justify-center gap-3 mb-8">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-lg font-black tracking-tight text-amber-900">WhatsMyBookName</span>
                    </div>
                    <p class="text-amber-900/55 font-bold mb-8">© 2026 WhatsMyBookName. All rights reserved.</p>
                    <div class="flex items-center justify-center gap-8">
                        <a href="#" class="text-sm font-bold text-amber-900/70 hover:text-amber-900">Privacy Policy</a>
                        <a href="#" class="text-sm font-bold text-amber-900/70 hover:text-amber-900">Terms of Service</a>
                        <a href="{{ route('feedback.index') }}" class="text-sm font-bold text-amber-900/70 hover:text-amber-900">Feedback</a>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Auth Modals --}}
        @include('auth.modals')
    </body>
</html>
