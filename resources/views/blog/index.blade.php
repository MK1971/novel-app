<x-guest-layout
    page-title="The Narrative — WhatsMyBookName Blog"
    meta-description="Updates from behind the manuscript: launch notes, contributor spotlights, and practical editing craft from WhatsMyBookName."
>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <section class="space-y-10">
                    <section class="rounded-2xl bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 p-4 md:p-6">
                        <div class="rounded-xl bg-[#FFFBF7] border border-[#E8E0D6] shadow-[0_2px_12px_rgba(0,0,0,0.08)] px-6 py-8 md:px-8 md:py-10">
                            <p class="text-xs font-black uppercase tracking-[0.26em] text-[#C4A965] mb-4">The Narrative</p>
                            <h1 class="text-4xl md:text-[3.2rem] leading-[1.06] font-black text-[#2C2C2C] tracking-tight">Behind the manuscript as it evolves</h1>
                            <p class="mt-4 max-w-3xl text-base md:text-lg font-semibold text-[#554433]">
                                Stories from behind the book that writes itself: launch notes, contributor highlights, and practical editing craft.
                            </p>
                        </div>
                    </section>

                    @if($featuredPost)
                        <article class="rounded-xl border border-[#E8E8E8] bg-[#FFFBF7] p-6 md:p-8 shadow-[0_2px_12px_rgba(0,0,0,0.08)]">
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-[#B8860B] mb-4">Latest update</p>
                            <div class="grid gap-6 lg:grid-cols-[280px_minmax(0,1fr)]">
                                @if(!empty($featuredPost['cover_image_url']))
                                    <div class="relative h-full min-h-[220px] lg:min-h-[240px] rounded-xl overflow-hidden bg-[#1A1A1A] flex items-center justify-center">
                                        <img
                                            src="{{ $featuredPost['cover_image_url'] }}"
                                            alt="{{ $featuredPost['title'] }} cover"
                                            class="h-full w-full object-contain"
                                            onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                        >
                                        <div class="hidden absolute inset-0 bg-gradient-to-br from-[#C4A965] to-[#A67D4C] flex items-center justify-center">
                                            <span class="text-[120px] leading-none">{{ $featuredPost['cover_emoji'] ?? '📘' }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="h-full min-h-[260px] rounded-xl bg-gradient-to-br from-[#C4A965] to-[#A67D4C] flex items-center justify-center">
                                        <span class="text-[120px] leading-none">{{ $featuredPost['cover_emoji'] ?? '📘' }}</span>
                                    </div>
                                @endif
                                <div class="flex min-h-[260px] flex-col">
                                    <p class="text-[11px] font-black uppercase tracking-wider text-[#8C8C8C] lg:text-right">
                                        @if(!empty($featuredPost['category']))
                                            {{ $featuredPost['category'] }} ·
                                        @endif
                                        {{ $featuredPost['published_at']->format('M j, Y') }}
                                    </p>
                                    <h2 class="mt-2 text-4xl md:text-[2.8rem] font-black text-[#2C2C2C] leading-tight">{{ $featuredPost['title'] }}</h2>
                                    <p class="mt-4 text-[#554433] font-semibold leading-relaxed">{{ $featuredPost['excerpt'] }}</p>
                                    <a href="{{ route('blog.show', ['slug' => $featuredPost['slug']]) }}" class="mt-auto inline-flex w-fit items-center rounded-lg bg-[#C4A965] px-7 py-3 text-[#1A1A1A] font-bold hover:bg-[#A67D4C] transition-colors">
                                        Read the full story
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endif

                    <section>
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-[#B8860B] mb-3">Behind the Scenes & Story Evolution</p>
                        <div class="grid gap-6 md:grid-cols-2">
                            @forelse($latestPosts as $post)
                                <article class="rounded-xl border border-[#E8E8E8] bg-white overflow-hidden shadow-[0_2px_8px_rgba(0,0,0,0.05)] transition-all hover:-translate-y-0.5">
                                    @if(!empty($post['cover_image_url']))
                                        <div class="relative aspect-video overflow-hidden border-b border-[#E8E8E8] bg-[#1A1A1A] flex items-center justify-center">
                                            <img
                                                src="{{ $post['cover_image_url'] }}"
                                                alt="{{ $post['title'] }} cover"
                                                class="h-full w-full object-contain p-2"
                                                onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');"
                                            >
                                            <div class="hidden absolute inset-0 bg-gradient-to-br from-[#C4A965] to-[#A67D4C] flex items-center justify-center">
                                                <span class="text-7xl leading-none">{{ $post['cover_emoji'] ?? '✨' }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="aspect-[16/9] bg-gradient-to-br from-[#C4A965] to-[#A67D4C] flex items-center justify-center border-b border-[#E8E8E8]">
                                            <span class="text-7xl leading-none">{{ $post['cover_emoji'] ?? '✨' }}</span>
                                        </div>
                                    @endif
                                    <div class="p-8">
                                        <p class="text-[11px] font-black uppercase tracking-wider text-[#8C8C8C] flex items-center gap-2">
                                            <span>{{ $post['category_icon'] ?? '📘' }}</span>
                                            @if(!empty($post['category']))
                                                <span class="inline-flex items-center rounded-full border border-[#E7D0B2] bg-[#FFFBF7] px-2 py-0.5 text-[#B8860B]">{{ $post['category'] }}</span>
                                            @endif
                                            <span>{{ $post['published_at']->format('M j, Y') }}</span>
                                        </p>
                                        <h3 class="mt-3 text-[1.75rem] font-black text-[#2C2C2C] leading-snug">{{ $post['title'] }}</h3>
                                        <p class="mt-3 text-sm font-semibold text-[#554433] leading-relaxed" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                            {{ $post['excerpt'] }}
                                        </p>
                                        <div class="mt-5 flex items-center justify-between border-t border-[#E8E8E8] pt-4">
                                            <span class="text-xs font-bold text-[#8C8C8C]">By {{ $post['author'] }}</span>
                                            <a href="{{ route('blog.show', ['slug' => $post['slug']]) }}" class="text-sm font-black text-[#B8860B] hover:text-[#7A4C1A]">
                                                Read →
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="md:col-span-2 rounded-2xl border border-[#E8E8E8] bg-white p-8 text-center">
                                    <p class="text-[#554433] font-bold">No blog posts yet. Check back soon.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-xl border border-[#E8E8E8] bg-[#FFFBF7] px-5 py-4">
                        <p class="text-[11px] font-black uppercase tracking-[0.18em] text-[#B8860B]">More</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-[#6A5A47]">
                            <a href="{{ route('about') }}" class="rounded-md border border-[#E8E0D6] bg-white px-2.5 py-1 hover:text-[#7A4C1A] hover:border-[#D8C8B2] transition-colors">How it works</a>
                            <a href="{{ route('prizes') }}" class="rounded-md border border-[#E8E0D6] bg-white px-2.5 py-1 hover:text-[#7A4C1A] hover:border-[#D8C8B2] transition-colors">Prizes</a>
                            <a href="{{ route('feedback.index') }}" class="rounded-md border border-[#E8E0D6] bg-white px-2.5 py-1 hover:text-[#7A4C1A] hover:border-[#D8C8B2] transition-colors">Feedback</a>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <a href="{{ route('chapters.index') }}" class="inline-flex items-center rounded-md bg-[#C4A965] px-3 py-1.5 text-xs font-semibold text-[#1A1A1A] hover:bg-[#A67D4C] transition-colors">
                                Start reading
                            </a>
                            @if(Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-[#C4A965] px-3 py-1.5 text-xs font-semibold text-[#1A1A1A] hover:bg-[#A67D4C] transition-colors">
                                    Create contributor
                                </a>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-xl border border-[#E8E8E8] bg-[#FFFBF7] p-6 md:p-8">
                        <h2 class="text-2xl font-black text-[#2C2C2C] mb-5">Latest From The Narrative</h2>
                        <div class="space-y-4">
                            @foreach($quickUpdates as $update)
                                <article class="rounded-xl border-l-[3px] border-[#C4A965] bg-[#FFFBF7] px-5 py-4 hover:bg-[#FAF7F2] transition-colors">
                                    <p class="text-xs font-black uppercase tracking-wider text-[#B8860B]">{{ $update['meta'] ?? 'Update' }}</p>
                                    <h3 class="mt-1 text-lg font-black text-[#2C2C2C]">{{ $update['title'] }}</h3>
                                    <p class="mt-1 text-sm font-semibold text-[#554433]">{{ $update['summary'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </section>

                    <section class="rounded-xl border border-stone-700 bg-stone-900 px-6 py-8 text-stone-100">
                        <h2 class="text-2xl font-black mb-5 text-white">Live manuscript snapshot</h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                                <p class="text-3xl font-black text-[#C4A965]">{{ number_format($stats['contributors']) }}</p>
                                <p class="text-xs font-bold uppercase tracking-wider text-stone-200">Contributors</p>
                            </div>
                            <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                                <p class="text-3xl font-black text-[#C4A965]">{{ number_format($stats['accepted_edits']) }}</p>
                                <p class="text-xs font-bold uppercase tracking-wider text-stone-200">Accepted edits</p>
                            </div>
                            <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                                <p class="text-3xl font-black text-[#C4A965]">{{ number_format($stats['live_chapters']) }}</p>
                                <p class="text-xs font-bold uppercase tracking-wider text-stone-200">Live chapters</p>
                            </div>
                            <div class="rounded-2xl border border-stone-700 bg-stone-800 px-4 py-5">
                                <p class="text-3xl font-black text-[#C4A965]">{{ number_format($stats['posts_published']) }}</p>
                                <p class="text-xs font-bold uppercase tracking-wider text-stone-200">Published posts</p>
                            </div>
                        </div>
                    </section>
            </section>
        </div>
    </div>
</x-guest-layout>
