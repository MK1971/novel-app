<?php

namespace App\Support;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use Illuminate\Support\Facades\DB;

class ChapterLifecycle
{
    /** @var list<string> */
    public const ACCEPTED_EDIT_STATUSES = ['accepted', 'accepted_full', 'accepted_partial'];

    public static function tbwnnBook(): ?Book
    {
        return Book::query()->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME)->first();
    }

    public static function isTbwChapter(Chapter $chapter): bool
    {
        $chapter->loadMissing('book');

        return $chapter->book && $chapter->book->name === Book::NAME_THE_BOOK_WITH_NO_NAME;
    }

    public static function isPeterTrullChapter(Chapter $chapter): bool
    {
        $chapter->loadMissing('book');

        return $chapter->book && $chapter->book->name === Book::NAME_PETER_TRULL;
    }

    public static function latestOpenTbwChapter(): ?Chapter
    {
        $book = self::tbwnnBook();
        if (! $book) {
            return null;
        }

        return Chapter::query()
            ->where('book_id', $book->id)
            ->where('is_archived', false)
            ->where('is_locked', false)
            ->orderByDesc('id')
            ->first();
    }

    public static function hasPendingSuggestionsForChapter(Chapter $chapter): bool
    {
        $chapterEdits = Edit::query()
            ->where('chapter_id', $chapter->id)
            ->where('status', 'pending')
            ->where('type', '!=', 'inline_edit')
            ->exists();

        $inline = InlineEdit::query()
            ->where('chapter_id', $chapter->id)
            ->where('status', 'pending')
            ->exists();

        return $chapterEdits || $inline;
    }

    public static function hasAcceptedSuggestionsForChapter(Chapter $chapter): bool
    {
        $edits = Edit::query()
            ->where('chapter_id', $chapter->id)
            ->whereIn('status', self::ACCEPTED_EDIT_STATUSES)
            ->exists();

        $inline = InlineEdit::query()
            ->where('chapter_id', $chapter->id)
            ->where('status', 'approved')
            ->exists();

        return $edits || $inline;
    }

    public static function editingWindowExpired(Chapter $chapter): bool
    {
        if (! $chapter->editing_closes_at) {
            return false;
        }

        return now()->greaterThan($chapter->editing_closes_at);
    }

    public static function suggestionsClosedForTbwChapter(Chapter $chapter): bool
    {
        if (! self::isTbwChapter($chapter)) {
            return false;
        }

        if ($chapter->is_locked) {
            return true;
        }

        return self::editingWindowExpired($chapter);
    }

    public static function canCloseTbwWithoutMergedUpload(Chapter $chapter): bool
    {
        if (! self::isTbwChapter($chapter)) {
            return false;
        }

        return ! $chapter->is_locked;
    }

    /**
     * @return true|string error message
     */
    public static function canUploadNextStoryChapter(): bool|string
    {
        $open = self::latestOpenTbwChapter();
        if (! $open) {
            return true;
        }

        if (! self::hasAcceptedSuggestionsForChapter($open)) {
            return true;
        }

        return 'This manuscript still has accepted suggestions on an open chapter. Publish the integrated revision (lock that chapter) before uploading a new chapter.';
    }

    public static function archiveTbwRowsForSlot(Book $book, int $number, string $listSection): void
    {
        $ids = Chapter::query()
            ->where('book_id', $book->id)
            ->where('number', $number)
            ->where('list_section', $listSection)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $canonicalId = $ids->max();

        Chapter::query()->whereIn('id', $ids)->update([
            'is_archived' => true,
            'is_reader_archive_link' => false,
        ]);

        Chapter::query()->where('id', $canonicalId)->update([
            'is_reader_archive_link' => true,
        ]);
    }

    public static function applyPublicationWindow(Chapter $chapter): void
    {
        if (! self::isTbwChapter($chapter)) {
            return;
        }

        if ($chapter->published_at) {
            return;
        }

        $chapter->published_at = now();
        $chapter->editing_closes_at = now()->addDays(30);
        $chapter->saveQuietly();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Chapter>  $pair
     */
    public static function resolvePeterTrullWinningVersion(\Illuminate\Support\Collection $pair, string $mode): Chapter
    {
        $a = $pair->firstWhere('version', 'A');
        $b = $pair->firstWhere('version', 'B');
        if (! $a || ! $b) {
            return $pair->first();
        }

        if ($mode === 'A') {
            return $a;
        }
        if ($mode === 'B') {
            return $b;
        }

        $countA = (int) DB::table('votes')->where('chapter_id', $a->id)->count();
        $countB = (int) DB::table('votes')->where('chapter_id', $b->id)->count();

        if ($countA >= $countB) {
            return $a;
        }

        return $b;
    }
}
