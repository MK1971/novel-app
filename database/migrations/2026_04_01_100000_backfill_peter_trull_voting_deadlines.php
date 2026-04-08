<?php

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $book = Book::query()->where('name', Book::NAME_PETER_TRULL)->first();
        if (! $book) {
            return;
        }

        Chapter::query()
            ->where('book_id', $book->id)
            ->whereNull('editing_closes_at')
            ->each(function (Chapter $chapter) {
                $pub = $chapter->published_at ?? $chapter->created_at;
                if (! $pub) {
                    return;
                }
                $chapter->published_at = $chapter->published_at ?? $chapter->created_at;
                $chapter->editing_closes_at = $pub->copy()->addDays(30);
                $chapter->saveQuietly();
            });
    }

    public function down(): void
    {
        //
    }
};
