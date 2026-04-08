<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InlineEdit extends Model
{
    public const OUTCOME_FULL = 'full';

    public const OUTCOME_PARTIAL = 'partial';

    protected $fillable = [
        'chapter_id',
        'user_id',
        'paragraph_number',
        'original_text',
        'suggested_text',
        'reason',
        'status',
        'moderation_outcome',
        'admin_notes',
        'payment_id',
        'show_in_public_feed',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(EditFeedback::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
