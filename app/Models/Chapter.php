<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'title', 'number', 'content', 'version', 'status'];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function edits(): HasMany
    {
        return $this->hasMany(Edit::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function statistics(): HasOne
    {
        return $this->hasOne(ChapterStatistic::class);
    }

    public function paragraphReactions(): HasMany
    {
        return $this->hasMany(ParagraphReaction::class);
    }

    public function activityFeed(): HasMany
    {
        return $this->hasMany(ActivityFeed::class);
    }
}
