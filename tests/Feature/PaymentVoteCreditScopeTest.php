<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Book;
use App\Models\Chapter;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vote;
use App\Support\AchievementUnlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentVoteCreditScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_with_available_vote_credit_counts_completed_payments_without_vote_ballot(): void
    {
        $user = User::factory()->create();
        $book = Book::create(['name' => 'Peter Trull Solitary Detective', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Case',
            'number' => 1,
            'content' => 'A',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount_cents' => 200,
            'payment_id' => 'paypal-order-xyz',
            'status' => 'completed',
            'edit_id' => null,
        ]);

        $this->assertSame(
            1,
            Payment::query()->where('user_id', $user->id)->withAvailableVoteCredit()->count()
        );

        Vote::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'version_chosen' => 'A',
            'session_id' => 'test',
            'paid_at' => now(),
            'payment_id' => $payment->id,
        ]);

        $this->assertSame(
            0,
            Payment::query()->where('user_id', $user->id)->withAvailableVoteCredit()->count()
        );
    }

    public function test_completed_payment_unlocks_paid_to_play_achievement(): void
    {
        Achievement::updateOrCreate(
            ['name' => 'Paid to play'],
            [
                'description' => 'Test',
                'icon_emoji' => '💳',
                'requirement_type' => 'completed_payments',
                'requirement_value' => 1,
            ]
        );

        $user = User::factory()->create();
        Payment::create([
            'user_id' => $user->id,
            'amount_cents' => 200,
            'payment_id' => 'paypal-order-abc',
            'status' => 'completed',
            'edit_id' => null,
        ]);

        AchievementUnlock::syncForUser($user);

        $this->assertTrue(
            $user->achievements()->where('achievements.name', 'Paid to play')->exists()
        );
    }
}
