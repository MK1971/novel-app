<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications', false);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $user = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'comment',
            'title' => 'One',
            'message' => 'Unread one',
            'read_at' => null,
        ]);
        Notification::create([
            'user_id' => $user->id,
            'type' => 'vote',
            'title' => 'Two',
            'message' => 'Unread two',
            'read_at' => null,
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifications', [
            'user_id' => $user->id,
            'read_at' => null,
        ]);
    }
}
