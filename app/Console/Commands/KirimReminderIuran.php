<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Mengirim pengingat iuran bulanan ke KK yang belum lunas pada bulan berjalan.
 * Dijadwalkan tiap tanggal 1, 10, dan 28 (lihat routes/console.php).
 */
class KirimReminderIuran extends Command
{
    protected $signature = 'iuran:reminder';

    protected $description = 'Kirim pengingat iuran bulanan ke KK yang belum lunas bulan ini.';

    public function handle(): int
    {
        $jumlah = NotificationService::reminderIuranBulanan();
        $this->info("Pengingat iuran terkirim ke {$jumlah} KK.");

        return self::SUCCESS;
    }
}
