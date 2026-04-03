<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LeaderboardScoring;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');

        $period = $request->query('period', LeaderboardScoring::PERIOD_ALL);
        if (! in_array($period, [LeaderboardScoring::PERIOD_ALL, LeaderboardScoring::PERIOD_30_DAYS], true)) {
            $period = LeaderboardScoring::PERIOD_ALL;
        }

        $adminUser = User::query()->where('email', $adminEmail)->first();
        $excludeUserId = $adminUser?->id;

        if ($period === LeaderboardScoring::PERIOD_30_DAYS) {
            $since = Carbon::now()->subDays(30);
            $pointsByUser = LeaderboardScoring::periodPointsByUserId($since, $excludeUserId)
                ->filter(fn (int $p) => $p > 0);

            $totalRanked = $pointsByUser->count();

            $topIds = $pointsByUser->keys()->take(20)->values();
            $users = User::query()
                ->whereIn('id', $topIds)
                ->get()
                ->sortByDesc(fn (User $u) => $pointsByUser[$u->id] ?? 0)
                ->values();

            foreach ($users as $u) {
                $u->setAttribute('display_points', (int) ($pointsByUser[$u->id] ?? 0));
            }

            $yourRank = null;
            $yourPoints = null;
            if (auth()->check()) {
                $uid = (int) auth()->id();
                $sortedIds = $pointsByUser->keys()->values();
                $pos = $sortedIds->search(fn ($id) => (int) $id === $uid);
                if ($pos !== false) {
                    $yourRank = $pos + 1;
                    $yourPoints = (int) ($pointsByUser[$uid] ?? 0);
                }
            }
        } else {
            $eligible = User::query()
                ->where('email', '!=', $adminEmail)
                ->orderByDesc('points')
                ->orderBy('id');

            $users = (clone $eligible)->limit(20)->get();
            foreach ($users as $u) {
                $u->setAttribute('display_points', (int) $u->points);
            }

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
        }

        return view('leaderboard', compact('users', 'yourRank', 'yourPoints', 'totalRanked', 'period'));
    }
}
