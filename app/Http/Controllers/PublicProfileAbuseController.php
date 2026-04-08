<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportPublicProfileRequest;
use App\Models\ProfileReport;
use App\Models\User;
use App\Models\UserBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PublicProfileAbuseController extends Controller
{
    public function report(ReportPublicProfileRequest $request, string $slug): RedirectResponse
    {
        $profileUser = User::query()
            ->where('public_profile_enabled', true)
            ->where('public_slug', $slug)
            ->firstOrFail();

        $viewer = $request->user();
        if ($viewer->id === $profileUser->id) {
            abort(403);
        }

        ProfileReport::query()->create([
            'reporter_id' => $viewer->id,
            'reported_user_id' => $profileUser->id,
            'category' => $request->validated()['category'],
            'details' => $request->validated()['details'] ?? null,
        ]);

        return redirect()
            ->route('profile.public', ['slug' => $slug])
            ->with('success', __('Thanks — we received your report. Moderators may follow up if more detail is needed.'));
    }

    public function block(Request $request, string $slug): RedirectResponse
    {
        $profileUser = User::query()
            ->where('public_profile_enabled', true)
            ->where('public_slug', $slug)
            ->firstOrFail();

        $viewer = $request->user();
        if ($viewer->id === $profileUser->id) {
            abort(403);
        }

        UserBlock::query()->firstOrCreate([
            'blocker_id' => $viewer->id,
            'blocked_id' => $profileUser->id,
        ]);

        return redirect()
            ->to(route('profile.edit').'#blocked-contributors')
            ->with('success', __('You will not see each other’s public profiles. You can remove the block below anytime.'));
    }

    public function unblock(Request $request, string $slug): RedirectResponse
    {
        $profileUser = User::query()
            ->where('public_profile_enabled', true)
            ->where('public_slug', $slug)
            ->firstOrFail();

        $viewer = $request->user();
        UserBlock::query()
            ->where('blocker_id', $viewer->id)
            ->where('blocked_id', $profileUser->id)
            ->delete();

        return redirect()
            ->route('profile.public', ['slug' => $slug])
            ->with('success', __('Block removed.'));
    }

    public function unblockByUser(Request $request, User $blocked): RedirectResponse
    {
        $viewer = $request->user();
        if ($viewer->id === $blocked->id) {
            abort(403);
        }

        UserBlock::query()
            ->where('blocker_id', $viewer->id)
            ->where('blocked_id', $blocked->id)
            ->delete();

        return redirect()
            ->to(route('profile.edit').'#blocked-contributors')
            ->with('success', __('Block removed.'));
    }
}
