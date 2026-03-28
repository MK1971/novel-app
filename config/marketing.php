<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Landing page — prize pool line
    |--------------------------------------------------------------------------
    |
    | Shown in the stats strip. This is a campaign / marketing figure, not a
    | live database tally. Override via LANDING_PRIZE_POOL_DISPLAY in .env.
    |
    */

    'landing_prize_pool_display' => env('LANDING_PRIZE_POOL_DISPLAY', '$5k'),

];
