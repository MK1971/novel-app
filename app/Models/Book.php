<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    public const NAME_THE_BOOK_WITH_NO_NAME = 'The Book With No Name';

    public const NAME_PETER_TRULL = 'Peter Trull Solitary Detective';

    protected $fillable = ['name', 'status', 'winner_id'];

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}
