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
                        <div class="overflow-x-auto mb-6">
                            <table class="w-full text-left border border-amber-200 rounded-lg overflow-hidden">
                                <thead class="bg-amber-50 text-amber-900">
                                    <tr>
                                        <th class="px-3 py-2 border-b border-amber-200">Cookie</th>
                                        <th class="px-3 py-2 border-b border-amber-200">Purpose</th>
                                        <th class="px-3 py-2 border-b border-amber-200">Type</th>
                                        <th class="px-3 py-2 border-b border-amber-200">Typical duration</th>
                                    </tr>
                                </thead>
                                <tbody class="text-amber-800">
                                    <tr class="bg-white">
                                        <td class="px-3 py-2 border-b border-amber-100"><code>laravel_session</code></td>
                                        <td class="px-3 py-2 border-b border-amber-100">Session state and sign-in continuity</td>
                                        <td class="px-3 py-2 border-b border-amber-100">Essential</td>
                                        <td class="px-3 py-2 border-b border-amber-100">Session / configurable</td>
                                    </tr>
                                    <tr class="bg-amber-50/40">
                                        <td class="px-3 py-2 border-b border-amber-100"><code>XSRF-TOKEN</code></td>
                                        <td class="px-3 py-2 border-b border-amber-100">CSRF protection for form requests</td>
                                        <td class="px-3 py-2 border-b border-amber-100">Essential</td>
                                        <td class="px-3 py-2 border-b border-amber-100">Session / short-lived</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="px-3 py-2"><code>localStorage</code> keys (for example theme/focus preferences)</td>
                                        <td class="px-3 py-2">Stores user-selected UI preferences</td>
                                        <td class="px-3 py-2">Functional</td>
                                        <td class="px-3 py-2">Until cleared by user/browser</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Third-party and analytics</h2>
                        <p class="mb-6">
                            We do not use third-party advertising cookies on this site by default. If we add analytics or embedded media that set their own cookies, we will update this page and, where required, ask for consent in the product.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Consent model</h2>
                        <p class="mb-6">
                            Essential cookies are used to run core site features. If we introduce non-essential analytics or marketing cookies, we will add a consent banner or preference center and collect consent before those cookies are set where required by law.
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
