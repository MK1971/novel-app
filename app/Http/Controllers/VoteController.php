<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Payment;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoteController extends Controller
{
    public function index()
    {
        $book = \App\Models\Book::where('name', 'Peter Trull Solitary Detective')->first();
        $chapters = $book?->chapters()->orderBy('number')->get() ?? collect();

        $hasVoted = [];
        $canVote = false;
        $voteCreditsRemaining = 0;

        if (Auth::check()) {
            $hasVoted = Vote::where('user_id', Auth::id())
                ->pluck('chapter_id')
                ->toArray();

            $voteCreditsRemaining = Payment::query()
                ->where('user_id', Auth::id())
                ->withAvailableVoteCredit()
                ->count();

            $canVote = $voteCreditsRemaining > 0;
        }

        // Group chapters by number for the view
        $chapters = $chapters->groupBy('number');

        $chapterIds = $chapters->flatten()->pluck('id')->unique()->filter()->values();
        $voteCounts = collect();
        if ($chapterIds->isNotEmpty()) {
            $voteCounts = Vote::query()
                ->whereIn('chapter_id', $chapterIds)
                ->selectRaw('chapter_id, COUNT(*) as cnt')
                ->groupBy('chapter_id')
                ->pluck('cnt', 'chapter_id');
        }

        return view('vote.index', compact('chapters', 'hasVoted', 'canVote', 'voteCounts', 'voteCreditsRemaining'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to vote');
        }

        if ($chapter->is_locked) {
            return back()->with('error', 'Voting is closed for this chapter.');
        }

        $existingVote = Vote::where('user_id', Auth::id())
            ->whereIn('chapter_id', function ($query) use ($chapter) {
                $query->select('id')->from('chapters')
                    ->where('book_id', $chapter->book_id)
                    ->where('number', $chapter->number);
            })
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
            return back()->with('error', 'Each vote on Peter Trull uses one completed $2 edit payment. You have no unused vote credits.');
        }

        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $chapter->id]);
        $stats->increment('total_votes');

        return back()->with('success', 'Your vote has been cast!');
    }
}
