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
            {{-- Profile Information --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">{{ __('Profile Information') }}</h3>
                    <p class="text-amber-800/60 font-bold mb-8">{{ __("Update your account's profile information and email address.") }}</p>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <div class="max-w-xl">
                    <h3 class="text-2xl font-extrabold text-amber-900 mb-2">{{ __('Update Password') }}</h3>
                    <p class="text-amber-800/60 font-bold mb-8">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
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
