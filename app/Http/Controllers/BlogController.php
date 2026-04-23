<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $posts = $this->posts();
        $featuredPost = $posts->firstWhere('featured', true) ?? $posts->first();
        $latestPosts = $posts
            ->reject(fn (array $post): bool => $featuredPost !== null && $post['slug'] === $featuredPost['slug'])
            ->take(6)
            ->values();

        $acceptedStatuses = ['accepted', 'accepted_full', 'accepted_partial'];
        $contributorsCount = User::query()
            ->where(function ($q) use ($acceptedStatuses) {
                $q->whereHas('edits', fn ($e) => $e->whereIn('status', $acceptedStatuses))
                    ->orWhereHas('inlineEdits', fn ($ie) => $ie->where('status', 'approved'));
            })
            ->count();

        $stats = [
            'contributors' => $contributorsCount,
            'accepted_edits' => Edit::query()->whereIn('status', $acceptedStatuses)->count()
                + InlineEdit::query()->where('status', 'approved')->count(),
            'live_chapters' => Chapter::logicalReaderPieceCount(),
            'posts_published' => $posts->count(),
        ];

        return view('blog.index', [
            'featuredPost' => $featuredPost,
            'latestPosts' => $latestPosts,
            'quickUpdates' => collect(config('blog.quick_updates', [])),
            'stats' => $stats,
        ]);
    }

    public function show(string $slug)
    {
        $posts = $this->posts();
        $post = $posts->firstWhere('slug', $slug);
        abort_if($post === null, 404);

        $relatedPosts = $posts
            ->reject(fn (array $candidate): bool => $candidate['slug'] === $slug)
            ->take(3)
            ->values();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    private function posts(): Collection
    {
        $databasePosts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->get()
            ->map(function (BlogPost $post): array {
                return [
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'category' => $post->category,
                    'author' => $post->author,
                    'published_at' => $post->published_at ?? now(),
                    'featured' => (bool) $post->is_featured,
                    'cover_emoji' => $post->cover_emoji,
                    'cover_image_path' => $post->cover_image_path,
                    'cover_image_url' => $this->resolveCoverImageUrl($post->cover_image_path),
                    'category_icon' => $this->categoryIcon($post->category),
                    'excerpt' => $post->excerpt,
                    'content' => is_array($post->content) ? $post->content : [],
                ];
            });

        $configPosts = collect(config('blog.posts', []))
            ->map(function (array $post): array {
                $post['published_at'] = Carbon::parse($post['published_at']);
                $post['cover_image_path'] = $post['cover_image_path'] ?? null;
                $post['cover_image_url'] = $this->resolveCoverImageUrl($post['cover_image_path']);
                $post['category_icon'] = $this->categoryIcon($post['category'] ?? null);

                return $post;
            });

        if ($databasePosts->isEmpty()) {
            return $configPosts
                ->sortByDesc(fn (array $post) => $post['published_at']->timestamp)
                ->values();
        }

        return $databasePosts
            ->keyBy('slug')
            ->union($configPosts->keyBy('slug'))
            ->sortByDesc(fn (array $post) => $post['published_at']->timestamp)
            ->values();
    }

    private function categoryIcon(?string $category): string
    {
        return match (mb_strtolower(trim((string) $category))) {
            'launch' => '🚀',
            'community' => '🏆',
            'craft' => '✍️',
            'data' => '📊',
            'scheduling' => '🗓️',
            'policy' => '📜',
            'the story' => '🕵️',
            'editorial' => '📝',
            'vision' => '🔮',
            default => '📘',
        };
    }

    private function resolveCoverImageUrl(?string $path): ?string
    {
        $normalized = trim((string) $path);
        if ($normalized === '') {
            return null;
        }

        if (Storage::disk('public')->exists($normalized)) {
            return asset('storage/'.$normalized);
        }

        if (is_file(public_path($normalized))) {
            return asset($normalized);
        }

        return null;
    }
}
