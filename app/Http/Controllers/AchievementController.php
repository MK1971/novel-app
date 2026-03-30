<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Support\AchievementUnlock;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::all();
        $userAchievements = [];
        if (Auth::check()) {
            AchievementUnlock::syncForUser(Auth::user());
            $userAchievements = Auth::user()->achievements()->pluck('achievement_id')->toArray();
        }

        return view('achievements.index', compact('achievements', 'userAchievements'));
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
        if (Auth::check()) {
            AchievementUnlock::syncForUser(Auth::user());
        }

        return view('achievements.show', compact('achievement'));
    }
}
