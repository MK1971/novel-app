<?php

namespace Tests\Feature;

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
}
