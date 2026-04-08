<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminNotifier
{
    public static function notify(string $subject, string $body): void
    {
        $to = self::resolveRecipient();
        if ($to === null || trim($to) === '') {
            Log::info('admin.notify skipped: no recipient (set Admin notification email in /admin/settings or ADMIN_EMAIL in .env).');

            return;
        }

        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        try {
            Mail::raw($body, function ($message) use ($to, $subject, $fromAddress, $fromName) {
                $message->from($fromAddress, $fromName)
                    ->to($to)
                    ->subject($subject);
            });
            Log::info('admin.notify sent', [
                'to' => $to,
                'subject' => $subject,
                'mailer' => config('mail.default'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('admin.notify failed', [
                'message' => $e->getMessage(),
                'subject' => $subject,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.'.config('mail.default').'.host'),
            ]);
        }
    }

    /**
     * Admin Settings value, else ADMIN_EMAIL when it looks like a real address.
     */
    public static function resolveRecipient(): ?string
    {
        $configured = AppSetting::get(AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL);
        if (is_string($configured) && trim($configured) !== '' && filter_var(trim($configured), FILTER_VALIDATE_EMAIL)) {
            return trim($configured);
        }

        $fallback = env('ADMIN_EMAIL');
        if (is_string($fallback) && trim($fallback) !== '' && filter_var(trim($fallback), FILTER_VALIDATE_EMAIL)) {
            return trim($fallback);
        }

        return null;
    }

    public static function notifyNewPaidSuggestion(string $summaryLine): void
    {
        self::notify(
            'WhatsMyBookName: new paid suggestion',
            $summaryLine."\n\nReview: ".url('/admin/chapters')
        );
    }
}
