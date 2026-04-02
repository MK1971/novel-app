<x-guest-layout
    page-title="Terms of Service — What's My Book Name"
    meta-description="Terms of service for What's My Book Name: using the collaborative reading platform, contributions, and conduct."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Terms of Service</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">Last updated: {{ date('F j, Y') }}</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            By using {{ config('app.name') }} (“the Service”), you agree to these terms. If you do not agree, please do not use the Service. This is a general community agreement, not a substitute for professional legal advice.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">The Service</h2>
                        <p class="mb-6">
                            We provide a collaborative reading and participation experience, including chapters, suggested edits, voting where enabled, and related features. We may change or discontinue features with reasonable notice when practical.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Accounts and conduct</h2>
                        <p class="mb-6">
                            You are responsible for your account and for content you submit. Do not misuse the Service (e.g. harassment, illegal content, attempts to break security or others’ accounts). We may suspend or remove access for violations or risk to the community.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Contributions and rights</h2>
                        <p class="mb-6">
                            By submitting edits, votes, or other content, you grant us the rights needed to operate, display, moderate, and incorporate that content into the project as described in the product. Specific payment or acceptance rules apply where stated in the app.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Disclaimer</h2>
                        <p class="mb-6">
                            The Service is provided “as is.” We strive for reliability but do not guarantee uninterrupted access or error-free operation. To the extent permitted by law, we limit liability for indirect or consequential damages.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Contact</h2>
                        <p class="mb-6">
                            For questions about these terms, use <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a> or your project contact.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            See also our <a href="{{ route('privacy') }}" class="text-amber-700 underline hover:text-amber-900">Privacy Policy</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
