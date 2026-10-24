<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder DEMO (showcase).
 *
 * Menjalankan MasterSeeder lebih dahulu (agar seluruh data fondasi tersedia),
 * kemudian mengisi data dummy lengkap untuk kebutuhan demonstrasi aplikasi:
 * user demo, keluarga, penduduk, berita, agenda, pengumuman, UMKM, transaksi
 * keuangan, dan laporan keuangan.
 *
 * Dapat dipanggil manual: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterSeeder::class,

            // Data demo/showcase
            UserSeeder::class,
            KeluargaSeeder::class,
            PendudukSeeder::class,
            PendudukDemoSeeder::class,
            PejabatDemoSeeder::class,
            BeritaSeeder::class,
            DummyBeritaSeeder::class,
            DummyAgendaSeeder::class,
            DummyPengumumanSeeder::class,
            UmkmUsahaSeeder::class,
            UmkmProdukSeeder::class,
            TransaksiKeuanganSeeder::class,
            LaporanKeuanganDemoSeeder::class,
        ]);
    }
}
