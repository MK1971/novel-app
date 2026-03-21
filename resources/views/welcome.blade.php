@extends('layouts.app')

@section('content')
<div class="relative min-h-screen bg-[#fff9f0]">
    <!-- Hero Section -->
    <div class="container mx-auto px-4 pt-20 pb-32">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-7xl font-bold text-amber-900 mb-6 leading-tight">
                The Book With <br> <span class="text-amber-600">No Name</span>
            </h1>
            <p class="text-xl text-amber-800 mb-10 leading-relaxed">
                A collaborative storytelling experiment. Contribute your edits, vote on versions, and help shape the future of literature.
            </p>
            
            <div class="flex flex-col md:flex-row justify-center gap-4">
                <a href="{{ route('chapters.index') }}" class="px-8 py-4 bg-amber-600 text-white rounded-full font-bold text-lg hover:bg-amber-700 transition-colors shadow-lg">
                    Start Reading
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-amber-900 border-2 border-amber-600 rounded-full font-bold text-lg hover:bg-amber-50 transition-colors">
                        Join the Community
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white text-amber-900 border-2 border-amber-600 rounded-full font-bold text-lg hover:bg-amber-50 transition-colors">
                        Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Explore Section -->
    <div class="bg-white py-24">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-amber-900 mb-12 text-center">Explore the Project</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-8 bg-amber-50 rounded-2xl border border-amber-100">
                    <h3 class="text-xl font-bold text-amber-900 mb-4">Read & Edit</h3>
                    <p class="text-amber-800 mb-6">Dive into "The Book With No Name" and submit your own improvements to the story.</p>
                    <a href="{{ route('chapters.index') }}" class="text-amber-600 font-bold hover:text-amber-700">Browse Chapters →</a>
                </div>
                <div class="p-8 bg-amber-50 rounded-2xl border border-amber-100">
                    <h3 class="text-xl font-bold text-amber-900 mb-4">Vote on Peter Trull</h3>
                    <p class="text-amber-800 mb-6">Help decide which version of the solitary detective's story prevails.</p>
                    <a href="{{ route('vote.index') }}" class="text-amber-600 font-bold hover:text-amber-700">Cast Your Vote →</a>
                </div>
                <div class="p-8 bg-amber-50 rounded-2xl border border-amber-100">
                    <h3 class="text-xl font-bold text-amber-900 mb-4">Leaderboard</h3>
                    <p class="text-amber-800 mb-6">See who the top contributors are and track your own progress.</p>
                    <a href="{{ route('leaderboard') }}" class="text-amber-600 font-bold hover:text-amber-700">View Rankings →</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
