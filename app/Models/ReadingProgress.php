<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $table = 'reading_progress';

    protected $fillable = [
        'user_id',
        'chapter_id',
        'scroll_position',
        'scroll_extent_max',
        'completed',
        'last_read_at',
    ];

    /** Approximate read-through on the chapter page (0–100). Null until scroll extent has been saved at least once. */
    public function scrollProgressPercent(): ?int
    {
        if (! $this->scroll_extent_max || $this->scroll_extent_max < 1) {
            return null;
        }

        if ($this->scroll_position <= 0) {
            return 0;
        }

        return (int) min(100, max(0, round(100 * $this->scroll_position / $this->scroll_extent_max)));
    }

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'last_read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
