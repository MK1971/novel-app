<?php

namespace App\Http\Controllers;

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

        $inlineEdit = \App\Models\InlineEdit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Inline edit suggestion submitted successfully!',
            'inline_edit' => $inlineEdit,
        ]);
    }

    public function destroy($id)
    {
        $inlineEdit = \App\Models\InlineEdit::findOrFail($id);

        // Only the user who created it or an admin can delete it
        if (auth()->id() !== $inlineEdit->user_id && auth()->user()->email !== 'admin@example.com') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inlineEdit->delete();

        return response()->json(['success' => true, 'message' => 'Inline edit deleted successfully!']);
    }
}
