<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('layouts.partials.theme-boot')
        <title>{{ config('app.name', 'WhatsMyBookName') }}</title>
        @include('layouts.partials.seo-head', [
            'pageTitle' => config('app.name', 'WhatsMyBookName'),
            'metaDescription' => config('seo.default_description'),
        ])
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700,800,900&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
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
            /* Self-hosted hero (UX #19); scroll on small / touch, fixed from md+ (UX #13) */
            .landing-hero-bg {
                position: absolute;
                inset: 0;
                z-index: 0;
                overflow: hidden;
                pointer-events: none;
            }
            .landing-hero-bg-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: center;
            }
            .landing-hero-bg-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(rgba(0, 0, 0, 0.68), rgba(0, 0, 0, 0.86));
            }
            @media (min-width: 768px) {
                .landing-hero-bg {
                    position: fixed;
                    height: 100vh;
                    width: 100%;
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
            /* Hero headline: reserved height + fade-in (avoids width-based “typing” reflow) */
            .landing-hero-headline-slot {
                min-height: 7.25rem;
            }
            @media (min-width: 768px) {
                .landing-hero-headline-slot {
                    min-height: 8.75rem;
                }
            }
            @media (min-width: 1024px) {
                .landing-hero-headline-slot {
                    min-height: 10.5rem;
                }
            }
            /* Headline: JS typewriter + fixed min-height on slot limits vertical jump */
            .landing-hero-headline-slot h1 {
                margin: 0;
                letter-spacing: -0.02em;
                overflow: visible;
            }
            .landing-hero-typewriter-caret {
                display: inline-block;
                width: 0.12em;
                min-width: 3px;
                height: 0.82em;
                margin-left: 0.06em;
                background: #fbbf24;
                vertical-align: -0.07em;
                border-radius: 1px;
                animation: landingHeroCaretBlink 0.95s step-end infinite;
            }
            .landing-hero-typewriter-caret.is-done {
                animation: none;
                opacity: 0;
                width: 0;
                min-width: 0;
                margin-left: 0;
                transition: opacity 0.45s ease, width 0.35s ease, margin 0.35s ease;
            }
            @keyframes landingHeroCaretBlink {
                0%, 45% { opacity: 1; }
                50%, 100% { opacity: 0; }
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
            /* Micro-interactions: shared duration/easing for nav + card CTAs (UX #22) */
            #landing-root .landing-ui-transition {
                transition-duration: 200ms;
                transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }

            @media (prefers-reduced-motion: reduce) {
                .landing-hero-typewriter-caret {
                    display: none !important;
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
                #landing-root .landing-ui-transition {
                    transition-duration: 0.01ms !important;
                }
            }
        </style>
    </head>
    <body class="antialiased font-['Nunito'] bg-[#fff9f0] dark:bg-stone-950 text-amber-900 dark:text-amber-100 selection:bg-amber-200 selection:text-amber-900 dark:selection:bg-amber-700 dark:selection:text-amber-50 transition-colors duration-200" x-data="{ showLoginModal: false, showRegisterModal: false, mobileNavOpen: false }" @keydown.escape.window="mobileNavOpen = false">
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
        <div id="landing-root" class="relative flex min-h-screen flex-col">
            <div class="landing-hero-bg" aria-hidden="true">
                <img
                    src="{{ asset('images/landing/hero-books-960.jpg') }}"
                    srcset="{{ asset('images/landing/hero-books-960.jpg') }} 960w, {{ asset('images/landing/hero-books-1920.jpg') }} 1920w"
                    sizes="100vw"
                    width="1920"
                    height="1280"
                    fetchpriority="high"
                    alt=""
                    class="landing-hero-bg-img"
                    decoding="async"
                >
                <div class="landing-hero-bg-overlay"></div>
            </div>
            {{-- Solid light bar (opaque) so it stays visible over hero + cream sections; amber only on CTAs like Join Now --}}
            <nav class="relative z-50 sticky top-0 bg-white dark:bg-stone-900 border-b border-amber-200/90 dark:border-stone-700 shadow-md shadow-amber-900/10 dark:shadow-black/40" @click.outside="mobileNavOpen = false">
                <div class="max-w-7xl mx-auto px-6 min-h-20 flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="landing-ui-transition flex items-center gap-3 shrink-0 min-h-11 py-2 -my-1 rounded-lg text-amber-900 hover:text-amber-700 transition-colors focus:outline-none">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/25 shrink-0" aria-hidden="true">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-xl font-black tracking-tight">WhatsMyBookName</span>
                    </a>

                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('chapters.index') }}" class="landing-ui-transition inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Read</a>
                        <a href="{{ route('about') }}" class="landing-ui-transition inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">How it works</a>
                        @guest
                            @if(!($guestFirstVisitNav ?? false))
                                <a href="{{ route('leaderboard') }}" class="landing-ui-transition inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Leaderboard</a>
                                <a href="{{ route('vote.index') }}" class="landing-ui-transition inline-flex items-center min-h-11 max-w-[11rem] md:max-w-none text-xs md:text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg text-center md:text-left leading-snug whitespace-normal">Vote</a>
                            @endif
                        @endguest
                        @auth
                            <a href="{{ route('leaderboard') }}" class="landing-ui-transition inline-flex items-center min-h-11 text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg">Leaderboard</a>
                            <a href="{{ route('vote.index') }}" class="landing-ui-transition inline-flex items-center min-h-11 max-w-[11rem] md:max-w-none text-xs md:text-sm font-bold text-amber-900 hover:text-amber-600 transition-colors px-1 -mx-1 rounded-lg text-center md:text-left leading-snug whitespace-normal">Vote</a>
                        @endauth
                    </div>

                    <div class="hidden md:flex items-center gap-4">
                        @include('layouts.partials.theme-toggle')
                        @auth
                            <div class="flex items-center gap-6">
                                <div class="flex flex-col items-end">
                                    <span class="text-xs font-black uppercase tracking-widest text-amber-800/80">Member</span>
                                    <span class="text-sm font-black text-amber-950">{{ Auth::user()->name }}</span>
                                </div>
                                <a href="{{ route('dashboard') }}" class="landing-ui-transition inline-flex items-center justify-center min-h-11 px-6 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25">Dashboard</a>
                            </div>
                        @else
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="landing-ui-transition inline-flex items-center min-h-11 px-2 -mx-2 text-sm font-bold text-amber-900 hover:text-amber-700 transition-colors rounded-lg">Sign In</button>
                            <button type="button" @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="landing-ui-transition inline-flex items-center justify-center min-h-11 px-6 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/25">Become a Contributor</button>
                        @endauth
                    </div>

                    <div class="md:hidden relative shrink-0">
                        <button
                            type="button"
                            class="landing-ui-transition inline-flex items-center justify-center min-h-11 min-w-11 rounded-xl text-amber-900 border border-amber-200 bg-amber-50 hover:bg-amber-100 transition-colors"
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
                                    <a href="{{ route('chapters.index') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Read</a>
                                    <a href="{{ route('about') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">How it works</a>
                                    @guest
                                        @if(!($guestFirstVisitNav ?? false))
                                            <a href="{{ route('leaderboard') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Leaderboard</a>
                                            <a href="{{ route('vote.index') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-xs font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors leading-snug">Vote</a>
                                        @endif
                                    @endguest
                                    @auth
                                        <a href="{{ route('leaderboard') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-sm font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors">Leaderboard</a>
                                        <a href="{{ route('vote.index') }}" @click="mobileNavOpen = false" class="landing-ui-transition flex items-center min-h-11 rounded-xl px-3 text-xs font-bold text-white hover:bg-white/10 hover:text-amber-100 transition-colors leading-snug">Vote</a>
                                    @endauth
                                </nav>
                                @auth
                                    <div class="p-2 space-y-2 bg-black/20">
                                        <div class="px-2 pt-0.5">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-amber-200/80">Member</p>
                                            <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                                        </div>
                                        <a href="{{ route('dashboard') }}" @click="mobileNavOpen = false" class="landing-ui-transition inline-flex items-center justify-center w-full min-h-11 px-4 bg-amber-500 text-black text-xs font-black rounded-xl hover:bg-amber-600 transition-colors shadow-md shadow-amber-500/15">Dashboard</a>
                                    </div>
                                @else
                                    <div class="p-2 flex flex-col gap-1.5 bg-black/20">
                                        <button type="button" @click="mobileNavOpen = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="landing-ui-transition flex items-center justify-center w-full min-h-11 text-xs font-bold text-white rounded-xl bg-white/5 hover:bg-white/12 transition-colors">Sign In</button>
                                        <button type="button" @click="mobileNavOpen = false; window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="landing-ui-transition inline-flex items-center justify-center w-full min-h-11 px-4 bg-amber-500 text-black text-xs font-black rounded-xl hover:bg-amber-600 transition-colors">Become a Contributor</button>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            @php
                $hasLiveChapters = (int) ($landingStats['chapters_live'] ?? 0) > 0;
            @endphp
            <main id="main-content" class="relative z-10" tabindex="-1">
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
                            
                            <div class="landing-hero-headline-slot mb-8">
                                <h1
                                    class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.2] break-words"
                                    id="landing-hero-headline"
                                    data-type-text="The manuscript is not final. You can change it."
                                >
                                    <span class="landing-hero-typewriter" aria-hidden="true">The manuscript is not final. You can change it.</span><span class="landing-hero-typewriter-caret" aria-hidden="true"></span>
                                </h1>
                            </div>
                            
                            <p class="hero-lead text-xl md:text-2xl text-white/95 font-bold mb-12 leading-relaxed max-w-2xl">
                                <span class="block text-white font-black text-2xl md:text-3xl tracking-tight mb-3">Each chapter is released before it is decided.</span>
                                <span class="block font-bold text-white/95">Read live chapters, submit your version, and vote on what survives. What you change here may become permanent.</span>
                            </p>

                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                <div class="flex flex-col items-center sm:items-start gap-2 w-full sm:w-auto">
                                    <a href="{{ route('chapters.index') }}" data-track-event="landing_cta_primary_click" data-track-label="hero_enter_manuscript" class="landing-ui-transition w-full sm:w-auto px-10 py-5 bg-amber-500 text-black text-lg font-black rounded-2xl hover:bg-amber-600 transition-all shadow-2xl shadow-amber-500/30 transform hover:-translate-y-1 text-center">
                                        {{ $hasLiveChapters ? 'Read the manuscript' : 'Read the manuscript (Opening Soon)' }}
                                    </a>
                                    <p id="landing-hero-cta-subline" class="text-sm font-bold text-white/90 text-center sm:text-left max-w-xs sm:max-w-sm leading-snug hero-cta-subline-shadow">
                                        {{ $hasLiveChapters ? 'Read now. Submit when your wording is ready.' : 'Read the setup and prepare your first submission for Chapter 1.' }}
                                    </p>
                                </div>
                                @guest
                                    <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" data-track-event="landing_cta_signup_click" data-track-label="hero_signup" class="landing-ui-transition w-full sm:w-auto px-10 py-5 bg-stone-900 text-amber-100 text-lg font-black rounded-2xl border-2 border-amber-500/70 hover:bg-black hover:border-amber-400 transition-all shadow-xl shadow-black/35 transform hover:-translate-y-1">
                                        Change the text
                                    </button>
                                @else
                                    <a href="{{ route('dashboard') }}" class="landing-ui-transition w-full sm:w-auto px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/20 text-white text-lg font-black rounded-2xl hover:bg-white/20 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                        Continue in Dashboard
                                    </a>
                                @endguest
                                @auth
                                    <a href="{{ route('dashboard') }}#publishing-fund" class="landing-ui-transition w-full sm:w-auto px-8 py-4 bg-black/30 border border-white/20 text-white text-sm font-black rounded-2xl hover:bg-black/45">
                                        Support publishing fund
                                    </a>
                                @endauth
                            </div>

                            <div class="mt-12 max-w-2xl rounded-[2rem] border border-white/20 bg-black/35 backdrop-blur-md p-6 shadow-xl shadow-black/20">
                                <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-300/90 mb-3">Chapter preview</p>
                                <p class="text-white text-xl font-black leading-snug mb-2">{{ $previewChapter ? $previewChapter->readerHeadingLine() : 'Chapter 1' }}</p>
                                @if($previewChapter && filled($previewUrgencyLabel ?? null))
                                    <p class="mb-3 inline-flex items-center rounded-xl border border-amber-400/35 bg-amber-500/20 px-3 py-1 text-xs font-black uppercase tracking-widest text-amber-200">{{ $previewUrgencyLabel }}</p>
                                @endif
                                @php
                                    $heroPreviewLines = $previewExcerptLines ?? [];
                                    if ($heroPreviewLines === []) {
                                        $heroPreviewLines = [
                                            'He didn’t notice the door was already open.',
                                            'That was the first mistake.',
                                        ];
                                    }
                                @endphp
                                <div class="space-y-2 text-white/90 font-bold leading-relaxed">
                                    @foreach($heroPreviewLines as $line)
                                        <p>“{{ $line }}”</p>
                                    @endforeach
                                </div>
                                <p class="mt-4 text-xs font-black uppercase tracking-wide text-amber-200/90">
                                    This line feels off?
                                    <a href="{{ $previewChapter ? route('chapters.show', $previewChapter).'#chapter-suggest-edit-sidebar' : route('chapters.index') }}" class="underline decoration-amber-300/80 underline-offset-2 hover:text-white">Submit your version.</a>
                                </p>
                                <div class="mt-5 flex flex-wrap gap-3">
                                    <a href="{{ $previewChapter ? route('chapters.show', $previewChapter) : route('chapters.index') }}" data-track-event="landing_preview_read_click" data-track-label="hero_preview_read" class="landing-ui-transition inline-flex items-center justify-center rounded-2xl bg-amber-500 px-6 py-3 text-sm font-black text-black hover:bg-amber-600">
                                        Read this chapter
                                    </a>
                                    <a href="{{ route('chapters.index') }}" data-track-event="landing_preview_all_chapters_click" data-track-label="hero_preview_all" class="landing-ui-transition inline-flex items-center justify-center rounded-2xl border border-white/25 bg-white/10 px-6 py-3 text-sm font-black text-white hover:bg-white/15">
                                        Read chapters
                                    </a>
                                </div>
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
                    <div class="text-center mb-12 max-w-3xl mx-auto">
                        <h2 class="text-4xl md:text-5xl font-black text-amber-900 mb-6">The Journey</h2>
                        <p class="text-2xl md:text-3xl font-black text-amber-900 mb-5">Two connected books. One contribution system.</p>
                        <p class="text-lg md:text-xl text-amber-900/80 font-bold leading-relaxed mb-6">
                            In <span class="text-amber-950">The Book With No Name (Collaborative Novel)</span>, you read live chapters and submit edits that can be accepted into the manuscript. In <span class="text-amber-950">Peter Trull Solitary Detective (Interactive Mystery)</span>, you use vote credits to decide which version survives. Completed contributions in Book 1 grant vote credits in Book 2.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12">
                        {{-- Part 1 --}}
                        <div class="landing-motion-card group bg-[#fff9f0] p-12 rounded-[3rem] border border-amber-100 shadow-sm hover:shadow-2xl hover:shadow-amber-900/5 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="landing-motion-icon w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-600">01</span>
                            </div>
                            <h3 class="text-3xl font-black text-amber-900 mb-6">The Book With No Name</h3>
                            <p class="text-lg text-amber-900/70 font-bold mb-8 leading-relaxed">
                                Six lives. Six powers. A collision none of them can escape. Each one wields a different ability they do not fully understand — but as instinct turns to control and control to consequence, a hidden structure pulls them toward an inevitable convergence. Challenge the text, and accepted replacements can become permanent parts of the novel.
                            </p>
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-center gap-3 text-amber-900 font-bold">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Up to 2 points per accepted edit (1 partial · 2 full · 0 if rejected)
                                </li>
                                <li class="flex items-center gap-3 text-amber-900 font-bold">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Climb the global leaderboard
                                </li>
                            </ul>
                            <a href="{{ route('chapters.index') }}" data-track-event="landing_cta_chapters_card_click" data-track-label="book1_card" class="landing-ui-transition inline-flex items-center gap-2 text-amber-600 font-black hover:gap-4 transition-all">
                                {{ $hasLiveChapters ? 'Read the manuscript' : 'Read chapter previews' }} <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>

                        {{-- Part 2 --}}
                        <div class="landing-motion-card group bg-amber-900 p-12 rounded-[3rem] text-white shadow-2xl shadow-amber-900/20 hover:shadow-amber-900/40 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="landing-motion-icon w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-400">02</span>
                            </div>
                            <h3 class="text-3xl font-black mb-6 leading-tight">Peter Trull Solitary Detective</h3>
                            <div class="mb-6 inline-flex items-center gap-3 rounded-2xl border border-amber-400/40 bg-black/30 px-4 py-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-400 text-black font-black">PT</span>
                                <span class="text-xs font-black uppercase tracking-widest text-amber-200">An interactive mystery</span>
                            </div>
                            <p class="text-lg text-amber-100/75 font-bold mb-6 leading-relaxed">
                                A damaged officer. An unseen threat. A mystery shaped by ghosts that haunt the traumatized. A Navy intelligence officer with CPTSD spots a stranger watching him — and is pulled into a covert investigation that tests his trust, control, and survival. At each split, you decide which path survives.
                            </p>
                            <p class="text-sm text-amber-100/90 font-bold mb-8 leading-relaxed rounded-2xl border border-amber-400/35 bg-black/25 px-4 py-3">
                                <span class="text-amber-300">Voting is gated:</span> you need <strong class="text-white">unused vote credits</strong> from <strong class="text-white">completed contributions</strong> in <em>The Book With No Name</em> — one vote per contribution.
                                <a href="{{ route('chapters.index') }}" class="mt-3 block text-amber-300 font-black underline decoration-amber-400/60 underline-offset-2 hover:text-white w-fit">Start with chapters <span aria-hidden="true">→</span></a>
                            </p>
                            <ul class="space-y-4 mb-10">
                                <li class="flex items-center gap-3 font-bold">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    One vote per completed contribution
                                </li>
                                <li class="flex items-center gap-3 font-bold">
                                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    Hold the detective's fate in your hands
                                </li>
                            </ul>
                            <a href="{{ route('vote.index') }}" data-track-event="landing_cta_vote_card_click" data-track-label="book2_card" class="landing-ui-transition inline-flex items-center gap-2 text-amber-400 font-black hover:gap-4 transition-all">
                                Vote on which path survives <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    {{-- Explicit gap so prizes block doesn’t visually merge with the two cards (margins can collapse) --}}
                    <div class="h-24 md:h-32 lg:h-40 max-w-7xl mx-auto" aria-hidden="true"></div>

                    <div id="landing-prizes-progression" class="max-w-3xl mx-auto rounded-[2rem] border-2 border-amber-200 bg-gradient-to-br from-[#fff9f0] to-amber-50/80 px-8 py-10 md:px-12 md:py-12 shadow-lg shadow-amber-900/5">
                        <h3 class="text-2xl md:text-3xl font-black text-amber-950 text-center mb-2">Recognition ladder</h3>
                        <p class="text-center text-amber-800/90 font-bold mb-8">Final rewards are based on all-time leaderboard placement.</p>
                        <ul class="space-y-4 text-left font-bold text-amber-900 leading-relaxed">
                            <li class="flex gap-3">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-lg" aria-hidden="true">
                                    🏅
                                </span>
                                <span><strong class="font-black">#3 — Name a character</strong><br><span class="text-amber-800/80 text-sm">Third place adds a permanent character name to the story world.</span></span>
                            </li>
                            <li class="flex gap-3">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-lg" aria-hidden="true">
                                    📖
                                </span>
                                <span><strong class="font-black">#2 — Name the book</strong><br><span class="text-amber-800/80 text-sm">Second place helps set the final title seen by every reader.</span></span>
                            </li>
                            <li class="flex gap-3">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-lg" aria-hidden="true">
                                    ✨
                                </span>
                                <span><strong class="font-black">#1 — Your name on the cover (top reward)</strong><br><span class="text-amber-800/80 text-sm">First place receives final cover credit, subject to production approval.</span></span>
                            </li>
                        </ul>
                        <div class="mt-5 rounded-xl border border-amber-200 bg-white/60 px-4 py-3 text-sm font-bold text-amber-900/90 space-y-2">
                            <p><strong>Additional rewards:</strong> Top 10 receive a signed first print, and Top 50 are listed in the Editor Hall of Fame.</p>
                            <p><strong>Chapter sponsor note:</strong> Each live chapter can credit its highest-impact contributor in a sponsor note below that chapter.</p>
                        </div>
                        <p class="mt-8 text-center text-sm md:text-base font-black text-amber-900/85 leading-snug">
                            Precision compounds. The higher your accepted-replacement rate, the higher you climb.
                        </p>
                        <p class="mt-4 text-center">
                            <a href="{{ route('prizes') }}" data-track-event="landing_prizes_bottom_click" data-track-label="what_you_could_win_rules" class="text-amber-700 font-black hover:text-amber-900 underline decoration-amber-400/80 underline-offset-4">Full prizes &amp; rules →</a>
                        </p>
                    </div>

                    {{-- How it works — 3 steps for scannability (UX #17) --}}
                    <div id="landing-how-steps" class="mt-20 max-w-5xl mx-auto" aria-labelledby="landing-how-heading">
                        <h3 id="landing-how-heading" class="text-center text-xs font-black uppercase tracking-[0.2em] text-amber-800/70 mb-8">How it works</h3>
                        <ol class="grid md:grid-cols-3 gap-6 md:gap-8 list-none p-0 m-0">
                            <li class="flex flex-col items-center text-center md:items-start md:text-left rounded-3xl border border-amber-100 bg-[#fff9f0] p-6 shadow-sm">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-black text-sm font-black mb-4" aria-hidden="true">1</span>
                                <p class="text-lg font-black text-amber-900 mb-2">Read the chapter</p>
                                <p class="text-sm font-bold text-amber-800/75 leading-relaxed">Open the live manuscript chapter and read the current version.</p>
                            </li>
                            <li class="flex flex-col items-center text-center md:items-start md:text-left rounded-3xl border border-amber-100 bg-[#fff9f0] p-6 shadow-sm">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-black text-sm font-black mb-4" aria-hidden="true">2</span>
                                <p class="text-lg font-black text-amber-900 mb-2">Submit your version</p>
                                <p class="text-sm font-bold text-amber-800/75 leading-relaxed">Replace a line or chapter section. If accepted, your wording becomes part of the manuscript.</p>
                            </li>
                            <li class="flex flex-col items-center text-center md:items-start md:text-left rounded-3xl border border-amber-100 bg-[#fff9f0] p-6 shadow-sm">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-black text-sm font-black mb-4" aria-hidden="true">3</span>
                                <p class="text-lg font-black text-amber-900 mb-2">Vote on what survives</p>
                                <p class="text-sm font-bold text-amber-800/75 leading-relaxed">Use vote credits in Peter Trull Solitary Detective to decide which branch remains canon.</p>
                            </li>
                        </ol>
                    </div>
                </div>
            </section>

            <section class="relative z-10 bg-[#fff9f0] border-y border-amber-100 py-16" aria-labelledby="landing-social-proof">
                <div class="max-w-5xl mx-auto px-6">
                    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
                        <div>
                            <h2 id="landing-social-proof" class="text-2xl md:text-3xl font-black text-amber-950">Latest accepted replacement</h2>
                            <p class="mt-2 text-sm font-bold text-amber-900/70 max-w-2xl">Proof the manuscript moves. A single accepted line can define how the story evolves.</p>
                        </div>
                        <a href="{{ route('edits.public') }}" class="landing-ui-transition inline-flex items-center rounded-2xl border border-amber-200 bg-white px-5 py-3 text-sm font-black text-amber-900 hover:bg-amber-50">
                            Read accepted replacements
                        </a>
                    </div>

                    @php
                        $fallbackReplacement = [
                            'label' => 'Example (shown until the first live acceptance)',
                            'user_name' => 'Contributor',
                            'chapter_heading' => 'Chapter 1 (preview)',
                            'original' => 'He didn’t notice the door was already open.',
                            'suggested' => 'He didn’t notice the door had already been open.',
                        ];
                        $hasLiveReplacement = is_array($latestReplacement ?? null) && filled($latestReplacement['original'] ?? null) && filled($latestReplacement['suggested'] ?? null);
                        $recentReplacementRows = collect($recentAcceptedReplacements ?? [])->filter(function ($item) {
                            return is_array($item)
                                && filled($item['suggested'] ?? null)
                                && filled($item['chapter_heading'] ?? null);
                        })->values();
                        if ($recentReplacementRows->isEmpty()) {
                            $recentReplacementRows = collect([
                                [
                                    'chapter_heading' => 'Chapter 1 (preview)',
                                    'suggested' => 'He didn’t notice the door had already been open.',
                                    'user_name' => 'Contributor',
                                    'chapter_url' => null,
                                ],
                                [
                                    'chapter_heading' => 'Chapter 1 (preview)',
                                    'suggested' => 'The room was quiet, but the silence felt staged.',
                                    'user_name' => 'Contributor',
                                    'chapter_url' => null,
                                ],
                                [
                                    'chapter_heading' => 'Chapter 1 (preview)',
                                    'suggested' => 'By the time he stepped inside, the trap had already closed.',
                                    'user_name' => 'Contributor',
                                    'chapter_url' => null,
                                ],
                            ]);
                        }
                    @endphp

                    <div class="rounded-[2rem] border border-amber-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-amber-50/60 border-b border-amber-100 flex flex-wrap items-center justify-between gap-3">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-800/70">
                                {{ $hasLiveReplacement ? 'Live acceptance' : $fallbackReplacement['label'] }}
                            </p>
                            <p class="text-xs font-bold text-amber-800/70">
                                {{ $hasLiveReplacement ? ($latestReplacement['chapter_heading'] ?? 'Chapter') : $fallbackReplacement['chapter_heading'] }}
                                @if($hasLiveReplacement && filled($latestReplacement['user_name'] ?? null))
                                    <span class="text-amber-800/40" aria-hidden="true">·</span> accepted from {{ $latestReplacement['user_name'] }}
                                @endif
                            </p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-0">
                            <div class="p-6 border-b md:border-b-0 md:border-r border-amber-100">
                                <p class="text-[11px] font-black uppercase tracking-widest text-amber-800/55 mb-3">Published text</p>
                                <p class="text-amber-950 font-bold leading-relaxed">
                                    {{ $hasLiveReplacement ? ($latestReplacement['original'] ?? '') : $fallbackReplacement['original'] }}
                                </p>
                            </div>
                            <div class="p-6 bg-amber-50/40">
                                <p class="text-[11px] font-black uppercase tracking-widest text-amber-800/55 mb-3">Accepted replacement</p>
                                <p class="text-amber-950 font-black leading-relaxed">
                                    {{ $hasLiveReplacement ? ($latestReplacement['suggested'] ?? '') : $fallbackReplacement['suggested'] }}
                                </p>
                            </div>
                        </div>
                        @if($hasLiveReplacement && filled($latestReplacement['chapter_url'] ?? null))
                            <div class="px-6 py-4 border-t border-amber-100 bg-white flex flex-wrap items-center justify-between gap-3">
                                <p class="text-xs font-bold text-amber-900/70">Want context? Read the chapter where this landed.</p>
                                <a href="{{ $latestReplacement['chapter_url'] }}" class="landing-ui-transition inline-flex items-center rounded-2xl bg-amber-900 px-5 py-3 text-sm font-black text-white hover:bg-black">
                                    Read that chapter
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="mt-4 rounded-[1.5rem] border border-amber-200 bg-white px-5 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.16em] text-amber-800/70">Recent accepted replacements</p>
                        <ul class="mt-3 space-y-2">
                            @foreach($recentReplacementRows->take(3) as $recentItem)
                                <li class="rounded-xl border border-amber-100 bg-amber-50/45 px-3 py-2">
                                    <p class="text-xs font-black text-amber-900">{{ $recentItem['chapter_heading'] ?? 'Chapter' }}</p>
                                    <p class="text-sm font-bold text-amber-900/85">“{{ $recentItem['suggested'] ?? '' }}”</p>
                                    <p class="text-[11px] font-bold text-amber-800/70">
                                        accepted from {{ $recentItem['user_name'] ?? 'Contributor' }}
                                        @if(filled($recentItem['chapter_url'] ?? null))
                                            ·
                                            <a href="{{ $recentItem['chapter_url'] }}" class="underline decoration-amber-300/90 underline-offset-2 hover:text-amber-950">Read</a>
                                        @endif
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Community Stats — live counts + configurable fund goal display (UX #14); cream band + borders (UX #23) --}}
            <section id="landing-stats-strip" class="py-32 bg-[#fff9f0] border-y border-amber-100" aria-labelledby="landing-stats-heading">
                <h2 id="landing-stats-heading" class="sr-only">Community statistics</h2>
                <div class="max-w-7xl mx-auto px-6">
                    @if (! empty($landingStatsQuiet))
                        <p id="landing-stats-quiet-lead" class="text-center text-lg md:text-xl font-black text-amber-900 max-w-2xl mx-auto leading-snug">
                            Early days — be among the first contributors on the living manuscript.
                        </p>
                        <p class="mt-3 text-center text-sm font-bold text-amber-800/70 max-w-xl mx-auto">
                            Contributor counts, accepted edits, and live chapters will appear here as the community grows. The fund goal below is already set for the campaign.
                        </p>
                        <div class="grid md:grid-cols-3 gap-6 mt-12">
                            <div class="rounded-2xl border border-amber-200 bg-white px-6 py-5 text-left">
                                <p class="text-xs font-black uppercase tracking-widest text-amber-700/70 mb-2">Launch state</p>
                                <p class="text-base font-black text-amber-900">No accepted replacements yet.</p>
                                <p class="text-sm font-bold text-amber-800/75 mt-2">The earliest contributors will define manuscript tone and standards.</p>
                            </div>
                            <div class="rounded-2xl border border-amber-200 bg-white px-6 py-5 text-left">
                                <p class="text-xs font-black uppercase tracking-widest text-amber-700/70 mb-2">Early influence</p>
                                <p class="text-base font-black text-amber-900">First accepted lines carry outsized impact.</p>
                                <p class="text-sm font-bold text-amber-800/75 mt-2">Early rounds establish voice, rhythm, and authority.</p>
                            </div>
                            <div class="rounded-2xl border border-amber-200 bg-white px-6 py-5 text-left">
                                <p class="text-xs font-black uppercase tracking-widest text-amber-700/70 mb-2">Campaign target</p>
                                <p class="text-3xl font-black text-amber-900">
                                    <a href="{{ route('prizes') }}" class="rounded-xl outline-none hover:text-amber-700 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-[#fff9f0]">{{ $landingStats['prize_pool'] }}</a>
                                </p>
                                <p class="text-sm font-bold text-amber-800/75 mt-2">Contributor rewards campaign target, not a live balance meter.</p>
                            </div>
                        </div>
                        <p class="mt-8 text-center">
                            <a href="{{ route('chapters.index') }}" data-track-event="landing_stats_quiet_cta_click" data-track-label="stats_be_first" class="inline-flex items-center px-6 py-3 bg-amber-500 text-black font-black rounded-xl hover:bg-amber-600 transition-colors">Be the first contributor</a>
                        </p>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                            <div class="text-center">
                                <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['contributors'] }}</div>
                                <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Contributors</div>
                                <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">With an accepted edit</div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['edits_accepted'] }}</div>
                                <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Replacements Accepted</div>
                                <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">Story and inline</div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-black text-amber-900 mb-2">{{ $landingStats['chapters_live'] }}</div>
                                <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Chapters Live</div>
                                <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">On the chapter list</div>
                            </div>
                            <div class="text-center">
                                <div class="text-5xl font-black text-amber-900 mb-2">
                                    <a href="{{ route('prizes') }}" class="rounded-xl outline-none hover:text-amber-700 focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-[#fff9f0]">{{ $landingStats['prize_pool'] }}</a>
                                </div>
                                <div class="text-sm font-black uppercase tracking-widest text-amber-800/70">Fund goal</div>
                                <div class="text-xs font-bold text-amber-800/55 mt-2 leading-snug">Announced target · not a live balance</div>
                            </div>
                        </div>
                    @endif
                    <p id="landing-stats-footnote" class="mt-10 text-center text-xs font-bold text-amber-800/60 max-w-2xl mx-auto leading-relaxed">
                        Community figures update as people contribute. The fund goal is an announced campaign figure, not a live tally.
                    </p>
                </div>
            </section>
            </main>

            <section id="landing-updates-signup" class="relative z-10 py-16 bg-amber-50 border-t border-amber-100">
                <div class="max-w-4xl mx-auto px-6 text-center">
                    <h2 class="text-3xl font-black text-amber-900">Don't miss the next chapter</h2>
                    <p class="mt-3 text-amber-800/75 font-bold max-w-2xl mx-auto">
                        Join the updates list and we will email you when new chapters open or major rounds begin.
                    </p>
                    <form method="POST" action="{{ route('feedback.store') }}" class="mt-8 max-w-xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-3" data-track-form-event="landing_waitlist_submit">
                        @csrf
                        <input type="hidden" name="type" value="waitlist">
                        <input type="hidden" name="content" value="Landing updates waitlist signup form">
                        @guest
                            <label for="landing-waitlist-email" class="sr-only">Email for updates</label>
                            <input id="landing-waitlist-email" type="email" name="email" required placeholder="you@example.com" class="w-full sm:flex-1 rounded-xl border border-amber-300 bg-white px-4 py-3 text-sm font-bold text-amber-900 placeholder:text-amber-700/60 focus:border-amber-500 focus:ring-amber-500">
                        @endguest
                        <button type="submit" data-track-event="landing_waitlist_button_click" data-track-label="stay_connected_waitlist" class="landing-ui-transition inline-flex items-center justify-center px-6 py-3 bg-amber-600 text-white font-black rounded-xl hover:bg-amber-700">
                            Join updates list
                        </button>
                    </form>
                    <p class="mt-4 text-xs font-bold text-amber-800/65">Prefer open feedback instead? <a href="{{ route('feedback.index') }}" class="underline decoration-amber-300 hover:text-amber-950">Send feedback</a>.</p>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="relative z-10 py-20 border-t border-amber-100 bg-white">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <div class="flex items-center justify-center gap-3 mb-8">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-lg font-black tracking-tight text-amber-900">WhatsMyBookName</span>
                    </div>
                    <p class="text-amber-900/55 font-bold mb-8">© {{ date('Y') }} WhatsMyBookName. All rights reserved.</p>
                    <div class="space-y-3">
                        <p class="text-[11px] font-black uppercase tracking-[0.2em] text-amber-800/60">Policies</p>
                        <nav class="flex flex-wrap items-center justify-center gap-x-3 gap-y-2 text-sm font-bold text-amber-900/70" aria-label="Policies">
                            <a href="{{ route('legal.index') }}" class="landing-ui-transition whitespace-nowrap transition-colors hover:text-amber-900">Legal</a>
                            <span class="text-amber-300 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('privacy') }}" class="landing-ui-transition whitespace-nowrap transition-colors hover:text-amber-900">Privacy</a>
                            <span class="text-amber-300 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('terms') }}" class="landing-ui-transition whitespace-nowrap transition-colors hover:text-amber-900">Terms</a>
                        </nav>
                        <nav class="flex items-center justify-center gap-3 text-sm font-bold text-amber-900/70" aria-label="Support">
                            <a href="{{ route('about') }}" class="landing-ui-transition whitespace-nowrap transition-colors hover:text-amber-900">About</a>
                            <span class="text-amber-300 select-none pointer-events-none" aria-hidden="true">·</span>
                            <a href="{{ route('feedback.index') }}" class="landing-ui-transition whitespace-nowrap transition-colors hover:text-amber-900">Feedback</a>
                        </nav>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Auth Modals --}}
        @include('auth.modals')
    </body>
</html>
