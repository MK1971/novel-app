<x-guest-layout>
    <div class="relative min-h-screen bg-[#fff9f0]">
        <!-- Hero Section -->
        <div class="container mx-auto px-4 pt-20 pb-32">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-5xl md:text-7xl font-black text-amber-900 mb-8 leading-tight tracking-tighter">
                    The Book With <br> <span class="text-amber-500">No Name</span>
                </h1>
                <p class="text-xl md:text-2xl text-amber-800/60 mb-12 leading-relaxed font-bold">
                    A collaborative storytelling experiment. Contribute your edits, vote on versions, and help shape the future of literature.
                </p>
                
                <div class="flex flex-col md:flex-row justify-center gap-6">
                    <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="px-12 py-5 bg-amber-500 text-black rounded-full font-black text-xl hover:bg-amber-600 transition-all shadow-xl shadow-amber-500/30 transform hover:-translate-y-1">
                        Start Reading
                    </a>
                    @guest
                        <a href="{{ route('register') }}" class="px-12 py-5 bg-white text-amber-900 border-4 border-amber-500 rounded-full font-black text-xl hover:bg-amber-50 transition-all shadow-xl shadow-amber-500/10 transform hover:-translate-y-1">
                            Join the Community
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-12 py-5 bg-white text-amber-900 border-4 border-amber-500 rounded-full font-black text-xl hover:bg-amber-50 transition-all shadow-xl shadow-amber-500/10 transform hover:-translate-y-1">
                            Go to Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>

        <!-- Explore Section -->
        <div class="bg-white py-32 rounded-[5rem] shadow-2xl shadow-amber-900/5">
            <div class="container mx-auto px-4">
                <div class="text-center mb-20">
                    <h2 class="text-4xl font-black text-amber-900 mb-4">Explore the Project</h2>
                    <div class="w-24 h-2 bg-amber-500 mx-auto rounded-full"></div>
                </div>
                
                <div class="grid md:grid-cols-3 gap-12">
                    <div class="p-10 bg-amber-50/50 rounded-[3rem] border border-amber-100 hover:shadow-xl transition-all group">
                        <div class="w-16 h-16 bg-amber-500 rounded-2xl flex items-center justify-center mb-8 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-amber-900 mb-4">Read & Edit</h3>
                        <p class="text-amber-800/60 font-bold mb-8 leading-relaxed">Dive into "The Book With No Name" and submit your own improvements to the story.</p>
                        <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="inline-flex items-center text-amber-500 font-black uppercase tracking-widest text-sm hover:text-amber-600">
                            Browse Chapters
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>

                    <div class="p-10 bg-amber-50/50 rounded-[3rem] border border-amber-100 hover:shadow-xl transition-all group">
                        <div class="w-16 h-16 bg-amber-500 rounded-2xl flex items-center justify-center mb-8 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-amber-900 mb-4">Vote on Peter Trull</h3>
                        <p class="text-amber-800/60 font-bold mb-8 leading-relaxed">Help decide which version of the solitary detective's story prevails.</p>
                        <a href="{{ route('vote.index') }}" class="inline-flex items-center text-amber-500 font-black uppercase tracking-widest text-sm hover:text-amber-600">
                            Cast Your Vote
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>

                    <div class="p-10 bg-amber-50/50 rounded-[3rem] border border-amber-100 hover:shadow-xl transition-all group">
                        <div class="w-16 h-16 bg-amber-500 rounded-2xl flex items-center justify-center mb-8 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h3 class="text-2xl font-black text-amber-900 mb-4">Leaderboard</h3>
                        <p class="text-amber-800/60 font-bold mb-8 leading-relaxed">See who the top contributors are and track your own progress.</p>
                        <a href="{{ route('leaderboard') }}" class="inline-flex items-center text-amber-500 font-black uppercase tracking-widest text-sm hover:text-amber-600">
                            View Rankings
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
