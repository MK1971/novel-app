<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCancelFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_cancel_redirects_to_chapter_with_warning_and_keeps_pending_payment_edit(): void
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
        $edit = Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Hello',
            'edited_text' => 'Hello world',
            'status' => 'pending_payment',
        ]);

        $this->actingAs($user)
            ->get(route('payment.cancel', [
                'chapter_id' => $chapter->id,
                'edit_id' => $edit->id,
            ]))
            ->assertRedirect(route('chapters.show', $chapter))
            ->assertSessionHas('warning');

        $edit->refresh();
        $this->assertSame('pending_payment', $edit->status);
    }
}
