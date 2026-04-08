<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-amber-900">
                ⚙️ {{ __('Profile Settings') }}
            </h2>
            <p class="text-amber-800/60 font-bold">Manage your account and security</p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-bold text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif
            {{-- Profile Information --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <div class="max-w-xl">
                    @if (session('status') === 'social-disconnected')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 3000)"
                            class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-bold text-amber-900"
                            role="status"
                        >{{ __('That sign-in method has been disconnected.') }}</p>
                    @endif
                    @if ($errors->any())
                        <div class="mb-8 rounded-2xl border-2 border-red-200 bg-red-50 px-5 py-4" role="alert">
                            <p class="text-sm font-black text-red-900">{{ __('Please fix the following:') }}</p>
                            <ul class="mt-2 list-disc list-inside text-sm font-bold text-red-800 space-y-1">
                                @foreach ($errors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">{{ __('Profile Information') }}</h3>
                    <p class="text-amber-800/60 font-bold mb-8">{{ __("Update your account's profile information and email address.") }}</p>
                    @include('profile.partials.update-profile-information-form')
                    @include('profile.partials.connected-social-accounts')
                    @include('profile.partials.update-public-profile-form')
                    @include('profile.partials.blocked-contributors', ['blockedContributors' => $blockedContributors ?? collect()])
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">{{ $user->password ? __('Update Password') : __('Set a password') }}</h3>
                    <p class="text-amber-800/60 font-bold mb-8">
                        @if($user->password)
                            {{ __('Ensure your account is using a long, random password to stay secure.') }}
                        @else
                            {{ __('You signed in with Google or Apple. Add a password if you want to sign in with email, or before disconnecting your only social login.') }}
                        @endif
                    </p>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="bg-red-50 border-2 border-red-100 rounded-[2rem] p-8 shadow-sm">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-extrabold text-red-900 mb-2">{{ __('Delete Account') }}</h3>
                    <p class="text-red-800/60 font-bold mb-8">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}</p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
