<?php

namespace App\Support;

use App\Models\Edit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LeaderboardScoring
{
    public const PERIOD_ALL = 'all';

    public const PERIOD_30_DAYS = '30d';

    /**
     * Points earned from paid, accepted full-chapter edits (by edit approval time).
     *
     * @return Collection<int, int> user_id => points
     */
    public static function editPointsSince(Carbon $since, ?int $excludeUserId): Collection
    {
        $q = Edit::query()
            ->select('user_id', DB::raw('SUM(points_awarded) as pts'))
            ->whereIn('status', ['accepted_full', 'accepted_partial'])
            ->where('points_awarded', '>', 0)
            ->where('updated_at', '>=', $since);

        if ($excludeUserId !== null) {
            $q->where('user_id', '!=', $excludeUserId);
        }

        return $q->groupBy('user_id')->pluck('pts', 'user_id')->map(fn ($v) => (int) $v);
    }

    /**
     * Points from approved paragraph edits with completed payment (by moderation time).
     *
     * @return Collection<int, int> user_id => points
     */
    public static function inlinePointsSince(Carbon $since, ?int $excludeUserId): Collection
    {
        $q = DB::table('inline_edits')
            ->join('payments', 'payments.id', '=', 'inline_edits.payment_id')
            ->where('inline_edits.status', 'approved')
            ->where('payments.status', 'completed')
            ->whereNotNull('inline_edits.payment_id')
            ->where('inline_edits.updated_at', '>=', $since);

        if ($excludeUserId !== null) {
            $q->where('inline_edits.user_id', '!=', $excludeUserId);
        }

        return $q
            ->select(
                'inline_edits.user_id',
                DB::raw('SUM(CASE WHEN inline_edits.moderation_outcome = \'partial\' THEN 1 ELSE 2 END) as pts')
            )
            ->groupBy('inline_edits.user_id')
            ->pluck('pts', 'user_id')
            ->map(fn ($v) => (int) $v);
    }

    /**
     * @return Collection<int, int> user_id => total points in window
     */
    public static function periodPointsByUserId(Carbon $since, ?int $excludeUserId): Collection
    {
        $merged = collect();

        foreach (self::editPointsSince($since, $excludeUserId) as $uid => $pts) {
            $merged[(int) $uid] = $pts;
        }

        foreach (self::inlinePointsSince($since, $excludeUserId) as $uid => $pts) {
            $uid = (int) $uid;
            $merged[$uid] = ($merged[$uid] ?? 0) + $pts;
        }

        return $merged->sortDesc();
    }
}
