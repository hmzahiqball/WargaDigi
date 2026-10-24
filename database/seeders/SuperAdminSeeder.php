<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder MASTER (produksi): membuat HANYA 1 akun superadmin.
 *
 * Model User memiliki event booted() yang otomatis memanggil syncSpatieRole(),
 * sehingga role Spatie "Admin Aplikasi" ikut tersinkron saat akun dibuat.
 */
class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Akun pengurus (termasuk superadmin) TIDAK memakai NIK; login via username.
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'nik'             => null,
                'password'        => Hash::make('password'),
                'role'            => 'Admin Aplikasi',
                'status_akun'     => 'Active',
                'nik_verified_at' => now(),
            ]
        );
    }
}
