<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $roles = [
                'Admin Aplikasi',
                'Admin RW',
                'Pimpinan RW',
                'Op Konten RW',
                'Op Keuangan RW',
                'Ketua RT',
                'Op Konten RT',
                'Op Keuangan RT',
                'DKM',
                'Warga'
            ];

            foreach ($roles as $role) {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role]);
            }
        }
    }
}