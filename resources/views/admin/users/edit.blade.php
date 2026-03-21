<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Edit User: {{ $user->name }}
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Update profile details and administrative status.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-10">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <div>
                            <label class="block text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required>
                            @error('name') <p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-amber-900/30 uppercase tracking-[0.2em] mb-4">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required>
                            @error('email') <p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="bg-amber-50/50 rounded-3xl p-8 border border-amber-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-lg font-extrabold text-amber-900">Administrative Access</h4>
                                    <p class="text-amber-800/60 font-bold text-sm mt-1">Grant this user full access to moderation and management tools.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_admin" value="1" class="sr-only peer" {{ $user->is_admin ? 'checked' : '' }}>
                                    <div class="w-14 h-8 bg-amber-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-900"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-8 border-t border-amber-50">
                        <button type="submit" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                            Save Changes
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="px-10 py-4 bg-amber-50 text-amber-900 font-extrabold rounded-2xl hover:bg-amber-100 transition-all">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
