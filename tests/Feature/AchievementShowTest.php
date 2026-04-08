<?php

namespace Tests\Feature;

use App\Models\Achievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_achievements_index_creates_catalog_when_table_empty(): void
    {
        $this->assertDatabaseCount('achievements', 0);

        $this->get(route('achievements.index'))
            ->assertOk()
            ->assertSee('First steps', false)
            ->assertSee('Requirement', false);

        $this->assertGreaterThan(0, Achievement::count());
    }

    public function test_achievement_show_returns_ok(): void
    {
        $achievement = Achievement::create([
            'name' => 'P0 Test Badge',
            'description' => 'Test description for show page.',
            'icon_emoji' => '🏅',
            'requirement_type' => 'points_earned',
            'requirement_value' => 10,
        ]);

        $this->get(route('achievements.show', $achievement))
            ->assertOk()
            ->assertSee($achievement->name, false)
            ->assertSee($achievement->description, false)
            ->assertSee('10 leaderboard points', false);
    }
}
