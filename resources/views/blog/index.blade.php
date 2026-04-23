<x-guest-layout
    page-title="The Narrative — WhatsMyBookName Blog"
    meta-description="Updates from behind the manuscript: launch notes, contributor spotlights, and practical editing craft from WhatsMyBookName."
>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">
            <section class="rounded-3xl border border-stone-700 bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 px-8 py-14 shadow-xl">
                <p class="text-sm font-black uppercase tracking-[0.26em] text-amber-300 mb-4">The Narrative</p>
                <h1 class="text-4xl md:text-5xl font-black text-stone-100 tracking-tight">Behind the manuscript as it evolves</h1>
                <p class="mt-4 max-w-3xl text-base md:text-lg font-bold text-stone-300">
                    Stories from behind the book that writes itself: launch notes, contributor highlights, and practical editing craft.
                </p>
            </section>

            @if($featuredPost)
                <section class="rounded-3xl border border-amber-100 bg-gradient-to-br from-stone-50 to-amber-50/60 p-6 md:p-8 shadow-lg">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-amber-700 mb-3">Latest Update</p>
                    <div class="grid gap-6 lg:grid-cols-2 lg:items-center">
                        @if(!empty($featuredPost['cover_image_path']))
                            <div class="h-44 rounded-2xl overflow-hidden border-b border-stone-200">
                                <img
                                    src="{{ asset('storage/'.$featuredPost['cover_image_path']) }}"
                                    alt="{{ $featuredPost['title'] }} cover"
                                    class="h-full w-full object-cover"
                                >
                            </div>
                        @else
                            <div class="h-44 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 border-b border-stone-200 flex items-center justify-center">
                                <span class="text-5xl md:text-[3.25rem] leading-tight">{{ $featuredPost['cover_emoji'] ?? '📘' }}</span>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-black uppercase tracking-wider text-amber-700/80">
                                @if(!empty($featuredPost['category']))
                                    {{ $featuredPost['category'] }} ·
                                @endif
                                {{ $featuredPost['published_at']->format('M j, Y') }}
                            </p>
                            <h2 class="mt-2 text-3xl md:text-4xl font-black text-stone-900 leading-tight">{{ $featuredPost['title'] }}</h2>
                            <p class="mt-4 text-stone-700 font-bold leading-relaxed">{{ $featuredPost['excerpt'] }}</p>
                            <a href="{{ route('blog.show', ['slug' => $featuredPost['slug']]) }}" class="mt-6 inline-flex items-center px-6 py-3 rounded-xl bg-amber-500 text-stone-900 font-black hover:bg-amber-600 transition-colors">
                                Read the full story
                            </a>
                        </div>
                    </div>
                </section>
            @endif

            <section>
                <div class="mb-6">
                    <h2 class="text-3xl font-black text-stone-900">Behind the Scenes & Story Evolution</h2>
                    <p class="mt-1 text-stone-600 font-semibold">Explore how the manuscript evolves and how contributors shape each chapter.</p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @forelse($latestPosts as $post)
                        <article class="rounded-2xl border border-stone-200 bg-white overflow-hidden shadow-sm hover:shadow-lg transition-shadow">
                            @if(!empty($post['cover_image_path']))
                                <div class="h-44 border-b border-stone-200 overflow-hidden">
                                    <img
                                        src="{{ asset('storage/'.$post['cover_image_path']) }}"
                                        alt="{{ $post['title'] }} cover"
                                        class="h-full w-full object-cover"
                                    >
                                </div>
                            @else
                                <div class="h-44 bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center border-b border-stone-200">
                                    <span class="text-5xl md:text-[3.25rem] leading-tight">{{ $post['cover_emoji'] ?? '✨' }}</span>
                                </div>
                            @endif
                            <div class="p-5">
                                <p class="text-xs font-black uppercase tracking-wider text-amber-700/80">
                                    @if(!empty($post['category']))
                                        {{ $post['category'] }} ·
                                    @endif
                                    {{ $post['published_at']->format('M j, Y') }}
                                </p>
                                <h3 class="mt-2 text-xl font-black text-stone-900 leading-snug">{{ $post['title'] }}</h3>
                                <p class="mt-3 text-sm font-semibold text-stone-600 leading-relaxed">{{ $post['excerpt'] }}</p>
                                <div class="mt-5 flex items-center justify-between border-t border-stone-200 pt-4">
                                    <span class="text-xs font-bold text-stone-500">By {{ $post['author'] }}</span>
                                    <a href="{{ route('blog.show', ['slug' => $post['slug']]) }}" class="text-sm font-black text-amber-700 hover:text-amber-900">
                                        Read →
                                    </a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-stone-200 bg-white p-8 text-center">
                            <p class="text-stone-700 font-bold">No blog posts yet. Check back soon.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-3xl border border-stone-200 bg-white p-6 md:p-8">
                <h2 class="text-2xl font-black text-stone-900 mb-5">Latest From The Narrative</h2>
                <div class="space-y-4">
                    @foreach($quickUpdates as $update)
                        <article class="rounded-xl border-l-4 border-amber-500 bg-stone-50 px-5 py-4">
                            <p class="text-xs font-black uppercase tracking-wider text-amber-700">{{ $update['meta'] ?? 'Update' }}</p>
                            <h3 class="mt-1 text-lg font-black text-stone-900">{{ $update['title'] }}</h3>
                            <p class="mt-1 text-sm font-semibold text-stone-600">{{ $update['summary'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-stone-700 bg-stone-900 px-6 py-8 text-stone-100">
                <h2 class="text-2xl font-black mb-5">Live manuscript snapshot</h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                        <p class="text-3xl font-black text-amber-300">{{ number_format($stats['contributors']) }}</p>
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-300">Contributors</p>
                    </div>
                    <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                        <p class="text-3xl font-black text-amber-300">{{ number_format($stats['accepted_edits']) }}</p>
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-300">Accepted edits</p>
                    </div>
                    <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                        <p class="text-3xl font-black text-amber-300">{{ number_format($stats['live_chapters']) }}</p>
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-300">Live chapters</p>
                    </div>
                    <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                        <p class="text-3xl font-black text-amber-300">{{ number_format($stats['posts_published']) }}</p>
                        <p class="text-xs font-bold uppercase tracking-wider text-stone-300">Published posts</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>
