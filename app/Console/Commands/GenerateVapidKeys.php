<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

/**
 * Generate pasangan kunci VAPID untuk Web Push, lalu tampilkan agar bisa
 * disalin ke .env (WEBPUSH_PUBLIC_KEY / WEBPUSH_PRIVATE_KEY).
 *
 * Jalankan di server saat deploy: php artisan webpush:vapid
 */
class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';

    protected $description = 'Generate VAPID keys untuk Web Push (salin ke .env)';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->info('VAPID keys berhasil dibuat. Salin ke file .env:');
        $this->newLine();
        $this->line('WEBPUSH_PUBLIC_KEY=' . $keys['publicKey']);
        $this->line('WEBPUSH_PRIVATE_KEY=' . $keys['privateKey']);
        $this->newLine();
        $this->warn('Simpan privateKey dengan aman dan jangan commit ke git.');

        return self::SUCCESS;
    }
}
