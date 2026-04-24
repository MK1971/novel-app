<x-guest-layout
    :page-title="$post['title'].' — The Narrative'"
    :meta-description="$post['excerpt']"
>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center text-sm font-black text-amber-700 hover:text-amber-900 underline decoration-amber-300">
                ← Back to blog
            </a>

            <article class="rounded-3xl border border-amber-100 bg-white/85 backdrop-blur-sm p-8 md:p-10 shadow-lg">
                <p class="text-xs font-black uppercase tracking-[0.22em] text-amber-700/85 flex items-center gap-2">
                    <span>{{ $post['category_icon'] ?? '📘' }}</span>
                    @if(!empty($post['category']))
                        <span class="inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5">{{ $post['category'] }}</span>
                    @endif
                    {{ $post['published_at']->format('F j, Y') }} · By {{ $post['author'] }}
                </p>
                <h1 class="mt-3 text-4xl md:text-5xl font-black text-amber-950 leading-tight">{{ $post['title'] }}</h1>
                <p class="mt-5 text-lg font-bold text-amber-900/80 leading-relaxed">{{ $post['excerpt'] }}</p>

                @if(!empty($post['cover_image_url']))
                    <div class="mt-6 rounded-2xl overflow-hidden border border-amber-100 aspect-[16/9] bg-[#F3EEE7]">
                        <img
                            src="{{ $post['cover_image_url'] }}"
                            alt="{{ $post['title'] }} cover"
                            class="h-full w-full object-contain p-1"
                        >
                    </div>
                @endif

                <div class="mt-8 prose prose-amber max-w-none">
                    @foreach($post['content'] as $paragraph)
                        <p class="text-amber-900/85 font-bold leading-relaxed">{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="mt-8 rounded-2xl border border-amber-200 bg-amber-50/80 px-5 py-4">
                    <p class="text-xs font-black uppercase tracking-wider text-amber-700/80 mb-2">What to do next</p>
                    <p class="text-sm font-bold text-amber-900/80 mb-3">Read live chapters, submit your first edit, and start earning leaderboard points.</p>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-amber-500 text-black font-black hover:bg-amber-600 transition-colors">
                            Start reading chapter 1
                        </a>
                        <a href="{{ route('chapters.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-amber-300 bg-white text-amber-900 font-black hover:bg-amber-50 transition-colors">
                            Submit your first edit
                        </a>
                    </div>
                </div>
            </article>

            @if($relatedPosts->isNotEmpty())
                <section class="rounded-3xl border border-amber-100 bg-white/85 p-6 md:p-8">
                    <h2 class="text-2xl font-black text-amber-950 mb-5">More from The Narrative</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach($relatedPosts as $related)
                            <article class="rounded-2xl border border-amber-100 bg-amber-50/60 p-4">
                                <p class="text-xs font-black uppercase tracking-wider text-amber-700/80">{{ $related['category'] }}</p>
                                <h3 class="mt-1 text-lg font-black text-amber-950 leading-snug">{{ $related['title'] }}</h3>
                                <p class="mt-2 text-xs font-bold text-amber-900/70">{{ $related['published_at']->format('M j, Y') }}</p>
                                <a href="{{ route('blog.show', ['slug' => $related['slug']]) }}" class="mt-3 inline-flex text-sm font-black text-amber-700 hover:text-amber-900 underline decoration-amber-300">
                                    Read post
                                </a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-guest-layout>
