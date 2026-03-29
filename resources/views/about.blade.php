<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8">The Story Behind "What's My Book Name"</h1>
                    
                    <div class="prose prose-amber max-w-none">
                        <p class="text-lg text-amber-800 leading-relaxed mb-6">
                            "What's My Book Name" is a revolutionary collaborative storytelling platform where the community doesn't just read the story—they shape it.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Our Vision</h2>
                        <p class="text-amber-800 mb-6">
                            We believe that the best stories are those that resonate with their audience. By opening up the creative process, we allow readers to become co-creators, suggesting edits, voting on directions, and ultimately naming the masterpiece they helped build.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">How It Works</h2>
                        <div class="grid md:grid-cols-2 gap-8 mt-6">
                            <div class="bg-amber-50 p-6 rounded-xl border border-amber-100">
                                <h3 class="text-xl font-bold text-amber-900 mb-3">Part 1: The Book With No Name</h3>
                                <p class="text-amber-800">
                                    Contribute directly to the narrative. Suggest edits (small $2 fee per suggestion); when an edit is accepted you earn <strong class="text-amber-900">up to 2 points</strong> — <strong class="text-amber-900">2</strong> for a full accept, <strong class="text-amber-900">1</strong> for partial, <strong class="text-amber-900">0</strong> if rejected. Your <strong class="text-amber-900">first accepted edit</strong> unlocks voting on Peter Trull.
                                </p>
                            </div>
                            <div class="bg-amber-50 p-6 rounded-xl border border-amber-100">
                                <h3 class="text-xl font-bold text-amber-900 mb-3">Part 2: Peter Trull Solitary Detective</h3>
                                <p class="text-amber-800">
                                    Engage in the ultimate comparison. Vote between two distinct versions of the same chapter and help decide which path the legendary detective should take.
                                </p>
                            </div>
                        </div>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Join the Adventure</h2>
                        <p class="text-amber-800 mb-8">
                            Whether you're a seasoned writer or a passionate reader, there's a place for you here. Start reading, start suggesting, and let's write something unforgettable together.
                        </p>

                        <div class="flex justify-center mt-12">
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-8 py-4 bg-amber-600 border border-transparent rounded-full font-bold text-lg text-white hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                Start Your Adventure
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
