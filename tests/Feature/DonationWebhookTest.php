<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_creates_donation_payment_and_dedupes_by_order_id(): void
    {
        $user = User::factory()->create();
        $token = (string) env('PAYPAL_WEBHOOK_TOKEN', 'test-token');

        $payload = [
            'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
            'resource' => [
                'id' => 'CAPTURE-1',
                'custom_id' => 'user:'.$user->id,
                'amount' => ['value' => '10.00'],
                'supplementary_data' => [
                    'related_ids' => ['order_id' => 'ORDER-123'],
                ],
            ],
        ];

        $this->postJson(route('payment.donation.webhook'), $payload, [
            'X-Webhook-Token' => $token,
        ])->assertOk();

        $this->postJson(route('payment.donation.webhook'), $payload, [
            'X-Webhook-Token' => $token,
        ])->assertOk();

        $this->assertSame(1, Payment::query()
            ->where('user_id', $user->id)
            ->where('purpose', 'donation')
            ->where('payment_id', 'ORDER-123')
            ->count());
    }
}
