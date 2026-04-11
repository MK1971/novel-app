<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminEmailConfigGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_gate_uses_config_admin_email(): void
    {
        config(['app.admin_email' => 'ops@example.com']);

        $user = User::factory()->create([
            'email' => 'ops@example.com',
            'is_admin' => false,
        ]);

        $this->assertTrue(Gate::forUser($user)->allows('admin'));
    }

    public function test_non_matching_email_not_granted_by_admin_email_rule(): void
    {
        config(['app.admin_email' => 'ops@example.com']);

        $user = User::factory()->create([
            'email' => 'reader@example.com',
            'is_admin' => false,
        ]);

        $this->assertFalse(Gate::forUser($user)->allows('admin'));
    }
}
