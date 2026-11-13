<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder MASTER (produksi).
 *
 * Berisi HANYA data fondasi yang wajib ada di lingkungan produksi:
 * role, wilayah, master RT, kategori UMKM/produk/transaksi/konten, pengaturan
 * sistem, satu akun superadmin, serta data master fasilitas umum.
 *
 * TIDAK berisi data dummy/showcase. Untuk data demo gunakan DemoSeeder.
 *
 * Dapat dipanggil manual: php artisan db:seed --class=MasterSeeder
 */
class MasterSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            WilayahSeeder::class,
            MasterRtSeeder::class,
            KategoriUmkmSeeder::class,
            KategoriProdukSeeder::class,
            KategoriTransaksiSeeder::class,
            KategoriKontenSeeder::class,
            SystemSettingSeeder::class,
            SuperAdminSeeder::class,
            FasilitasUmumSeeder::class,
        ]);
    }
}
