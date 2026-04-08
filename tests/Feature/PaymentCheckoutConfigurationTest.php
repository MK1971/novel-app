<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCheckoutConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_blocked_without_paypal_credentials_does_not_create_edit(): void
    {
        config([
            'paypal.mode' => 'sandbox',
            'paypal.sandbox.client_id' => '',
            'paypal.sandbox.client_secret' => '',
        ]);

        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'content' => 'Once upon a time.',
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->actingAs($user)
            ->from(route('chapters.show', $chapter))
            ->post(route('payment.checkout'), [
                'chapter_id' => $chapter->id,
                'type' => 'writing',
                'edited_text' => 'Suggested change.',
            ])
            ->assertSessionHas('error')
            ->assertRedirect(route('chapters.show', $chapter));

        $this->assertSame(0, Edit::count());
    }

    public function test_checkout_blocked_when_live_mode_missing_live_credentials(): void
    {
        config([
            'paypal.mode' => 'live',
            'paypal.live.client_id' => 'live-id',
            'paypal.live.client_secret' => '',
            'paypal.sandbox.client_id' => 'sandbox-id',
            'paypal.sandbox.client_secret' => 'sandbox-secret',
        ]);

        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Chapter 1',
            'number' => 1,
            'content' => 'Once upon a time.',
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->actingAs($user)
            ->from(route('chapters.show', $chapter))
            ->post(route('payment.checkout'), [
                'chapter_id' => $chapter->id,
                'type' => 'writing',
                'edited_text' => 'Suggested change.',
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Edit::count());
    }
}
