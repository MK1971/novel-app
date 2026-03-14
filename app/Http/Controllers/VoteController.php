<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
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
        if (auth()->check()) {
            $storyBook = Book::where('name', 'The Book With No Name')->first();
            $canVote = $storyBook && Edit::where('user_id', $request->user()->id)
                ->whereHas('chapter', fn ($q) => $q->where('book_id', $storyBook->id))
                ->whereIn('status', ['pending', 'accepted_full', 'accepted_partial'])
                ->exists();
        }

        if ($book) {
            $chapters = Chapter::where('book_id', $book->id)
                ->whereIn('version', ['A', 'B'])
                ->orderBy('number')
                ->orderBy('version')
                ->get()
                ->groupBy('number');
        }

        return view('vote.index', compact('chapters', 'book', 'canVote'));
    }

    public function store(Request $request, Chapter $chapter)
    {
        $storyBook = Book::where('name', 'The Book With No Name')->first();
        $canVote = $storyBook && Edit::where('user_id', $request->user()->id)
            ->whereHas('chapter', fn ($q) => $q->where('book_id', $storyBook->id))
            ->whereIn('status', ['pending', 'accepted_full', 'accepted_partial'])
            ->exists();

        if (!$canVote) {
            return back()->with('error', 'Only users who have suggested an edit in The Book With No Name can vote on Peter Trull chapters.');
        }

        $request->validate(['version_chosen' => 'required|in:A,B']);
        Vote::updateOrCreate(
            ['user_id' => $request->user()->id, 'chapter_id' => $chapter->id],
            ['version_chosen' => $request->version_chosen]
        );
        return back()->with('success', 'Vote recorded!');
    }
}
