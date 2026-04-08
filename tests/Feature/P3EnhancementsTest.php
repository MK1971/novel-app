<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class P3EnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderation_approve_creates_in_app_notification_for_author(): void
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
                'status' => 'accepted_partial',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'type' => 'edit_accepted',
        ]);
    }

    public function test_moderation_reject_creates_notification(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $author = User::factory()->create();
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
            'edited_text' => 'Hi',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.edits.reject', $edit))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $author->id,
            'type' => 'edit_rejected',
        ]);
    }

    public function test_tbw_chapters_rss_feed_returns_xml(): void
    {
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        Chapter::create([
            'book_id' => $book->id,
            'title' => 'Feed Test',
            'number' => 1,
            'content' => 'Body',
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->get(route('feed.chapters'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('Feed Test', false);
    }

    public function test_edit_diff_preview_returns_json_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $book = Book::create(['name' => 'The Book With No Name', 'status' => 'in_progress']);
        $chapter = Chapter::create([
            'book_id' => $book->id,
            'title' => 'Ch1',
            'number' => 1,
            'content' => "Line one\nLine two",
            'version' => 'A',
            'status' => 'published',
        ]);

        $this->actingAs($user)
            ->postJson(route('edits.preview-diff'), [
                'chapter_id' => $chapter->id,
                'edited_text' => "Line one\nChanged",
            ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonStructure(['diff' => ['lines', 'truncated', 'collapsed_same']]);
    }

    public function test_payment_checkout_is_rate_limited_per_user(): void
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

        RateLimiter::clear('payment-checkout:'.$user->id);

        for ($i = 0; $i < 25; $i++) {
            $this->actingAs($user)
                ->from(route('chapters.show', $chapter))
                ->post(route('payment.checkout'), [
                    'chapter_id' => $chapter->id,
                    'type' => 'writing',
                    'edited_text' => 'Draft '.$i,
                ]);
        }

        $this->actingAs($user)
            ->from(route('chapters.show', $chapter))
            ->post(route('payment.checkout'), [
                'chapter_id' => $chapter->id,
                'type' => 'writing',
                'edited_text' => 'One more',
            ])
            ->assertSessionHas('error');

        RateLimiter::clear('payment-checkout:'.$user->id);
    }

    public function test_profile_submissions_tab_renders(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->get(route('profile.show', ['tab' => 'submissions']))
            ->assertOk()
            ->assertSee('My submissions', false);
    }

    public function test_profile_payments_page_requires_auth(): void
    {
        $this->get(route('profile.payments'))->assertRedirect();
    }
}
