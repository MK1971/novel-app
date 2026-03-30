<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Landing page — prize pool line
    |--------------------------------------------------------------------------
    |
    | Shown in the stats strip as the big number next to “Prize goal”. This is
    | an announced campaign/marketing figure (e.g. total planned giveaway), not
    | a running total from the database. Override via LANDING_PRIZE_POOL_DISPLAY.
    |
    */

    'landing_prize_pool_display' => env('LANDING_PRIZE_POOL_DISPLAY', '$5k'),

];
