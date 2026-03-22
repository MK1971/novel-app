<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('resume') && Auth::check()) {
            $lastProgress = ReadingProgress::where('user_id', Auth::id())
                ->orderByDesc('updated_at')
                ->first();
            
            if ($lastProgress) {
                return redirect()->route('chapters.show', $lastProgress->chapter_id);
            }
        }

        // Automatic locking logic: Lock all chapters except the latest one for each book
        // We identify "latest" by the highest ID for each book to handle duplicate numbers
        $latestChapterIds = Chapter::selectRaw('book_id, MAX(id) as max_id')
            ->groupBy('book_id')
            ->pluck('max_id');

        // Lock all chapters that are NOT the latest for their respective books
        Chapter::whereNotIn('id', $latestChapterIds)
            ->where('is_locked', false)
            ->update(['is_locked' => true]);
            
        // Ensure the latest chapters are UNLOCKED (unless manually locked by admin)
        // For now, we follow the rule: only the latest is editable.
        Chapter::whereIn('id', $latestChapterIds)
            ->update(['is_locked' => false]);

        $chapters = Chapter::with('statistics')
            ->whereHas('book', function($query) {
                $query->where('name', 'The Book With No Name');
            })
            ->orderBy('number')
            ->orderBy('id')
            ->get();

        return view('chapters.index', compact('chapters'));
    }

    public function show(Chapter $chapter)
    {
        $progress = 0;
        if (Auth::check()) {
            $readingProgress = ReadingProgress::firstOrCreate(
                ['user_id' => Auth::id(), 'chapter_id' => $chapter->id]
            );
            $progress = $readingProgress->scroll_position;
        }
        
        $stats = $chapter->statistics()->firstOrCreate(['chapter_id' => $chapter->id]);
        
        return view('chapters.show', compact('chapter', 'progress', 'stats'));
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
