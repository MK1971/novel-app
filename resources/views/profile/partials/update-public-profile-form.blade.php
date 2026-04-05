<section class="mt-10 border-t border-amber-100 pt-10">
    <h3 class="text-xl font-extrabold text-amber-900 mb-2">{{ __('Public contributor page') }}</h3>
    <p class="text-amber-800/70 font-bold text-sm mb-4 max-w-xl leading-relaxed">
        {{ __('Optional. When enabled, anyone with your link can see your display name, photo, a short bio, and high-level contribution stats — not your email or private account details. You can turn this off anytime.') }}
    </p>
    <div class="rounded-xl border-2 border-amber-300 bg-amber-50 px-4 py-3 text-xs font-bold text-amber-950 mb-6 max-w-xl leading-relaxed">
        <p class="font-black text-amber-900 mb-1">{{ __('404 on /people/your-name?') }}</p>
        <p>{{ __('The page only goes live when “Show a public profile” is checked and you click “Save public profile”. A slug alone stays private until both are saved.') }}</p>
    </div>
    @if ($user->public_profile_enabled && filled($user->public_slug))
        <p class="text-sm font-black text-emerald-900 mb-6">
            {{ __('Your live page:') }}
            <a href="{{ route('profile.public', ['slug' => $user->public_slug]) }}" class="underline break-all hover:text-emerald-700">{{ route('profile.public', ['slug' => $user->public_slug], false) }}</a>
        </p>
    @endif

    <form method="post" action="{{ route('profile.public-settings.update') }}" class="space-y-6" id="public-profile-settings-form">
        @csrf
        @method('patch')

        <input type="hidden" name="public_profile_enabled" value="0" />
        <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/40 p-4">
            <input
                id="public_profile_enabled"
                name="public_profile_enabled"
                type="checkbox"
                value="1"
                class="mt-1 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                @checked(old('public_profile_enabled', $user->public_profile_enabled))
            />
            <div class="min-w-0">
                <x-input-label for="public_profile_enabled" value="Show a public profile" class="text-amber-900 font-extrabold cursor-pointer" />
                <p class="text-xs font-bold text-amber-800/70 mt-1">{{ __('Uses the name and photo from your profile information above.') }}</p>
            </div>
        </div>

        <input type="hidden" name="leaderboard_visible" value="0" />
        <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/40 p-4">
            <input
                id="leaderboard_visible"
                name="leaderboard_visible"
                type="checkbox"
                value="1"
                class="mt-1 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                @checked(old('leaderboard_visible', $user->leaderboard_visible ?? true))
            />
            <div class="min-w-0">
                <x-input-label for="leaderboard_visible" :value="__('Show me on the public leaderboard')" class="text-amber-900 font-extrabold cursor-pointer" />
                <p class="text-xs font-bold text-amber-800/70 mt-1">{{ __('When off, your name and points are hidden from the leaderboard lists (your account still works normally).') }}</p>
            </div>
        </div>

        <input type="hidden" name="profile_indexable" value="0" />
        <div class="flex items-start gap-3 rounded-xl border border-amber-100 bg-amber-50/40 p-4">
            <input
                id="profile_indexable"
                name="profile_indexable"
                type="checkbox"
                value="1"
                class="mt-1 h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500"
                @checked(old('profile_indexable', $user->profile_indexable ?? true))
            />
            <div class="min-w-0">
                <x-input-label for="profile_indexable" :value="__('Allow search engines to index my public profile')" class="text-amber-900 font-extrabold cursor-pointer" />
                <p class="text-xs font-bold text-amber-800/70 mt-1">{{ __('When off, we ask crawlers not to index your /people/… page (noindex). Applies only while your public profile is enabled.') }}</p>
            </div>
        </div>

        <div>
            <x-input-label for="public_slug" value="Public URL slug" class="text-amber-900 font-extrabold mb-2" />
            <div class="flex flex-wrap items-center gap-2 text-sm font-bold text-amber-800/80">
                <span class="select-none">{{ url('/people') }}/</span>
                <x-text-input
                    id="public_slug"
                    name="public_slug"
                    type="text"
                    class="max-w-xs border-amber-200 focus:border-amber-500 focus:ring-amber-500 rounded-xl font-mono text-sm"
                    :value="old('public_slug', $user->public_slug)"
                    autocomplete="off"
                    placeholder="your-name"
                />
            </div>
            <p class="mt-1 text-xs font-bold text-amber-800/60">{{ __('Lowercase letters, numbers, and hyphens only. Required when the box above is checked.') }}</p>
            <x-input-error class="mt-2" :messages="$errors->get('public_slug')" />
        </div>

        <div>
            <x-input-label for="profile_bio" value="Short bio (optional)" class="text-amber-900 font-extrabold mb-2" />
            <textarea
                id="profile_bio"
                name="profile_bio"
                rows="4"
                maxlength="500"
                class="block w-full max-w-xl rounded-xl border-amber-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 text-sm font-bold text-amber-900"
                placeholder="{{ __('A few lines about you — shown only on your public page.') }}"
            >{{ old('profile_bio', $user->profile_bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('profile_bio')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button type="submit" form="public-profile-settings-form" class="bg-amber-600 hover:bg-amber-700 text-white font-extrabold px-8 py-3 rounded-xl border-none">
                {{ __('Save public profile') }}
            </x-primary-button>
            @if (session('status') === 'public-profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-amber-600 font-bold"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
