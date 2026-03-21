<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::all();
        $userAchievements = [];
        if (Auth::check()) {
            $userAchievements = Auth::user()->achievements()->pluck('achievement_id')->toArray();
        }
        return view('achievements.index', compact('achievements', 'userAchievements'));
    }

    public function checkAndUnlock()
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        $achievements = Achievement::all();

        foreach ($achievements as $achievement) {
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            $hasAchievement = false;
            switch ($achievement->requirement_type) {
                case 'edits_accepted':
                    $acceptedEdits = $user->edits()->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])->count();
                    $hasAchievement = $acceptedEdits >= $achievement->requirement_value;
                    break;
                case 'votes_cast':
                    $votesCast = $user->votes()->count();
                    $hasAchievement = $votesCast >= $achievement->requirement_value;
                    break;
                case 'points_earned':
                    $hasAchievement = $user->points >= $achievement->requirement_value;
                    break;
                case 'chapters_read':
                    $chaptersRead = $user->readingProgress()->count();
                    $hasAchievement = $chaptersRead >= $achievement->requirement_value;
                    break;
            }

            if ($hasAchievement) {
                $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
            }
        }
    }
}
