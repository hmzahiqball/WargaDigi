<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder DEMO (showcase).
 *
 * Menjalankan MasterSeeder lebih dahulu (agar seluruh data fondasi tersedia),
 * kemudian mengisi data demo yang sesuai dengan aturan aplikasi terbaru:
 * akun per peran, keluarga dan penduduk, pejabat RT/RW, konten (berita, agenda,
 * galeri, pengumuman), UMKM, keuangan (rekening, iuran, tagihan, transaksi,
 * laporan), dan contoh surat.
 *
 * Aman dijalankan ulang (idempotent). Semua kata sandi akun demo: "password".
 *
 * Dapat dipanggil manual: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MasterSeeder::class,

            // Akun dan kependudukan
            UserSeeder::class,
            KeluargaSeeder::class,
            PendudukSeeder::class,
            PendudukDemoSeeder::class,
            PejabatDemoSeeder::class,

            // Konten
            KontenDemoSeeder::class,

            // UMKM
            UmkmUsahaSeeder::class,
            UmkmProdukSeeder::class,

            // Keuangan
            KeuanganDemoSeeder::class,
            TransaksiKeuanganSeeder::class,
            LaporanKeuanganDemoSeeder::class,

            // Layanan surat
            ContohSuratSeeder::class,
        ]);
    }
}
