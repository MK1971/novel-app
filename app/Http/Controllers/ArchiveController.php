<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Vote;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function chapters()
    {
        $archivedChapters = Chapter::where('is_archived', true)
            ->orderBy('round_number', 'desc')
            ->get();
        return view('archive.chapters', compact('archivedChapters'));
    }

    public function rounds()
    {
        $rounds = Vote::select('chapter_id as round_number')
            ->distinct()
            ->orderBy('chapter_id', 'desc')
            ->get();
        return view('archive.rounds', compact('rounds'));
    }
}
