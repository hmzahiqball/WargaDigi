<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nik' => '3217010101010001',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'Admin Aplikasi',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010002',
                'username' => 'adminrw21',
                'password' => Hash::make('password'),
                'role' => 'Admin RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010003',
                'username' => 'Budi Sampurno',
                'password' => Hash::make('password'),
                'role' => 'Warga',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010004',
                'username' => 'pimpinan',
                'password' => Hash::make('password'),
                'role' => 'Pimpinan RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010005',
                'username' => 'opkontenrw21',
                'password' => Hash::make('password'),
                'role' => 'Op Konten RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010006',
                'username' => 'opkeuanganrw21',
                'password' => Hash::make('password'),
                'role' => 'Op Keuangan RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010007',
                'username' => 'dkm',
                'password' => Hash::make('password'),
                'role' => 'DKM',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010008',
                'username' => 'ketuart01rw21',
                'password' => Hash::make('password'),
                'role' => 'Ketua RT',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010009',
                'username' => 'opkeuanganrt01rw21',
                'password' => Hash::make('password'),
                'role' => 'Op Keuangan RT',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'nik' => '3217010101010010',
                'username' => 'opkontenrt01rw21',
                'password' => Hash::make('password'),
                'role' => 'Op Konten RT',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            // Akun demo untuk pengajuan permohonan surat
            [
                'nik' => '3204xxxxxxxx0001',
                'username' => 'budisantoso',
                'password' => Hash::make('warga_pass'),
                'role' => 'Warga',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['nik' => $userData['nik']],
                $userData
            );
        }
    }
}
