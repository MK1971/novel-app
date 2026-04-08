<x-guest-layout
    page-title="Refunds & cancellation — What's My Book Name"
    meta-description="Refunds and cancellation policy for What's My Book Name: voting credits and paid edits via PayPal."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Refunds &amp; cancellation</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">Last updated: {{ date('F j, Y') }}</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            This page explains how paid features work and when refunds may apply. It is a plain-language summary, not legal advice. For the full agreement, see our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms of Service</a>.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Payment processor</h2>
                        <p class="mb-6">
                            Payments are processed by <strong>PayPal</strong>. PayPal’s terms and privacy practices also apply when you pay through them. We do not store your full payment credentials on our servers.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Voting credits</h2>
                        <p class="mb-6">
                            Where the product sells voting credits or similar digital entitlements, those purchases are generally <strong>final</strong> once delivered to your account, except where required by law or where we explicitly state otherwise in the app at purchase.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Paid edits and services</h2>
                        <p class="mb-6">
                            Fees for suggested edits or other services are tied to features described in the product. If a payment fails or we cannot deliver the stated feature due to an error on our side, contact us via <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a> and we will review the case.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Donations</h2>
                        <p class="mb-6">
                            Donations are voluntary support contributions. They are separate from paid edit fees, do not grant vote credits by default, and are not represented as tax-deductible charitable gifts.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Chargebacks and abuse</h2>
                        <p class="mb-6">
                            Misuse of payment systems or chargebacks filed in bad faith may result in suspension of access, as described in our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms</a>.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            <a href="{{ route('legal.index') }}" class="text-amber-700 underline hover:text-amber-900">← Legal hub</a>
                            · <a href="{{ route('privacy') }}" class="text-amber-700 underline hover:text-amber-900">Privacy</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
