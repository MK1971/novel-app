<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Peter Trull pilot voting round
    |--------------------------------------------------------------------------
    |
    | When a Peter Trull pair is marked is_pilot, voting closes after a count
    | of total votes across Version A + Version B for that pair, instead of
    | the normal calendar window.
    |
    */
    'pilot' => [
        'close_after_votes' => (int) env('PETER_TRULL_PILOT_CLOSE_AFTER_VOTES', 50),
    ],

];

