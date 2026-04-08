<x-guest-layout
    page-title="Community guidelines — What's My Book Name"
    meta-description="Community guidelines for What's My Book Name: respectful collaboration, edits, voting, and safety."
>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-2">Community guidelines</h1>
                    <p class="text-sm font-bold text-amber-800/60 mb-10">Last updated: {{ date('F j, Y') }}</p>

                    <div class="prose prose-amber max-w-none text-amber-800">
                        <p class="text-lg leading-relaxed mb-6">
                            {{ config('app.name') }} works when readers collaborate with care. These guidelines summarize what we expect. They sit alongside our <a href="{{ route('terms') }}" class="text-amber-700 underline hover:text-amber-900">Terms of Service</a>.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Be respectful</h2>
                        <p class="mb-6">
                            Treat other people with respect. No harassment, hate, threats, or stalking. Do not share others’ private information without permission.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Keep content lawful and appropriate</h2>
                        <p class="mb-6">
                            Do not post or solicit illegal content, spam, malware, or material that sexualizes minors. Respect intellectual property: don’t copy large portions of others’ work without rights or permission.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Edits, votes, and good faith</h2>
                        <p class="mb-6">
                            Suggested edits and votes should aim to improve the reading experience. Gaming, brigading, or automated abuse of voting or payments undermines the project and may lead to moderation action.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Examples of prohibited behavior</h2>
                        <ul class="list-disc pl-6 space-y-2 mb-6">
                            <li>Coordinated brigading, vote manipulation, or attempts to bypass payment/voting rules.</li>
                            <li>Multiple-account abuse (“sockpuppeting”) to influence edits, votes, or moderation outcomes.</li>
                            <li>Spam, repetitive low-effort suggestions, or commercial self-promotion unrelated to the project.</li>
                            <li>Submitting text you do not have rights to use.</li>
                        </ul>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Moderation</h2>
                        <p class="mb-6">
                            We may remove content, suspend accounts, or limit features to protect the community or comply with law. If you see something that breaks these rules, use <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a> or the reporting paths offered in the product.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Enforcement approach</h2>
                        <p class="mb-6">
                            Enforcement is usually progressive: warning, temporary restrictions, suspension, then permanent ban for repeated or severe violations. We may skip steps for serious safety or legal risks.
                        </p>

                        <h2 class="text-2xl font-semibold text-amber-900 mt-10 mb-4">Appeals</h2>
                        <p class="mb-6">
                            If you believe moderation action was incorrect, submit an appeal through <a href="{{ route('feedback.index') }}" class="text-amber-700 underline hover:text-amber-900">Feedback</a> within 14 days of the action. Include relevant links and context. We target review within 10 business days.
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
