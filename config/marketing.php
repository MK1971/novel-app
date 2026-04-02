<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Landing page — prize pool line
    |--------------------------------------------------------------------------
    |
    | Shown in the stats strip as the big number next to “Fund goal”. This is
    | an announced campaign/marketing figure (e.g. total planned giveaway), not
    | a running total from the database. Override via LANDING_PRIZE_POOL_DISPLAY.
    |
    */

    'landing_prize_pool_display' => env('LANDING_PRIZE_POOL_DISPLAY', '$50k'),

    /** When true and all landing counters are zero, soften the stats strip copy. */
    'landing_soft_stats_when_empty' => env('LANDING_SOFT_STATS_WHEN_EMPTY', true),

];
