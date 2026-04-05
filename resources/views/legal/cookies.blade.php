<x-guest-layout
    page-title="Cookie policy — What's My Book Name"
    meta-description="Cookie policy for What's My Book Name: session, security, and preferences."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Cookie policy</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">Last updated: {{ date('F j, Y') }}</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            This page describes how {{ config('app.name') }} uses cookies and similar technologies. For broader data practices, see our <a href="{{ route('privacy') }}" class="text-amber-700 underline hover:text-amber-900">Privacy Policy</a>.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">What are cookies?</h2>
                        <p class="mb-6">
                            Cookies are small text files stored on your device. We use them where needed to run the site securely and to remember preferences.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Cookies we use</h2>
                        <ul class="list-disc pl-6 space-y-2 mb-6">
                            <li><strong>Session</strong> — keeps you signed in and ties requests to your session.</li>
                            <li><strong>CSRF token</strong> — helps prevent cross-site request forgery on form submissions.</li>
                            <li><strong>Functional preferences</strong> — where the product stores choices that improve your experience (for example UI state the app explicitly saves).</li>
                        </ul>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Third-party and analytics</h2>
                        <p class="mb-6">
                            We do not use third-party advertising cookies on this site by default. If we add analytics or embedded media that set their own cookies, we will update this page and, where required, ask for consent in the product.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Managing cookies</h2>
                        <p class="mb-6">
                            You can block or delete cookies in your browser settings. Blocking essential cookies may prevent sign-in or other core features from working.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            <a href="{{ route('legal.index') }}" class="text-amber-700 underline hover:text-amber-900">← Legal hub</a>
                            · <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
