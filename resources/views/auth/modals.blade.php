<x-modal name="login-modal" :show="($errors->has('email') || $errors->has('password')) && !old('name')" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-xl font-bold text-amber-900 mb-4">Sign in</h2>
        <x-auth-session-status class="mb-3 text-sm" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div>
                <x-input-label for="login-email" :value="__('Email')" class="text-sm" />
                <x-text-input id="login-email" class="block mt-0.5 w-full text-sm py-1.5" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="login-password" :value="__('Password')" class="text-sm" />
                <x-text-input id="login-password" class="block mt-0.5 w-full text-sm py-1.5" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="block mt-3">
                <label for="login-remember_me" class="inline-flex items-center">
                    <input id="login-remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-3.5 w-3.5" name="remember">
                    <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex flex-col gap-2 mt-4">
                <x-primary-button type="submit" class="w-full justify-center py-1.5 text-sm">
                    {{ __('Log in') }}
                </x-primary-button>
                @if (Route::has('password.request'))
                    <a class="text-xs text-gray-600 dark:text-gray-400 hover:underline text-center" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </form>

        @if (Route::has('register'))
            <p class="text-center mt-3 text-xs text-gray-600 dark:text-gray-400">
                Don't have an account?
                <button type="button" x-data @click="$dispatch('close-modal', 'login-modal'); $dispatch('open-modal', 'register-modal')" class="font-semibold text-amber-600 hover:text-amber-700 hover:underline">
                    Create account
                </button>
            </p>
        @endif
    </div>
</x-modal>

{{-- Register modal (same style as Sign in) --}}
<x-modal name="register-modal" :show="$errors->has('name') || $errors->has('password_confirmation') || (old('name') && ($errors->has('email') || $errors->has('password')))" maxWidth="sm">
    <div class="p-6">
        <h2 class="text-xl font-bold text-amber-900 mb-4">Create account</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div>
                <x-input-label for="register-name" :value="__('Name')" class="text-sm" />
                <x-text-input id="register-name" class="block mt-0.5 w-full text-sm py-1.5" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="register-email" :value="__('Email')" class="text-sm" />
                <x-text-input id="register-email" class="block mt-0.5 w-full text-sm py-1.5" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3" x-data="{ showHint: false }">
                <div class="flex items-center justify-between">
                    <x-input-label for="register-password" :value="__('Password')" class="text-sm" />
                    <button type="button" @mouseenter="showHint = true" @mouseleave="showHint = false" @click="showHint = !showHint" class="text-amber-600 hover:text-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                </div>
                <div class="relative">
                    <x-text-input id="register-password" class="block mt-0.5 w-full text-sm py-1.5" type="password" name="password" required autocomplete="new-password" />
                    <div x-show="showHint" x-transition class="absolute z-50 w-64 p-3 mt-2 text-xs bg-white border border-amber-200 rounded-xl shadow-xl text-amber-900 -left-2 sm:left-auto sm:right-0">
                        <p class="font-bold mb-1">Password Requirements:</p>
                        <ul class="list-disc list-inside space-y-1 text-amber-800/80">
                            <li>At least 8 characters long</li>
                            <li>Include uppercase & lowercase</li>
                            <li>Include at least one number</li>
                            <li>Include a special character</li>
                        </ul>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="register-password_confirmation" :value="__('Confirm Password')" class="text-sm" />
                <x-text-input id="register-password_confirmation" class="block mt-0.5 w-full text-sm py-1.5" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>

            <div class="flex flex-col gap-2 mt-4">
                <x-primary-button type="submit" class="w-full justify-center py-1.5 text-sm">
                    {{ __('Register') }}
                </x-primary-button>
                <p class="text-center text-xs text-gray-600 dark:text-gray-400">
                    Already registered?
                    <button type="button" x-data @click="$dispatch('close-modal', 'register-modal'); $dispatch('open-modal', 'login-modal')" class="font-semibold text-amber-600 hover:text-amber-700 hover:underline">
                        Sign in
                    </button>
                </p>
            </div>
        </form>
    </div>
</x-modal>
