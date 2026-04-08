<?php

namespace App\Http\Controllers;

use App\Models\Edit;
use App\Models\InlineEdit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfileSubmissionVisibilityController extends Controller
{
    public function update(Request $request, string $kind, int $id): RedirectResponse
    {
        $data = $request->validate([
            'show_in_public_feed' => ['required', 'boolean'],
        ]);

        if ($kind === 'chapter') {
            $submission = Edit::query()
                ->whereKey($id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        } elseif ($kind === 'inline') {
            $submission = InlineEdit::query()
                ->whereKey($id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();
        } else {
            abort(404);
        }

        $submission->update(['show_in_public_feed' => (bool) $data['show_in_public_feed']]);

        return back()->with('status', 'submission-visibility-updated');
    }
}
