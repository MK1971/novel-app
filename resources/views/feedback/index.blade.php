<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">Community Feedback</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-xl sm:rounded-2xl border border-amber-100">
                <div class="p-8 md:p-12">
                    <h1 class="text-4xl font-bold text-amber-900 mb-8 text-center">Community Feedback</h1>
                    
                    <div class="grid lg:grid-cols-2 gap-12">
                        {{-- Feedback Form --}}
                        <div>
                            @if(session('success'))
                                <div class="mb-8 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl font-bold text-center">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @include('feedback.form')
                        </div>
                        {{-- Feedback List --}}
                        <div>
                            <h2 class="text-2xl font-bold text-amber-900 mb-6">Recent Feedback</h2>
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @forelse($feedbacks ?? [] as $feedback)
                                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 shadow-sm">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                @if($feedback->user)
                                                    <p class="font-bold text-amber-900">{{ $feedback->user->name }}</p>
                                                @elseif($feedback->email)
                                                    <p class="font-bold text-amber-900">{{ $feedback->email }}</p>
                                                @else
                                                    <p class="font-bold text-amber-900">Anonymous</p>
                                                @endif
                                                <p class="text-xs text-amber-700/60">{{ $feedback->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <p class="text-amber-900/80 text-sm leading-relaxed">{{ Str::limit($feedback->content, 150) }}</p>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-amber-900/40">
                                        <p class="font-bold">No feedback yet. Be the first to share!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
