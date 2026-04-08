<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edit extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'chapter_id', 'type', 'original_text', 'edited_text', 'inline_edit_payload', 'status', 'show_in_public_feed', 'points_awarded'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(EditFeedback::class);
    }
}
