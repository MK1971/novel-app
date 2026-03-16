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
            .bg-book-pattern {
                background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?q=80&w=2256&auto=format&fit=crop');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
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
        </style>
    </head>
    <body class="antialiased font-['Nunito'] bg-[#fff9f0] text-amber-900 selection:bg-amber-200 selection:text-amber-900" x-data="{ showLoginModal: false, showRegisterModal: false }">
        <div class="min-h-screen bg-book-pattern flex flex-col">
            {{-- Navigation --}}
            <nav class="sticky top-0 z-50 bg-white/10 backdrop-blur-xl border-b border-white/10">
                <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-xl font-black tracking-tight text-white">WhatsMyBookName</span>
                    </div>
                    
                    <div class="hidden md:flex items-center gap-8">
                        <a href="{{ route('chapters.index') }}" class="text-sm font-bold text-white/60 hover:text-white transition-colors">Chapters</a>
                        <a href="{{ route('leaderboard') }}" class="text-sm font-bold text-white/60 hover:text-white transition-colors">Leaderboard</a>
                        <a href="{{ route('vote.index') }}" class="text-sm font-bold text-white/60 hover:text-white transition-colors">Peter Trull</a>
                        <a href="{{ route('about') }}" class="text-sm font-bold text-white/60 hover:text-white transition-colors">About</a>
                    </div>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">Dashboard</a>
                        @else
                            <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'login' }))" class="text-sm font-bold text-white/60 hover:text-white transition-colors">Sign In</button>
                            <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="px-6 py-2.5 bg-amber-500 text-black text-sm font-black rounded-xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">Join Now</button>
                        @endauth
                    </div>
                </div>
            </nav>

            {{-- Hero Section --}}
            <header class="relative flex-grow flex items-center pt-20 pb-32 overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="text-left">
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/20 rounded-full text-amber-400 text-xs font-black uppercase tracking-widest mb-8 border border-amber-500/30 backdrop-blur-md">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                Round 1 is Live
                            </div>
                            
                            <div class="typewriter mb-8">
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.2] break-words whitespace-normal">
                                    Where your edits shape the narrative...
                                </h1>
                            </div>
                            
                            <p class="text-xl md:text-2xl text-white/90 font-bold mb-12 leading-relaxed max-w-xl">
                                A two-part collaborative journey where your edits shape the narrative and your votes decide the final mystery.
                            </p>

                            <div class="flex flex-col sm:flex-row items-center gap-6">
                                <a href="{{ route('chapters.index') }}" class="w-full sm:w-auto px-10 py-5 bg-amber-500 text-black text-lg font-black rounded-2xl hover:bg-amber-600 transition-all shadow-2xl shadow-amber-500/30 transform hover:-translate-y-1">
                                    Start Your Adventure
                                </a>
                                <button @click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'register' }))" class="w-full sm:w-auto px-10 py-5 bg-white/10 backdrop-blur-md border-2 border-white/20 text-white text-lg font-black rounded-2xl hover:bg-white/20 transition-all shadow-xl shadow-black/20 transform hover:-translate-y-1">
                                    Join the Community
                                </button>
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
                                    <h2 class="text-3xl font-black text-white mb-4 tracking-tighter uppercase">THE BOOK <br>WITH NO <br>NAME</h2>
                                    <div class="w-16 h-1 bg-amber-500 mb-6"></div>
                                    <p class="text-amber-200 font-bold italic">"A collaborative masterpiece in the making"</p>
                                    <div class="mt-auto text-white/40 text-sm font-bold tracking-widest uppercase">Volume I</div>
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
                        <p class="text-xl text-amber-900/60 font-bold max-w-2xl mx-auto">Two distinct ways to contribute, earn points, and win prizes.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12">
                        {{-- Part 1 --}}
                        <div class="group bg-[#fff9f0] p-12 rounded-[3rem] border border-amber-100 shadow-sm hover:shadow-2xl hover:shadow-amber-900/5 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-600">01</span>
                            </div>
                            <h3 class="text-3xl font-black text-amber-900 mb-6">The Book With No Name</h3>
                            <p class="text-lg text-amber-900/60 font-bold mb-8 leading-relaxed">
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
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center gap-2 text-amber-600 font-black hover:gap-4 transition-all">
                                Browse Chapters <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </a>
                        </div>

                        {{-- Part 2 --}}
                        <div class="group bg-amber-900 p-12 rounded-[3rem] text-white shadow-2xl shadow-amber-900/20 hover:shadow-amber-900/40 transition-all duration-500 transform hover:-translate-y-2">
                            <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                <span class="text-2xl font-black text-amber-400">02</span>
                            </div>
                            <h3 class="text-3xl font-black mb-6">Peter Trull: Solitary Detective</h3>
                            <p class="text-lg text-amber-100/60 font-bold mb-8 leading-relaxed">
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

            {{-- Community Stats --}}
            <section class="py-32">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">1.2k</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-900/40">Contributors</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">450</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-900/40">Edits Accepted</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">12</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-900/40">Chapters Live</div>
                        </div>
                        <div class="text-center">
                            <div class="text-5xl font-black text-amber-900 mb-2">$5k</div>
                            <div class="text-sm font-black uppercase tracking-widest text-amber-900/40">Prize Pool</div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="py-20 border-t border-amber-100 bg-white">
                <div class="max-w-7xl mx-auto px-6 text-center">
                    <div class="flex items-center justify-center gap-3 mb-8">
                        <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18 18.246 18.477 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <span class="text-lg font-black tracking-tight text-amber-900">WhatsMyBookName</span>
                    </div>
                    <p class="text-amber-900/40 font-bold mb-8">© 2026 WhatsMyBookName. All rights reserved.</p>
                    <div class="flex items-center justify-center gap-8">
                        <a href="#" class="text-sm font-bold text-amber-900/60 hover:text-amber-900">Privacy Policy</a>
                        <a href="#" class="text-sm font-bold text-amber-900/60 hover:text-amber-900">Terms of Service</a>
                        <a href="{{ route('feedback.index') }}" class="text-sm font-bold text-amber-900/60 hover:text-amber-900">Feedback</a>
                    </div>
                </div>
            </footer>
        </div>

        {{-- Auth Modals --}}
        @include('auth.modals')
    </body>
</html>
