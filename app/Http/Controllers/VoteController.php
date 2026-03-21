<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Vote;
use App\Models\Edit;
use App\Models\InlineEdit;
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
        $canVote = false;
        
        if (Auth::check()) {
            $userVotes = Vote::where('user_id', Auth::id())
                ->pluck('chapter_id')
                ->toArray();
                
            // Check if user has at least one accepted edit (either full edit or inline edit)
            $hasAcceptedEdit = Edit::where('user_id', Auth::id())
                ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
                ->exists();
                
            $hasAcceptedInlineEdit = InlineEdit::where('user_id', Auth::id())
                ->where('status', 'approved')
                ->exists();
                
            $canVote = $hasAcceptedEdit || $hasAcceptedInlineEdit;
        }
        
        return view('vote.index', compact('chapters', 'userVotes', 'canVote'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to vote');
        }

        // Check eligibility
        $hasAcceptedEdit = Edit::where('user_id', Auth::id())
            ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
            ->exists();
            
        $hasAcceptedInlineEdit = InlineEdit::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->exists();
            
        if (!$hasAcceptedEdit && !$hasAcceptedInlineEdit) {
            return back()->with('error', 'You must have at least one accepted edit to vote');
        }

        // Check if user has already voted on this chapter
        $existingVote = Vote::where('user_id', Auth::id())
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted on this chapter');
        }

        // Create the vote
        Vote::create([
            'user_id' => Auth::id(),
            'chapter_id' => $chapter->id,
            'version_chosen' => $request->input('version', 'A'),
            'session_id' => session()->getId(),
            'paid_at' => now(),
        ]);

        // Update chapter statistics
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $chapter->id]);
        $stats->increment('total_votes');

        return back()->with('success', 'Your vote has been cast!');
    }
}
