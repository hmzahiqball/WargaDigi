<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys untuk Web Push
    |--------------------------------------------------------------------------
    |
    | Generate dengan: php artisan webpush:vapid (lihat command) atau
    | web-push generate-vapid-keys. Simpan di .env. Bila kosong, Web Push
    | tidak aktif (fail-safe) namun notifikasi in-app tetap berjalan.
    |
    */
    'subject'     => env('WEBPUSH_SUBJECT', env('APP_URL', 'mailto:admin@wargadigi.test')),
    'public_key'  => env('WEBPUSH_PUBLIC_KEY', ''),
    'private_key' => env('WEBPUSH_PRIVATE_KEY', ''),
];
