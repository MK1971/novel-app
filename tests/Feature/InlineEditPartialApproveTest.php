<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\InlineEdit;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InlineEditPartialApproveTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_paragraph_accept_awards_one_point_when_paid(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create(['points' => 0]);
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Ch1',
            'number' => 1,
            'content' => 'Hello',
            'version' => 'A',
            'status' => 'published',
        ]);
        $payment = Payment::create([
            'user_id' => $author->id,
            'amount_cents' => 200,
            'payment_id' => 'pp-test',
            'status' => 'completed',
            'edit_id' => null,
        ]);
        $inline = InlineEdit::create([
            'user_id' => $author->id,
            'chapter_id' => $chapter->id,
            'paragraph_number' => 0,
            'original_text' => 'Hello',
            'suggested_text' => 'Hi',
            'status' => 'pending',
            'payment_id' => $payment->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.inline-edits.approve', $inline), ['partial' => '1'])
            ->assertSessionHas('success');

        $author->refresh();
        $inline->refresh();
        $this->assertSame(1, $author->points);
        $this->assertSame('approved', $inline->status);
        $this->assertSame(InlineEdit::OUTCOME_PARTIAL, $inline->moderation_outcome);
    }

    public function test_full_paragraph_accept_awards_two_points_when_paid(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create(['points' => 0]);
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Ch1',
            'number' => 1,
            'content' => 'Hello',
            'version' => 'A',
            'status' => 'published',
        ]);
        $payment = Payment::create([
            'user_id' => $author->id,
            'amount_cents' => 200,
            'payment_id' => 'pp-test-2',
            'status' => 'completed',
            'edit_id' => null,
        ]);
        $inline = InlineEdit::create([
            'user_id' => $author->id,
            'chapter_id' => $chapter->id,
            'paragraph_number' => 0,
            'original_text' => 'Hello',
            'suggested_text' => 'Hi',
            'status' => 'pending',
            'payment_id' => $payment->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.inline-edits.approve', $inline))
            ->assertSessionHas('success');

        $author->refresh();
        $inline->refresh();
        $this->assertSame(2, $author->points);
        $this->assertSame(InlineEdit::OUTCOME_FULL, $inline->moderation_outcome);
    }
}
