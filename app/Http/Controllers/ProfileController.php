<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PublicProfileSettingsRequest;
use App\Models\ReadingProgress;
use App\Support\UploadFailureMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $readingProgress = ReadingProgress::with('chapter')
            ->where('user_id', $user->id)
            ->whereHas('chapter') // Ensure only reading progress with existing chapters are fetched
            ->orderBy('last_read_at', 'desc')
            ->get();

        $chapterSubmissions = $user->chapterLevelEditsForStats()
            ->with('chapter.book')
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get();

        $paragraphSubmissions = $user->inlineEdits()
            ->with('chapter.book')
            ->orderByDesc('updated_at')
            ->limit(40)
            ->get();

        $submissionsTab = $request->query('tab') === 'submissions';

        return view('profile.show', [
            'user' => $user,
            'readingProgress' => $readingProgress,
            'chapterSubmissions' => $chapterSubmissions,
            'paragraphSubmissions' => $paragraphSubmissions,
            'submissionsTab' => $submissionsTab,
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('socialAccounts');
        $blockedContributors = $user->blocksInitiated()
            ->with(['blocked' => fn ($q) => $q->select('id', 'name', 'public_slug', 'public_profile_enabled')])
            ->orderByDesc('created_at')
            ->get();

        return view('profile.edit', [
            'user' => $user,
            'blockedContributors' => $blockedContributors,
        ]);
    }

    /**
     * Remove a linked Google or Apple sign-in. Blocked if it would leave no password and no other sign-in method.
     * (Apple remains in the allow-list for deferred enablement and any legacy linked rows.)
     */
    public function disconnectSocial(Request $request, string $provider): RedirectResponse
    {
        $provider = strtolower($provider);
        abort_unless(in_array($provider, ['google', 'apple'], true), 404);

        $user = $request->user();
        $account = $user->socialAccounts()->where('provider', $provider)->first();

        if (! $account) {
            return Redirect::route('profile.edit')->withErrors([
                'social' => __('That sign-in method is not linked to your account.'),
            ]);
        }

        if ($user->password === null && $user->socialAccounts()->count() <= 1) {
            return Redirect::route('profile.edit')->withErrors([
                'social' => __('Set a password in “Set a password” below first, so you can still sign in after disconnecting :provider.', ['provider' => ucfirst($provider)]),
            ]);
        }

        $account->delete();

        return Redirect::route('profile.edit')->with('status', 'social-disconnected');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = collect($request->validated())->except('avatar')->all();
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            if (! $file->isValid()) {
                return Redirect::route('profile.edit')->withErrors([
                    'avatar' => UploadFailureMessage::forInvalidUpload($file),
                ]);
            }
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            try {
                $user->avatar_path = $file->store('avatars', 'public');
            } catch (\Throwable $e) {
                report($e);

                return Redirect::route('profile.edit')->withErrors([
                    'avatar' => 'Could not save the photo. On the server, run php artisan storage:link and make sure storage/app/public (and the avatars folder inside it) is writable by the web server.',
                ]);
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update opt-in public contributor profile (P4-2).
     */
    public function updatePublicSettings(PublicProfileSettingsRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'public-profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
