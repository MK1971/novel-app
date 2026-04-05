<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBlock;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    /**
     * Opt-in public contributor page (P4-2). No email or private account data.
     */
    public function show(string $slug): View
    {
        $user = User::query()
            ->where('public_profile_enabled', true)
            ->where('public_slug', $slug)
            ->firstOrFail();

        $viewer = auth()->user();
        if ($user->publicProfileBlockedFor($viewer)) {
            abort(404);
        }

        $viewerHasBlocked = $viewer
            && UserBlock::query()
                ->where('blocker_id', $viewer->id)
                ->where('blocked_id', $user->id)
                ->exists();

        $stats = [
            'points' => (int) ($user->points ?? 0),
            'accepted' => $user->acceptedChapterAndParagraphEditCount(),
            'submitted' => $user->editSuggestionsSubmittedCount(),
        ];

        return view('profile.public', [
            'profileUser' => $user,
            'stats' => $stats,
            'viewerHasBlocked' => $viewerHasBlocked,
        ]);
    }
}
