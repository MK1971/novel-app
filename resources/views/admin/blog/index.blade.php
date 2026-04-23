<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-3xl text-amber-900">Blog publishing</h2>
        <p class="text-amber-800/60 font-bold mt-1">Create, publish, feature, and edit blog posts shown on /blog.</p>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @php
                $clockUtc = now()->copy()->setTimezone('UTC');
                $clockEdt = now()->copy()->setTimezone('America/New_York');
                $clockCdt = now()->copy()->setTimezone('America/Chicago');
                $clockIsrael = now()->copy()->setTimezone('Asia/Jerusalem');
            @endphp

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-2xl border border-green-200 font-bold">{{ session('success') }}</div>
            @endif

            <div class="rounded-2xl border-2 border-amber-100 bg-amber-50 px-5 py-4">
                <p class="text-sm font-black text-amber-900">Blog clock reference</p>
                <p class="text-xs font-bold text-amber-900/70 mt-1">
                    UTC: {{ $clockUtc->format('M j, Y H:i T') }} |
                    EDT: {{ $clockEdt->format('M j, Y H:i T') }} |
                    CDT: {{ $clockCdt->format('M j, Y H:i T') }} |
                    Israel: {{ $clockIsrael->format('M j, Y H:i T') }}
                </p>
                <p class="text-xs font-bold text-amber-900/60 mt-1">Admin "Published at" input is Israel time. We store internally in UTC and show both below.</p>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('admin.blog.create') }}" class="px-6 py-3 bg-amber-900 text-white font-extrabold rounded-xl hover:bg-black transition-all">
                    New blog post
                </a>
            </div>

            <div class="bg-white border-2 border-amber-100 rounded-[2rem] p-6 shadow-sm overflow-x-auto">
                <table class="w-full min-w-[780px]">
                    <thead>
                        <tr class="text-left border-b border-amber-100">
                            <th class="py-3 pr-4 text-xs uppercase tracking-wider text-amber-800/70 font-black">Title</th>
                            <th class="py-3 pr-4 text-xs uppercase tracking-wider text-amber-800/70 font-black">Status</th>
                            <th class="py-3 pr-4 text-xs uppercase tracking-wider text-amber-800/70 font-black">Published</th>
                            <th class="py-3 pr-4 text-xs uppercase tracking-wider text-amber-800/70 font-black">Author</th>
                            <th class="py-3 text-xs uppercase tracking-wider text-amber-800/70 font-black">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr class="border-b border-amber-50">
                                <td class="py-4 pr-4">
                                    <p class="font-extrabold text-amber-950">{{ $post->title }}</p>
                                    <p class="text-xs font-bold text-amber-700/70 mt-1">/{{ $post->slug }}</p>
                                </td>
                                <td class="py-4 pr-4">
                                    @if($post->is_published)
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-green-100 text-green-800 text-xs font-extrabold">Published</span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-stone-100 text-stone-700 text-xs font-extrabold">Draft</span>
                                    @endif
                                    @if($post->is_featured)
                                        <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-extrabold ml-2">Featured</span>
                                    @endif
                                </td>
                                <td class="py-4 pr-4 text-sm font-bold text-amber-900/80">
                                    @if($post->published_at)
                                        <span class="block">Israel: {{ $post->published_at->copy()->setTimezone('Asia/Jerusalem')->format('M j, Y H:i T') }}</span>
                                        <span class="block text-xs text-amber-900/60">UTC: {{ $post->published_at->copy()->setTimezone('UTC')->format('M j, Y H:i T') }}</span>
                                    @else
                                        Not scheduled
                                    @endif
                                </td>
                                <td class="py-4 pr-4 text-sm font-bold text-amber-900/80">{{ $post->author }}</td>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.blog.edit', $post) }}" class="text-sm font-extrabold text-amber-800 hover:text-amber-950 underline">
                                            Edit
                                        </a>
                                        <a href="{{ route('admin.blog.preview', $post) }}" class="text-sm font-extrabold text-amber-700 hover:text-amber-900 underline">
                                            Preview
                                        </a>
                                        <form method="POST" action="{{ route('admin.blog.publish-now', $post) }}" onsubmit="return confirm('Publish this post now?');">
                                            @csrf
                                            <button type="submit" class="text-sm font-extrabold text-green-700 hover:text-green-900 underline">
                                                {{ $post->is_published ? 'Republish now' : 'Publish now' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-extrabold text-red-600 hover:text-red-800 underline">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-amber-900/70 font-bold">No blog posts yet. Create your first post.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
