@php
    $publicPageTitle = $profileUser->name.' — Contributor — '.config('app.name');
    $publicMetaDescription = 'Public contributor profile for '.$profileUser->name.' on '.config('app.name').'. Points and suggestion stats only; email is not shown.';
@endphp
<x-guest-layout :page-title="$publicPageTitle" :meta-description="$publicMetaDescription">
    <x-slot name="headMeta">
        @if (! ($profileUser->profile_indexable ?? true))
            <meta name="robots" content="noindex, nofollow" />
        @endif
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl border border-green-200 dark:border-emerald-800 bg-green-50 dark:bg-emerald-950/50 text-green-800 dark:text-emerald-200 text-sm font-bold" role="status">
                    {{ session('success') }}
                </div>
            @endif
            <div class="bg-white/80 dark:bg-stone-900/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100 dark:border-stone-700">
                <div class="p-8 md:p-10">
                    <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/50 dark:text-amber-200/50 mb-3">Contributor</p>
                    <div class="flex flex-col sm:flex-row sm:items-start gap-6">
                        <div class="w-24 h-24 bg-amber-100 dark:bg-stone-800 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-300 text-3xl font-black border-2 border-amber-200 dark:border-stone-600 shrink-0 overflow-hidden">
                            @if ($profileUser->avatarUrl())
                                <img src="{{ $profileUser->avatarUrl() }}" alt="" class="w-full h-full object-cover" width="96" height="96" />
                            @else
                                {{ substr($profileUser->name, 0, 1) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h1 class="text-3xl font-black text-amber-900 dark:text-amber-50 leading-tight">{{ $profileUser->name }}</h1>
                            <p class="text-sm font-bold text-amber-800/60 dark:text-stone-400 mt-1">Member since {{ $profileUser->created_at->format('F Y') }}</p>
                            @if (filled($profileUser->profile_bio))
                                <div class="mt-4 prose prose-amber dark:prose-invert prose-sm max-w-none text-amber-900 dark:text-amber-100 font-bold leading-relaxed">
                                    {!! nl2br(e($profileUser->profile_bio)) !!}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-amber-100 dark:border-stone-700 bg-amber-50/60 dark:bg-stone-800/50 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55 dark:text-stone-400">Points</p>
                            <p class="text-2xl font-black text-amber-950 dark:text-amber-50 tabular-nums">{{ number_format($stats['points']) }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-100 dark:border-stone-700 bg-amber-50/60 dark:bg-stone-800/50 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55 dark:text-stone-400">Suggestions submitted</p>
                            <p class="text-2xl font-black text-amber-950 dark:text-amber-50 tabular-nums">{{ number_format($stats['submitted']) }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-100 dark:border-stone-700 bg-amber-50/60 dark:bg-stone-800/50 px-4 py-3 text-center">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-800/55 dark:text-stone-400">Accepted by moderators</p>
                            <p class="text-2xl font-black text-amber-950 dark:text-amber-50 tabular-nums">{{ number_format($stats['accepted']) }}</p>
                        </div>
                    </div>

                    <p class="mt-10 text-xs font-bold text-amber-800/55 dark:text-stone-500 leading-relaxed">
                        This is an optional public page. Email and account details are not shown here.
                        <a href="{{ route('leaderboard') }}" class="text-amber-700 dark:text-amber-300 underline hover:text-amber-900 dark:hover:text-amber-100">Leaderboard</a>
                        ·
                        <a href="{{ route('chapters.index') }}" class="text-amber-700 dark:text-amber-300 underline hover:text-amber-900 dark:hover:text-amber-100">Read chapters</a>
                    </p>

                    @auth
                        @if (auth()->id() !== $profileUser->id)
                            <div class="mt-10 pt-8 border-t border-amber-100 dark:border-stone-700 space-y-6">
                                <div>
                                    <h2 class="text-sm font-black uppercase tracking-widest text-amber-800/50 dark:text-stone-500">{{ __('Safety') }}</h2>
                                    <p class="mt-2 text-sm font-bold text-amber-800/70 dark:text-stone-400 leading-relaxed max-w-xl">
                                        {{ __('Block stops you and this person from seeing each other’s public profiles. Report sends a confidential note to moderators (use the form below).') }}
                                    </p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    @if ($viewerHasBlocked ?? false)
                                        <form method="post" action="{{ route('profile.public.unblock', ['slug' => $profileUser->public_slug]) }}" class="inline">
                                            @csrf
                                            @method('delete')
                                            <x-secondary-button type="submit">{{ __('Unblock this contributor') }}</x-secondary-button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('profile.public.block', ['slug' => $profileUser->public_slug]) }}" class="inline" onsubmit="return confirm(@json(__('Block this contributor? You will not see each other’s public profiles.')));">
                                            @csrf
                                            <x-secondary-button type="submit" class="border-red-200 text-red-800 dark:border-red-900 dark:text-red-300">
                                                {{ __('Block') }}
                                            </x-secondary-button>
                                        </form>
                                    @endif
                                    {{-- Match <x-secondary-button> + Block: white field, clear border, readable label (not amber-on-amber) --}}
                                    <a
                                        href="#profile-public-report"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-500 rounded-md font-semibold text-xs uppercase tracking-widest text-stone-800 dark:text-stone-100 shadow-sm hover:bg-stone-50 dark:hover:bg-stone-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 dark:focus-visible:ring-amber-400 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-stone-900 scroll-mt-24"
                                    >
                                        {{ __('Report profile') }}
                                    </a>
                                </div>

                                <div id="profile-public-report" class="scroll-mt-28 rounded-2xl border border-stone-200 dark:border-stone-600 bg-white dark:bg-stone-900/60 p-5 sm:p-6 shadow-sm">
                                    <h3 class="text-base font-extrabold text-stone-900 dark:text-stone-50 mb-1">{{ __('Report this profile') }}</h3>
                                    <p class="text-xs font-bold text-stone-600 dark:text-stone-400 mb-4">{{ __('Choose a category and add details if helpful. We review reports; you won’t get a public reply here.') }}</p>
                                    <form method="post" action="{{ route('profile.public.report', ['slug' => $profileUser->public_slug]) }}" class="space-y-4 max-w-lg">
                                        @csrf
                                        <div>
                                            <x-input-label for="report_category" value="{{ __('Category') }}" class="text-stone-900 dark:text-stone-100" />
                                            <select id="report_category" name="category" required class="mt-1 block w-full rounded-xl border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-sm font-bold">
                                                <option value="spam">{{ __('Spam or scam') }}</option>
                                                <option value="harassment">{{ __('Harassment or abuse') }}</option>
                                                <option value="impersonation">{{ __('Impersonation') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            </select>
                                            <x-input-error class="mt-2" :messages="$errors->get('category')" />
                                        </div>
                                        <div>
                                            <x-input-label for="report_details" value="{{ __('Details (optional)') }}" class="text-stone-900 dark:text-stone-100" />
                                            <textarea id="report_details" name="details" rows="3" maxlength="2000" class="mt-1 block w-full rounded-xl border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-800 text-stone-900 dark:text-stone-100 text-sm font-bold">{{ old('details') }}</textarea>
                                            <x-input-error class="mt-2" :messages="$errors->get('details')" />
                                        </div>
                                        <x-primary-button type="submit" class="bg-amber-700 hover:bg-amber-800">{{ __('Submit report') }}</x-primary-button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
