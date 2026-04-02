<section class="space-y-6">
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700 text-white font-extrabold px-8 py-3 rounded-xl transition-all shadow-lg shadow-red-600/20 border-none"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-2xl font-extrabold text-red-900 mb-2">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="text-red-800/60 font-bold mb-8">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-password-reveal-field
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                    placeholder="{{ __('Password') }}"
                    class="mt-1 border-red-200 focus:border-red-500 focus:ring-red-500"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')" class="px-6 py-3 rounded-xl font-bold border-amber-200 text-amber-900 hover:bg-amber-50">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-700 text-white font-extrabold px-8 py-3 rounded-xl transition-all shadow-lg shadow-red-600/20 border-none">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
