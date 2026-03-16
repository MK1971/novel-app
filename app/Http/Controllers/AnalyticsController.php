<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $voteStats = Vote::select('round_number', 'version_voted', DB::raw('count(*) as total'))
            ->groupBy('round_number', 'version_voted')
            ->orderBy('round_number', 'desc')
            ->get();

        // Only show chapters from "The Book With No Name" (exclude Peter Trull)
        $chapterStats = Chapter::where('book_id', function($query) {
            $query->select('id')->from('books')->where('name', 'The Book With No Name');
        })
        ->select('id', 'title', DB::raw('(SELECT count(*) FROM edits WHERE chapter_id = chapters.id) as edits_count'))
        ->get();

        return view('analytics.index', compact('voteStats', 'chapterStats'));
    }
}
