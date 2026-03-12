<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peter Trull Solitary Detective - Vote</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
            @endif

            @if (!($canVote ?? true))
                <div class="mb-6 p-4 bg-amber-100 text-amber-800 rounded-lg">
                    <p class="font-medium">You need to suggest at least one edit in <strong>The Book With No Name</strong> before you can vote on the finished chapters of Peter Trull Solitary Detective.</p>
                    <a href="{{ route('chapters.index') }}" class="inline-block mt-2 text-amber-700 underline font-medium">Go to Chapters →</a>
                </div>
            @endif

            <p class="mb-6 text-gray-600">Vote on which version of each chapter works best. Your selection is recorded to your account.</p>

            @foreach($chapters ?? [] as $chapterNum => $versions)
                @php
                    $versionA = $versions->firstWhere('version', 'A');
                    $versionB = $versions->firstWhere('version', 'B');
                @endphp
                @if($versionA && $versionB)
                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <h3 class="font-bold mb-4">Chapter {{ $chapterNum }}</h3>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-medium mb-2">Version A</h4>
                                <p class="text-sm text-gray-700 mb-4">{{ Str::limit($versionA->content, 300) }}</p>
                                @if($canVote ?? false)
                                <form action="{{ route('vote.store', $versionA) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="version_chosen" value="A">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Vote A</button>
                                </form>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-medium mb-2">Version B</h4>
                                <p class="text-sm text-gray-700 mb-4">{{ Str::limit($versionB->content, 300) }}</p>
                                @if($canVote ?? false)
                                <form action="{{ route('vote.store', $versionB) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="version_chosen" value="B">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">Vote B</button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(!isset($chapters) || $chapters->isEmpty())
                <p class="text-gray-600">No chapter pairs to vote on yet. Add A and B versions of chapters to enable voting.</p>
            @endif
        </div>
    </div>
</x-app-layout>
