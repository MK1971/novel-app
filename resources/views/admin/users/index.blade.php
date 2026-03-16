<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8">Registered Contributors</h1>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-amber-100">
                            <thead class="bg-amber-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-amber-600 uppercase tracking-wider">User</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-amber-600 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-amber-600 uppercase tracking-wider">Points</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-amber-600 uppercase tracking-wider">Joined</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-amber-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-amber-50">
                                @foreach($users as $user)
                                    <tr class="hover:bg-amber-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 font-bold border border-amber-200">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-amber-900">{{ $user->name }}</div>
                                                    @if($user->email === 'admin@example.com')
                                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded-full uppercase tracking-wider">Admin</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-700">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                                                {{ $user->points ?? 0 }} pts
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-amber-600">{{ $user->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <a href="#" class="text-amber-600 hover:text-amber-900 font-bold">Edit</a>
                                                <button class="text-red-600 hover:text-red-900 font-bold">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
