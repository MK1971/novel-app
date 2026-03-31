<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEditDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_paragraph_flow_counts_once_not_twice_for_your_edits(): void
    {
        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Ch1',
            'number' => 1,
            'content' => 'Hello',
            'version' => 'A',
            'status' => 'published',
        ]);

        Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => 'inline_edit',
            'original_text' => '',
            'edited_text' => 'Inline edit pending',
            'status' => 'pending',
        ]);
        InlineEdit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'paragraph_number' => 0,
            'original_text' => 'Hello',
            'suggested_text' => 'Hi',
            'reason' => null,
            'status' => 'pending',
        ]);

        $this->assertSame(1, $user->fresh()->editSuggestionsSubmittedCount());
    }

    public function test_chapter_and_paragraph_submissions_sum_correctly(): void
    {
        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Ch1',
            'number' => 1,
            'content' => 'X',
            'version' => 'A',
            'status' => 'published',
        ]);

        Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'X',
            'edited_text' => 'Y',
            'status' => 'pending',
        ]);
        Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => 'inline_edit',
            'original_text' => '',
            'edited_text' => 'stub',
            'status' => 'pending',
        ]);
        InlineEdit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'paragraph_number' => 0,
            'original_text' => 'a',
            'suggested_text' => 'b',
            'status' => 'pending',
        ]);

        $this->assertSame(2, $user->fresh()->editSuggestionsSubmittedCount());
    }
}
