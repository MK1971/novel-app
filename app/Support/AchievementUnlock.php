<?php

namespace App\Support;

use App\Models\Achievement;
use App\Models\Payment;
use App\Models\User;
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

        if (Achievement::query()->exists()) {
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
            default => 0,
        };
    }
}
