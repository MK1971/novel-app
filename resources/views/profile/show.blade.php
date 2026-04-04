<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-amber-900 leading-tight">
            {{ __('User Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Header -->
            <div class="p-8 bg-white/80 backdrop-blur-sm shadow-xl sm:rounded-2xl border border-amber-100 flex flex-col md:flex-row items-center gap-8">
                <div class="w-32 h-32 bg-amber-100 rounded-full flex items-center justify-center text-amber-600 text-4xl font-bold border-4 border-amber-200 shadow-inner overflow-hidden shrink-0">
                    @if($user->avatar_path)
                        <img src="{{ asset('storage/'.$user->avatar_path) }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ substr($user->name, 0, 1) }}
                    @endif
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-amber-900 mb-2">{{ $user->name }}</h1>
                    <p class="text-amber-700 mb-4">{{ $user->email }}</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4">
                        <div class="bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                            <span class="text-sm text-amber-600 block">Total Points</span>
                            <span class="text-xl font-bold text-amber-900">{{ $user->points ?? 0 }}</span>
                        </div>
                        <div class="bg-amber-50 px-4 py-2 rounded-lg border border-amber-100">
                            <span class="text-sm text-amber-600 block">Member Since</span>
                            <span class="text-xl font-bold text-amber-900">{{ $user->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center px-6 py-3 bg-amber-600 border border-transparent rounded-full font-bold text-white hover:bg-amber-700 transition ease-in-out duration-150 shadow-md">
                        Edit Profile
                    </a>
                    <a href="{{ route('profile.payments') }}" class="inline-flex items-center justify-center px-6 py-3 bg-amber-50 border-2 border-amber-200 rounded-full font-bold text-amber-900 hover:bg-amber-100 transition text-sm">
                        Payments &amp; vote credits
                    </a>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 border-b border-amber-200 pb-4" role="tablist" aria-label="Profile sections">
                <a href="{{ route('profile.show') }}" class="px-5 py-2 rounded-full text-sm font-black border-2 transition-colors {{ !($submissionsTab ?? false) ? 'bg-amber-500 text-black border-amber-600' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-50' }}">
                    Reading progress
                </a>
                <a href="{{ route('profile.show', ['tab' => 'submissions']) }}" class="px-5 py-2 rounded-full text-sm font-black border-2 transition-colors {{ ($submissionsTab ?? false) ? 'bg-amber-500 text-black border-amber-600' : 'bg-white text-amber-900 border-amber-200 hover:bg-amber-50' }}">
                    My submissions
                </a>
            </div>

            @if($submissionsTab ?? false)
                <div class="p-8 bg-white/80 backdrop-blur-sm shadow-xl sm:rounded-2xl border border-amber-100 space-y-10">
                    <div>
                        <h3 class="text-xl font-black text-amber-900 mb-4">Whole-chapter &amp; phrase suggestions</h3>
                        @if($chapterSubmissions->isEmpty())
                            <p class="text-amber-700 font-bold italic">No chapter-level submissions yet.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach($chapterSubmissions as $edit)
                                    <li class="p-4 rounded-xl border border-amber-100 bg-amber-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <span class="font-black text-amber-950">{{ $edit->chapter?->readerHeadingLine() ?? 'Chapter removed' }}</span>
                                            <span class="block text-xs font-bold text-amber-800/70 mt-1">Updated {{ $edit->updated_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-white border border-amber-200 text-amber-900">{{ str_replace('_', ' ', $edit->status) }}</span>
                                            @if($edit->chapter)
                                                <a href="{{ route('chapters.show', $edit->chapter) }}" class="text-sm font-black text-amber-700 underline">Open chapter</a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-amber-900 mb-4">Paragraph suggestions</h3>
                        @if($paragraphSubmissions->isEmpty())
                            <p class="text-amber-700 font-bold italic">No paragraph submissions yet.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach($paragraphSubmissions as $ie)
                                    <li class="p-4 rounded-xl border border-amber-100 bg-amber-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <div>
                                            <span class="font-black text-amber-950">{{ $ie->chapter?->readerHeadingLine() ?? 'Chapter removed' }}</span>
                                            <span class="block text-xs font-bold text-amber-800/70 mt-1">Paragraph #{{ $ie->paragraph_number }} · {{ $ie->updated_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-black uppercase bg-white border border-amber-200 text-amber-900">{{ $ie->status }}</span>
                                            @if($ie->chapter)
                                                <a href="{{ route('chapters.show', $ie->chapter) }}" class="text-sm font-black text-amber-700 underline">Open chapter</a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @else
            <!-- Reading Progress -->
            <div class="p-8 bg-white/80 backdrop-blur-sm shadow-xl sm:rounded-2xl border border-amber-100">
                <h3 class="text-2xl font-bold text-amber-900 mb-6">Reading Progress</h3>
                @if($readingProgress->count() > 0)
                    <div class="grid gap-4">
                        @foreach($readingProgress as $progress)
                            <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-100">
                                <div>
                                    <h4 class="font-bold text-amber-900">{{ $progress->chapter->readerHeadingLine() }}</h4>
                                    <p class="text-sm text-amber-600">Last read: {{ $progress->last_read_at?->diffForHumans() ?? __('Not recorded yet') }}</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    @if($progress->completed)
                                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full uppercase tracking-wider">Completed</span>
                                    @elseif($progress->chapter && $progress->chapter->is_locked)
                                        <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full uppercase tracking-wider">Locked</span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full uppercase tracking-wider">In Progress</span>
                                    @endif
                                    <a href="{{ route('chapters.show', $progress->chapter) }}" class="text-amber-600 hover:text-amber-800 font-bold">{{ ($progress->chapter && $progress->chapter->is_locked) ? __('Read') : __('Continue') }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-amber-700 mb-6 italic">You haven't started reading any chapters yet.</p>
                        <a href="{{ route('chapters.index') }}" class="text-amber-600 hover:text-amber-800 font-bold underline">Browse Chapters</a>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
