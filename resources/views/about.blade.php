<x-guest-layout
    page-title="About — What's My Book Name"
    meta-description="The story behind What's My Book Name: collaborative fiction, paid edits on The Book With No Name, Peter Trull voting, and the community leaderboard."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8">The Story Behind "What's My Book Name"</h1>

                    <div class="prose prose-amber max-w-none">
                        <p class="text-lg text-amber-800 leading-relaxed mb-6">
                            "What's My Book Name" is a collaborative storytelling platform where the community doesn't just read the story—they shape it.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Our Vision</h2>
                        <p class="text-amber-800 mb-6">
                            We believe that the best stories are those that resonate with their audience. By opening up the creative process, we invite readers to become co-creators: suggest edits on the live manuscript, earn points when suggestions are accepted, spend paid credits to vote on alternate chapter endings, and help name the work everyone built together.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">How It Works</h2>
                        <div class="grid md:grid-cols-2 gap-8 mt-6">
                            <div class="bg-amber-50 p-6 rounded-xl border border-amber-100">
                                <h3 class="text-xl font-bold text-amber-900 mb-3">Part 1: The Book With No Name</h3>
                                <p class="text-amber-800 mb-4">
                                    Read published chapters and submit edit suggestions through a secure <strong class="text-amber-900">$2 PayPal checkout per suggestion</strong>. If a moderator accepts your edit, you earn <strong class="text-amber-900">up to 2 points</strong> — <strong class="text-amber-900">2</strong> for a full accept, <strong class="text-amber-900">1</strong> for partial, <strong class="text-amber-900">0</strong> if rejected. Points feed the public leaderboard.
                                </p>
                                <p class="text-amber-800 text-sm font-bold leading-relaxed">
                                    Each <strong class="text-amber-900">completed payment</strong> also adds <strong class="text-amber-900">one vote credit</strong> for Peter Trull Solitary Detective. There are no free votes based on accepted edits alone.
                                </p>
                            </div>
                            <div class="bg-amber-50 p-6 rounded-xl border border-amber-100">
                                <h3 class="text-xl font-bold text-amber-900 mb-3">Part 2: Peter Trull Solitary Detective</h3>
                                <p class="text-amber-800 mb-4">
                                    Compare <strong class="text-amber-900">Version A</strong> and <strong class="text-amber-900">Version B</strong> of the same chapter and vote for the direction the detective story should take. <strong class="text-amber-900">Casting a vote uses one unused vote credit</strong> from a completed $2 edit payment; you can vote once per chapter pair.
                                </p>
                                <p class="text-amber-800 text-sm font-bold leading-relaxed">
                                    <a href="{{ route('vote.index') }}" class="text-amber-700 underline decoration-amber-300 hover:text-amber-900">Open the Peter Trull Solitary Detective voting hub</a>
                                    — you can browse without credits, but ballots require an available credit.
                                </p>
                            </div>
                        </div>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Join the Adventure</h2>
                        <p class="text-amber-800 mb-6">
                            Whether you're a seasoned writer or a passionate reader, there's a place for you here. Start reading, suggest edits when you're ready, and help decide where the mystery goes next.
                        </p>

                        <div class="not-prose rounded-2xl border border-amber-100 bg-[#fff9f0] px-6 py-5 mb-10">
                            <p class="text-xs font-black uppercase tracking-widest text-amber-800/70 mb-3">Policies &amp; feedback</p>
                            <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm font-bold text-amber-900" aria-label="Legal and feedback">
                                <a href="{{ route('legal.index') }}" class="underline decoration-amber-300 hover:text-amber-600">Legal hub</a>
                                <a href="{{ route('privacy') }}" class="underline decoration-amber-300 hover:text-amber-600">Privacy Policy</a>
                                <a href="{{ route('terms') }}" class="underline decoration-amber-300 hover:text-amber-600">Terms of Service</a>
                                <a href="{{ route('feedback.index') }}" class="underline decoration-amber-300 hover:text-amber-600">Feedback</a>
                            </nav>
                        </div>

                        <div class="flex justify-center mt-4">
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-8 py-4 bg-amber-600 border border-transparent rounded-full font-bold text-lg text-white hover:bg-amber-700 focus:bg-amber-700 active:bg-amber-900 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                Browse chapters
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
