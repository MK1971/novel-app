<x-guest-layout
    page-title="Privacy Policy — What's My Book Name"
    meta-description="Privacy policy for What's My Book Name: how we handle account data, contributions, payments, and contact options."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Privacy Policy</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">Last updated: {{ date('F j, Y') }}</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            This page describes how {{ config('app.name') }} (“we”, “us”) handles information when you use the site. It is a plain-language summary, not legal advice. We may update it from time to time; the date above will change when we do.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">What we collect</h2>
                        <ul class="list-disc pl-6 space-y-2 mb-6">
                            <li><strong>Account data</strong> — such as name and email, if you register or sign in.</li>
                            <li><strong>Content you submit</strong> — including edit suggestions, votes, feedback, and payment-related records needed to operate those features.</li>
                            <li><strong>Technical data</strong> — standard server and session information (e.g. IP, browser type, cookies) used to keep the service secure and working.</li>
                        </ul>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">How we use it</h2>
                        <p class="mb-6">
                            We use this information to run the platform (accounts, chapters, voting, moderation, payments where applicable), improve the product, comply with law, and respond to you when you contact us.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Sharing</h2>
                        <p class="mb-6">
                            We do not sell your personal information. We may share data with service providers who help us host or process payments, under agreements that require them to protect it, or when required by law.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Your choices</h2>
                        <p class="mb-6">
                            You can review or update account details in your profile where the product supports it. For other requests (access, correction, deletion), contact us using the feedback link in the footer or your usual support channel.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            Questions? See also our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms of Service</a> or <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">send feedback</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
