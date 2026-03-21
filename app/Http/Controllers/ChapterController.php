<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $lastProgress = ReadingProgress::where('user_id', Auth::id())
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if ($lastProgress) {
                return redirect()->route('chapters.show', $lastProgress->chapter_id);
            }
        }

        $chapters = Chapter::orderBy('number')->get();
        return view('chapters.index', compact('chapters'));
    }

    public function show(Chapter $chapter)
    {
        $progress = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::firstOrCreate([
                'user_id' => Auth::id(),
                'chapter_id' => $chapter->id,
            ]);
            $progress = $readingProgress->scroll_position;
        }

        return view('chapters.show', compact('chapter', 'progress'));
    }

    public function trackProgress(Request $request, Chapter $chapter)
    {
        if (Auth::check()) {
            ReadingProgress::updateOrCreate(
                ['user_id' => Auth::id(), 'chapter_id' => $chapter->id],
                ['scroll_position' => $request->input('scroll_position', 0)]
            );
        }
        return response()->json(['success' => true]);
    }

    public function getProgress(Chapter $chapter)
    {
        $progress = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::where('user_id', Auth::id())
                ->where('chapter_id', $chapter->id)
                ->first();
            $progress = $readingProgress ? $readingProgress->scroll_position : 0;
        }
        return response()->json(['scroll_position' => $progress]);
    }
}
