<?php

namespace Database\Seeders;

use App\Models\FasilitasUmum;
use Illuminate\Database\Seeder;

/**
 * Seeder MASTER (produksi): daftar fasilitas umum RW.
 * Idempoten (updateOrCreate by nama).
 */
class FasilitasUmumSeeder extends Seeder
{
    public function run(): void
    {
        $fasilitas = [
            ['icon' => 'bi-mosque', 'nama' => 'Masjid Jami Al-Ikhlas', 'lokasi' => 'Pusat RT 04', 'urutan' => 1],
            ['icon' => 'bi-tree', 'nama' => 'Taman Bermain Anak', 'lokasi' => 'Area RT 02', 'urutan' => 2],
            ['icon' => 'bi-heart-pulse', 'nama' => 'Posyandu Flamboyan', 'lokasi' => 'Balai Warga, RT 01', 'urutan' => 3],
        ];

        foreach ($fasilitas as $item) {
            FasilitasUmum::updateOrCreate(
                ['nama' => $item['nama']],
                $item
            );
        }
    }
}
