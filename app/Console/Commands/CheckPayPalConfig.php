<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPayPalConfig extends Command
{
    protected $signature = 'paypal:check';
    protected $description = 'Verify PayPal credentials are loaded';

    public function handle(): int
    {
        $mode = config('paypal.mode', 'sandbox');
        $clientId = config("paypal.{$mode}.client_id");
        $clientSecret = config("paypal.{$mode}.client_secret");

        $this->info("PayPal mode: {$mode}");
        $this->info('Client ID: ' . ($clientId ? substr($clientId, 0, 10) . '...' : 'NOT SET'));
        $this->info('Client Secret: ' . ($clientSecret ? '***' . substr($clientSecret, -4) : 'NOT SET'));

        if (empty($clientId) || empty($clientSecret)) {
            $this->error('Credentials are missing! Add to .env:');
            $this->line('  PAYPAL_MODE=sandbox');
            $this->line('  PAYPAL_SANDBOX_CLIENT_ID=your_client_id');
            $this->line('  PAYPAL_SANDBOX_CLIENT_SECRET=your_client_secret');
            return 1;
        }

        $this->info('Credentials appear to be set.');
        return 0;
    }
}
