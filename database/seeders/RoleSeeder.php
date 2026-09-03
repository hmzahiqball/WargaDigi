<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin Aplikasi',
            'Admin RW',
            'Pimpinan',
            'Op. Konten RW',
            'Op. Keuangan RW',
            'ketua RT',
            'Op. Konten RT',
            'Op. Keuangan RT',
            'DKM',
            'Warga'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}