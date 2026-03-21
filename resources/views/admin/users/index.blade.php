<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    User Management
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Manage community members and administrative privileges.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-8 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
            @endif

            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-amber-50/50 border-b border-amber-100">
                                <th class="px-10 py-6 text-xs font-black text-amber-900/30 uppercase tracking-[0.2em]">User Details</th>
                                <th class="px-10 py-6 text-xs font-black text-amber-900/30 uppercase tracking-[0.2em]">Points</th>
                                <th class="px-10 py-6 text-xs font-black text-amber-900/30 uppercase tracking-[0.2em]">Role</th>
                                <th class="px-10 py-6 text-xs font-black text-amber-900/30 uppercase tracking-[0.2em]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @foreach($users as $user)
                                <tr class="hover:bg-amber-50/30 transition-colors group">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-900 font-black text-xl">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-extrabold text-amber-900 text-lg">{{ $user->name }}</div>
                                                <div class="text-amber-800/40 font-bold text-sm">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">
                                            {{ number_format($user->points) }} PTS
                                        </span>
                                    </td>
                                    <td class="px-10 py-8">
                                        @if($user->is_admin)
                                            <span class="px-4 py-1 bg-amber-900 text-white text-[10px] font-black rounded-full uppercase tracking-widest">Administrator</span>
                                        @else
                                            <span class="px-4 py-1 bg-amber-100 text-amber-800 text-[10px] font-black rounded-full uppercase tracking-widest">Community Member</span>
                                        @endif
                                    </td>
                                    <td class="px-10 py-8">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-6 py-3 bg-amber-50 text-amber-900 font-black text-xs rounded-xl hover:bg-amber-900 hover:text-white transition-all uppercase tracking-widest">
                                            Edit Profile
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-10 border-t border-amber-50">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
