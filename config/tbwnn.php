<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pilot chapter (first TBWNN upload)
    |--------------------------------------------------------------------------
    |
    | When a chapter is marked is_pilot, paid editing closes after a count of
    | accepted suggestions (story + inline), not by the calendar deadline.
    | Set is_pilot on the chapter row (admin upload) and optionally clear the
    | one-month deadline for that row.
    |
    */
    'pilot' => [
        'close_after_accepted_edits' => (int) env('TBWNN_PILOT_CLOSE_AFTER_ACCEPTED', 50),
    ],

];
