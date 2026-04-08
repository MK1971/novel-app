<?php

namespace Tests\Unit;

use App\Models\AppSetting;
use App\Support\AdminNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotifierRecipientTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolve_recipient_prefers_app_settings(): void
    {
        AppSetting::set(AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL, 'notify@test.org');

        $this->assertSame('notify@test.org', AdminNotifier::resolveRecipient());
    }
}
