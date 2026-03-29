<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'amount_cents', 'payment_id', 'status', 'edit_id'];

    public function scopeWithAvailableVoteCredit($query)
    {
        return $query->where('status', 'completed')->whereDoesntHave('vote');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function edit(): BelongsTo
    {
        return $this->belongsTo(Edit::class);
    }

    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }
}
