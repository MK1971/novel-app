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
        $voteStats = Vote::select('chapter_id', 'version_chosen', DB::raw('count(*) as total'))
            ->groupBy('chapter_id', 'version_chosen')
            ->get();

        // Only show chapters from "The Book With No Name" (exclude Peter Trull)
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
