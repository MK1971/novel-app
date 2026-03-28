<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoteIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_index_does_not_error_when_chapter_pair_is_incomplete(): void
    {
        $book = Book::create([
            'name' => 'Peter Trull Solitary Detective',
            'status' => 'in_progress',
        ]);

        Chapter::create([
            'book_id' => $book->id,
            'title' => 'Case 1',
            'number' => 1,
            'content' => 'Only version A exists.',
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->get(route('vote.index'))->assertOk();
    }
}
