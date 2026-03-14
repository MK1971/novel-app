<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">
            Registered Users
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl overflow-hidden">
                <div class="p-8 border-b border-amber-50">
                    <h3 class="text-2xl font-bold text-amber-900">User Management</h3>
                    <p class="text-amber-800/70">View and manage all registered contributors.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-amber-100">
                        <thead class="bg-amber-50">
                            <tr>
                                <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Name</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Email</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Points</th>
                                <th class="px-8 py-4 text-left text-sm font-bold text-amber-900 uppercase tracking-wider">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-amber-50">
                            @foreach($users as $user)
                                <tr class="hover:bg-amber-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="text-lg font-bold text-amber-900">{{ $user->name }}</div>
                                        @if($user->email === 'admin@example.com')
                                            <span class="px-2 py-0.5 bg-amber-500 text-black text-[10px] font-bold rounded-full uppercase">Admin</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-amber-800/80">{{ $user->email }}</td>
                                    <td class="px-8 py-6">
                                        <span class="inline-flex items-center px-3 py-1 bg-amber-100 text-amber-800 text-sm font-bold rounded-full">
                                            {{ $user->points }} pts
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-amber-800/60 text-sm">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-8 py-6 bg-amber-50/50 border-t border-amber-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
