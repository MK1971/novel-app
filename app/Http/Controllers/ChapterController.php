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

        // Manuscript is single-stream Version A (admin); exclude stray B rows from the reader list.
        $chapters = Chapter::with('statistics')
            ->whereHas('book', function ($query) {
                $query->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME);
            })
            ->where('is_archived', false)
            ->where(function ($q) {
                $q->whereNull('version')
                    ->orWhere('version', '')
                    ->orWhereRaw('LOWER(TRIM(version)) = ?', ['a']);
            })
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('id')
            ->get();

        $archiveChapters = Chapter::with('statistics')
            ->whereHas('book', function ($query) {
                $query->where('name', Book::NAME_THE_BOOK_WITH_NO_NAME);
            })
            ->where('is_archived', true)
            ->where('is_reader_archive_link', true)
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

        $progress = [];
        $resumeReading = null;
        if (Auth::check()) {
            $progress = ReadingProgress::where('user_id', Auth::id())
                ->whereIn('chapter_id', $chapters->pluck('id'))
                ->get()
                ->pluck('scroll_position', 'chapter_id')
                ->toArray();

            if ($chapters->isNotEmpty()) {
                $resume = ReadingProgress::query()
                    ->where('user_id', Auth::id())
                    ->whereIn('chapter_id', $chapters->pluck('id'))
                    ->where('scroll_position', '>', 0)
                    ->orderByDesc('updated_at')
                    ->first();

                if ($resume) {
                    $resumeReading = [
                        'chapter_id' => (int) $resume->chapter_id,
                        'scroll_position' => (int) $resume->scroll_position,
                    ];
                }
            }
        }

        return view('chapters.index', compact('chapters', 'archiveChapters', 'progress', 'resumeReading'));
    }

    public function show(Chapter $chapter)
    {
        $chapter->loadMissing('book');
        if ($chapter->is_archived && ! $chapter->is_reader_archive_link) {
            abort(404);
        }

        $progress = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::firstOrCreate(
                ['user_id' => Auth::id(), 'chapter_id' => $chapter->id]
            );
            $progress = $readingProgress->scroll_position;
            if ($readingProgress->wasRecentlyCreated) {
                AchievementUnlock::syncForUser(Auth::user());
            }
        }

        $stats = $chapter->statistics()->firstOrCreate(['chapter_id' => $chapter->id]);
        $stats->increment('total_reads');

        $pendingPaymentEdit = null;
        if (Auth::check()) {
            $pendingPaymentEdit = Edit::query()
                ->where('user_id', Auth::id())
                ->where('chapter_id', $chapter->id)
                ->where('status', 'pending_payment')
                ->first();
        }

        $suggestionsClosed = ChapterLifecycle::suggestionsClosedForTbwChapter($chapter);
        $editingWindowEndsAt = $chapter->editing_closes_at;

        return view('chapters.show', compact('chapter', 'progress', 'stats', 'pendingPaymentEdit', 'suggestionsClosed', 'editingWindowEndsAt'));
    }

    public function trackProgress(Request $request, Chapter $chapter)
    {
        if (Auth::check()) {
            $progress = ReadingProgress::updateOrCreate(
                ['user_id' => Auth::id(), 'chapter_id' => $chapter->id],
                ['scroll_position' => $request->input('scroll_position', 0)]
            );

            // Only increment total_reads if this is a new progress record (first time reading)
            if ($progress->wasRecentlyCreated) {
                $stats = $chapter->statistics()->firstOrCreate(['chapter_id' => $chapter->id]);
                $stats->increment('total_reads');
                AchievementUnlock::syncForUser(Auth::user());
            }
        }

        return response()->json(['success' => true]);
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
