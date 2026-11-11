<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Mengirim pengingat Iuran Lainnya (tagihan insidental) sesuai daftar tanggal
 * reminder tiap tagihan. Dijadwalkan harian (lihat routes/console.php).
 */
class KirimReminderIuranLainnya extends Command
{
    protected $signature = 'iuran:reminder-lainnya';

    protected $description = 'Kirim pengingat Iuran Lainnya ke KK yang belum lunas, sesuai jadwal tanggal tagihan.';

    public function handle(): int
    {
        $jumlah = NotificationService::reminderIuranLainnya();
        $this->info("Pengingat Iuran Lainnya terkirim: {$jumlah}.");

        return self::SUCCESS;
    }
}
