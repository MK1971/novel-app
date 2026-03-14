@php
    $layout = auth()->check() ? 'app-layout' : 'guest-layout';
@endphp

<x-dynamic-component :component="$layout">
    @auth
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-amber-900 leading-tight">{{ $chapter->title }}</h2>
        </x-slot>
    @else
        <nav class="border-b border-amber-200/60 bg-white/80 backdrop-blur-sm sticky top-0 z-40">
            <div class="max-w-5xl mx-auto px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="text-xl font-extrabold text-amber-800 hover:text-amber-600 transition-colors">
                    What's My Book Name
                </a>
                <div class="flex items-center gap-6 md:gap-8">
                    @if($topLeader)
                        <div class="hidden sm:block text-amber-700 font-bold">
                            🏆 Leader: {{ $topLeader->name }} ({{ $topLeader->points }} pts)
                        </div>
                    @endif
                    <div class="flex gap-4">
                        <button type="button" x-data @click="$dispatch('open-modal', 'login-modal')" class="text-amber-900 font-semibold hover:text-amber-600 transition-colors">Sign In</button>
                        <button type="button" x-data @click="$dispatch('open-modal', 'register-modal')" class="px-4 py-2 bg-amber-500 text-black font-semibold rounded-full hover:bg-amber-600 transition-colors shrink-0">
                            Create Account
                        </button>
                    </div>
                </div>
            </div>
        </nav>
        <div class="max-w-5xl mx-auto px-6 pt-12">
            <h2 class="text-center text-4xl font-extrabold text-amber-900 mb-2">{{ $chapter->title }}</h2>
            <p class="text-center text-amber-800/70 text-xl font-medium mb-12">Read the story and shape the narrative.</p>
        </div>
    @endauth

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200 shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8 mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-amber-900">Current Chapter Content</h3>
                    <span class="px-4 py-1 bg-amber-100 text-amber-800 text-sm font-bold rounded-full">Chapter {{ $chapter->number }}</span>
                </div>
                <p class="whitespace-pre-wrap text-amber-900/80 text-lg leading-relaxed">{{ $chapter->content }}</p>
            </div>

            @auth
                <div class="bg-white border border-amber-100 shadow-sm rounded-3xl p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-amber-900">Suggest an Edit</h3>
                        <span class="px-4 py-1 bg-amber-500 text-black text-sm font-bold rounded-full">$2 Contribution</span>
                    </div>
                    <p class="text-amber-800/70 text-lg mb-8">Pay $2 to submit a writing or phrase edit. If accepted fully: 2 points. Partially: 1 point.</p>
                    
                    <form action="{{ route('payment.checkout') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                        
                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Edit Type</label>
                            <select name="type" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required>
                                <option value="writing" {{ old('type') === 'writing' ? 'selected' : '' }}>Writing Edit</option>
                                <option value="phrase" {{ old('type') === 'phrase' ? 'selected' : '' }}>Phrase Edit</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-amber-900 font-bold mb-2">Your Edited Text</label>
                            <textarea name="edited_text" rows="12" class="w-full bg-amber-50 border border-amber-200 rounded-2xl px-4 py-3 text-amber-900 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition-all" required placeholder="Enter your suggested edit...">{{ old('edited_text', $chapter->content) }}</textarea>
                            @error('edited_text')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full px-8 py-4 bg-amber-500 text-black text-lg font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/25">
                            Submit & Pay $2 via PayPal
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-amber-100/50 border border-amber-200 rounded-3xl p-12 text-center">
                    <h3 class="text-2xl font-bold text-amber-900 mb-4">Want to shape the story?</h3>
                    <p class="text-amber-800/70 text-lg mb-8">Sign in or create an account to suggest edits and earn points toward the leaderboard.</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <button type="button" x-data @click="$dispatch('open-modal', 'login-modal')" class="px-8 py-3 bg-amber-500 text-black font-bold rounded-full hover:bg-amber-600 transition-colors shadow-lg shadow-amber-500/20">Sign In</button>
                        <button type="button" x-data @click="$dispatch('open-modal', 'register-modal')" class="px-8 py-3 bg-white border-2 border-amber-200 text-amber-800 font-bold rounded-full hover:bg-amber-50 transition-colors">Create Account</button>
                    </div>
                </div>
            @endauth
        </div>
    </div>

</x-dynamic-component>
