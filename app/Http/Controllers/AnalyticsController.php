<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Chapter;
use App\Models\Edit;
use App\Models\InlineEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Get vote stats for Peter Trull chapters
        $voteStats = Vote::select('chapter_id', 'version_chosen', DB::raw('count(*) as total'))
            ->whereIn('chapter_id', function($query) {
                $query->select('id')->from('chapters')->where('book_id', function($q) {
                    $q->select('id')->from('books')->where('name', 'Peter Trull Solitary Detective');
                });
            })
            ->groupBy('chapter_id', 'version_chosen')
            ->get()
            ->map(function($stat) {
                $chapter = Chapter::find($stat->chapter_id);
                $stat->chapter_number = $chapter->number;
                $stat->chapter_title = $chapter->title;
                return $stat;
            });

        // Get chapter stats for "The Book With No Name"
        $chapterStats = Chapter::where('book_id', function($query) {
            $query->select('id')->from('books')->where('name', 'The Book With No Name');
        })
        ->get()
        ->map(function($chapter) {
            $chapter->edits_count = Edit::where('chapter_id', $chapter->id)->count();
            $chapter->inline_edits_count = InlineEdit::where('chapter_id', $chapter->id)->count();
            return $chapter;
        });

        return view('analytics.index', compact('voteStats', 'chapterStats'));
    }
}
