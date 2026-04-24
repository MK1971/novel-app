<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    private const DEFAULT_COVER_IMAGE_MAX_KB = 10240;

    public function index(): View
    {
        $posts = BlogPost::query()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($uploadError = $this->validateUploadPayload($request)) {
            return $uploadError;
        }

        $validated = $this->validatePost($request);
        $post = new BlogPost();
        $this->fillPost($post, $validated, $request);
        $post->save();
        $this->syncFeaturedFlag($post);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post created.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog.edit', compact('blogPost'));
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        if ($uploadError = $this->validateUploadPayload($request)) {
            return $uploadError;
        }

        $validated = $this->validatePost($request, $blogPost);
        $this->fillPost($blogPost, $validated, $request);
        $blogPost->save();
        $this->syncFeaturedFlag($blogPost);

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->deleteCoverImageIfAny($blogPost);
        $blogPost->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post deleted.');
    }

    public function publishNow(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->forceFill([
            'is_published' => true,
            'published_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.blog.index')
            ->with('success', 'Blog post published now.');
    }

    public function preview(BlogPost $blogPost): View
    {
        $post = $this->toPostArray($blogPost);

        $relatedPosts = BlogPost::query()
            ->where('id', '!=', $blogPost->id)
            ->published()
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (BlogPost $related): array => $this->toPostArray($related))
            ->values();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    private function validatePost(Request $request, ?BlogPost $existing = null): array
    {
        $coverImageMaxKb = $this->coverImageMaxKb();
        $coverImageMaxMb = number_format($coverImageMaxKb / 1024, 0);

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blog_posts', 'slug')->ignore($existing?->id),
            ],
            'category' => ['nullable', 'string', 'max:80'],
            'author' => ['required', 'string', 'max:120'],
            'excerpt' => ['required', 'string', 'max:1000'],
            'content_text' => ['required', 'string', 'max:20000'],
            'cover_emoji' => ['nullable', 'string', 'max:16'],
            'cover_image' => ['nullable', 'image', 'max:'.$coverImageMaxKb],
            'remove_cover_image' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];

        $validated = $request->validate($rules, [
            'cover_image.image' => 'Cover image must be a valid JPG, PNG, WebP, or GIF.',
            'cover_image.max' => 'Cover image must be '.$coverImageMaxMb.'MB or smaller.',
            'published_at.date' => 'Published at must be a valid date and time.',
        ]);
        $validated['slug'] = $this->resolveSlug(
            (string) ($validated['slug'] ?? ''),
            (string) $validated['title'],
            $existing?->id
        );

        return $validated;
    }

    private function fillPost(BlogPost $post, array $validated, Request $request): void
    {
        $isPublished = (bool) ($validated['is_published'] ?? false);
        $publishedAt = $this->parsePublishedAt($validated['published_at'] ?? null);
        if ($isPublished && ! $publishedAt) {
            $publishedAt = now();
        }

        $post->fill([
            'slug' => $validated['slug'],
            'title' => $validated['title'],
            'category' => trim((string) ($validated['category'] ?? '')) ?: 'Update',
            'author' => $validated['author'],
            'excerpt' => $validated['excerpt'],
            'content' => $this->paragraphsFromText($validated['content_text']),
            'cover_emoji' => trim((string) ($validated['cover_emoji'] ?? '')) ?: null,
            'is_featured' => (bool) ($validated['is_featured'] ?? false),
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
        ]);

        $removeCoverImage = (bool) ($validated['remove_cover_image'] ?? false);
        if ($removeCoverImage) {
            $this->deleteCoverImageIfAny($post);
            $post->cover_image_path = null;
        }

        $uploaded = $request->file('cover_image');
        if ($uploaded instanceof UploadedFile) {
            $this->deleteCoverImageIfAny($post);
            $post->cover_image_path = $uploaded->store('blog-covers', 'public');
        }
    }

    private function parsePublishedAt(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value, 'Asia/Jerusalem')->utc();
    }

    private function validateUploadPayload(Request $request): ?RedirectResponse
    {
        $contentLength = (int) $request->server('CONTENT_LENGTH', 0);
        $postMaxBytes = $this->iniSizeToBytes((string) ini_get('post_max_size'));
        if ($postMaxBytes > 0 && $contentLength > $postMaxBytes) {
            return back()
                ->withInput()
                ->withErrors([
                    'cover_image' => 'Upload failed: request exceeded server upload limit ('.strtoupper((string) ini_get('post_max_size')).').',
                ]);
        }

        $uploaded = $request->file('cover_image');
        if ($uploaded instanceof UploadedFile && ! $uploaded->isValid()) {
            return back()
                ->withInput()
                ->withErrors([
                    'cover_image' => 'Upload failed: '.$uploaded->getErrorMessage(),
                ]);
        }

        return null;
    }

    private function iniSizeToBytes(string $size): int
    {
        $trimmed = trim($size);
        if ($trimmed === '') {
            return 0;
        }

        $unit = strtolower(substr($trimmed, -1));
        $number = (int) $trimmed;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => (int) $trimmed,
        };
    }

    private function coverImageMaxKb(): int
    {
        $configured = (int) config('blog.cover_image_max_kb', self::DEFAULT_COVER_IMAGE_MAX_KB);

        return $configured > 0 ? $configured : self::DEFAULT_COVER_IMAGE_MAX_KB;
    }

    private function paragraphsFromText(string $content): array
    {
        $parts = preg_split("/\R{2,}/", trim($content)) ?: [];
        $paragraphs = [];
        foreach ($parts as $part) {
            $line = trim((string) $part);
            if ($line !== '') {
                $paragraphs[] = preg_replace("/\R+/", ' ', $line) ?? $line;
            }
        }

        return $paragraphs;
    }

    private function resolveSlug(string $inputSlug, string $title, ?int $ignoreId = null): string
    {
        $base = $inputSlug !== '' ? $inputSlug : Str::slug($title);
        $base = $base !== '' ? $base : 'blog-post';

        $slug = $base;
        $suffix = 2;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function slugExists(string $slug, ?int $ignoreId): bool
    {
        return BlogPost::query()
            ->where('slug', $slug)
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();
    }

    private function syncFeaturedFlag(BlogPost $post): void
    {
        if (! $post->is_featured) {
            return;
        }

        BlogPost::query()
            ->where('id', '!=', $post->id)
            ->update(['is_featured' => false]);
    }

    private function toPostArray(BlogPost $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'category' => $post->category,
            'author' => $post->author,
            'published_at' => $post->published_at ?? now(),
            'featured' => (bool) $post->is_featured,
            'cover_emoji' => $post->cover_emoji,
            'cover_image_path' => $post->cover_image_path,
            'excerpt' => $post->excerpt,
            'content' => is_array($post->content) ? $post->content : [],
        ];
    }

    private function deleteCoverImageIfAny(BlogPost $post): void
    {
        if (! $post->cover_image_path) {
            return;
        }

        Storage::disk('public')->delete($post->cover_image_path);
    }
}
