<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder MASTER (produksi).
 *
 * Berisi HANYA data fondasi yang wajib ada di lingkungan produksi:
 * role, master RT, kategori UMKM/produk, pengaturan sistem, satu akun
 * superadmin, serta data master fasilitas umum dan layanan surat.
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
            MasterRtSeeder::class,
            KategoriUmkmSeeder::class,
            KategoriProdukSeeder::class,
            SystemSettingSeeder::class,
            SuperAdminSeeder::class,
            FasilitasUmumSeeder::class,
            LayananSuratSeeder::class,
        ]);
    }
}
