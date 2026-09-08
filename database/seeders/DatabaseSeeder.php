<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MasterRt;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        User::create([
            'nik' => '3217010101010001',
            'username' => 'superadmin',
            'password' => Hash::make('password'),
            'role' => 'Admin Aplikasi',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010002',
            'username' => 'adminrw21',
            'password' => Hash::make('password'),
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);

        User::create([
            'nik' => '3217010101010003',
            'username' => 'Budi Sampurno',
            'password' => Hash::make('password'),
            'role' => 'Warga',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010004',
            'username' => 'pimpinan',
            'password' => Hash::make('password'),
            'role' => 'Pimpinan RW',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);

    
        User::create([
            'nik' => '3217010101010005',
            'username' => 'opkontenrw21',
            'password' => Hash::make('password'),
            'role' => 'Op Konten RW',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010006',
            'username' => 'opkeuanganrw21',
            'password' => Hash::make('password'),
            'role' => 'Op Keuangan RW',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010007',
            'username' => 'dkm',
            'password' => Hash::make('password'),
            'role' => 'DKM',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010008',
            'username' => 'ketuart01rw21',
            'password' => Hash::make('password'),
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010009',
            'username' => 'opkeuanganrt01rw21',
            'password' => Hash::make('password'),
            'role' => 'Op Keuangan RT',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        User::create([
            'nik' => '3217010101010010',
            'username' => 'opkontenrt01rw21',
            'password' => Hash::make('password'),
            'role' => 'Op Konten RT',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);


        $rtData = [
            ['kode_rt' => '01', 'nama_rt' => 'RT 01 / RW 21'],
            ['kode_rt' => '02', 'nama_rt' => 'RT 02 / RW 21'],
            ['kode_rt' => '03', 'nama_rt' => 'RT 03 / RW 21'],
            ['kode_rt' => '04', 'nama_rt' => 'RT 04 / RW 21'],
            ['kode_rt' => '05', 'nama_rt' => 'RT 05 / RW 21'],
            ['kode_rt' => '06', 'nama_rt' => 'RT 06 / RW 21'],
        ];
        
        foreach ($rtData as $rt) {
            MasterRt::create($rt);
        }

        // 12. Membuat Akun Warga (Budi Santoso) — untuk demo pengajuan surat
        $wargaUser = User::create([
            'nik' => '3204111122220002',
            'username' => 'budisantoso',
            'password' => Hash::make('warga_pass'),
            'role' => 'Warga',
            'status_akun' => 'Active',
            'nik_verified_at' => now(),
        ]);

        // Data Keluarga (KK) milik Budi Santoso
        $keluarga = \App\Models\Keluarga::create([
            'no_kk' => '3204xxxxxxxx0001',
            'nik_kepala_keluarga' => '3204111122220002',
            'alamat' => 'Jl. Merdeka Barat No. 45, RT 03 / RW 05, Kelurahan Sukamaju',
            'rt_id' => MasterRt::where('kode_rt', '01')->first()->id ?? 1, // RT 01 / RW 21
            'no_wa' => '081234567890',
            'status_aktivasi' => 'Active',
        ]);

        // Anggota Keluarga: Kepala Keluarga
        \App\Models\Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => '3204111122220002',
            'nama_lengkap' => 'Budi Santoso',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '1985-08-15',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);

        // Anggota Keluarga: Istri
        \App\Models\Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => '3204xxxxxxxx0002',
            'nama_lengkap' => 'Siti Rahma',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1988-03-22',
            'agama' => 'Islam',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'status_hubungan_keluarga' => 'Istri',
            'status_perkawinan' => 'Kawin',
        ]);

        // Anggota Keluarga: Anak
        \App\Models\Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => '3204xxxxxxxx0003',
            'nama_lengkap' => 'Andi Saputra',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2010-11-05',
            'agama' => 'Islam',
            'pekerjaan' => 'Pelajar',
            'status_hubungan_keluarga' => 'Anak',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        
        // Data Keluarga (KK) milik Budi Sampurno (Demo)
        $keluargaSampurno = \App\Models\Keluarga::create([
            'no_kk' => '3217010101010000',
            'nik_kepala_keluarga' => '3217010101010003',
            'alamat' => 'Jl. Budi Sampurno No. 1, RT 01 / RW 21',
            'rt_id' => MasterRt::where('kode_rt', '01')->first()->id ?? 1,
            'no_wa' => '081234567890',
            'status_aktivasi' => 'Active',
        ]);

        \App\Models\Penduduk::create([
            'keluarga_id' => $keluargaSampurno->id,
            'nik' => '3217010101010003',
            'nama_lengkap' => 'Budi Sampurno',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);

        $this->call([
            RoleSeeder::class,
            MasterRtSeeder::class,
            UserSeeder::class,
            KeluargaSeeder::class,
            PendudukSeeder::class,
            BeritaSeeder::class,
            KategoriUmkmSeeder::class,
            UmkmUsahaSeeder::class,
            KategoriProdukSeeder::class,
            UmkmProdukSeeder::class,
        ]);
    }
}


