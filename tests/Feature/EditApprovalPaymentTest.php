<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditApprovalPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_approve_without_completed_payment_awards_zero_points(): void
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
        $edit = Edit::create([
            'user_id' => $author->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Hello',
            'edited_text' => 'Hello world',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.edits.approve', $edit), [
                'status' => 'accepted_full',
            ])
            ->assertSessionHas('success');

        $author->refresh();
        $edit->refresh();
        $this->assertSame(0, $author->points);
        $this->assertSame(0, (int) $edit->points_awarded);
    }

    public function test_admin_approve_with_completed_payment_awards_points(): void
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
        $edit = Edit::create([
            'user_id' => $author->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Hello',
            'edited_text' => 'Hello world',
            'status' => 'pending',
        ]);
        Payment::create([
            'user_id' => $author->id,
            'amount_cents' => 200,
            'payment_id' => 'test-cap',
            'status' => 'completed',
            'edit_id' => $edit->id,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.edits.approve', $edit), [
                'status' => 'accepted_full',
            ])
            ->assertSessionHas('success');

        $author->refresh();
        $edit->refresh();
        $this->assertSame(2, $author->points);
        $this->assertSame(2, (int) $edit->points_awarded);
    }
}
