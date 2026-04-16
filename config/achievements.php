<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default achievement definitions
    |--------------------------------------------------------------------------
    |
    | Used by DatabaseSeeder and by AchievementUnlock when the achievements
    | table is empty (e.g. migrate without --seed). Sync is idempotent per name.
    |
    */

    'definitions' => [
        [
            'name' => 'First steps',
            'description' => 'Open a chapter and start reading with the community.',
            'icon_emoji' => '📖',
            'requirement_type' => 'chapters_read',
            'requirement_value' => 1,
        ],
        [
            'name' => 'Paid to play',
            'description' => 'Completed a $2 edit checkout — your Peter Trull ballot credit is ready.',
            'icon_emoji' => '💳',
            'requirement_type' => 'completed_payments',
            'requirement_value' => 1,
        ],
        [
            'name' => 'Contributor',
            'description' => 'Have at least one edit accepted into the manuscript.',
            'icon_emoji' => '✏️',
            'requirement_type' => 'edits_accepted',
            'requirement_value' => 1,
        ],
        [
            'name' => 'Detective’s ballot',
            'description' => 'Cast your first vote on Peter Trull.',
            'icon_emoji' => '🗳️',
            'requirement_type' => 'votes_cast',
            'requirement_value' => 1,
        ],
        [
            'name' => 'Rising voice',
            'description' => 'Reach 10 leaderboard points.',
            'icon_emoji' => '🏆',
            'requirement_type' => 'points_earned',
            'requirement_value' => 10,
        ],
        [
            'name' => 'Hall of fame entrant',
            'description' => 'Reach Top 50 by accepted replacements.',
            'icon_emoji' => '🏅',
            'requirement_type' => 'accepted_rank_at_or_better',
            'requirement_value' => 50,
        ],
        [
            'name' => 'Signed print range',
            'description' => 'Reach Top 10 by accepted replacements.',
            'icon_emoji' => '📚',
            'requirement_type' => 'accepted_rank_at_or_better',
            'requirement_value' => 10,
        ],
        [
            'name' => 'Podium finisher',
            'description' => 'Reach Top 3 by accepted replacements.',
            'icon_emoji' => '🥉',
            'requirement_type' => 'accepted_rank_at_or_better',
            'requirement_value' => 3,
        ],
        [
            'name' => 'Cover leader',
            'description' => 'Reach #1 by accepted replacements.',
            'icon_emoji' => '✨',
            'requirement_type' => 'accepted_rank_at_or_better',
            'requirement_value' => 1,
        ],
    ],

];
