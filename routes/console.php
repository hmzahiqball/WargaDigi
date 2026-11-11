<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Catatan: scheduler 'tagihan:generate-rutin' lama dinonaktifkan. Iuran rutin
// bulanan kini ditangani modul Iuran Wajib (grid tahunan), bukan generate tagihan.

// Pengingat Iuran Wajib ke KK yang belum lunas: tanggal 1, 10, dan 28.
Schedule::command('iuran:reminder')
    ->cron('0 8 1,10,28 * *')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Pengingat Iuran Lainnya sesuai daftar tanggal tiap tagihan: dicek tiap hari.
Schedule::command('iuran:reminder-lainnya')
    ->dailyAt('08:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
