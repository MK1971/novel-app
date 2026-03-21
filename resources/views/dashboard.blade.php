@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-amber-900">Welcome, {{ Auth::user()->name }}</h1>
            @if(Auth::user()->is_admin)
                <div class="flex gap-4">
                    <a href="{{ route('admin.inline-edits.index') }}" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700">Admin Moderation</a>
                    <a href="{{ route('admin.users.index') }}" class="bg-amber-100 text-amber-900 px-4 py-2 rounded-lg hover:bg-amber-200">User Management</a>
                </div>
            @endif
        </div>

        <div class="grid md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-amber-100">
                <p class="text-amber-600 text-sm font-medium mb-1">Total Edits</p>
                <p class="text-3xl font-bold text-amber-900">{{ Auth::user()->edits()->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-amber-100">
                <p class="text-amber-600 text-sm font-medium mb-1">Accepted Edits</p>
                <p class="text-3xl font-bold text-green-600">{{ Auth::user()->edits()->whereIn('status', ['accepted_full', 'accepted_partial'])->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-amber-100">
                <p class="text-amber-600 text-sm font-medium mb-1">Rejected Edits</p>
                <p class="text-3xl font-bold text-red-600">{{ Auth::user()->edits()->where('status', 'rejected')->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-amber-100">
                <p class="text-amber-600 text-sm font-medium mb-1">Total Votes</p>
                <p class="text-3xl font-bold text-amber-900">{{ Auth::user()->votes()->count() }}</p>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100">
                <h2 class="text-xl font-bold text-amber-900 mb-6">Recent Activity</h2>
                <div class="space-y-4">
                    @forelse(Auth::user()->activityFeed()->latest()->take(5)->get() as $activity)
                        <div class="flex items-start gap-4 pb-4 border-b border-amber-50 last:border-0">
                            <div class="w-2 h-2 mt-2 rounded-full bg-amber-400"></div>
                            <div>
                                <p class="text-amber-900">{{ $activity->description }}</p>
                                <p class="text-sm text-amber-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-amber-600 italic">No recent activity found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Achievements -->
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-amber-100">
                <h2 class="text-xl font-bold text-amber-900 mb-6">Your Achievements</h2>
                <div class="grid grid-cols-3 gap-4">
                    @forelse(Auth::user()->achievements as $achievement)
                        <div class="text-center">
                            <div class="w-16 h-16 mx-auto mb-2 bg-amber-100 rounded-full flex items-center justify-center text-2xl">
                                {{ $achievement->icon ?? '🏆' }}
                            </div>
                            <p class="text-xs font-bold text-amber-900">{{ $achievement->name }}</p>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8">
                            <p class="text-amber-600 italic">Start contributing to earn badges!</p>
                            <a href="{{ route('chapters.index') }}" class="text-amber-600 font-bold hover:underline mt-2 inline-block">Browse Chapters</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
