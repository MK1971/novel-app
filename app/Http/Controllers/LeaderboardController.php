<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(): View
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');

        $eligible = User::query()
            ->where('email', '!=', $adminEmail)
            ->orderByDesc('points')
            ->orderBy('id');

        $users = (clone $eligible)->limit(20)->get();

        $rankedIds = (clone $eligible)->pluck('id');
        $yourRank = null;
        $yourPoints = null;
        if (auth()->check()) {
            $uid = auth()->id();
            $pos = $rankedIds->search(fn ($id) => (int) $id === (int) $uid);
            if ($pos !== false) {
                $yourRank = $pos + 1;
                $yourPoints = (int) auth()->user()->points;
            }
        }

        $totalRanked = $rankedIds->count();

        return view('leaderboard', compact('users', 'yourRank', 'yourPoints', 'totalRanked'));
    }
}
