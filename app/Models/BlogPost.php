<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'category',
        'author',
        'excerpt',
        'content',
        'cover_emoji',
        'cover_image_path',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}
