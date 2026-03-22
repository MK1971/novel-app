<?php

namespace App\Http\Controllers;

use App\Models\InlineEdit;
use App\Models\ChapterStatistic;
use Illuminate\Http\Request;

class InlineEditController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'paragraph_number' => 'required|integer|min:0',
            'original_text' => 'required|string',
            'suggested_text' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $inlineEdit = InlineEdit::create($validated);

        // Update chapter statistics immediately
        $stats = ChapterStatistic::firstOrCreate(['chapter_id' => $validated['chapter_id']]);
        $stats->increment('total_edits');

        \App\Models\ActivityFeed::create([
            'user_id' => auth()->id(),
            'chapter_id' => $validated['chapter_id'],
            'activity_type' => 'inline_edit_submitted',
            'description' => auth()->user()->name . " suggested a change to a paragraph in Chapter " . \App\Models\Chapter::find($validated['chapter_id'])->number . ".",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inline edit suggestion submitted successfully!',
            'inline_edit' => $inlineEdit,
        ]);
    }

    public function destroy($id)
    {
        $inlineEdit = InlineEdit::findOrFail($id);

        // Only the user who created it or an admin can delete it
        if (auth()->id() !== $inlineEdit->user_id && !auth()->user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inlineEdit->delete();

        return response()->json(['success' => true, 'message' => 'Inline edit deleted successfully!']);
    }
}
