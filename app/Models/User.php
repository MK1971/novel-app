<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'points',
        'is_admin',
        'onboarding_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function edits()
    {
        return $this->hasMany(Edit::class);
    }

    public function inlineEdits()
    {
        return $this->hasMany(InlineEdit::class);
    }

    /**
     * Chapter-level edit rows only. Excludes `inline_edit` PayPal stub rows (each paid paragraph also has an `inline_edits` row).
     */
    public function chapterLevelEditsForStats()
    {
        return $this->edits()->where('type', '!=', 'inline_edit');
    }

    public function editSuggestionsSubmittedCount(): int
    {
        return $this->chapterLevelEditsForStats()->count() + $this->inlineEdits()->count();
    }

    public function acceptedChapterAndParagraphEditCount(): int
    {
        $chapterAccepted = $this->chapterLevelEditsForStats()
            ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
            ->count();

        return $chapterAccepted + $this->inlineEdits()->where('status', 'approved')->count();
    }

    public function rejectedChapterAndParagraphEditCount(): int
    {
        $chapterRejected = $this->chapterLevelEditsForStats()
            ->where('status', 'rejected')
            ->count();

        return $chapterRejected + $this->inlineEdits()->where('status', 'rejected')->count();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')->withTimestamps()->withPivot('unlocked_at');
    }

    public function activityFeed()
    {
        return $this->hasMany(ActivityFeed::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function paragraphReactions()
    {
        return $this->hasMany(ParagraphReaction::class);
    }
}
