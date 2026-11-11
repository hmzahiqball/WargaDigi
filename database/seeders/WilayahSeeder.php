<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder data referensi wilayah dari file database/data/wilayah.sql
 * (dataset cahyadsn/wilayah, Kepmendagri 2025).
 *
 * Membaca pasangan ('kode','nama') dari file lalu insert batch. Idempoten:
 * mengosongkan tabel lebih dulu. Jalankan terpisah:
 *   php artisan db:seed --class=WilayahSeeder
 */
class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('data/wilayah.sql');
        if (! is_file($file)) {
            $this->command?->error("File tidak ditemukan: {$file}");
            $this->command?->warn('Unduh dari: https://raw.githubusercontent.com/cahyadsn/wilayah/master/db/wilayah.sql');
            return;
        }

        // Lewati bila sudah terisi (data referensi statis & besar).
        if (DB::table('wilayah')->exists()) {
            $this->command?->info('Data wilayah sudah ada, seeding dilewati.');
            return;
        }

        $isi = file_get_contents($file);

        // Tangkap seluruh pasangan ('kode','nama') — nama bisa memuat '' (escaped quote).
        preg_match_all("/\\('([^']+)','((?:[^']|'')*)'\\)/", $isi, $m, PREG_SET_ORDER);

        if (empty($m)) {
            $this->command?->error('Tidak ada data wilayah yang terbaca dari file SQL.');
            return;
        }

        DB::table('wilayah')->truncate();

        $batch = [];
        $total = 0;
        foreach ($m as $row) {
            $batch[] = [
                'kode' => $row[1],
                'nama' => str_replace("''", "'", $row[2]),
            ];
            if (count($batch) >= 1000) {
                DB::table('wilayah')->insert($batch);
                $total += count($batch);
                $batch = [];
            }
        }
        if (! empty($batch)) {
            DB::table('wilayah')->insert($batch);
            $total += count($batch);
        }

        $this->command?->info("Seeder wilayah selesai: {$total} baris.");
    }
}
