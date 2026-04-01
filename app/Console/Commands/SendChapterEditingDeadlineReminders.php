<?php

namespace App\Console\Commands;

use App\Models\Chapter;
use App\Support\AdminNotifier;
use App\Support\ChapterLifecycle;
use Illuminate\Console\Command;

class SendChapterEditingDeadlineReminders extends Command
{
    protected $signature = 'chapter:editing-deadline-reminders';

    protected $description = 'Email admin when a TBWNN chapter editing window ends within 24 hours (once per chapter).';

    public function handle(): int
    {
        $book = ChapterLifecycle::tbwnnBook();
        if (! $book) {
            return self::SUCCESS;
        }

        $chapters = Chapter::query()
            ->where('book_id', $book->id)
            ->where('is_archived', false)
            ->where('is_locked', false)
            ->whereNotNull('editing_closes_at')
            ->whereNull('editing_deadline_reminder_sent_at')
            ->where('editing_closes_at', '<=', now()->addDay())
            ->where('editing_closes_at', '>', now())
            ->get();

        foreach ($chapters as $chapter) {
            AdminNotifier::notify(
                config('app.name').': chapter edit window ending soon',
                "The editing window for \"{$chapter->displayTitle()}\" (chapter id {$chapter->id}) closes at ".
                $chapter->editing_closes_at->toIso8601String().". Review or upload the next chapter as needed.\n\n".
                url('/admin/chapters')
            );
            $chapter->forceFill(['editing_deadline_reminder_sent_at' => now()])->saveQuietly();
            $this->line("Reminder queued/marked for chapter {$chapter->id}");
        }

        return self::SUCCESS;
    }
}
