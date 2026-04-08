<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VotePaymentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Book, 1: Chapter, 2: Chapter}
     */
    private function peterTrullChapterPair(int $number = 1): array
    {
        $book = Book::create([
            'name' => 'Peter Trull Solitary Detective',
            'status' => 'in_progress',
        ]);
        $chA = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Case '.$number,
            'number' => $number,
            'content' => 'Version A',
            'version' => 'A',
            'status' => 'published',
            'is_locked' => false,
        ]);
        $chB = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Case '.$number,
            'number' => $number,
            'content' => 'Version B',
            'version' => 'B',
            'status' => 'published',
            'is_locked' => false,
        ]);

        return [$book, $chA, $chB];
    }

    public function test_vote_store_requires_completed_payment_credit(): void
    {
        [, $chA] = $this->peterTrullChapterPair();
        $user = User::factory()->create();

        Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chA->id,
            'type' => 'writing',
            'original_text' => 'a',
            'edited_text' => 'b',
            'status' => 'accepted_full',
        ]);

        $this->actingAs($user)
            ->post(route('vote.store', $chA))
            ->assertSessionHas('error');

        $this->assertSame(0, Vote::count());
    }

    public function test_vote_store_attaches_payment_and_allows_one_vote_per_credit(): void
    {
        [, $chA, $chB] = $this->peterTrullChapterPair();
        $user = User::factory()->create();

        $edit = Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chA->id,
            'type' => 'writing',
            'original_text' => 'a',
            'edited_text' => 'b',
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount_cents' => 200,
            'payment_id' => 'capture-test-1',
            'status' => 'completed',
            'edit_id' => $edit->id,
        ]);

        $this->actingAs($user)
            ->post(route('vote.store', $chA))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'chapter_id' => $chA->id,
            'payment_id' => $payment->id,
        ]);

        $this->actingAs($user)
            ->post(route('vote.store', $chB))
            ->assertSessionHas('error');

        $this->assertSame(1, Vote::where('user_id', $user->id)->count());
    }

    public function test_visiting_chapter_index_does_not_lock_peter_trull_version_a(): void
    {
        [, $chA, $chB] = $this->peterTrullChapterPair(1);

        $this->assertFalse((bool) $chA->fresh()->is_locked);
        $this->assertFalse((bool) $chB->fresh()->is_locked);

        $this->get(route('chapters.index'))->assertOk();

        $this->assertFalse((bool) $chA->fresh()->is_locked, 'Version A must stay unlocked for voting (auto-lock is manuscript-only).');
        $this->assertFalse((bool) $chB->fresh()->is_locked);
    }

    public function test_second_chapter_pair_requires_second_payment(): void
    {
        [, $ch1A] = $this->peterTrullChapterPair(1);
        [, $ch2A] = $this->peterTrullChapterPair(2);
        $user = User::factory()->create();

        $edit = Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $ch1A->id,
            'type' => 'writing',
            'original_text' => 'a',
            'edited_text' => 'b',
            'status' => 'pending',
        ]);

        Payment::create([
            'user_id' => $user->id,
            'amount_cents' => 200,
            'payment_id' => 'capture-test-2',
            'status' => 'completed',
            'edit_id' => $edit->id,
        ]);

        $this->actingAs($user)
            ->post(route('vote.store', $ch1A))
            ->assertSessionHas('success');

        $this->actingAs($user)
            ->post(route('vote.store', $ch2A))
            ->assertSessionHas('error');

        $this->assertSame(1, Vote::where('user_id', $user->id)->count());
    }
}
