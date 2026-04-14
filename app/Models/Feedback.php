<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Feedback extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'content',
        'type',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'general' => 'General feedback',
            'chapter' => 'Chapter-specific',
            'site_suggestion' => 'Site suggestion',
            'suggestion' => 'Story suggestion',
            'bug' => 'Bug report',
            'accessibility' => 'Accessibility',
            'account' => 'Account / login',
            'payment' => 'Payment / PayPal',
            'content_issue' => 'Content / typo',
            'waitlist' => 'Updates waitlist',
            default => Str::title(str_replace('_', ' ', $this->type)),
        };
    }
}
