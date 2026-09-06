<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\UmkmUsaha;
use Illuminate\Database\Seeder;

class KategoriProdukSeeder extends Seeder
{
    public function run(): void
    {
        $kategoriMapping = [
            '9b7f5e41-0123-4567-89ab-cdef01234567' => [ // Kriya Kayu Jati Tanimulya (Kerajinan)
                ['id' => 'c16f5e41-0001-4567-89ab-cdef01234561', 'nama_kategori' => 'Dekorasi & Hiasan'],
                ['id' => 'c16f5e41-0002-4567-89ab-cdef01234562', 'nama_kategori' => 'Furniture'],
                ['id' => 'c16f5e41-0003-4567-89ab-cdef01234563', 'nama_kategori' => 'Aksesoris'],
            ],
            '9b7f5e42-0123-4567-89ab-cdef01234567' => [ // Warung Nasi Ibu Ani (Kuliner)
                ['id' => 'c16f5e42-0001-4567-89ab-cdef01234561', 'nama_kategori' => 'Makanan'],
                ['id' => 'c16f5e42-0002-4567-89ab-cdef01234562', 'nama_kategori' => 'Minuman'],
            ],
            '9b7f5e43-0123-4567-89ab-cdef01234567' => [ // Fashion Muslimah Aini (Fashion)
                ['id' => 'c16f5e43-0001-4567-89ab-cdef01234561', 'nama_kategori' => 'Pakaian'],
                ['id' => 'c16f5e43-0002-4567-89ab-cdef01234562', 'nama_kategori' => 'Hijab'],
            ],
            '9b7f5e44-0123-4567-89ab-cdef01234567' => [ // Jasa Service Elektronik Pak Budi (Jasa)
                ['id' => 'c16f5e44-0001-4567-89ab-cdef01234561', 'nama_kategori' => 'Servis AC'],
                ['id' => 'c16f5e44-0002-4567-89ab-cdef01234562', 'nama_kategori' => 'Servis Elektronik'],
            ],
            '9b7f5e45-0123-4567-89ab-cdef01234567' => [ // Koperasi Unit Desa Tanimulya (Koperasi)
                ['id' => 'c16f5e45-0001-4567-89ab-cdef01234561', 'nama_kategori' => 'Bahan Pokok'],
                ['id' => 'c16f5e45-0002-4567-89ab-cdef01234562', 'nama_kategori' => 'Kebutuhan Dapur'],
            ],
        ];

        foreach ($kategoriMapping as $usahaId => $kategoris) {
            $usaha = UmkmUsaha::find($usahaId);
            if (!$usaha) {
                continue;
            }

            foreach ($kategoris as $item) {
                KategoriProduk::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'umkm_usaha_id' => $usaha->id,
                        'nama_kategori' => $item['nama_kategori'],
                    ]
                );
            }
        }
    }
}
