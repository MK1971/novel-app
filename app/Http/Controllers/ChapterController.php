<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\ReadingProgress;
use App\Support\AchievementUnlock;
use App\Support\ChapterLifecycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        // Optional deep link only: ?resume=1 sends the reader to their most recently updated chapter.
        // Main "Chapters" nav must NOT use this, or every visit skips the full chapter list.
        if ($request->boolean('resume') && Auth::check()) {
            $lastProgress = ReadingProgress::where('user_id', Auth::id())
                ->orderByDesc('updated_at')
                ->first();

            if ($lastProgress) {
                return redirect()->route('chapters.show', $lastProgress->chapter_id);
            }
        }

        // Automatic locking: only for the main manuscript book — not Peter Trull (A/B pairs).
        // Otherwise visiting /chapters would lock Version A (lower id) and break voting on both columns.
        $bookIdsToAutoLock = Book::query()
            ->where('name', '!=', Book::NAME_PETER_TRULL)
            ->pluck('id');

        if ($bookIdsToAutoLock->isNotEmpty()) {
            // Only non-archived rows count as the live manuscript; archived slots can have higher ids and
            // must not steal "latest" or every new chapter appears locked on the reader index.
            $latestChapterIds = Chapter::query()
                ->selectRaw('book_id, MAX(id) as max_id')
                ->whereIn('book_id', $bookIdsToAutoLock)
                ->where('is_archived', false)
                ->groupBy('book_id')
                ->pluck('max_id');

            Chapter::query()
                ->whereIn('book_id', $bookIdsToAutoLock)
                ->whereNotIn('id', $latestChapterIds)
                ->where('is_locked', false)
                ->update(['is_locked' => true, 'locked_at' => now()]);

            Chapter::query()
                ->whereIn('id', $latestChapterIds)
                ->update(['is_locked' => false, 'locked_at' => null]);
        }

        $chapters = Chapter::with('statistics')
            ->forTbwReaderManuscript()
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get();

        $archiveChapters = Chapter::with('statistics')
            ->forTbwReaderArchive()
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get();

        // Ensure statistics exist for all chapters
        foreach ($chapters as $chapter) {
            if (! $chapter->statistics) {
                $chapter->statistics()->create([
                    'total_reads' => 0,
                    'total_edits' => 0,
                    'accepted_edits' => 0,
                    'rejected_edits' => 0,
                    'total_votes' => 0,
                ]);
            }
        }

        $readingProgressByChapter = collect();
        if (Auth::check()) {
            $readingProgressByChapter = ReadingProgress::query()
                ->where('user_id', Auth::id())
                ->whereIn('chapter_id', $chapters->pluck('id'))
                ->get()
                ->keyBy('chapter_id');
        }

        return view('chapters.index', compact('chapters', 'archiveChapters', 'readingProgressByChapter'));
    }

    public function show(Chapter $chapter)
    {
        $chapter->loadMissing('book');
        if ($chapter->is_archived && ! $chapter->is_reader_archive_link) {
            abort(404);
        }

        $progress = 0;
        $progressBarPercent = null;
        $progressExtentMax = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::firstOrCreate(
                ['user_id' => Auth::id(), 'chapter_id' => $chapter->id]
            );
            $progress = (int) $readingProgress->scroll_position;
            $progressExtentMax = (int) ($readingProgress->scroll_extent_max ?? 0);
            if ($readingProgress->wasRecentlyCreated) {
                AchievementUnlock::syncForUser(Auth::user());
            }
            $progressBarPercent = $readingProgress->displayProgressPercent();
        }

        $stats = $chapter->statistics()->firstOrCreate(['chapter_id' => $chapter->id]);
        $stats->increment('total_reads');

        $pendingPaymentEdit = null;
        $queuedPendingEdits = collect();
        if (Auth::check()) {
            $pendingPaymentEdit = Edit::query()
                ->where('user_id', Auth::id())
                ->where('chapter_id', $chapter->id)
                ->where('status', 'pending_payment')
                ->first();
            $queuedPendingEdits = Edit::query()
                ->with('chapter.book')
                ->where('user_id', Auth::id())
                ->where('status', 'pending_payment')
                ->orderByDesc('updated_at')
                ->get();
        }

        $suggestionsClosed = ChapterLifecycle::suggestionsClosedForTbwChapter($chapter);
        $editingWindowEndsAt = $chapter->editing_closes_at;

        [$prevChapter, $nextChapter] = $this->adjacentTbwChapters($chapter);

        $chapter->loadMissing('book');
        $tbwArchiveSiblings = collect();
        $tbwLiveForNav = null;
        $tbwOtherArchiveSiblings = collect();
        if ($chapter->book && $chapter->book->name === Book::NAME_THE_BOOK_WITH_NO_NAME) {
            if (! $chapter->is_archived) {
                $tbwArchiveSiblings = $chapter->tbwArchiveSiblingsForReader();
            } else {
                $tbwLiveForNav = $chapter->tbwLiveManuscriptForSameSlot();
                $tbwOtherArchiveSiblings = $chapter->tbwOtherArchiveSiblingsForReader();
            }
        }

        return view('chapters.show', compact(
            'chapter',
            'progress',
            'progressBarPercent',
            'progressExtentMax',
            'stats',
            'pendingPaymentEdit',
            'queuedPendingEdits',
            'suggestionsClosed',
            'editingWindowEndsAt',
            'prevChapter',
            'nextChapter',
            'tbwArchiveSiblings',
            'tbwLiveForNav',
            'tbwOtherArchiveSiblings',
        ));
    }

    public function trackProgress(Request $request, Chapter $chapter)
    {
        if (! Auth::check()) {
            return response()->json(['success' => true]);
        }

        $progress = ReadingProgress::firstOrNew(
            ['user_id' => Auth::id(), 'chapter_id' => $chapter->id]
        );
        $wasNew = ! $progress->exists;

        $existingPct = 0;
        if ($progress->exists) {
            $existingPct = $progress->displayProgressPercent() ?? 0;
        }

        if ($request->has('read_percent')) {
            $incoming = max(0.0, min(100.0, (float) $request->input('read_percent')));
            $targetPct = max($existingPct, $incoming);
            $progress->scroll_position = (int) round($targetPct * 10);
            $progress->scroll_extent_max = 1000;
        } else {
            $incomingPos = max(0, (int) $request->input('scroll_position', 0));

            if (! $request->filled('scroll_extent_max') && $incomingPos === 0) {
                return response()->json(['success' => true]);
            }

            if ($request->filled('scroll_extent_max')) {
                $incomingExt = max(1, min((int) $request->input('scroll_extent_max'), 50_000_000));
            } else {
                $incomingExt = max(1, (int) ceil($incomingPos * 1.08));
            }

            $ext = max((int) ($progress->scroll_extent_max ?? 0), $incomingExt);
            $pixelPct = min(100.0, 100.0 * $incomingPos / max(1, $incomingExt));
            $targetPct = max($existingPct, $pixelPct);
            $minPosForTarget = (int) ceil($targetPct / 100.0 * $ext);

            $progress->scroll_position = max(
                (int) ($progress->scroll_position ?? 0),
                $incomingPos,
                $minPosForTarget
            );
            $progress->scroll_extent_max = $ext;
        }

        $progress->last_read_at = now();
        $progress->save();

        if ($wasNew) {
            $stats = $chapter->statistics()->firstOrCreate(['chapter_id' => $chapter->id]);
            $stats->increment('total_reads');
            AchievementUnlock::syncForUser(Auth::user());
        }

        return response()->json(['success' => true]);
    }

    /**
     * @return array{0: ?Chapter, 1: ?Chapter} Previous and next in TBWNN reader order (manuscript or archive stream).
     */
    private function adjacentTbwChapters(Chapter $chapter): array
    {
        $chapter->loadMissing('book');
        if (! $chapter->book || $chapter->book->name !== Book::NAME_THE_BOOK_WITH_NO_NAME) {
            return [null, null];
        }

        if ($chapter->is_archived) {
            if (! $chapter->is_reader_archive_link) {
                return [null, null];
            }
            $stream = Chapter::query()
                ->forTbwReaderArchive()
                ->orderByRaw(Chapter::listSectionOrderSql())
                ->orderBy('number')
                ->orderBy('id')
                ->get();
        } else {
            $stream = Chapter::query()
                ->forTbwReaderManuscript()
                ->orderByRaw(Chapter::listSectionOrderSql())
                ->orderBy('number')
                ->orderBy('id')
                ->get();
        }

        $idx = $stream->search(fn (Chapter $c) => $c->id === $chapter->id);
        if ($idx === false) {
            return [null, null];
        }

        $prev = $idx > 0 ? $stream[$idx - 1] : null;
        $next = $idx < $stream->count() - 1 ? $stream[$idx + 1] : null;

        return [$prev, $next];
    }

    public function getProgress(Chapter $chapter)
    {
        $progress = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::where('user_id', Auth::id())
                ->where('chapter_id', $chapter->id)
                ->first();
            $progress = $readingProgress ? $readingProgress->scroll_position : 0;
        }

        return response()->json(['scroll_position' => $progress]);
    }
}
