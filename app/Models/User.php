<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'avatar_path',
        'password',
        'points',
        'is_admin',
        'onboarding_completed_at',
        'public_profile_enabled',
        'public_slug',
        'profile_bio',
        'leaderboard_visible',
        'profile_indexable',
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
            'public_profile_enabled' => 'boolean',
            'leaderboard_visible' => 'boolean',
            'profile_indexable' => 'boolean',
        ];
    }

    public function publicProfileUrl(): ?string
    {
        if (! $this->public_profile_enabled || ! filled($this->public_slug)) {
            return null;
        }

        return route('profile.public', ['slug' => $this->public_slug], absolute: true);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : null;
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

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function blocksInitiated()
    {
        return $this->hasMany(UserBlock::class, 'blocker_id');
    }

    public function blocksReceived()
    {
        return $this->hasMany(UserBlock::class, 'blocked_id');
    }

    /**
     * Whether the viewer cannot access this user's public profile due to a block (either direction).
     */
    public function publicProfileBlockedFor(?User $viewer): bool
    {
        if ($viewer === null || $viewer->id === $this->id) {
            return false;
        }

        return UserBlock::query()
            ->where(function ($q) use ($viewer) {
                $q->where('blocker_id', $viewer->id)->where('blocked_id', $this->id);
            })
            ->orWhere(function ($q) use ($viewer) {
                $q->where('blocker_id', $this->id)->where('blocked_id', $viewer->id);
            })
            ->exists();
    }
}
