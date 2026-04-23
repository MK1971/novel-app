@php
    $paragraphText = old(
        'content_text',
        isset($post) ? implode("\n\n", (array) ($post->content ?? [])) : ''
    );
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if(($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="title" class="block text-amber-900 font-extrabold mb-2">Title</label>
            <input id="title" name="title" type="text" required value="{{ old('title', $post->title ?? '') }}"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
            @error('title') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="slug" class="block text-amber-900 font-extrabold mb-2">Slug (optional)</label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug ?? '') }}"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold"
                placeholder="auto-generated-from-title">
            @error('slug') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="cover_emoji" class="block text-amber-900 font-extrabold mb-2">Cover emoji</label>
            <input id="cover_emoji" name="cover_emoji" type="text" value="{{ old('cover_emoji', $post->cover_emoji ?? '') }}"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold"
                placeholder="📝">
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach(['📝', '📖', '🏆', '🕵️', '🔎', '🎯', '🎬', '📊', '💡', '🔮', '✨'] as $emojiChoice)
                    <button
                        type="button"
                        onclick="document.getElementById('cover_emoji').value='{{ $emojiChoice }}'"
                        class="px-3 py-2 rounded-lg border border-amber-200 bg-white text-xl hover:bg-amber-50"
                        aria-label="Choose {{ $emojiChoice }}"
                    >{{ $emojiChoice }}</button>
                @endforeach
            </div>
            @error('cover_emoji') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="category" class="block text-amber-900 font-extrabold mb-2">Category</label>
            <select id="category" name="category"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
                <option value="">(No category)</option>
                @foreach(['Launch', 'Community', 'The Story', 'Craft', 'Update', 'Editorial', 'Data', 'Vision'] as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected(old('category', $post->category ?? '') === $categoryOption)>
                        {{ $categoryOption }}
                    </option>
                @endforeach
            </select>
            @error('category') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="author" class="block text-amber-900 font-extrabold mb-2">Author</label>
            <input id="author" name="author" type="text" required value="{{ old('author', $post->author ?? 'Editorial Team') }}"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
            @error('author') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="published_at" class="block text-amber-900 font-extrabold mb-2">Published at</label>
            <input id="published_at" name="published_at" type="datetime-local"
                value="{{ old('published_at', isset($post->published_at) && $post->published_at ? $post->published_at->copy()->setTimezone('Asia/Jerusalem')->format('Y-m-d\TH:i') : '') }}"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
            <div class="mt-2 flex items-center gap-3">
                <p class="text-xs font-bold text-amber-800/60">Optional. This input uses Israel time (Asia/Jerusalem). Leave empty + check Published to publish immediately when you save.</p>
                <button type="button" onclick="document.getElementById('published_at').value=''" class="text-xs font-extrabold text-amber-900 underline hover:text-black">
                    Clear
                </button>
            </div>
            @error('published_at') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-6 items-center">
            <label class="inline-flex items-center gap-2 text-amber-900 font-bold">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $post->is_published ?? false))
                    class="rounded border-amber-300 text-amber-700 focus:ring-amber-500">
                Published
            </label>
            <label class="inline-flex items-center gap-2 text-amber-900 font-bold">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $post->is_featured ?? false))
                    class="rounded border-amber-300 text-amber-700 focus:ring-amber-500">
                Featured
            </label>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="cover_image" class="block text-amber-900 font-extrabold mb-2">Cover image (optional)</label>
            <input id="cover_image" name="cover_image" type="file" accept="image/*"
                class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">
            <p class="text-xs font-bold text-amber-800/60 mt-2">
                Upload JPG/PNG/WebP/GIF (app limit: 10MB). Current server limits: upload {{ strtoupper((string) ini_get('upload_max_filesize')) }}, post {{ strtoupper((string) ini_get('post_max_size')) }}.
                If set, image is used instead of emoji.
            </p>
            @error('cover_image') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            @if(!empty($post?->cover_image_path))
                <p class="block text-amber-900 font-extrabold mb-2">Current cover image</p>
                <img src="{{ asset('storage/'.$post->cover_image_path) }}" alt="Current blog cover image" class="h-28 w-full object-cover rounded-xl border border-amber-200">
                <label class="mt-3 inline-flex items-center gap-2 text-amber-900 font-bold">
                    <input type="checkbox" name="remove_cover_image" value="1"
                        class="rounded border-amber-300 text-amber-700 focus:ring-amber-500">
                    Remove current image
                </label>
            @else
                <p class="block text-amber-900 font-extrabold mb-2">Current cover image</p>
                <p class="text-sm font-bold text-amber-800/60">No image uploaded yet.</p>
            @endif
            @error('remove_cover_image') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="excerpt" class="block text-amber-900 font-extrabold mb-2">Excerpt</label>
        <textarea id="excerpt" name="excerpt" rows="3" required
            class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
        @error('excerpt') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="content_text" class="block text-amber-900 font-extrabold mb-2">Content paragraphs</label>
        <textarea id="content_text" name="content_text" rows="12" required
            class="w-full bg-amber-50 border-2 border-amber-100 rounded-xl px-4 py-3 text-amber-900 font-bold">{{ $paragraphText }}</textarea>
        <p class="text-xs font-bold text-amber-800/60 mt-2">Separate paragraphs with a blank line.</p>
        @error('content_text') <p class="text-red-600 text-sm font-bold mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="px-8 py-3 bg-amber-900 text-white font-extrabold rounded-xl hover:bg-black transition-all">
            {{ $submitLabel }}
        </button>
        <a href="{{ route('admin.blog.index') }}" class="px-6 py-3 rounded-xl border-2 border-amber-200 text-amber-900 font-extrabold hover:bg-amber-50 transition-colors">
            Cancel
        </a>
    </div>
</form>
