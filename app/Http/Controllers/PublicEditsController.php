<?php

namespace App\Http\Controllers;

use App\Models\Edit;
use App\Models\EditFeedback;
use App\Models\InlineEdit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicEditsController extends Controller
{
    public function index()
    {
        $chapterEdits = Edit::query()
            ->with(['user:id,name', 'chapter:id,book_id,chapter_number,title', 'chapter.book:id,name', 'feedback.user:id,name'])
            ->where('show_in_public_feed', true)
            ->whereIn('status', ['pending', 'accepted', 'accepted_partial', 'accepted_full', 'rejected'])
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get()
            ->map(fn (Edit $edit) => [
                'kind' => 'chapter',
                'id' => $edit->id,
                'user_id' => $edit->user_id,
                'updated_at' => $edit->updated_at,
                'status' => $edit->status,
                'user_name' => $edit->user?->name ?? 'Unknown',
                'chapter_label' => $edit->chapter?->readerHeadingLine() ?? 'Chapter removed',
                'excerpt' => mb_strimwidth((string) $edit->edited_text, 0, 220, '...'),
                'feedback' => $edit->feedback,
            ]);

        $inlineEdits = InlineEdit::query()
            ->with(['user:id,name', 'chapter:id,book_id,chapter_number,title', 'chapter.book:id,name', 'feedback.user:id,name'])
            ->where('show_in_public_feed', true)
            ->whereIn('status', ['pending', 'approved', 'rejected'])
            ->orderByDesc('updated_at')
            ->limit(100)
            ->get()
            ->map(fn (InlineEdit $edit) => [
                'kind' => 'inline',
                'id' => $edit->id,
                'user_id' => $edit->user_id,
                'updated_at' => $edit->updated_at,
                'status' => $edit->status,
                'user_name' => $edit->user?->name ?? 'Unknown',
                'chapter_label' => $edit->chapter?->readerHeadingLine() ?? 'Chapter removed',
                'excerpt' => mb_strimwidth((string) $edit->suggested_text, 0, 220, '...'),
                'feedback' => $edit->feedback,
            ]);

        $items = $chapterEdits
            ->concat($inlineEdits)
            ->sortByDesc('updated_at')
            ->take(80)
            ->values();

        return view('edits.public-feed', ['items' => $items]);
    }

    public function storeFeedback(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', 'in:chapter,inline'],
            'id' => ['required', 'integer', 'min:1'],
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $feedback = [
            'user_id' => $request->user()->id,
            'message' => $data['message'],
        ];

        if ($data['kind'] === 'chapter') {
            $edit = Edit::query()->whereKey($data['id'])->where('show_in_public_feed', true)->firstOrFail();
            if ((int) $edit->user_id === (int) $request->user()->id) {
                return back()->with('error', 'You cannot post feedback on your own edit.');
            }
            $feedback['edit_id'] = $data['id'];
        } else {
            $inlineEdit = InlineEdit::query()->whereKey($data['id'])->where('show_in_public_feed', true)->firstOrFail();
            if ((int) $inlineEdit->user_id === (int) $request->user()->id) {
                return back()->with('error', 'You cannot post feedback on your own edit.');
            }
            $feedback['inline_edit_id'] = $data['id'];
        }

        EditFeedback::create($feedback);

        return back()->with('success', 'Feedback posted.');
    }
}
