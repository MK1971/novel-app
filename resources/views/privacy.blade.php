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

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Data controller</h2>
                        <p class="mb-6">
                            Data controller: <strong>{{ config('legal.entity_name') }}</strong>@if(filled(config('legal.entity_address'))) · {{ config('legal.entity_address') }}@endif. Privacy contact: <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a>@if(filled(config('legal.contact_email'))) or <a href="mailto:{{ config('legal.contact_email') }}" class="text-amber-700 underline hover:text-amber-900">{{ config('legal.contact_email') }}</a>@endif.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">What we collect</h2>
                        <ul class="list-disc pl-6 space-y-2 mb-6">
                            <li><strong>Account data</strong> — such as name and email, if you register or sign in.</li>
                            <li><strong>Sign in with Google or Apple</strong> — if you choose this option, Google or Apple shares a stable account identifier and (usually) your name and email with us so we can create or log you into your account. We do not receive your Google or Apple password. Their use of your data is governed by their policies.</li>
                            <li><strong>Public profiles</strong> — if you opt in, we publish the display name, optional photo, optional short bio, and aggregate contribution stats you choose at a public URL (<code class="text-sm">/people/…</code>). We do not show your email there. You can disable the page or limit search indexing in profile settings.</li>
                            <li><strong>Abuse prevention</strong> — if you use report or block on a public profile, we store the minimum data needed to process the report or enforce the block (for example who reported whom, category, optional message, and block relationships).</li>
                            <li><strong>Content you submit</strong> — including edit suggestions, votes, feedback, and payment-related records needed to operate those features.</li>
                            <li><strong>Technical data</strong> — standard server and session information (e.g. IP, browser type, cookies) used to keep the service secure and working.</li>
                        </ul>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">How we use it</h2>
                        <p class="mb-6">
                            We use this information to run the platform (accounts, chapters, voting, moderation, payments where applicable), improve the product, comply with law, and respond to you when you contact us.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Payments</h2>
                        <p class="mb-6">
                            Payments are processed by <strong>PayPal</strong>. We do not store your full card details or your PayPal credentials on our servers. PayPal handles payment authentication according to its own policies. We may store metadata needed to operate purchases (for example transaction identifiers, amounts, timestamps, and status) and to provide history in your account where the product supports it. See PayPal’s policy at <a href="https://www.paypal.com/privacy" class="text-amber-700 underline hover:text-amber-900">paypal.com/privacy</a>.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Sharing</h2>
                        <p class="mb-6">
                            We do not sell your personal information. We may share data with service providers who help us host the site, send email, or process payments (including PayPal), under agreements that require them to protect it, or when required by law.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Legal bases and regional rights</h2>
                        <p class="mb-6">
                            Depending on where you live, privacy laws may give you rights to access, correct, delete, or export certain information, or to object to or limit some processing. Where the GDPR applies, we generally rely on <strong>contract</strong> (to provide the service you asked for), <strong>legitimate interests</strong> (for example security, fraud prevention, and improving the product in ways users expect), and, where required, <strong>consent</strong> (for example optional communications or non-essential cookies if we add them). Where the CCPA/CPRA applies, we do not sell personal information as defined by those laws; you may still have rights to know, delete, and correct certain data subject to exceptions.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Retention</h2>
                        <ul class="list-disc pl-6 space-y-2 mb-6">
                            <li><strong>Account profile data:</strong> retained while account is active, then generally removed or anonymized within 30 days after validated deletion requests unless legal obligations require longer retention.</li>
                            <li><strong>Payment and tax-relevant metadata:</strong> retained up to 7 years where needed for accounting, audit, chargeback, fraud prevention, and legal compliance.</li>
                            <li><strong>Security and technical logs:</strong> typically retained on a rolling window (about 90 days), except where incident investigation requires longer retention.</li>
                        </ul>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Cookies</h2>
                        <p class="mb-6">
                            We use cookies and similar technologies for sessions, security (including CSRF protection), and essential site function. See our <a href="{{ route('legal.cookies') }}" class="text-amber-700 underline hover:text-amber-900">Cookie policy</a> for detail.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Your choices</h2>
                        <p class="mb-6">
                            You can review or update account details in your profile where the product supports it. You can turn off your public profile, hide yourself from the public leaderboard, or request <code class="text-sm">noindex</code> for your public page from profile settings when those options are available. For privacy requests (access, correction, deletion, or other rights offered in your region), contact us using <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a> in the footer so we can verify and respond. We target response within 30 days for validated requests. We may need to keep certain records where law requires.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">International transfers and complaints</h2>
                        <p class="mb-6">
                            Some providers we use (including PayPal) may process data in countries different from yours. Where required, we rely on lawful transfer mechanisms and contractual safeguards. If you are in a region with supervisory authorities (for example under GDPR), you also have the right to lodge a complaint with your local authority.
                        </p>

                        <p class="mt-10 text-sm font-bold text-amber-800/70">
                            Questions? See our <a href="{{ route('legal.index') }}" class="text-amber-700 underline hover:text-amber-900">Legal hub</a>, <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms of Service</a>, or <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">send feedback</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
