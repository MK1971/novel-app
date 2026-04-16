<?php

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    // Only enable this when a misconfigured proxy breaks PayPal requests.
    // Default false keeps system/corporate proxy behavior intact.
    'disable_proxy_env' => env('PAYPAL_DISABLE_PROXY_ENV', false),
    // Optional DNS override for environments that cannot resolve PayPal hosts.
    // Example: PAYPAL_FORCE_API_IP=146.75.119.1
    'force_api_ip' => env('PAYPAL_FORCE_API_IP', ''),
    'sandbox' => [
        // Support both explicit sandbox keys and generic PAYPAL_CLIENT_* keys.
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', env('PAYPAL_CLIENT_ID')),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', env('PAYPAL_CLIENT_SECRET')),
        'app_id' => 'APP-80W284485P519543T',
    ],
    'live' => [
        // Support both explicit live keys and generic PAYPAL_CLIENT_* keys.
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', env('PAYPAL_CLIENT_ID')),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', env('PAYPAL_CLIENT_SECRET')),
        'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
    ],
    'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'),
    'currency' => env('PAYPAL_CURRENCY', 'USD'),
    'notify_url' => env('PAYPAL_NOTIFY_URL', ''),
    'locale' => env('PAYPAL_LOCALE', 'en_US'),
    'validate_ssl' => env('PAYPAL_VALIDATE_SSL', true),
];
