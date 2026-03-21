<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-3xl text-amber-900 leading-tight">
                    Chapter Management
                </h2>
                <p class="text-amber-800/60 font-bold mt-1">Upload and manage chapters for both books.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200 shadow-sm font-bold">{{ session('success') }}</div>
            @endif

            {{-- The Book With No Name --}}
            <div class="bg-white border border-amber-100 shadow-sm rounded-[3rem] p-10">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-2xl font-extrabold text-amber-900">The Book With No Name</h3>
                        <p class="text-amber-800/60 font-bold mt-1">Add a single chapter for community editing.</p>
                    </div>
                    <span class="px-4 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-full uppercase tracking-widest">Main Story</span>
                </div>

                <form action="{{ route('admin.chapters.store-story') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-3">Chapter Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" placeholder="e.g., The Beginning of the End" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-3">Chapter Number</label>
                            <input type="number" name="number" value="{{ old('number', ($storyChapters->max('number') ?? 0) + 1) }}" min="1" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-2xl px-6 py-4 text-amber-900 font-bold focus:border-amber-500 focus:ring-0 transition-all" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-amber-900/30 uppercase tracking-widest mb-3">Chapter Content</label>
                        <textarea name="content" rows="10" class="w-full bg-amber-50/50 border-2 border-amber-100 rounded-[2rem] px-6 py-6 text-amber-900 font-medium focus:border-amber-500 focus:ring-0 transition-all" placeholder="Write the chapter content here..." required>{{ old('content') }}</textarea>
                    </div>
                    <button type="submit" class="px-10 py-4 bg-amber-900 text-white font-extrabold rounded-2xl hover:bg-black transition-all shadow-xl shadow-amber-900/20 transform hover:-translate-y-0.5">
                        Publish Chapter
                    </button>
                </form>

                @if($storyChapters->isNotEmpty())
                    <div class="mt-12 pt-10 border-t border-amber-50">
                        <h4 class="text-xs font-black text-amber-900/30 uppercase tracking-widest mb-6">Published Chapters</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($storyChapters as $ch)
                                <div class="bg-amber-50/50 rounded-xl p-4 border border-amber-100 text-center group hover:bg-amber-100 transition-colors">
                                    <span class="block text-xs font-black text-amber-900/40 mb-1">CH {{ $ch->number }}</span>
                                    <span class="text-sm font-bold text-amber-900 truncate block">{{ $ch->title }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Peter Trull --}}
            <div class="bg-amber-900 rounded-[3rem] p-10 text-amber-50 shadow-xl shadow-amber-900/20">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-2xl font-extrabold">Peter Trull: Solitary Detective</h3>
                        <p class="text-amber-50/40 font-bold mt-1">Add a chapter pair for community voting.</p>
                    </div>
                    <span class="px-4 py-1 bg-amber-500 text-black text-xs font-black rounded-full uppercase tracking-widest">Voting Book</span>
                </div>

                <form action="{{ route('admin.chapters.store-peter-trull') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-amber-50/30 uppercase tracking-widest mb-3">Chapter Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="w-full bg-white/5 border-2 border-white/10 rounded-2xl px-6 py-4 text-white font-bold focus:border-amber-500 focus:ring-0 transition-all" placeholder="e.g., A Cold Night in London" required>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-amber-50/30 uppercase tracking-widest mb-3">Chapter Number</label>
                            @php $maxNum = $peterChapters->isEmpty() ? 1 : (int) $peterChapters->groupBy('number')->keys()->max() + 1; @endphp
                            <input type="number" name="number" value="{{ old('number', $maxNum) }}" min="1" class="w-full bg-white/5 border-2 border-white/10 rounded-2xl px-6 py-4 text-white font-bold focus:border-amber-500 focus:ring-0 transition-all" required>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-xs font-black text-amber-50/30 uppercase tracking-widest mb-3">Version A Content</label>
                            <textarea name="content_a" rows="8" class="w-full bg-white/5 border-2 border-white/10 rounded-[2rem] px-6 py-6 text-white font-medium focus:border-amber-500 focus:ring-0 transition-all" required>{{ old('content_a') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-amber-50/30 uppercase tracking-widest mb-3">Version B Content</label>
                            <textarea name="content_b" rows="8" class="w-full bg-white/5 border-2 border-white/10 rounded-[2rem] px-6 py-6 text-white font-medium focus:border-amber-500 focus:ring-0 transition-all" required>{{ old('content_b') }}</textarea>
                        </div>
                    </div>
                    <button type="submit" class="px-10 py-4 bg-amber-500 text-black font-extrabold rounded-2xl hover:bg-amber-400 transition-all shadow-xl shadow-amber-500/20 transform hover:-translate-y-0.5">
                        Publish Chapter Pair
                    </button>
                </form>

                @if($peterChapters->isNotEmpty())
                    <div class="mt-12 pt-10 border-t border-white/10">
                        <h4 class="text-xs font-black text-amber-50/30 uppercase tracking-widest mb-6">Published Pairs</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($peterChapters->groupBy('number') as $num => $vers)
                                <div class="bg-white/5 rounded-xl p-4 border border-white/10 text-center group hover:bg-white/10 transition-colors">
                                    <span class="block text-xs font-black text-amber-50/40 mb-1">CH {{ $num }}</span>
                                    <span class="text-sm font-bold text-white truncate block">A & B Versions</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
