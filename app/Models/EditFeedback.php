<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EditFeedback extends Model
{
    protected $table = 'edit_feedback';

    protected $fillable = [
        'user_id',
        'edit_id',
        'inline_edit_id',
        'message',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function edit(): BelongsTo
    {
        return $this->belongsTo(Edit::class);
    }

    public function inlineEdit(): BelongsTo
    {
        return $this->belongsTo(InlineEdit::class);
    }
}
