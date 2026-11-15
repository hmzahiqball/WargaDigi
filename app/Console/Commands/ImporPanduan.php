<?php

namespace App\Console\Commands;

use App\Services\Panduan\PanduanImportir;
use App\Services\Panduan\PanduanRepository;
use Illuminate\Console\Command;
use Throwable;

/**
 * Impor Manual Book (Word) menjadi isi Pusat Bantuan.
 *
 * Dijalankan di komputer pengembang setiap kali manual direvisi, lalu hasilnya
 * (database/data/panduan/panduan.json dan public/panduan/*) di-commit. Server memuat
 * JSON ke database otomatis saat Pusat Bantuan dibuka.
 */
class ImporPanduan extends Command
{
    protected $signature = 'panduan:impor
        {folder : Folder berisi WargaDigi21_Buku_XX_*.docx}
        {--tanpa-muat : Jangan langsung memuat hasil ke database lokal}';

    protected $description = 'Impor Manual Book (docx) menjadi topik Pusat Bantuan (JSON + gambar WebP)';

    public function handle(PanduanImportir $importir, PanduanRepository $repo): int
    {
        $folder = (string) $this->argument('folder');
        if (! is_dir($folder)) {
            $this->error("Folder tidak ditemukan: {$folder}");

            return self::FAILURE;
        }

        $this->info('Mengimpor Manual Book dari ' . $folder . ' ...');
        try {
            $hasil = $importir->impor($folder);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->table(['Buku', 'Topik'], collect($hasil['buku'])->map(fn ($n, $b) => [$b, $n])->values()->all());
        $this->line(sprintf('Total topik: %d | Gambar: %d (%.1f MB) | Versi: %s',
            $hasil['topik'], $hasil['gambar'], $hasil['ukuran_gambar'] / 1048576, substr($hasil['versi'], 0, 12)));
        if ($hasil['sisa_nama'] !== []) {
            $this->warn('Masih ada nama/akun demo di topik berikut (tambahkan aturan di PenyesuaianTeksPanduan bila perlu):');
            foreach ($hasil['sisa_nama'] as $baris) {
                $this->line('  - ' . $baris);
            }
        }
        $this->line('JSON  : ' . config('panduan.sumber'));
        $this->line('Gambar: ' . config('panduan.folder_gambar'));

        if (! $this->option('tanpa-muat')) {
            $repo->sinkron(true);
            $this->info('Hasil dimuat ke database lokal.');
        }

        return self::SUCCESS;
    }
}
