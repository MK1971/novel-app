<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Chapter;
use App\Support\TextDiff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditDiffPreviewController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chapter_id' => ['required', 'integer', 'exists:chapters,id'],
            'edited_text' => ['required', 'string', 'max:600000'],
        ]);

        $chapter = Chapter::query()->findOrFail($validated['chapter_id']);
        $chapter->loadMissing('book');
        if ($chapter->book && $chapter->book->name === Book::NAME_PETER_TRULL) {
            return response()->json(['ok' => false, 'message' => 'Diff preview is for manuscript chapters only.'], 422);
        }

        $diff = TextDiff::linesForDisplay($chapter->content, $validated['edited_text']);

        if ($diff === null) {
            return response()->json(['ok' => false, 'message' => 'Text is too long to diff in the browser.'], 422);
        }

        return response()->json(['ok' => true, 'diff' => $diff]);
    }
}
