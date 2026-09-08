<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;

class PendudukSeeder extends Seeder
{
    public function run(): void
    {
        $kkBudi = Keluarga::where('no_kk', '3217010101000001')->first();
        $kkHendra = Keluarga::where('no_kk', '3217010101000002')->first();
        $kkZaki = Keluarga::where('no_kk', '3217010101000003')->first();
        $kkDedi = Keluarga::where('no_kk', '3217010101000004')->first();
        $kkBambang = Keluarga::where('no_kk', '3217010101000005')->first();
        $kkRian = Keluarga::where('no_kk', '3217010101000006')->first();
        $kkFajar = Keluarga::where('no_kk', '3217010101000007')->first();
        $kkBudiSantoso = Keluarga::where('no_kk', '3204xxxxxxxx0001')->first();

        $penduduks = [
            // 1. Budi Sampurno (Warga / Pemilik UMKM Kriya Kayu)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000001',
                'keluarga_id' => $kkBudi ? $kkBudi->id : null,
                'nik' => '3217010101010003',
                'nama_lengkap' => 'Budi Sampurno',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung Barat',
                'tanggal_lahir' => '1984-05-14',
                'agama' => 'Islam',
                'pekerjaan' => 'Pengrajin Kayu & Wiraswasta',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
            // Istri Budi Sampurno
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000002',
                'keluarga_id' => $kkBudi ? $kkBudi->id : null,
                'nik' => '3217010101010011',
                'nama_lengkap' => 'Siti Aminah',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Cimahi',
                'tanggal_lahir' => '1988-08-20',
                'agama' => 'Islam',
                'pekerjaan' => 'Mengurus Rumah Tangga',
                'status_hubungan_keluarga' => 'Istri',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
            // Anak Budi Sampurno
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000003',
                'keluarga_id' => $kkBudi ? $kkBudi->id : null,
                'nik' => '3217010101010012',
                'nama_lengkap' => 'Rizky Pratama Sampurno',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2012-03-10',
                'agama' => 'Islam',
                'pekerjaan' => 'Pelajar',
                'status_hubungan_keluarga' => 'Anak',
                'status_perkawinan' => 'Belum Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 2. Hendra Gunawan (Admin RW)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000004',
                'keluarga_id' => $kkHendra ? $kkHendra->id : null,
                'nik' => '3217010101010002',
                'nama_lengkap' => 'Hendra Gunawan',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1979-11-22',
                'agama' => 'Islam',
                'pekerjaan' => 'Pegawai Swasta',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
            // Istri Hendra Gunawan
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000005',
                'keluarga_id' => $kkHendra ? $kkHendra->id : null,
                'nik' => '3217010101010013',
                'nama_lengkap' => 'Dewi Sartika',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Garut',
                'tanggal_lahir' => '1982-01-15',
                'agama' => 'Islam',
                'pekerjaan' => 'Guru',
                'status_hubungan_keluarga' => 'Istri',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 3. Ahmad Zaki (Ketua RT 01)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000006',
                'keluarga_id' => $kkZaki ? $kkZaki->id : null,
                'nik' => '3217010101010008',
                'nama_lengkap' => 'Ahmad Zaki',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Cimahi',
                'tanggal_lahir' => '1975-06-04',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 4. Dedi Mulyadi (Pimpinan RW)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000007',
                'keluarga_id' => $kkDedi ? $kkDedi->id : null,
                'nik' => '3217010101010004',
                'nama_lengkap' => 'Dedi Mulyadi',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung Barat',
                'tanggal_lahir' => '1968-09-17',
                'agama' => 'Islam',
                'pekerjaan' => 'PNS / Pensiunan',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 5. Bambang Sutrisno (Superadmin / Admin Aplikasi)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000008',
                'keluarga_id' => $kkBambang ? $kkBambang->id : null,
                'nik' => '3217010101010001',
                'nama_lengkap' => 'Bambang Sutrisno',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1987-12-01',
                'agama' => 'Islam',
                'pekerjaan' => 'Software Engineer',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 6. Rian Hidayat (Op Konten RW)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000009',
                'keluarga_id' => $kkRian ? $kkRian->id : null,
                'nik' => '3217010101010005',
                'nama_lengkap' => 'Rian Hidayat',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1995-04-18',
                'agama' => 'Islam',
                'pekerjaan' => 'Desainer Grafis',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 7. Fajar Nugraha (Op Keuangan RW)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000010',
                'keluarga_id' => $kkFajar ? $kkFajar->id : null,
                'nik' => '3217010101010006',
                'nama_lengkap' => 'Fajar Nugraha',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Tasikmalaya',
                'tanggal_lahir' => '1992-07-29',
                'agama' => 'Islam',
                'pekerjaan' => 'Akuntan',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 8. Ustadz Hasan Basri (DKM)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000011',
                'keluarga_id' => $kkHendra ? $kkHendra->id : null,
                'nik' => '3217010101010007',
                'nama_lengkap' => 'Hasan Basri',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Sumedang',
                'tanggal_lahir' => '1970-02-11',
                'agama' => 'Islam',
                'pekerjaan' => 'Tokoh Agama / Pengajar',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 9. Yudi Santoso (Op Keuangan RT)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000012',
                'keluarga_id' => $kkZaki ? $kkZaki->id : null,
                'nik' => '3217010101010009',
                'nama_lengkap' => 'Yudi Santoso',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1991-10-05',
                'agama' => 'Islam',
                'pekerjaan' => 'Karyawan Swasta',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 10. Doni Setiawan (Op Konten RT)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000013',
                'keluarga_id' => $kkBudi ? $kkBudi->id : null,
                'nik' => '3217010101010010',
                'nama_lengkap' => 'Doni Setiawan',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Cimahi',
                'tanggal_lahir' => '1996-03-25',
                'agama' => 'Islam',
                'pekerjaan' => 'Fotografer & Kreator Konten',
                'status_hubungan_keluarga' => 'Anak',
                'status_perkawinan' => 'Belum Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],

            // 11. Keluarga Budi Santoso (Demo Surat)
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000014',
                'keluarga_id' => $kkBudiSantoso ? $kkBudiSantoso->id : null,
                'nik' => '3204xxxxxxxx0001',
                'nama_lengkap' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1985-08-15',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000015',
                'keluarga_id' => $kkBudiSantoso ? $kkBudiSantoso->id : null,
                'nik' => '3204xxxxxxxx0002',
                'nama_lengkap' => 'Siti Rahma',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1988-03-22',
                'agama' => 'Islam',
                'pekerjaan' => 'Ibu Rumah Tangga',
                'status_hubungan_keluarga' => 'Istri',
                'status_perkawinan' => 'Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
            [
                'id' => 'b1c2d3e4-0001-4000-8000-000000000016',
                'keluarga_id' => $kkBudiSantoso ? $kkBudiSantoso->id : null,
                'nik' => '3204xxxxxxxx0003',
                'nama_lengkap' => 'Andi Saputra',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2010-11-05',
                'agama' => 'Islam',
                'pekerjaan' => 'Pelajar',
                'status_hubungan_keluarga' => 'Anak',
                'status_perkawinan' => 'Belum Kawin',
                'file_kk' => null,
                'file_ktp' => null,
            ],
        ];

        foreach ($penduduks as $item) {
            Penduduk::updateOrCreate(
                ['nik' => $item['nik']],
                $item
            );
        }
    }
}
