<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_shows_empty_state_when_only_admin_listed(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'points' => 100,
        ]);

        $this->get(route('leaderboard'))
            ->assertOk()
            ->assertSee('No contributors on the board yet', false)
            ->assertSee('Sign in', false)
            ->assertSee('Create account', false)
            ->assertDontSee('Read chapters', false);
    }

    public function test_leaderboard_shows_table_when_non_admin_has_points(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'points' => 0,
        ]);
        User::factory()->create([
            'email' => 'writer@example.com',
            'name' => 'Casey Writer',
            'points' => 50,
        ]);

        $this->get(route('leaderboard'))
            ->assertOk()
            ->assertSee('Casey Writer', false)
            ->assertDontSee('No contributors on the board yet', false);
    }

    public function test_leaderboard_shows_authenticated_user_rank_and_you_label(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'points' => 0,
        ]);
        User::factory()->create([
            'name' => 'Top Contributor',
            'points' => 200,
        ]);
        $viewer = User::factory()->create([
            'name' => 'Mid Contributor',
            'points' => 50,
        ]);

        $this->actingAs($viewer)
            ->get(route('leaderboard'))
            ->assertOk()
            ->assertSee('Your rank', false)
            ->assertSee('#2', false)
            ->assertSee('Mid Contributor', false)
            ->assertSee('You', false);
    }

    public function test_leaderboard_last_30_days_lists_points_from_recent_approved_edits(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'points' => 0,
        ]);
        $writer = User::factory()->create([
            'name' => 'Recent Earner',
            'points' => 0,
        ]);
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
            'user_id' => $writer->id,
            'chapter_id' => $chapter->id,
            'type' => 'writing',
            'original_text' => 'Hello',
            'edited_text' => 'Hi',
            'status' => 'accepted_full',
            'points_awarded' => 2,
        ]);

        $html = $this->get(route('leaderboard', ['period' => '30d']))
            ->assertOk()
            ->assertSee('Recent Earner', false)
            ->assertSee('Points (30d)', false)
            ->getContent();

        $this->assertMatchesRegularExpression('/Recent Earner[\s\S]{0,800}?\b2\b[\s\S]{0,200}?pts/', $html);
    }
}
