<x-guest-layout
    page-title="Prizes & grand prize — What's My Book Name"
    meta-description="How the leaderboard grand prize works, points from accepted edits, and Peter Trull vote credits — What's My Book Name."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Prizes &amp; grand prize</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">How the leaderboard prize works</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            Contributors earn points when paid edits are accepted (full or partial, as shown in the app). Peter Trull voting uses separate vote credits from completed checkouts on The Book With No Name, as described on the vote hub.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Grand prize: name on the cover</h2>
                        <p class="mb-6">
                            The top contributor on the public <a href="{{ route('leaderboard') }}" class="text-amber-700 underline hover:text-amber-900">leaderboard</a> will have their name featured on the final book cover for the project, subject to author and production approval. Ties or edge cases are resolved at the project team’s reasonable discretion.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">No purchase necessary to read</h2>
                        <p class="mb-6">
                            Reading chapters is open as provided in the app. Paid flows (for example the $2 edit checkout) are optional and governed by the product copy at checkout and our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms</a>.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            <a href="{{ route('leaderboard') }}" class="text-amber-700 underline hover:text-amber-900">Back to leaderboard</a>
                            ·
                            <a href="{{ route('chapters.index') }}" class="text-amber-700 underline hover:text-amber-900">Read The Book With No Name</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
