<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | WhatsApp Gateway (IR51)
    | Fail-safe: bila url/token kosong, pengiriman WA hanya dicatat ke log
    | dan fitur lain (notifikasi in-app) tetap berjalan normal.
    | Mendukung gateway generik berbasis HTTP (mis. Fonnte, Wablas, dsb).
    */
    'whatsapp' => [
        'url' => env('WA_GATEWAY_URL'),
        'token' => env('WA_GATEWAY_TOKEN'),
        'sender' => env('WA_GATEWAY_SENDER'),
        'timeout' => (int) env('WA_GATEWAY_TIMEOUT', 10),
    ],

];
