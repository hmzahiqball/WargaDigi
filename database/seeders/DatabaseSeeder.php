<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterRt;
use Illuminate\Database\Seeder;
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


        $this->call([
            KategoriUmkmSeeder::class,
            UmkmUsahaSeeder::class,
            KategoriProdukSeeder::class,
            UmkmProdukSeeder::class,
        ]);
    }
}