<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReleaseBFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_only_adds_multiple_pending_edits_instead_of_overwriting(): void
    {
        config([
            'paypal.mode' => 'sandbox',
            'paypal.sandbox.client_id' => 'x',
            'paypal.sandbox.client_secret' => 'y',
        ]);

        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'content' => 'Original',
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->actingAs($user)->post(route('payment.checkout'), [
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'edited_text' => 'Edit one',
            'queue_only' => 1,
        ])->assertRedirect(route('chapters.show', $chapter));

        $this->actingAs($user)->post(route('payment.checkout'), [
            'chapter_id' => $chapter->id,
            'type' => 'phrase',
            'edited_text' => 'Edit two',
            'queue_only' => 1,
        ])->assertRedirect(route('chapters.show', $chapter));

        $this->assertSame(2, Edit::query()->where('user_id', $user->id)->where('status', 'pending_payment')->count());
    }

    public function test_user_can_remove_own_queued_edit_but_not_others(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'content' => 'Original',
            'version' => 'A',
            'status' => 'published',
        ]);

        $edit = Edit::create([
            'user_id' => $owner->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Original',
            'edited_text' => 'Queued',
            'status' => 'pending_payment',
        ]);

        $this->actingAs($other)
            ->delete(route('payment.queue.remove', $edit))
            ->assertStatus(403);

        $this->assertDatabaseHas('edits', ['id' => $edit->id]);

        $this->actingAs($owner)
            ->delete(route('payment.queue.remove', $edit))
            ->assertRedirect(route('chapters.show', $chapter));

        $this->assertDatabaseMissing('edits', ['id' => $edit->id]);
    }

    public function test_user_cannot_post_feedback_on_own_public_edit(): void
    {
        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'content' => 'Original',
            'version' => 'A',
            'status' => 'published',
        ]);
        $edit = Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Original',
            'edited_text' => 'Public edit',
            'status' => 'pending',
            'show_in_public_feed' => true,
        ]);

        $this->actingAs($user)
            ->post(route('edits.public.feedback'), [
                'kind' => 'chapter',
                'id' => $edit->id,
                'message' => 'My own feedback',
            ])
            ->assertSessionHas('error');
    }
}
