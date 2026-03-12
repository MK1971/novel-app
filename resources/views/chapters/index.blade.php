<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            The Book With No Name - Chapters
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            <div class="space-y-4">
                @forelse($chapters ?? [] as $chapter)
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="font-bold">{{ $chapter->title }}</h3>
                        <p class="text-gray-600 mt-2">{{ Str::limit($chapter->content, 200) }}</p>
                        <a href="{{ route('chapters.show', $chapter) }}" class="inline-block mt-4 text-blue-600 hover:underline">Read & Suggest Edit →</a>
                    </div>
                @empty
                    <p>No chapters yet.</p>
                @endforelse
            </div>

            <div class="mt-8 flex gap-4">
                <a href="{{ route('leaderboard') }}" class="text-blue-600 hover:underline">View Leaderboard →</a>
                <a href="{{ route('vote.index') }}" class="text-blue-600 hover:underline">Vote on Peter Trull Solitary Detective →</a>
            </div>
        </div>
    </div>
</x-app-layout>
