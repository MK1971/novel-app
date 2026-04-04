<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ReadingProgress;
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
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
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
                    'avatar' => match ($file->getError()) {
                        \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE => 'That photo is too large. Please use an image under 5 MB.',
                        default => 'The photo could not be uploaded. Try a JPG or PNG, or a smaller file.',
                    },
                ]);
            }
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $file->store('avatars', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
