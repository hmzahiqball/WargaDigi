<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Provider OCR aktif
    |--------------------------------------------------------------------------
    | Pilihan: 'ocrspace' (OCR.space HTTP API) atau 'null' (non-aktif / selalu
    | gagal baca sehingga user input manual). Fail-safe: bila provider error,
    | hasil selalu dianggap "tidak terbaca".
    */
    'provider' => env('OCR_PROVIDER', 'ocrspace'),

    // Bila true, teks mentah hasil OCR ditulis ke log (untuk debugging parser).
    'debug' => (bool) env('OCR_DEBUG', false),

    'ocrspace' => [
        'url'      => env('OCR_SPACE_URL', 'https://api.ocr.space/parse/image'),
        // 'helloworld' adalah key demo publik OCR.space (rate-limit ketat).
        // Ganti dengan API key sendiri di .env untuk produksi.
        'api_key'  => env('OCR_SPACE_API_KEY', 'helloworld'),
        'language' => env('OCR_SPACE_LANGUAGE', 'eng'),
        'engine'   => (int) env('OCR_SPACE_ENGINE', 2),
        'timeout'  => (int) env('OCR_SPACE_TIMEOUT', 30),
    ],
];
