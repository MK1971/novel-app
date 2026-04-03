<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingDismissTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_onboarding_when_not_completed(): void
    {
        $user = User::factory()->create(['onboarding_completed_at' => null]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Welcome — get started', false)
            ->assertSee('Browse chapters', false);
    }

    public function test_onboarding_dismiss_sets_timestamp_and_hides_card(): void
    {
        $user = User::factory()->create(['onboarding_completed_at' => null]);

        $this->actingAs($user)
            ->post(route('onboarding.dismiss'))
            ->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertNotNull($user->onboarding_completed_at);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertDontSee('Welcome — get started', false);
    }
}
