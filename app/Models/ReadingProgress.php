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
        $ext = (int) ($this->scroll_extent_max ?? 0);
        if ($ext < 1) {
            return null;
        }

        $pos = (int) ($this->scroll_position ?? 0);
        if ($pos <= 0) {
            return 0;
        }

        return (int) min(100, max(0, round(100 * $pos / $ext)));
    }

    /**
     * Percent for UI (chapter list, SSR bar). Uses stored extent when present; otherwise estimates from
     * scroll depth so label and fill stay in sync when extent was missing from older saves.
     */
    public function displayProgressPercent(): ?int
    {
        $strict = $this->scrollProgressPercent();
        if ($strict !== null) {
            return $strict;
        }

        $pos = (int) ($this->scroll_position ?? 0);
        if ($pos <= 0) {
            return null;
        }

        $ext = (int) ($this->scroll_extent_max ?? 0);
        $denom = $ext >= 1 ? $ext : (int) max(ceil($pos * 1.12), $pos + 100);

        return (int) min(100, max(1, round(100 * $pos / $denom)));
    }

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'last_read_at' => 'datetime',
            'scroll_position' => 'integer',
            'scroll_extent_max' => 'integer',
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
