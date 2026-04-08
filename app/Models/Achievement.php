<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon_emoji',
        'requirement_type',
        'requirement_value',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')->withTimestamps()->withPivot('unlocked_at');
    }

    /** One-line requirement for badges, dashboard tiles, and admin mail. */
    public function requirementLabel(): string
    {
        $n = max(1, (int) ($this->requirement_value ?? 1));

        return match ($this->requirement_type) {
            'edits_accepted' => $n === 1 ? '1 accepted edit' : $n.' accepted edits',
            'votes_cast' => $n === 1 ? '1 vote cast' : $n.' votes cast',
            'points_earned' => $n === 1 ? '1 leaderboard point' : $n.' leaderboard points',
            'chapters_read' => $n === 1 ? '1 chapter with reading progress' : $n.' chapters with reading progress',
            'completed_payments' => $n === 1 ? '1 completed $2 checkout' : $n.' completed $2 checkouts',
            default => ($this->requirement_type ?? '') !== ''
                ? $n.' × ('.$this->requirement_type.')'
                : 'See description',
        };
    }
}
