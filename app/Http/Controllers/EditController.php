<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Edit;
use App\Models\Payment;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function create($chapterId)
    {
        $chapter = Chapter::findOrFail($chapterId);
        $user = request()->user();
        $hasPaid = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNull('edit_id')
            ->exists();
        if (!$hasPaid) {
            return redirect()->route('chapters.show', $chapter)->with('error', 'Please pay $2 to submit an edit.');
        }
        return view('edits.create', compact('chapter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'type' => 'required|in:writing,phrase',
            'edited_text' => 'required|string',
        ]);

        $chapter = Chapter::findOrFail($request->chapter_id);
        $user = $request->user();
        $hasPaid = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereNull('edit_id')
            ->first();
        if (!$hasPaid) {
            return back()->with('error', 'Please complete payment first.');
        }

        $edit = Edit::create([
            'user_id' => $user->id,
            'chapter_id' => $chapter->id,
            'type' => $request->type,
            'original_text' => $chapter->content,
            'edited_text' => $request->edited_text,
            'status' => 'pending',
        ]);

        $hasPaid->update(['edit_id' => $edit->id]);

        return redirect()->route('chapters.index')->with('success', 'Edit submitted! We will review it.');
    }
}
