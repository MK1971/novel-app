<?php

namespace Tests\Feature;

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSocialDisconnectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_password_can_disconnect_linked_google(): void
    {
        $user = User::factory()->create();
        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'sub-123',
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.social.disconnect', ['provider' => 'google']))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status', 'social-disconnected');

        $this->assertDatabaseMissing('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
        ]);
    }

    public function test_oauth_only_user_cannot_disconnect_last_social_without_password(): void
    {
        $user = User::factory()->withoutPassword()->create();
        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'sub-456',
        ]);

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.social.disconnect', ['provider' => 'google']))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('social');

        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
        ]);
    }

    public function test_oauth_only_user_can_disconnect_one_provider_when_two_linked(): void
    {
        $user = User::factory()->withoutPassword()->create();
        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'g1',
        ]);
        SocialAccount::query()->create([
            'user_id' => $user->id,
            'provider' => 'apple',
            'provider_id' => 'a1',
        ]);

        $this->actingAs($user)
            ->delete(route('profile.social.disconnect', ['provider' => 'google']))
            ->assertSessionHas('status', 'social-disconnected');

        $this->assertDatabaseMissing('social_accounts', ['provider' => 'google', 'user_id' => $user->id]);
        $this->assertDatabaseHas('social_accounts', ['provider' => 'apple', 'user_id' => $user->id]);
    }
}
