<x-modal name="login-modal" :show="($errors->has('email') || $errors->has('password')) && !old('name')" maxWidth="sm">
    <div class="border-b border-amber-100 bg-gradient-to-br from-amber-50/90 to-white px-6 pt-5 pb-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-black shadow-md shadow-amber-500/30" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/50">What&apos;s My Book Name</p>
                <h2 class="text-lg font-black text-amber-900 leading-tight">Sign in</h2>
            </div>
        </div>
    </div>
    <div class="p-6">
        <x-auth-session-status class="mb-3 text-sm" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div>
                <x-input-label for="login-email" :value="__('Email')" class="text-sm" />
                <x-text-input id="login-email" class="mt-0.5 block w-full py-1.5 text-sm" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="login-password" :value="__('Password')" class="text-sm" />
                <x-password-reveal-field
                    id="login-password"
                    name="password"
                    autocomplete="current-password"
                    required
                    class="mt-0.5 py-1.5 text-sm"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3 block">
                <label for="login-remember_me" class="inline-flex items-center">
                    <input id="login-remember_me" type="checkbox" class="h-3.5 w-3.5 rounded border-amber-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
                    <span class="ms-2 text-xs text-amber-900/70">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <x-primary-button type="submit" class="w-full justify-center py-1.5 text-sm">
                    {{ __('Log in') }}
                </x-primary-button>
                @if (Route::has('password.request'))
                    <a class="text-center text-xs text-amber-800/70 hover:text-amber-900 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>
        </form>

        @if (Route::has('register'))
            <p class="mt-3 text-center text-xs text-amber-800/70">
                Don&apos;t have an account?
                <button type="button" x-data @click="$dispatch('close-modal', 'login-modal'); $dispatch('open-modal', 'register-modal')" class="font-black text-amber-700 hover:text-amber-900 hover:underline">
                    Create account
                </button>
            </p>
        @endif
    </div>
</x-modal>

{{-- Register modal --}}
<x-modal name="register-modal" :show="$errors->has('name') || $errors->has('password_confirmation') || (old('name') && ($errors->has('email') || $errors->has('password')))" maxWidth="sm">
    <div class="border-b border-amber-100 bg-gradient-to-br from-amber-50/90 to-white px-6 pt-5 pb-4">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-500 text-black shadow-md shadow-amber-500/30" aria-hidden="true">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-black uppercase tracking-widest text-amber-800/50">What&apos;s My Book Name</p>
                <h2 class="text-lg font-black text-amber-900 leading-tight">Create account</h2>
            </div>
        </div>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

            <div>
                <x-input-label for="register-name" :value="__('Name')" class="text-sm" />
                <x-text-input id="register-name" class="mt-0.5 block w-full py-1.5 text-sm" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="register-email" :value="__('Email')" class="text-sm" />
                <x-text-input id="register-email" class="mt-0.5 block w-full py-1.5 text-sm" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3" x-data="{ showHint: false }">
                <div class="flex items-center justify-between gap-2">
                    <x-input-label for="register-password" :value="__('Password')" class="text-sm" />
                    <button type="button" @mouseenter="showHint = true" @mouseleave="showHint = false" @click="showHint = !showHint" class="shrink-0 text-amber-700 hover:text-amber-900" aria-label="Password requirements">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </button>
                </div>
                <div class="relative">
                    <x-password-reveal-field
                        id="register-password"
                        name="password"
                        autocomplete="new-password"
                        required
                        class="mt-0.5 py-1.5 text-sm"
                    />
                    <div x-show="showHint" x-transition class="absolute z-50 mt-2 w-64 -left-2 rounded-xl border border-amber-200 bg-white p-3 text-xs text-amber-900 shadow-xl sm:left-auto sm:right-0">
                        <p class="mb-1 font-bold">Password requirements</p>
                        <ul class="list-inside list-disc space-y-1 text-amber-800/80">
                            <li>At least 8 characters long</li>
                            <li>Include uppercase &amp; lowercase</li>
                            <li>Include at least one number</li>
                            <li>Include a special character</li>
                        </ul>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="mt-3">
                <x-input-label for="register-password_confirmation" :value="__('Confirm Password')" class="text-sm" />
                <x-password-reveal-field
                    id="register-password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                    class="mt-0.5 py-1.5 text-sm"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-xs" />
            </div>

            <div class="mt-4 flex flex-col gap-2">
                <x-primary-button type="submit" class="w-full justify-center py-1.5 text-sm">
                    {{ __('Register') }}
                </x-primary-button>
                <p class="text-center text-xs text-amber-800/70">
                    Already registered?
                    <button type="button" x-data @click="$dispatch('close-modal', 'register-modal'); $dispatch('open-modal', 'login-modal')" class="font-black text-amber-700 hover:text-amber-900 hover:underline">
                        Sign in
                    </button>
                </p>
            </div>
        </form>
    </div>
</x-modal>
