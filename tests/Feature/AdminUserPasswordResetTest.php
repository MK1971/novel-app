<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reset_user_password(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $member = User::factory()->create([
            'email' => 'member@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $newPlain = 'NewSecurePass1!';

        $this->actingAs($admin)
            ->put(route('admin.users.update', $member), [
                'name' => $member->name,
                'email' => $member->email,
                'password' => $newPlain,
                'password_confirmation' => $newPlain,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success');

        $member->refresh();
        $this->assertTrue(Hash::check($newPlain, $member->password));
    }

    public function test_non_admin_cannot_update_user_password(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $target = User::factory()->create();

        $this->actingAs($user)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'password' => 'HijackPass1!',
                'password_confirmation' => 'HijackPass1!',
            ])
            ->assertForbidden();
    }
}
