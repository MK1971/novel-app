<?php

namespace App\Support;

use App\Models\Chapter;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class EditOutcomeNotifier
{
    public static function chapterEditAccepted(User $user, Chapter $chapter, string $status, bool $pointsAwarded): void
    {
        $label = $chapter->readerHeadingLine();
        $detail = match ($status) {
            'accepted_full' => 'Your suggestion was accepted in full.',
            'accepted_partial' => 'Your suggestion was partially accepted.',
            default => 'Your suggestion was updated.',
        };
        if (! $pointsAwarded) {
            $detail .= ' No leaderboard points were recorded (no completed payment on file).';
        } else {
            $pts = $status === 'accepted_full' ? 2 : 1;
            $detail .= " You earned {$pts} point(s).";
        }

        self::storeAndMail(
            $user,
            'edit_accepted',
            'Suggestion accepted',
            $detail.' — '.$label.'.',
            'Your edit for "'.$label.'" was accepted by moderators.'
        );
    }

    public static function chapterEditRejected(User $user, Chapter $chapter): void
    {
        $label = $chapter->readerHeadingLine();

        self::storeAndMail(
            $user,
            'edit_rejected',
            'Suggestion not accepted',
            'Moderators did not accept your suggestion for '.$label.'. You can submit a new idea while editing is open.',
            'Your edit for "'.$label.'" was not accepted. You can try again while the chapter accepts paid suggestions.'
        );
    }

    public static function paragraphAccepted(User $user, Chapter $chapter, bool $partial, bool $pointsAwarded): void
    {
        $label = $chapter->readerHeadingLine();
        $detail = $partial
            ? 'Your paragraph suggestion was partially accepted.'
            : 'Your paragraph suggestion was accepted.';
        if (! $pointsAwarded) {
            $detail .= ' No leaderboard points were recorded (no completed payment on file).';
        } else {
            $detail .= $partial ? ' You earned 1 point.' : ' You earned 2 points.';
        }

        self::storeAndMail(
            $user,
            'paragraph_accepted',
            'Paragraph suggestion accepted',
            $detail.' — '.$label.'.',
            'Your paragraph edit on "'.$label.'" was accepted.'
        );
    }

    public static function paragraphRejected(User $user, Chapter $chapter): void
    {
        $label = $chapter->readerHeadingLine();

        self::storeAndMail(
            $user,
            'paragraph_rejected',
            'Paragraph suggestion not accepted',
            'Moderators did not accept your paragraph change on '.$label.'.',
            'Your paragraph suggestion on "'.$label.'" was not accepted.'
        );
    }

    private static function storeAndMail(
        User $user,
        string $type,
        string $title,
        string $inAppMessage,
        string $mailBodyLine,
    ): void {
        Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $inAppMessage,
            'is_subscribed' => true,
        ]);

        try {
            $fromAddress = config('mail.from.address');
            $fromName = 'WhatsMyBookName';
            Mail::raw(
                $mailBodyLine."\n\nOpen the site to read chapters and notifications.\n",
                function ($message) use ($user, $title, $fromAddress, $fromName) {
                    $message
                        ->from($fromAddress, $fromName)
                        ->to($user->email)
                        ->subject($title.' — What\'s My Book Name');
                }
            );
        } catch (Throwable $e) {
            Log::warning('edit outcome email failed', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
