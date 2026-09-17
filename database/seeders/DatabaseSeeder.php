<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MasterRtSeeder::class,
            UserSeeder::class,
            KeluargaSeeder::class,
            PendudukSeeder::class,
            BeritaSeeder::class,
            KategoriUmkmSeeder::class,
            UmkmUsahaSeeder::class,
            KategoriProdukSeeder::class,
            UmkmProdukSeeder::class,
            TransaksiKeuanganSeeder::class,
        ]);
    }
}