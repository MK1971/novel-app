<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="avatar" value="Profile photo" class="text-amber-900 font-extrabold mb-2" />
            @if($user->avatarUrl())
                <div class="mb-4 flex flex-wrap items-center gap-4">
                    <img src="{{ $user->avatarUrl() }}" alt="" class="w-20 h-20 rounded-2xl object-cover border-2 border-amber-200 shrink-0" width="80" height="80" />
                    <p class="text-xs font-bold text-amber-800/80 max-w-xs">Your current photo. Choose a file below to replace it (the file control may still say “no file chosen” until you save — that is normal).</p>
                </div>
            @endif
            <div id="novel-profile-avatar-new-preview-wrap" class="hidden mb-4">
                <p class="text-xs font-black text-amber-900 mb-1">New photo preview</p>
                <img id="novel-profile-avatar-new-preview" src="" alt="" class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-200/80" width="80" height="80" />
            </div>
            <p id="novel-profile-avatar-file-label" class="text-xs font-bold text-amber-700 mb-2 min-h-[1.25rem]" aria-live="polite"></p>
            <input id="avatar" name="avatar" type="file" accept="image/*" class="block w-full text-sm text-amber-900 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-black file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200" />
            <p class="mt-1 text-xs font-bold text-amber-800/70">Optional. JPG, PNG, WebP, or GIF — max 5&nbsp;MB. Shown on your profile and in the header instead of your initial. If the image never appears after saving, run <code class="text-amber-900">php artisan storage:link</code> on the server and ensure <code class="text-amber-900">storage/app/public</code> is writable.</p>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" class="text-amber-900 font-extrabold mb-2" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-amber-500 rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-amber-900 font-extrabold mb-2" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full border-amber-200 focus:border-amber-500 focus:ring-amber-500 rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-amber-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-amber-600 hover:text-amber-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-amber-600 hover:bg-amber-700 text-white font-extrabold px-8 py-3 rounded-xl transition-all shadow-lg shadow-amber-600/20 border-none">
                {{ __('Save Changes') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-amber-600 font-bold"
                >{{ __('Saved successfully.') }}</p>
            @endif
        </div>
    </form>
    <script>
        (function () {
            var input = document.getElementById('avatar');
            if (!input) return;
            var wrap = document.getElementById('novel-profile-avatar-new-preview-wrap');
            var img = document.getElementById('novel-profile-avatar-new-preview');
            var label = document.getElementById('novel-profile-avatar-file-label');
            var lastUrl = null;
            input.addEventListener('change', function () {
                if (lastUrl) {
                    URL.revokeObjectURL(lastUrl);
                    lastUrl = null;
                }
                var f = input.files && input.files[0];
                if (!f) {
                    wrap.classList.add('hidden');
                    label.textContent = '';
                    return;
                }
                label.textContent = 'Selected: ' + f.name;
                lastUrl = URL.createObjectURL(f);
                img.src = lastUrl;
                wrap.classList.remove('hidden');
            });
        })();
    </script>
</section>
