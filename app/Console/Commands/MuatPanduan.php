<?php

namespace App\Console\Commands;

use App\Services\Panduan\PanduanRepository;
use Illuminate\Console\Command;

/**
 * Muat ulang tabel panduan_topik dari JSON hasil impor. Biasanya tidak perlu dijalankan
 * manual karena Pusat Bantuan menyinkronkan sendiri bila versi JSON berubah.
 */
class MuatPanduan extends Command
{
    protected $signature = 'panduan:muat';

    protected $description = 'Muat ulang isi Pusat Bantuan dari JSON hasil impor';

    public function handle(PanduanRepository $repo): int
    {
        if (! is_file((string) config('panduan.sumber'))) {
            $this->error('File panduan belum ada: ' . config('panduan.sumber'));

            return self::FAILURE;
        }
        $repo->sinkron(true);
        $this->info('Pusat Bantuan dimuat ulang: ' . \App\Models\PanduanTopik::count() . ' topik.');

        return self::SUCCESS;
    }
}
