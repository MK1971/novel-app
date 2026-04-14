<x-guest-layout
    page-title="Prizes & grand prize — What's My Book Name"
    meta-description="How the leaderboard grand prize works, points from accepted edits, and Peter Trull vote credits — What's My Book Name."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Earn your place in the story</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">How rewards scale from recognition to cover credit</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            Contributors earn points when contribution-backed edits are accepted (full or partial, as shown in the app). Peter Trull voting uses separate vote credits from completed contributions on The Book With No Name, as described on the vote hub.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Prize ladder (small → bigger)</h2>
                        <p class="mb-4">
                            Prizes scale with how much you shape the work. From lighter recognition to the biggest public stamp on the project, the ladder looks like this:
                        </p>
                        <ol class="list-decimal pl-6 space-y-3 mb-6 font-medium">
                            <li><strong>Name a character</strong> — leave a mark inside the story world.</li>
                            <li><strong>Name the book</strong> — help steer the title readers will see.</li>
                            <li><strong>Live on in the project</strong> — lasting recognition in the book and on the site so your contribution stays visible for the audience that follows the journey.</li>
                            <li><strong>Grand prize: your name on the cover</strong> — top contributors can be credited on the final cover, subject to author and production approval (see below).</li>
                        </ol>
                        <p class="text-sm font-bold text-amber-800/70 mb-6">
                            Tiers and exact wording may vary by campaign; the <a href="{{ route('leaderboard') }}" class="text-amber-700 underline hover:text-amber-900">leaderboard</a> reflects current standings.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Grand prize: name on the cover</h2>
                        <p class="mb-6">
                            The top contributor on the public <a href="{{ route('leaderboard') }}" class="text-amber-700 underline hover:text-amber-900">leaderboard</a> will have their name featured on the final book cover for the project, subject to author and production approval. Ties or edge cases are resolved at the project team’s reasonable discretion.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">No purchase necessary to read</h2>
                        <p class="mb-6">
                            Reading chapters is open as provided in the app. Contribution flows (for example the $2 submission checkout) are optional and governed by the product copy at checkout and our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms</a>.
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
