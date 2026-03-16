<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Models\ReadingProgress;
use App\Models\ChapterStatistic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function index()
    {
        $book = Book::where('name', 'The Book With No Name')->first();
        $chapters = $book?->chapters()->where('version', 'A')->orderBy('number')->get() ?? collect();
        return view('chapters.index', compact('chapters', 'book'));
    }

    public function show(Chapter $chapter)
    {
        // Track reading progress
        if (Auth::check()) {
            $progress = ReadingProgress::firstOrCreate(
                [
                    'user_id' => Auth::id(),
                    'chapter_id' => $chapter->id,
                ],
                [
                    'completed' => false,
                    'last_read_at' => now(),
                ]
            );
            $progress->update(['last_read_at' => now()]);
        }

        // Get or create chapter statistics
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

        // Update stats from database
        $stats->update([
            'total_edits' => $chapter->edits()->count(),
            'accepted_edits' => $chapter->edits()->whereIn('status', ['accepted', 'accepted_partial'])->count(),
            'total_votes' => $chapter->votes()->count(),
        ]);

        // Increment read count
        $stats->increment('total_reads');

        return view('chapters.show', compact('chapter', 'stats'));
    }
}
