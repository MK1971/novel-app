<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    My Profile
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Your journey through the collaborative story.</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-8 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                Edit Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            {{-- Profile Header --}}
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-12 flex flex-col md:flex-row items-center gap-12">
                <div class="w-32 h-32 bg-amber-500 rounded-[2rem] flex items-center justify-center text-black text-5xl font-black shadow-xl shadow-amber-500/20 transform -rotate-3">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-4xl font-black text-amber-900 mb-2 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-xl text-amber-800/60 font-bold mb-6">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-6">
                        <div class="bg-amber-50 px-6 py-3 rounded-2xl border border-amber-100">
                            <span class="text-xs font-black text-amber-900/30 uppercase tracking-widest block mb-1">Total Points</span>
                            <span class="text-2xl font-black text-amber-900">{{ number_format($user->points ?? 0) }}</span>
                        </div>
                        <div class="bg-amber-50 px-6 py-3 rounded-2xl border border-amber-100">
                            <span class="text-xs font-black text-amber-900/30 uppercase tracking-widest block mb-1">Member Since</span>
                            <span class="text-2xl font-black text-amber-900">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reading Progress --}}
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] overflow-hidden">
                <div class="p-10 border-b border-amber-50">
                    <h3 class="text-2xl font-extrabold text-amber-900">Reading Progress</h3>
                </div>
                <div class="p-10">
                    @if($readingProgress->count() > 0)
                        <div class="grid gap-6">
                            @foreach($readingProgress as $progress)
                                <div class="flex flex-col md:flex-row md:items-center justify-between p-8 bg-amber-50/50 rounded-[2rem] border border-amber-100 hover:bg-amber-50 transition-colors group">
                                    <div class="mb-6 md:mb-0">
                                        <h4 class="text-xl font-extrabold text-amber-900 group-hover:text-amber-600 transition-colors">{{ $progress->chapter->title }}</h4>
                                        <p class="text-xs text-amber-800/40 font-black uppercase tracking-widest mt-1">Last read: {{ $progress->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <span class="px-4 py-1 bg-amber-100 text-amber-800 text-xs font-black rounded-full uppercase tracking-widest">
                                            In Progress
                                        </span>
                                        <a href="{{ route('chapters.show', $progress->chapter) }}" class="inline-flex items-center px-6 py-3 bg-amber-900 text-white text-sm font-black rounded-xl hover:bg-black transition-all shadow-lg shadow-amber-900/20">
                                            Continue Reading
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <p class="text-amber-900/40 font-bold mb-8">You haven't started reading any chapters yet.</p>
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-8 py-4 bg-amber-500 text-black font-black rounded-2xl hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                                Browse Chapters
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
