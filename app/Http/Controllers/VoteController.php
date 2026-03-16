<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ChapterStatistic;
use App\Models\Edit;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index(Request $request)
    {
        $book = Book::where('name', 'Peter Trull Solitary Detective')->first();
        $chapters = collect();

        $canVote = false;
        $hasVoted = [];
        
        if (auth()->check()) {
            $storyBook = Book::where('name', 'The Book With No Name')->first();
            // Check if user has ANY accepted edit (not just pending)
            $canVote = $storyBook && Edit::where('user_id', $request->user()->id)
                ->whereHas('chapter', fn ($q) => $q->where('book_id', $storyBook->id))
                ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
                ->exists();
            
            // Get chapters user has already voted on
            $hasVoted = Vote::where('user_id', $request->user()->id)
                ->pluck('chapter_id')
                ->toArray();
        }

        if ($book) {
            $chapters = Chapter::where('book_id', $book->id)
                ->whereIn('version', ['A', 'B'])
                ->orderBy('number')
                ->orderBy('version')
                ->get()
                ->groupBy('number');
        }

        return view('vote.index', compact('chapters', 'book', 'canVote', 'hasVoted'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $storyBook = Book::where('name', 'The Book With No Name')->first();
        // Check if user has ANY accepted edit (not just pending)
        $canVote = $storyBook && Edit::where('user_id', $request->user()->id)
            ->whereHas('chapter', fn ($q) => $q->where('book_id', $storyBook->id))
            ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
            ->exists();

        if (!$canVote) {
            return back()->with('error', 'Only users who have suggested an accepted edit in The Book With No Name can vote on Peter Trull chapters.');
        }

        // Check if user has already voted on this chapter
        $existingVote = Vote::where('user_id', $request->user()->id)
            ->where('chapter_id', $chapter->id)
            ->first();

        if ($existingVote) {
            return back()->with('error', 'You have already voted on this chapter. Each user can vote only once per chapter.');
        }

        $request->validate(['version_chosen' => 'required|in:A,B']);
        
        Vote::create([
            'user_id' => $request->user()->id,
            'chapter_id' => $chapter->id,
            'version_chosen' => $request->version_chosen,
            'session_id' => session()->getId(),
            'paid_at' => now(),
        ]);
        
        // Update chapter statistics
        $stats = ChapterStatistic::firstOrCreate(
            ['chapter_id' => $chapter->id],
            [
                'total_reads' => 0,
                'total_edits' => 0,
                'accepted_edits' => 0,
                'total_votes' => 0,
                'total_reactions' => 0,
                'average_rating' => 0,
            ]
        );
        
        $stats->update([
            'total_votes' => $chapter->votes()->count(),
        ]);
        
        return back()->with('success', 'Vote recorded!');
    }
}
