<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        @if($user->password)
            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-amber-900 font-extrabold mb-2" />
                <x-password-reveal-field
                    id="update_password_current_password"
                    name="current_password"
                    autocomplete="current-password"
                    required
                    class="mt-1 border-amber-200 focus:border-amber-500 focus:ring-amber-500"
                />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>
        @endif

        <div>
            <x-input-label for="update_password_password" :value="$user->password ? __('New Password') : __('Password')" class="text-amber-900 font-extrabold mb-2" />
            <x-password-reveal-field
                id="update_password_password"
                name="password"
                autocomplete="new-password"
                required
                class="mt-1 border-amber-200 focus:border-amber-500 focus:ring-amber-500"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-amber-900 font-extrabold mb-2" />
            <x-password-reveal-field
                id="update_password_password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                required
                class="mt-1 border-amber-200 focus:border-amber-500 focus:ring-amber-500"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-amber-600 hover:bg-amber-700 text-white font-extrabold px-8 py-3 rounded-xl transition-all shadow-lg shadow-amber-600/20 border-none">
                {{ $user->password ? __('Update Password') : __('Save password') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-amber-600 font-bold"
                >{{ __('Password updated successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
