<?php

namespace Database\Seeders;

use App\Models\Keluarga;
use App\Models\MasterRt;
use Illuminate\Database\Seeder;

class KeluargaSeeder extends Seeder
{
    public function run(): void
    {
        $rt01 = MasterRt::where('kode_rt', '01')->first();
        $rt02 = MasterRt::where('kode_rt', '02')->first();
        $rt03 = MasterRt::where('kode_rt', '03')->first();
        $rt04 = MasterRt::where('kode_rt', '04')->first();
        $rt05 = MasterRt::where('kode_rt', '05')->first();
        $rt06 = MasterRt::where('kode_rt', '06')->first();

        $keluargas = [
            // Keluarga Budi Sampurno (Warga / UMKM) - RT 03
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000001',
                'no_kk' => '3217010101000001',
                'nik_kepala_keluarga' => '3217010101010003',
                'alamat' => 'Gg. Kenanga No. 45, RT 03 / RW 21, Tanimulya',
                'rt_id' => $rt03 ? $rt03->id : null,
                'no_wa' => '081987654321',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Hendra Gunawan (Admin RW) - RT 01
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000002',
                'no_kk' => '3217010101000002',
                'nik_kepala_keluarga' => '3217010101010002',
                'alamat' => 'Jl. Bougenville No. 12, RT 01 / RW 21, Tanimulya',
                'rt_id' => $rt01 ? $rt01->id : null,
                'no_wa' => '081234567890',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Ahmad Zaki (Ketua RT 01) - RT 01
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000003',
                'no_kk' => '3217010101000003',
                'nik_kepala_keluarga' => '3217010101010008',
                'alamat' => 'Jl. Melati No. 05, RT 01 / RW 21, Tanimulya',
                'rt_id' => $rt01 ? $rt01->id : null,
                'no_wa' => '081398765432',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Dedi Mulyadi (Pimpinan RW) - RT 02
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000004',
                'no_kk' => '3217010101000004',
                'nik_kepala_keluarga' => '3217010101010004',
                'alamat' => 'Jl. Flamboyan No. 18, RT 02 / RW 21, Tanimulya',
                'rt_id' => $rt02 ? $rt02->id : null,
                'no_wa' => '085712345678',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Bambang Sutrisno (Superadmin) - RT 04
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000005',
                'no_kk' => '3217010101000005',
                'nik_kepala_keluarga' => '3217010101010001',
                'alamat' => 'Jl. Anggrek No. 03, RT 04 / RW 21, Tanimulya',
                'rt_id' => $rt04 ? $rt04->id : null,
                'no_wa' => '082123456789',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Rian Hidayat (Op Konten RW) - RT 05
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000006',
                'no_kk' => '3217010101000006',
                'nik_kepala_keluarga' => '3217010101010005',
                'alamat' => 'Jl. Cempaka No. 22, RT 05 / RW 21, Tanimulya',
                'rt_id' => $rt05 ? $rt05->id : null,
                'no_wa' => '087812345678',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Fajar Nugraha (Op Keuangan RW) - RT 06
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000007',
                'no_kk' => '3217010101000007',
                'nik_kepala_keluarga' => '3217010101010006',
                'alamat' => 'Jl. Dahlia No. 14, RT 06 / RW 21, Tanimulya',
                'rt_id' => $rt06 ? $rt06->id : null,
                'no_wa' => '089612345678',
                'status_aktivasi' => 'Active',
            ],
            // Keluarga Budi Santoso (Demo Permohonan Surat) - RT 01
            [
                'id' => 'a1b2c3d4-0001-4000-8000-000000000008',
                'no_kk' => '3204xxxxxxxx0001',
                'nik_kepala_keluarga' => '3204xxxxxxxx0001',
                'alamat' => 'Jl. Merdeka Barat No. 45, RT 03 / RW 05, Kelurahan Sukamaju',
                'rt_id' => $rt01 ? $rt01->id : null,
                'no_wa' => '081234567890',
                'status_aktivasi' => 'Active',
            ],
        ];

        foreach ($keluargas as $item) {
            Keluarga::updateOrCreate(
                ['no_kk' => $item['no_kk']],
                $item
            );
        }
    }
}
