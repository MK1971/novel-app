<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMailCommand extends Command
{
    protected $signature = 'mail:test {email : Recipient email address}';

    protected $description = 'Send a test message using the configured mailer (verify SMTP, Mailpit, etc.)';

    public function handle(): int
    {
        $to = $this->argument('email');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');
        $mailer = config('mail.default');

        $body = implode("\n", [
            'This is a test message from '.config('app.name').'.',
            '',
            'Mailer: '.$mailer,
            'Time: '.now()->toIso8601String(),
            '',
            'If you use MAIL_MAILER=log, check storage/logs/laravel.log for the message.',
        ]);

        try {
            Mail::raw($body, function ($message) use ($to, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                    ->to($to)
                    ->subject(config('app.name').': mail test');
            });
        } catch (\Throwable $e) {
            $this->error('Failed to send: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("Sent test email to {$to} using mailer [{$mailer}] (from: {$fromName} <{$fromAddress}>).");

        return self::SUCCESS;
    }
}
