<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Voting Rounds Archive</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Past Voting Rounds</h1>
                    @if($rounds->count() > 0)
                        <div class="grid gap-4">
                            @foreach($rounds as $round)
                                <div class="p-4 border rounded-lg hover:bg-gray-50">
                                    <h3 class="text-lg font-bold">Round {{ $round->round_number }}</h3>
                                    <p class="text-gray-600">Historical voting data for this round.</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No voting rounds have been completed yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
