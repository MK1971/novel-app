<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Support\AchievementUnlock;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        AchievementUnlock::ensureDefinitionsExist();

        $achievements = Achievement::all();
        $userAchievements = [];
        $progressByAchievementId = [];
        if (Auth::check()) {
            $authUser = Auth::user();
            AchievementUnlock::syncForUser($authUser);
            $userAchievements = $authUser->achievements()->pluck('achievement_id')->toArray();
            foreach ($achievements as $achievement) {
                $progressByAchievementId[$achievement->id] = AchievementUnlock::currentProgressToward($authUser, $achievement);
            }
        }

        return view('achievements.index', compact('achievements', 'userAchievements', 'progressByAchievementId'));
    }

    public function checkAndUnlock()
    {
        if (! Auth::check()) {
            return;
        }

        AchievementUnlock::syncForUser(Auth::user());
    }

    public function show(Achievement $achievement)
    {
        AchievementUnlock::ensureDefinitionsExist();

        $currentProgress = null;
        if (Auth::check()) {
            $authUser = Auth::user();
            AchievementUnlock::syncForUser($authUser);
            $currentProgress = AchievementUnlock::currentProgressToward($authUser, $achievement);
        }

        return view('achievements.show', compact('achievement', 'currentProgress'));
    }
}
