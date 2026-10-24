<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rt01 = \App\Models\MasterRt::where('kode_rt', '01')->first();
        $rt01Id = $rt01 ? $rt01->id : null;

        // Akun PENGURUS/PETUGAS: tanpa NIK (login via username). Paket 2B.
        $pengurus = [
            [
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'role' => 'Admin Aplikasi',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'adminrw21',
                'password' => Hash::make('password'),
                'role' => 'Admin RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'pimpinan',
                'password' => Hash::make('password'),
                'role' => 'Pimpinan RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'opkontenrw21',
                'password' => Hash::make('password'),
                'role' => 'Op Konten RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'opkeuanganrw21',
                'password' => Hash::make('password'),
                'role' => 'Op Keuangan RW',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'dkm',
                'password' => Hash::make('password'),
                'role' => 'DKM',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'ketuart01rw21',
                'password' => Hash::make('password'),
                'role' => 'Ketua RT',
                'rt_id' => $rt01Id,
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'opkeuanganrt01rw21',
                'password' => Hash::make('password'),
                'role' => 'Op Keuangan RT',
                'rt_id' => $rt01Id,
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            [
                'username' => 'opkontenrt01rw21',
                'password' => Hash::make('password'),
                'role' => 'Op Konten RT',
                'rt_id' => $rt01Id,
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
        ];

        foreach ($pengurus as $userData) {
            // Dedupe berdasarkan username (akun pengurus tidak punya NIK).
            User::updateOrCreate(
                ['username' => $userData['username']],
                array_merge($userData, ['nik' => null])
            );
        }

        // Akun WARGA (Kepala Keluarga): tetap tertaut NIK kependudukan.
        $warga = [
            [
                'nik' => '3217010101010003',
                'username' => 'budisampurno',
                'password' => Hash::make('password'),
                'role' => 'Warga',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
            // Akun demo untuk pengajuan permohonan surat.
            [
                'nik' => '3204111122220002',
                'username' => 'budisantoso',
                'password' => Hash::make('warga_pass'),
                'role' => 'Warga',
                'status_akun' => 'Active',
                'nik_verified_at' => now(),
            ],
        ];

        foreach ($warga as $userData) {
            User::updateOrCreate(
                ['nik' => $userData['nik']],
                $userData
            );
        }
    }
}
