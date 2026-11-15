<?php

/*
|--------------------------------------------------------------------------
| Pusat Bantuan (panduan aplikasi)
|--------------------------------------------------------------------------
|
| Isi panduan berasal dari Manual Book (Word) yang diimpor dengan
|   php artisan panduan:impor "<folder berisi WargaDigi21_Buku_XX_*.docx>"
| Hasil impor (JSON + gambar WebP) ikut Git sehingga ikut ter-deploy, lalu dimuat
| otomatis ke tabel panduan_topik saat Pusat Bantuan dibuka (bila versinya berubah).
|
*/

return [
    // Hasil impor yang dimuat ke database.
    'sumber' => database_path('data/panduan/panduan.json'),

    // Folder gambar panduan (publik; screenshot memakai data demo fiktif).
    'folder_gambar' => public_path('panduan'),
    'url_gambar' => 'panduan',

    // Profil konversi gambar ke WebP.
    'gambar' => ['lebar' => 1366, 'kualitas' => 78],

    // Nomor buku pada nama file Word -> peran yang melihatnya. 'Semua' = semua peran.
    'buku' => [
        '00' => 'Semua',
        '01' => 'Warga',
        '02' => 'Ketua RT',
        '03' => 'Op Konten RT',
        '04' => 'Op Keuangan RT',
        '05' => 'Admin RW',
        '06' => 'Ketua RW',
        '07' => 'Op Konten RW',
        '08' => 'Op Keuangan RW',
        '09' => 'DKM',
        '10' => 'Admin Aplikasi',
    ],
];
