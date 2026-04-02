<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Payment;
use App\Models\Vote;
use App\Support\AchievementUnlock;
use App\Support\ChapterLifecycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function index()
    {
        $book = Book::where('name', Book::NAME_PETER_TRULL)->first();
        $chapters = $book?->chapters()
            ->where('is_archived', false)
            ->orderByRaw(Chapter::listSectionOrderSql())
            ->orderBy('number')
            ->orderBy('version')
            ->get() ?? collect();

        $hasVoted = [];
        $canVote = false;
        $voteCreditsRemaining = 0;

        if (Auth::check()) {
            AchievementUnlock::syncForUser(Auth::user());

            $hasVoted = Vote::where('user_id', Auth::id())
                ->pluck('chapter_id')
                ->toArray();

            $voteCreditsRemaining = Payment::query()
                ->where('user_id', Auth::id())
                ->withAvailableVoteCredit()
                ->count();

            $canVote = $voteCreditsRemaining > 0;
        }

        // Group A/B pairs by list section + number (cold open / prolog / chapter / epilog)
        $chapters = $chapters->groupBy(fn (Chapter $c) => $c->votePairGroupKey());

        $latestPtVotePairKey = $chapters->isNotEmpty() ? $chapters->keys()->last() : null;

        $chapterIds = $chapters->flatten()->pluck('id')->unique()->filter()->values();
        $voteCounts = collect();
        if ($chapterIds->isNotEmpty()) {
            $voteCounts = Vote::query()
                ->whereIn('chapter_id', $chapterIds)
                ->selectRaw('chapter_id, COUNT(*) as cnt')
                ->groupBy('chapter_id')
                ->pluck('cnt', 'chapter_id');
        }

        $archiveChapters = collect();
        if ($book) {
            $archiveChapters = Chapter::query()
                ->where('book_id', $book->id)
                ->where('is_archived', true)
                ->where('is_reader_archive_link', true)
                ->orderByRaw(Chapter::listSectionOrderSql())
                ->orderBy('number')
                ->orderBy('version')
                ->get();
        }

        $firstOpenTbwChapter = ChapterLifecycle::latestOpenTbwChapter();

        return view('vote.index', compact('chapters', 'hasVoted', 'canVote', 'voteCounts', 'voteCreditsRemaining', 'archiveChapters', 'latestPtVotePairKey', 'firstOpenTbwChapter'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to vote');
        }

        if ($chapter->is_locked) {
            return back()->with('error', 'Voting is closed for this chapter.');
        }

        if ($chapter->is_archived) {
            return back()->with('error', 'Voting is not available for archived versions.');
        }

        $chapter->loadMissing('book');
        if (ChapterLifecycle::isPeterTrullChapter($chapter) && ChapterLifecycle::editingWindowExpired($chapter)) {
            return back()->with('error', 'The voting period for this chapter has ended.');
        }

        $section = $chapter->list_section ?: Chapter::LIST_SECTION_CHAPTER;
        $pairIds = Chapter::query()
            ->where('book_id', $chapter->book_id)
            ->where('number', $chapter->number)
            ->where(function ($q) use ($section) {
                $q->where('list_section', $section);
                if ($section === Chapter::LIST_SECTION_CHAPTER) {
                    $q->orWhereNull('list_section');
                }
            })
            ->pluck('id');

        $existingVote = Vote::where('user_id', Auth::id())
            ->whereIn('chapter_id', $pairIds)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted on this chapter pair.');
        }

        try {
            $vote = DB::transaction(function () use ($chapter) {
                $payment = Payment::query()
                    ->where('user_id', Auth::id())
                    ->withAvailableVoteCredit()
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if (! $payment) {
                    return null;
                }

                return Vote::create([
                    'user_id' => Auth::id(),
                    'chapter_id' => $chapter->id,
                    'version_chosen' => $chapter->version,
                    'session_id' => session()->getId(),
                    'paid_at' => $payment->created_at,
                    'payment_id' => $payment->id,
                ]);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // e.g. duplicate payment_id if two requests raced past the app check
            return back()->with('error', 'Could not apply your vote. Please try again.');
        }

        if (! $vote) {
            return back()->with('error', 'Each vote on Peter Trull Solitary Detective uses one completed $2 edit payment. You have no unused vote credits.');
        }

        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $chapter->id]);
        $stats->increment('total_votes');

        AchievementUnlock::syncForUser(Auth::user());

        return back()->with('success', 'Your vote has been cast!');
    }
}
