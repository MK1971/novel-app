<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\LeaderboardScoring;
use App\Support\ChapterLifecycle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $adminEmail = (string) config('app.admin_email', 'admin@example.com');

        $period = $request->query('period', LeaderboardScoring::PERIOD_ALL);
        if (! in_array($period, [LeaderboardScoring::PERIOD_ALL, LeaderboardScoring::PERIOD_30_DAYS], true)) {
            $period = LeaderboardScoring::PERIOD_ALL;
        }

        $adminUser = User::query()->where('email', $adminEmail)->first();
        $excludeUserId = $adminUser?->id;

        $leaderboardHiddenUserIds = User::query()
            ->where('leaderboard_visible', false)
            ->pluck('id')
            ->all();

        if ($period === LeaderboardScoring::PERIOD_30_DAYS) {
            $since = Carbon::now()->subDays(30);
            $pointsByUser = LeaderboardScoring::periodPointsByUserId($since, $excludeUserId)
                ->except($leaderboardHiddenUserIds)
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
                ->where('leaderboard_visible', true)
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

        $acceptedCountsByUser = $this->acceptedCountsForUserIds($users->pluck('id')->all());
        $acceptedPrizeRows = $this->acceptedPrizeRankingRows($excludeUserId, $leaderboardHiddenUserIds);
        $acceptedPrizeLeaders = $acceptedPrizeRows->take(3)->values();
        $acceptedPrizeTopTen = $acceptedPrizeRows->take(10)->values();
        $acceptedRankByUserId = [];
        foreach ($acceptedPrizeRows as $idx => $row) {
            $acceptedRankByUserId[(int) $row->id] = $idx + 1;
        }

        $yourAcceptedCount = null;
        $yourAcceptedPrizeRank = null;
        if (auth()->check()) {
            $yourId = (int) auth()->id();
            $yourAcceptedCount = (int) ($acceptedCountsByUser[$yourId] ?? $this->acceptedCountsForUserIds([$yourId])[$yourId] ?? 0);
            $yourAcceptedPrizeRank = $acceptedRankByUserId[$yourId] ?? null;
        }

        return view('leaderboard', compact(
            'users',
            'yourRank',
            'yourPoints',
            'totalRanked',
            'period',
            'acceptedCountsByUser',
            'yourAcceptedCount',
            'acceptedPrizeLeaders',
            'acceptedPrizeTopTen',
            'yourAcceptedPrizeRank'
        ));
    }

    /**
     * @param  array<int>  $leaderboardHiddenUserIds
     * @return Collection<int, object{id:int,name:string,public_profile_enabled:bool,public_slug:?string,points:int,accepted_total:int}>
     */
    private function acceptedPrizeRankingRows(?int $excludeUserId, array $leaderboardHiddenUserIds): Collection
    {
        $chapterAccepted = DB::table('edits')
            ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
            ->where('type', '!=', 'inline_edit')
            ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
            ->groupBy('user_id');

        $inlineAccepted = DB::table('inline_edits')
            ->select('user_id', DB::raw('COUNT(*) as accepted_total'))
            ->where('status', 'approved')
            ->groupBy('user_id');

        $query = User::query()
            ->select('users.id', 'users.name', 'users.public_profile_enabled', 'users.public_slug', 'users.points')
            ->selectRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) as accepted_total')
            ->leftJoinSub($chapterAccepted, 'ch', function ($join): void {
                $join->on('ch.user_id', '=', 'users.id');
            })
            ->leftJoinSub($inlineAccepted, 'ia', function ($join): void {
                $join->on('ia.user_id', '=', 'users.id');
            })
            ->where('users.leaderboard_visible', true)
            ->whereRaw('(COALESCE(ch.accepted_total, 0) + COALESCE(ia.accepted_total, 0)) > 0')
            ->orderByDesc('accepted_total')
            ->orderByDesc('users.points')
            ->orderBy('users.id');

        if ($excludeUserId) {
            $query->where('users.id', '!=', $excludeUserId);
        }
        if ($leaderboardHiddenUserIds !== []) {
            $query->whereNotIn('users.id', $leaderboardHiddenUserIds);
        }

        return $query->get();
    }

    /**
     * @param  list<int>  $userIds
     * @return array<int, int>
     */
    private function acceptedCountsForUserIds(array $userIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $userIds)));
        if ($ids === []) {
            return [];
        }

        $chapterAccepted = DB::table('edits')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->whereIn('user_id', $ids)
            ->where('type', '!=', 'inline_edit')
            ->whereIn('status', ChapterLifecycle::ACCEPTED_EDIT_STATUSES)
            ->groupBy('user_id')
            ->pluck('c', 'user_id')
            ->all();

        $inlineAccepted = DB::table('inline_edits')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->whereIn('user_id', $ids)
            ->where('status', 'approved')
            ->groupBy('user_id')
            ->pluck('c', 'user_id')
            ->all();

        $out = [];
        foreach ($ids as $id) {
            $out[$id] = (int) ($chapterAccepted[$id] ?? 0) + (int) ($inlineAccepted[$id] ?? 0);
        }

        return $out;
    }
}
