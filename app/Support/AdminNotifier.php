<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminNotifier
{
    public static function notify(string $subject, string $body): void
    {
        $to = AppSetting::get(AppSetting::KEY_ADMIN_NOTIFICATION_EMAIL);
        if ($to === null || trim($to) === '') {
            return;
        }

        try {
            Mail::raw($body, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Throwable $e) {
            Log::warning('admin.notify failed', ['message' => $e->getMessage(), 'subject' => $subject]);
        }
    }

    public static function notifyNewPaidSuggestion(string $summaryLine): void
    {
        self::notify(
            config('app.name').': new paid suggestion',
            $summaryLine."\n\nReview: ".url('/admin/chapters')
        );
    }
}
