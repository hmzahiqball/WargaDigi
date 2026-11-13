<?php

namespace App\Console\Commands;

use App\Services\DokumenSementara;
use Illuminate\Console\Command;

/**
 * Hapus berkas KTP/KK sementara (unggahan OCR) yang tidak pernah disimpan ke data.
 */
class BersihkanDokumenSementara extends Command
{
    protected $signature = 'dokumen:bersihkan-sementara {--jam= : Umur minimal berkas dalam jam (bawaan 24)}';

    protected $description = 'Hapus berkas KTP/KK sementara hasil unggahan OCR yang tidak jadi disimpan';

    public function handle(): int
    {
        $jam = $this->option('jam') !== null ? max(0, (int) $this->option('jam')) : null;
        $jumlah = DokumenSementara::bersihkanKedaluwarsa($jam);
        $this->info("{$jumlah} berkas sementara dihapus.");

        return self::SUCCESS;
    }
}
