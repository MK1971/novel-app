<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterStatistic extends Model
{
    protected $fillable = [
        'chapter_id',
        'total_reads',
        'total_edits',
        'accepted_edits',
        'rejected_edits',
        'total_votes',
        'total_reactions',
        'average_rating',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }
}
