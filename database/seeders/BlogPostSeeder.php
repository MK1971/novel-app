<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = collect(config('blog.posts', []));

        $featuredSlug = null;

        $posts->each(function (array $post) use (&$featuredSlug): void {
            $slug = (string) ($post['slug'] ?? '');
            if ($slug === '') {
                return;
            }

            $isFeatured = (bool) ($post['featured'] ?? false);
            if ($isFeatured) {
                $featuredSlug = $slug;
            }

            BlogPost::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => (string) ($post['title'] ?? ''),
                    'category' => (string) ($post['category'] ?? 'Update'),
                    'author' => (string) ($post['author'] ?? 'Editorial Team'),
                    'excerpt' => (string) ($post['excerpt'] ?? ''),
                    'content' => array_values(array_filter((array) ($post['content'] ?? []), fn ($p) => is_string($p) && trim($p) !== '')),
                    'cover_emoji' => isset($post['cover_emoji']) ? (string) $post['cover_emoji'] : null,
                    'is_featured' => $isFeatured,
                    'is_published' => true,
                    'published_at' => Carbon::parse((string) ($post['published_at'] ?? now()->toDateString())),
                ]
            );
        });

        if ($featuredSlug !== null) {
            BlogPost::query()
                ->where('slug', '!=', $featuredSlug)
                ->update(['is_featured' => false]);
        }
    }
}
