<?php

namespace Database\Seeders;

use App\Models\KategoriUmkm;
use App\Models\UmkmUsaha;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder DEMO: usaha UMKM warga.
 *
 * Pemilik adalah akun warga demo (budisampurno dan budisantoso, keduanya RT 03),
 * sehingga alamat usaha disesuaikan ke RT 03 / RW 21. Kategori diambil dari master
 * (KategoriUmkmSeeder). Status dibuat beragam agar alur persetujuan Admin RW dapat
 * didemonstrasikan: Approved, Pending, Rejected (bisa diajukan ulang), dan nonaktif.
 */
class UmkmUsahaSeeder extends Seeder
{
    public function run(): void
    {
        $nikSampurno = User::where('username', 'budisampurno')->value('nik');
        $nikSantoso = User::where('username', 'budisantoso')->value('nik');

        if (! $nikSampurno || ! $nikSantoso) {
            $this->command?->warn('Akun warga demo belum ada. UmkmUsahaSeeder dilewati.');

            return;
        }

        $kategori = fn (string $nama) => KategoriUmkm::firstOrCreate(['nama_kategori' => $nama])->id;

        $usahas = [
            [
                'id' => '9b7f5e41-0123-4567-89ab-cdef01234567',
                'nik' => $nikSampurno,
                'nama_usaha' => 'Kriya Kayu Jati Tanimulya',
                'kategori_umkm_id' => $kategori('Kerajinan'),
                'deskripsi' => 'Kerajinan pahat dan ukiran kayu jati. Menerima pesanan furnitur custom, patung hiasan, dan panel dinding.',
                'alamat_usaha' => 'Gg. Kenanga No. 45, RT 03 / RW 21, Desa Tanimulya',
                'no_wa' => '6281987654321',
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Usaha telah diverifikasi dan memenuhi syarat.',
                'klikWA' => 15,
                'is_active' => true,
            ],
            [
                'id' => '9b7f5e42-0123-4567-89ab-cdef01234567',
                'nik' => $nikSantoso,
                'nama_usaha' => 'Warung Nasi Ibu Ani',
                'kategori_umkm_id' => $kategori('Kuliner'),
                'deskripsi' => 'Aneka masakan khas Sunda: nasi timbel, ayam bakar komplit, dan jajanan pasar.',
                'alamat_usaha' => 'Jl. Melati No. 12, RT 03 / RW 21, Desa Tanimulya',
                'no_wa' => '6281234567890',
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Data usaha valid dan terverifikasi.',
                'klikWA' => 42,
                'is_active' => true,
            ],
            [
                'id' => '9b7f5e43-0123-4567-89ab-cdef01234567',
                'nik' => $nikSantoso,
                'nama_usaha' => 'Fashion Muslimah Aini',
                'kategori_umkm_id' => $kategori('Fashion'),
                'deskripsi' => 'Gamis, jilbab syari, dan pakaian muslimah dengan bahan adem untuk dipakai sehari-hari.',
                'alamat_usaha' => 'Jl. Mawar No. 8, RT 03 / RW 21, Desa Tanimulya',
                'no_wa' => '6285712345678',
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Berkas lengkap dan sesuai.',
                'klikWA' => 8,
                'is_active' => true,
            ],
            [
                // Menunggu persetujuan Admin RW.
                'id' => '9b7f5e44-0123-4567-89ab-cdef01234567',
                'nik' => $nikSampurno,
                'nama_usaha' => 'Jasa Service Elektronik Pak Budi',
                'kategori_umkm_id' => $kategori('Jasa'),
                'deskripsi' => 'Perbaikan AC, mesin cuci, kulkas, dan peralatan elektronik rumah tangga dengan garansi pengerjaan.',
                'alamat_usaha' => 'Gg. Kenanga No. 45, RT 03 / RW 21, Desa Tanimulya',
                'no_wa' => '6282111223344',
                'status_verifikasi' => 'Pending',
                'catatan_verifikasi' => null,
                'klikWA' => 0,
                'is_active' => false,
            ],
            [
                // Contoh pengajuan ditolak; pemilik dapat memperbaiki lalu mengajukan ulang.
                'id' => '9b7f5e45-0123-4567-89ab-cdef01234567',
                'nik' => $nikSantoso,
                'nama_usaha' => 'Koperasi Warga Tanimulya',
                'kategori_umkm_id' => $kategori('Koperasi'),
                'deskripsi' => 'Simpan pinjam warga dan penjualan sembako.',
                'alamat_usaha' => 'Jl. Melati No. 12, RT 03 / RW 21, Desa Tanimulya',
                'no_wa' => '6283899887766',
                'status_verifikasi' => 'Rejected',
                'catatan_verifikasi' => 'Lengkapi deskripsi pengurus koperasi dan foto tempat usaha, lalu ajukan ulang.',
                'klikWA' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($usahas as $data) {
            UmkmUsaha::updateOrCreate(['id' => $data['id']], $data + ['foto_usaha' => null]);
        }
    }
}
