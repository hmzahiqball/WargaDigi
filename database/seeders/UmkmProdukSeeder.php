<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\UmkmProduk;
use App\Models\UmkmUsaha;
use Illuminate\Database\Seeder;

class UmkmProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kriyaUsaha = UmkmUsaha::find('9b7f5e41-0123-4567-89ab-cdef01234567') ?? UmkmUsaha::where('nama_usaha', 'like', '%Kriya%')->first();
        $kulinerUsaha = UmkmUsaha::find('9b7f5e42-0123-4567-89ab-cdef01234567') ?? UmkmUsaha::where('nama_usaha', 'like', '%Ani%')->first();
        $fashionUsaha = UmkmUsaha::find('9b7f5e43-0123-4567-89ab-cdef01234567') ?? UmkmUsaha::where('nama_usaha', 'like', '%Fashion%')->first();
        $jasaUsaha = UmkmUsaha::find('9b7f5e44-0123-4567-89ab-cdef01234567') ?? UmkmUsaha::where('nama_usaha', 'like', '%Budi%')->first();
        $koperasiUsaha = UmkmUsaha::find('9b7f5e45-0123-4567-89ab-cdef01234567') ?? UmkmUsaha::where('nama_usaha', 'like', '%Koperasi%')->first();

        $produks = [];
        if ($kriyaUsaha) {
            $dekorasiKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e41-0001-4567-89ab-cdef01234561'],
                ['umkm_usaha_id' => $kriyaUsaha->id, 'nama_kategori' => 'Dekorasi & Hiasan']
            );
            $furnitureKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e41-0002-4567-89ab-cdef01234562'],
                ['umkm_usaha_id' => $kriyaUsaha->id, 'nama_kategori' => 'Furniture']
            );
            $aksesorisKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e41-0003-4567-89ab-cdef01234563'],
                ['umkm_usaha_id' => $kriyaUsaha->id, 'nama_kategori' => 'Aksesoris']
            );

            $produks = array_merge($produks, [
                [
                    'id' => '8a6f5e41-0123-4567-89ab-cdef01234561',
                    'umkm_usaha_id' => $kriyaUsaha->id,
                    'kategori_produk_id' => $dekorasiKat->id,
                    'nama_produk' => 'Patung Kayu Jati Tradisional',
                    'deskripsi' => 'Ukiran tangan detail tinggi dengan motif klasik, cocok untuk hiasan ruang tamu.',
                    'harga' => 350000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 158,
                    'link_wa' => 'https://wa.me/' . $kriyaUsaha->no_wa . '?text=Saya%20tertarik%20dengan%20Patung%20Kayu%20Jati',
                ],
                [
                    'id' => '8a6f5e41-0123-4567-89ab-cdef01234562',
                    'umkm_usaha_id' => $kriyaUsaha->id,
                    'kategori_produk_id' => $dekorasiKat->id,
                    'nama_produk' => 'Panel Dinding Ukir Floral',
                    'deskripsi' => 'Hiasan dinding estetik ukuran 60x60cm, menambah kesan mewah ruangan.',
                    'harga' => 850000,
                    'status_stok' => 'menipis',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 65,
                    'link_wa' => 'https://wa.me/' . $kriyaUsaha->no_wa . '?text=Saya%20tertarik%20dengan%20Panel%20Dinding',
                ],
                [
                    'id' => '8a6f5e41-0123-4567-89ab-cdef01234563',
                    'umkm_usaha_id' => $kriyaUsaha->id,
                    'kategori_produk_id' => $furnitureKat->id,
                    'nama_produk' => 'Meja Kopi Jati Natural',
                    'deskripsi' => 'Desain minimalis dengan mempertahankan bentuk alami tepian kayu (live edge).',
                    'harga' => 1500000,
                    'status_stok' => 'habis',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 19,
                    'link_wa' => 'https://wa.me/' . $kriyaUsaha->no_wa . '?text=Saya%20tertarik%20dengan%20Meja%20Kopi',
                ],
                [
                    'id' => '8a6f5e41-0123-4567-89ab-cdef01234564',
                    'umkm_usaha_id' => $kriyaUsaha->id,
                    'kategori_produk_id' => $aksesorisKat->id,
                    'nama_produk' => 'Asbak Jati Ukir Eksklusif',
                    'deskripsi' => 'Asbak meja elegan dengan finishing halus, anti gores dan tahan panas.',
                    'harga' => 150000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 25,
                    'link_wa' => 'https://wa.me/' . $kriyaUsaha->no_wa . '?text=Saya%20tertarik%20dengan%20Asbak%20Jati',
                ],
            ]);
        }

        // 2. Warung Nasi Ibu Ani (Kuliner)
        if ($kulinerUsaha) {
            $makananKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e42-0001-4567-89ab-cdef01234561'],
                ['umkm_usaha_id' => $kulinerUsaha->id, 'nama_kategori' => 'Makanan']
            );
            $minumanKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e42-0002-4567-89ab-cdef01234562'],
                ['umkm_usaha_id' => $kulinerUsaha->id, 'nama_kategori' => 'Minuman']
            );

            $produks = array_merge($produks, [
                [
                    'id' => '8a6f5e42-0123-4567-89ab-cdef01234561',
                    'umkm_usaha_id' => $kulinerUsaha->id,
                    'kategori_produk_id' => $makananKat->id,
                    'nama_produk' => 'Paket Nasi Timbel Ayam Bakar',
                    'deskripsi' => 'Nasi timbel wangi daun pisang disajikan dengan ayam bakar bumbu komplit, tahu, tempe, lalapan, dan sambal terasi.',
                    'harga' => 25000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 112,
                    'link_wa' => 'https://wa.me/' . $kulinerUsaha->no_wa . '?text=Saya%20mau%20pesan%20Nasi%20Timbel',
                ],
                [
                    'id' => '8a6f5e42-0123-4567-89ab-cdef01234562',
                    'umkm_usaha_id' => $kulinerUsaha->id,
                    'kategori_produk_id' => $minumanKat->id,
                    'nama_produk' => 'Es Cendol Dawet Ayu',
                    'deskripsi' => 'Minuman cendol beras asli dengan santan gurih dan gula merah asli khas Jawa.',
                    'harga' => 10000,
                    'status_stok' => 'menipis',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 38,
                    'link_wa' => 'https://wa.me/' . $kulinerUsaha->no_wa . '?text=Saya%20mau%20pesan%20Es%20Cendol',
                ],
            ]);
        }

        // 3. Fashion Muslimah Aini (Fashion)
        if ($fashionUsaha) {
            $pakaianKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e43-0001-4567-89ab-cdef01234561'],
                ['umkm_usaha_id' => $fashionUsaha->id, 'nama_kategori' => 'Pakaian']
            );
            $hijabKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e43-0002-4567-89ab-cdef01234562'],
                ['umkm_usaha_id' => $fashionUsaha->id, 'nama_kategori' => 'Hijab']
            );

            $produks = array_merge($produks, [
                [
                    'id' => '8a6f5e43-0123-4567-89ab-cdef01234561',
                    'umkm_usaha_id' => $fashionUsaha->id,
                    'kategori_produk_id' => $pakaianKat->id,
                    'nama_produk' => 'Gamis Modern Premium',
                    'deskripsi' => 'Gamis bahan katun toyobo super yang dingin dan fleksibel untuk dipakai ke acara formal maupun harian.',
                    'harga' => 220000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 89,
                    'link_wa' => 'https://wa.me/' . $fashionUsaha->no_wa . '?text=Tanya%20stok%20Gamis%20Modern',
                ],
                [
                    'id' => '8a6f5e43-0123-4567-89ab-cdef01234562',
                    'umkm_usaha_id' => $fashionUsaha->id,
                    'kategori_produk_id' => $hijabKat->id,
                    'nama_produk' => 'Hijab Segiempat Premium',
                    'deskripsi' => 'Hijab voal ultrapure mudah dibentuk, tidak licin, dan tersedia dalam pelbagai warna pastel.',
                    'harga' => 45000,
                    'status_stok' => 'menipis',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 42,
                    'link_wa' => 'https://wa.me/' . $fashionUsaha->no_wa . '?text=Tanya%20stok%20Hijab%20Segiempat',
                ],
            ]);
        }

        // 4. Jasa Service Elektronik Pak Budi (Jasa)
        if ($jasaUsaha) {
            $servisAcKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e44-0001-4567-89ab-cdef01234561'],
                ['umkm_usaha_id' => $jasaUsaha->id, 'nama_kategori' => 'Servis AC']
            );
            $servisElektronikKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e44-0002-4567-89ab-cdef01234562'],
                ['umkm_usaha_id' => $jasaUsaha->id, 'nama_kategori' => 'Servis Elektronik']
            );

            $produks = array_merge($produks, [
                [
                    'id' => '8a6f5e44-0123-4567-89ab-cdef01234561',
                    'umkm_usaha_id' => $jasaUsaha->id,
                    'kategori_produk_id' => $servisAcKat->id,
                    'nama_produk' => 'Jasa Cuci & Servis AC Rumah',
                    'deskripsi' => 'Layanan perawatan rutin AC 0.5 PK - 2 PK meliputi pembersihan evaporator, pembuangan air, dan pengecekan freon.',
                    'harga' => 75000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Tidak Aktif',
                    'jumlah_akses' => 12,
                    'link_wa' => 'https://wa.me/' . $jasaUsaha->no_wa . '?text=Booking%20Jasa%20Cuci%20AC',
                ],
                [
                    'id' => '8a6f5e44-0123-4567-89ab-cdef01234562',
                    'umkm_usaha_id' => $jasaUsaha->id,
                    'kategori_produk_id' => $servisElektronikKat->id,
                    'nama_produk' => 'Perbaikan Kulkas & Mesin Cuci',
                    'deskripsi' => 'Jasa servis panggilan untuk kulkas tidak dingin, mesin cuci mati atau bocor, bergaransi 30 hari.',
                    'harga' => 100000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 28,
                    'link_wa' => 'https://wa.me/' . $jasaUsaha->no_wa . '?text=Booking%20Servis%20Elektronik',
                ],
            ]);
        }

        // 5. Koperasi Unit Desa Tanimulya (Koperasi)
        if ($koperasiUsaha) {
            $bahanPokokKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e45-0001-4567-89ab-cdef01234561'],
                ['umkm_usaha_id' => $koperasiUsaha->id, 'nama_kategori' => 'Bahan Pokok']
            );
            $kebutuhanDapurKat = KategoriProduk::updateOrCreate(
                ['id' => 'c16f5e45-0002-4567-89ab-cdef01234562'],
                ['umkm_usaha_id' => $koperasiUsaha->id, 'nama_kategori' => 'Kebutuhan Dapur']
            );

            $produks = array_merge($produks, [
                [
                    'id' => '8a6f5e45-0123-4567-89ab-cdef01234561',
                    'umkm_usaha_id' => $koperasiUsaha->id,
                    'kategori_produk_id' => $bahanPokokKat->id,
                    'nama_produk' => 'Beras Medium Tanimulya 5kg',
                    'deskripsi' => 'Beras kualitas medium hasil panen lokal warga Tanimulya, pulen dan sehat.',
                    'harga' => 65000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 54,
                    'link_wa' => 'https://wa.me/' . $koperasiUsaha->no_wa . '?text=Pesan%20Beras%20Medium',
                ],
                [
                    'id' => '8a6f5e45-0123-4567-89ab-cdef01234562',
                    'umkm_usaha_id' => $koperasiUsaha->id,
                    'kategori_produk_id' => $kebutuhanDapurKat->id,
                    'nama_produk' => 'Minyak Goreng Sawit 2L',
                    'deskripsi' => 'Minyak goreng kelapa sawit higienis jernih untuk kebutuhan memasak harian keluarga.',
                    'harga' => 34000,
                    'status_stok' => 'tersedia',
                    'foto_produk' => null,
                    'status_produk' => 'Aktif',
                    'jumlah_akses' => 31,
                    'link_wa' => 'https://wa.me/' . $koperasiUsaha->no_wa . '?text=Pesan%20Minyak%20Goreng',
                ],
            ]);
        }

        foreach ($produks as $data) {
            UmkmProduk::updateOrCreate(
                ['id' => $data['id']],
                $data
            );
        }

        // Hapus kategori produk yang tidak terhubung dengan produk mana pun
        KategoriProduk::doesntHave('produk')->delete();
    }
}
