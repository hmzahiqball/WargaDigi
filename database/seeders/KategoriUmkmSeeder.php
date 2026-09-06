<?php

namespace Database\Seeders;

use App\Models\KategoriUmkm;
use Illuminate\Database\Seeder;

class KategoriUmkmSeeder extends Seeder
{
    public function run(): void
    {
        $standar = ['Kuliner', 'Fashion', 'Jasa', 'Kerajinan', 'Koperasi'];

        foreach ($standar as $nama) {
            KategoriUmkm::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
