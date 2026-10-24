<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// IR39: Generate tagihan rutin bulanan (Iuran RT & Dana Kematian DKM) otomatis setiap tanggal 1.
Schedule::command('tagihan:generate-rutin')
    ->monthlyOn(1, '00:05')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
