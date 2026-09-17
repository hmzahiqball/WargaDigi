<?php

namespace Database\Seeders;

use App\Models\KategoriUmkm;
use App\Models\UmkmUsaha;
use App\Models\User;
use Illuminate\Database\Seeder;

class UmkmUsahaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('nik', '3217010101010003')->first() ?? User::first();
        $nik = $user ? $user->nik : '3217010101010003';

        $kuliner = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Kuliner']);
        $fashion = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Fashion']);
        $jasa = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Jasa']);
        $kerajinan = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Kerajinan']);
        $koperasi = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Koperasi']);

        $usahas = [
            [
                'id' => '9b7f5e41-0123-4567-89ab-cdef01234567',
                'nik' => $nik,
                'nama_usaha' => 'Kriya Kayu Jati Tanimulya',
                'kategori_umkm_id' => $kerajinan->id,
                'deskripsi' => 'Kerajinan pahat dan ukiran kayu jati berkualitas tinggi. Menerima pesanan custom furniture, patung hiasan, dan panel dinding.',
                'alamat_usaha' => 'Gg. Kenanga RT 03 / RW 21 No. 45',
                'no_wa' => '6281987654321',
                'foto_usaha' => null,
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Usaha telah diverifikasi dan memenuhi syarat.',
                'klikWA' => 15,
                'is_active' => true,
            ],
            [
                'id' => '9b7f5e42-0123-4567-89ab-cdef01234567',
                'nik' => $nik,
                'nama_usaha' => 'Warung Nasi Ibu Ani',
                'kategori_umkm_id' => $kuliner->id,
                'deskripsi' => 'Menyediakan aneka masakan khas Sunda, nasi timbel, ayam bakar komplit, dan berbagai aneka jajanan pasar khas Tanimulya.',
                'alamat_usaha' => 'Jl. Melati No. 12, RT 01 / RW 21',
                'no_wa' => '6281234567890',
                'foto_usaha' => null,
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Data usaha valid dan terverifikasi.',
                'klikWA' => 42,
                'is_active' => true,
            ],
            [
                'id' => '9b7f5e43-0123-4567-89ab-cdef01234567',
                'nik' => $nik,
                'nama_usaha' => 'Fashion Muslimah Aini',
                'kategori_umkm_id' => $fashion->id,
                'deskripsi' => 'Produsen gamis, jilbab syari, dan pakaian muslimah kekinian dengan bahan adem dan nyaman digunakan sehari-hari.',
                'alamat_usaha' => 'Jl. Mawar No. 08, RT 02 / RW 21',
                'no_wa' => '6285712345678',
                'foto_usaha' => null,
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Berkas lengkap dan sesuai.',
                'klikWA' => 8,
                'is_active' => true,
            ],
            [
                'id' => '9b7f5e44-0123-4567-89ab-cdef01234567',
                'nik' => $nik,
                'nama_usaha' => 'Jasa Service Elektronik Pak Budi',
                'kategori_umkm_id' => $jasa->id,
                'deskripsi' => 'Jasa perbaikan AC, mesin cuci, kulkas, dan peralatan elektronik rumah tangga dengan garansi pengerjaan.',
                'alamat_usaha' => 'Jl. Anggrek No. 88, RT 04 / RW 21',
                'no_wa' => '6282111223344',
                'foto_usaha' => null,
                'status_verifikasi' => 'Pending',
                'catatan_verifikasi' => null,
                'klikWA' => 0,
                'is_active' => false,
            ],
            [
                'id' => '9b7f5e45-0123-4567-89ab-cdef01234567',
                'nik' => $nik,
                'nama_usaha' => 'Koperasi Unit Desa Tanimulya',
                'kategori_umkm_id' => $koperasi->id,
                'deskripsi' => 'Melayani simpan pinjam warga, penjualan sembako bersubsidi, dan pengadaan kebutuhan pokok RT/RW.',
                'alamat_usaha' => 'Jl. Dahlia No. 15, RT 05 / RW 21',
                'no_wa' => '6283899887766',
                'foto_usaha' => null,
                'status_verifikasi' => 'Approved',
                'catatan_verifikasi' => 'Lembaga terverifikasi.',
                'klikWA' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($usahas as $data) {
            UmkmUsaha::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
        }
    }
}
