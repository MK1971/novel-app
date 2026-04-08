<x-guest-layout
    page-title="Legal — What's My Book Name"
    meta-description="Legal hub for What's My Book Name: terms, privacy, refunds, community guidelines, and cookies."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Legal</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">
                        Policies and guidelines for {{ config('app.name') }}. Plain-language summaries; not legal advice.
                    </p>

                    <ul class="space-y-4 text-amber-800 font-bold">
                        <li>
                            <a href="{{ route('terms') }}" class="text-amber-800 underline decoration-amber-300 hover:text-amber-900 hover:decoration-amber-600">Terms of Service</a>
                            — using the service, accounts, payments, and contributions.
                        </li>
                        <li>
                            <a href="{{ route('privacy') }}" class="text-amber-800 underline decoration-amber-300 hover:text-amber-900 hover:decoration-amber-600">Privacy Policy</a>
                            — what we collect and how we use it.
                        </li>
                        <li>
                            <a href="{{ route('legal.refunds') }}" class="text-amber-800 underline decoration-amber-300 hover:text-amber-900 hover:decoration-amber-600">Refunds &amp; cancellation</a>
                            — voting credits and paid edits.
                        </li>
                        <li>
                            <a href="{{ route('legal.community') }}" class="text-amber-800 underline decoration-amber-300 hover:text-amber-900 hover:decoration-amber-600">Community guidelines</a>
                            — respectful participation.
                        </li>
                        <li>
                            <a href="{{ route('legal.cookies') }}" class="text-amber-800 underline decoration-amber-300 hover:text-amber-900 hover:decoration-amber-600">Cookie policy</a>
                            — cookies and similar tech we use.
                        </li>
                    </ul>

                    <p class="mt-10 text-sm font-bold text-amber-800/70">
                        Questions? <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Send feedback</a>.
                    </p>

                    <p class="mt-3 text-xs text-amber-800/60">
                        Legal identity source: <code>LEGAL_ENTITY_NAME</code>, <code>LEGAL_ENTITY_ADDRESS</code>, <code>LEGAL_CONTACT_EMAIL</code>, and <code>LEGAL_JURISDICTION</code> in environment settings.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
