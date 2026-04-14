<?php

namespace App\Support;

use App\Mail\ChapterReleaseMail;
use App\Mail\WaitlistConfirmationMail;
use App\Models\Feedback;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReleaseNotificationService
{
    /**
     * Send immediate confirmation when someone joins updates list.
     */
    public function sendWaitlistConfirmation(string $email): void
    {
        $normalized = strtolower(trim($email));
        if ($normalized === '') {
            return;
        }

        try {
            Mail::to($normalized)->send(new WaitlistConfirmationMail());
        } catch (\Throwable $e) {
            Log::warning('waitlist.confirmation.failed', [
                'email' => $normalized,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast a chapter/voting release update to waitlist emails.
     *
     * @return int Number of successfully attempted recipients.
     */
    public function sendReleaseAnnouncement(string $subjectLine, string $headline, string $summary, string $actionUrl, string $actionLabel): int
    {
        $emails = Feedback::query()
            ->where('type', 'waitlist')
            ->whereNotNull('email')
            ->pluck('email')
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter()
            ->unique()
            ->values();

        $sent = 0;

        foreach ($emails as $email) {
            try {
                Mail::to($email)->send(new ChapterReleaseMail(
                    subjectLine: $subjectLine,
                    headline: $headline,
                    summary: $summary,
                    actionUrl: $actionUrl,
                    actionLabel: $actionLabel,
                ));
                $sent++;
            } catch (\Throwable $e) {
                Log::warning('chapter.release.mail.failed', [
                    'email' => $email,
                    'subject' => $subjectLine,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}

