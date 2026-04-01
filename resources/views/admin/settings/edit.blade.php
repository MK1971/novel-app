<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-amber-900">Site settings</h2>
        <p class="text-amber-800/60 font-bold mt-1">Operational email for moderation alerts and chapter deadlines (separate from login admin).</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-2xl border border-green-200 font-bold">{{ session('success') }}</div>
            @endif

            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-8 shadow-sm">
                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="admin_notification_email" class="block text-amber-900 font-extrabold mb-2">Admin notification email</label>
                        <input
                            type="email"
                            id="admin_notification_email"
                            name="admin_notification_email"
                            value="{{ old('admin_notification_email', $adminNotificationEmail) }}"
                            class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold"
                            placeholder="ops@example.com"
                        >
                        <p class="text-xs font-bold text-amber-800/50 mt-2">New paid suggestions and upcoming chapter edit deadlines can be sent here when mail is configured.</p>
                        @error('admin_notification_email')
                            <p class="text-red-600 text-sm font-bold mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="px-8 py-3 bg-amber-900 text-white font-extrabold rounded-xl hover:bg-black transition-all">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
