<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Legal identity and policy metadata
    |--------------------------------------------------------------------------
    |
    | Keep these values aligned with your registered business details.
    | Views in the legal hub read from this config so details stay consistent.
    |
    */
    'entity_name' => env('LEGAL_ENTITY_NAME', 'WhatsMyBookName'),
    'entity_address' => env('LEGAL_ENTITY_ADDRESS', ''),
    'contact_email' => env('LEGAL_CONTACT_EMAIL', env('ADMIN_EMAIL', '')),
    'jurisdiction' => env('LEGAL_JURISDICTION', 'State of Israel'),
    'dispute_notice_days' => (int) env('LEGAL_DISPUTE_NOTICE_DAYS', 30),
];
