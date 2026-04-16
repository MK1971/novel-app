<?php

namespace App\Support;

use App\Models\Achievement;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Single place to evaluate and attach eligible achievements.
 */
class AchievementUnlock
{
    /**
     * Ensure catalog rows exist when the DB was migrated without seeding.
     */
    public static function ensureDefinitionsExist(): void
    {
        if (! Schema::hasTable('achievements')) {
            return;
        }

        $definitions = config('achievements.definitions', []);
        if (! is_array($definitions) || $definitions === []) {
            return;
        }

        foreach ($definitions as $row) {
            if (! is_array($row) || empty($row['name'])) {
                continue;
            }
            Achievement::updateOrCreate(
                ['name' => $row['name']],
                $row
            );
        }
    }

    public static function syncForUser(User $user): void
    {
        self::ensureDefinitionsExist();

        foreach (Achievement::all() as $achievement) {
            if ($user->achievements()->where('achievement_id', $achievement->id)->exists()) {
                continue;
            }

            if (self::userMeetsRequirement($user, $achievement)) {
                $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
            }
        }
    }

    public static function userMeetsRequirement(User $user, Achievement $achievement): bool
    {
        return match ($achievement->requirement_type) {
            'edits_accepted' => $user->edits()
                ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
                ->count() >= $achievement->requirement_value,
            'votes_cast' => $user->votes()->count() >= $achievement->requirement_value,
            'points_earned' => $user->points >= $achievement->requirement_value,
            'chapters_read' => $user->readingProgress()->count() >= $achievement->requirement_value,
            'completed_payments' => Payment::query()
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count() >= $achievement->requirement_value,
            'accepted_rank_at_or_better' => ($rank = self::acceptedRankForUser($user)) !== null
                && $rank <= $achievement->requirement_value,
            default => false,
        };
    }

    /**
     * Current numeric progress toward an achievement (same metrics as {@see userMeetsRequirement}).
     */
    public static function currentProgressToward(User $user, Achievement $achievement): int
    {
        return match ($achievement->requirement_type) {
            'edits_accepted' => $user->edits()
                ->whereIn('status', ['accepted', 'accepted_full', 'accepted_partial'])
                ->count(),
            'votes_cast' => $user->votes()->count(),
            'points_earned' => (int) $user->points,
            'chapters_read' => $user->readingProgress()->count(),
            'completed_payments' => Payment::query()
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'accepted_rank_at_or_better' => (int) (self::acceptedRankForUser($user) ?? 0),
            default => 0,
        };
    }

    private static function acceptedRankForUser(User $user): ?int
    {
        $adminEmail = (string) config('app.admin_email', 'admin@example.com');
        $excludeUserId = User::query()->where('email', $adminEmail)->value('id');
        $hiddenUserIds = User::query()
            ->where('leaderboard_visible', false)
            ->pluck('id')
            ->all();

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
            ->select('users.id')
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
        if ($hiddenUserIds !== []) {
            $query->whereNotIn('users.id', $hiddenUserIds);
        }

        $rankedIds = $query->pluck('users.id')->values();
        $pos = $rankedIds->search(fn ($id) => (int) $id === (int) $user->id);

        return $pos === false ? null : $pos + 1;
    }
}
