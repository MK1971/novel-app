<?php

namespace Tests\Feature;

use App\Models\User;
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
}
