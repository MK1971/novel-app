<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Vote;
use App\Models\Edit;
use App\Models\ChapterStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function index()
    {
        $book = \App\Models\Book::where('name', 'Peter Trull Solitary Detective')->first();
        $chapters = $book?->chapters()->orderBy('number')->get() ?? collect();
        
        $userVotes = [];
        if (Auth::check()) {
            $userVotes = Vote::where('user_id', Auth::id())
                ->pluck('chapter_id')
                ->toArray();
        }
        
        return view('vote.index', compact('chapters', 'userVotes'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'You must be logged in to vote'], 401);
        }

        // Check if user has at least one accepted edit
        $hasAcceptedEdit = Edit::where('user_id', Auth::id())
            ->whereIn('status', ['accepted_full', 'accepted_partial'])
            ->exists();

        if (!$hasAcceptedEdit) {
            return response()->json(['error' => 'You must have at least one accepted edit to vote'], 403);
        }

        // Check if user has already voted on this chapter
        $existingVote = Vote::where('user_id', Auth::id())
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($existingVote) {
            return response()->json(['error' => 'You have already voted on this chapter'], 400);
        }

        // Create the vote
        $vote = Vote::create([
            'user_id' => Auth::id(),
            'chapter_id' => $chapter->id,
            'version' => $request->input('version', 'A'),
            'session_id' => session()->getId(),
            'paid_at' => now(),
        ]);

        // Update chapter statistics
        $stats = ChapterStatistic::where('chapter_id', $chapter->id)->first();
        if ($stats) {
            $stats->increment('total_votes');
        }

        return response()->json(['success' => true, 'vote_id' => $vote->id]);
    }
}
