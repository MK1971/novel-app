<?php

namespace Tests\Feature;

use App\Models\ProfileReport;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_returns_ok_when_enabled(): void
    {
        $user = User::factory()->withPublicProfile('jane-doe')->create();

        $this->get(route('profile.public', ['slug' => 'jane-doe']))
            ->assertOk()
            ->assertSee($user->name, false)
            ->assertSee('Test bio.', false)
            ->assertSee('<meta name="description"', false);
    }

    public function test_public_profile_returns_404_when_disabled(): void
    {
        User::factory()->create([
            'public_profile_enabled' => false,
            'public_slug' => 'hidden-user',
        ]);

        $this->get(route('profile.public', ['slug' => 'hidden-user']))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_update_public_settings(): void
    {
        $user = User::factory()->create([
            'public_profile_enabled' => false,
            'public_slug' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.public-settings.update'), [
                'public_profile_enabled' => '1',
                'public_slug' => 'my-slug',
                'profile_bio' => 'Writer and reader.',
            ])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertTrue($user->public_profile_enabled);
        $this->assertSame('my-slug', $user->public_slug);
        $this->assertSame('Writer and reader.', $user->profile_bio);
    }

    public function test_reserved_slug_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.public-settings.update'), [
                'public_profile_enabled' => '1',
                'public_slug' => 'admin',
            ])
            ->assertSessionHasErrors('public_slug');
    }

    public function test_public_profile_includes_noindex_when_profile_not_indexable(): void
    {
        User::factory()->withPublicProfile('no-bot')->create([
            'profile_indexable' => false,
        ]);

        $this->get(route('profile.public', ['slug' => 'no-bot']))
            ->assertOk()
            ->assertSee('noindex', false);
    }

    public function test_authenticated_user_can_report_public_profile(): void
    {
        $target = User::factory()->withPublicProfile('reported')->create();
        $reporter = User::factory()->create();

        $this->actingAs($reporter)
            ->post(route('profile.public.report', ['slug' => 'reported']), [
                'category' => 'spam',
                'details' => 'Test note',
            ])
            ->assertRedirect(route('profile.public', ['slug' => 'reported']));

        $this->assertTrue(ProfileReport::query()
            ->where('reporter_id', $reporter->id)
            ->where('reported_user_id', $target->id)
            ->where('category', 'spam')
            ->exists());
    }

    public function test_blocked_viewer_cannot_see_public_profile(): void
    {
        $subject = User::factory()->withPublicProfile('alice')->create();
        $viewer = User::factory()->create();
        UserBlock::query()->create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $subject->id,
        ]);

        $this->actingAs($viewer)
            ->get(route('profile.public', ['slug' => 'alice']))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_update_privacy_toggles(): void
    {
        $user = User::factory()->create([
            'leaderboard_visible' => true,
            'profile_indexable' => true,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.public-settings.update'), [
                'public_profile_enabled' => '0',
                'public_slug' => null,
                'leaderboard_visible' => '0',
                'profile_indexable' => '0',
            ])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertFalse($user->leaderboard_visible);
        $this->assertFalse($user->profile_indexable);
    }
}
