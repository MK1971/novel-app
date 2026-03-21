<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Welcome back, {{ Auth::user()->name }}!
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Your personal writing dashboard</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('chapters.index', ['resume' => 1]) }}" class="px-8 py-4 bg-amber-900 text-white text-sm font-black rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5 uppercase tracking-widest">
                    Continue Reading
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div class="bg-white rounded-[2.5rem] p-8 border border-amber-100 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-1">Total Edits</p>
                    <p class="text-4xl font-black text-amber-900">{{ number_format(Auth::user()->edits()->count() + Auth::user()->inlineEdits()->count()) }}</p>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-amber-100 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-1">Accepted</p>
                    <p class="text-4xl font-black text-green-600">
                        {{ number_format(
                            Auth::user()->edits()->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count() + 
                            Auth::user()->inlineEdits()->where('status', 'approved')->count()
                        ) }}
                    </p>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-amber-100 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-1">Rejected</p>
                    <p class="text-4xl font-black text-red-600">
                        {{ number_format(
                            Auth::user()->edits()->where('status', 'rejected')->count() + 
                            Auth::user()->inlineEdits()->where('status', 'rejected')->count()
                        ) }}
                    </p>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-amber-100 shadow-sm hover:shadow-md transition-all group">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <p class="text-xs font-black uppercase tracking-widest text-amber-900/30 mb-1">Total Points</p>
                    <p class="text-4xl font-black text-amber-900">{{ number_format(Auth::user()->points) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Recent Activity --}}
                <div class="lg:col-span-2 bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                    <div class="p-10 border-b border-amber-50 flex items-center justify-between">
                        <h3 class="text-2xl font-extrabold text-amber-900">Recent Activity</h3>
                        <a href="{{ route('activity-feed.index') }}" class="text-sm font-black text-amber-500 hover:text-amber-600 uppercase tracking-widest">View All</a>
                    </div>
                    <div class="p-10 space-y-8">
                        @forelse(Auth::user()->activityFeed()->latest()->take(5)->get() as $activity)
                            <div class="flex items-start gap-6 group">
                                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-amber-900 font-bold leading-tight mb-1">{{ $activity->description }}</p>
                                    <p class="text-xs text-amber-800/40 font-black uppercase tracking-widest">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-amber-900/40 font-bold">No activity yet. Start by reading a chapter!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Achievements --}}
                <div class="bg-amber-900 rounded-[3rem] p-10 text-amber-50 shadow-xl shadow-amber-900/20">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-2xl font-extrabold">Badges</h3>
                        <a href="{{ route('achievements.index') }}" class="text-xs font-black text-amber-400 hover:text-amber-300 uppercase tracking-widest">View All</a>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        @forelse(Auth::user()->achievements()->take(4)->get() as $achievement)
                            <div class="bg-white/5 rounded-3xl p-6 text-center hover:bg-white/10 transition-all group">
                                <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">{{ $achievement->icon ?? '🏆' }}</div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-amber-400">{{ $achievement->name }}</p>
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-12">
                                <p class="text-amber-50/40 font-bold mb-6">No badges earned yet.</p>
                                <a href="{{ route('chapters.index') }}" class="inline-block px-6 py-3 bg-amber-500 text-black text-xs font-black rounded-xl hover:bg-amber-400 transition-all uppercase tracking-widest">
                                    Start Reading
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
