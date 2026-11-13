<?php

namespace Database\Seeders;

use App\Models\MasterRt;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Seeder DEMO: akun per peran, disamakan dengan akun terbaru.
 * Semua kata sandi demo: "password".
 *
 * - Pengurus/petugas login dengan username (tanpa NIK).
 * - Warga tertaut NIK penduduk (lihat KeluargaSeeder/PendudukSeeder).
 * - budisampurno: Ketua RT 03 sekaligus Warga (dua peran, pilih peran saat login).
 * - donisetiawan: contoh pemegang akun lama yang dinonaktifkan.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rt = fn (string $kode) => MasterRt::where('kode_rt', $kode)->value('id');

        $akun = [
            // username, role, rt, nik, status, peran tambahan
            ['superadmin',         'Admin Aplikasi', null, null, 'Active', []],
            ['adminrw21',          'Admin RW',       null, null, 'Active', []],
            ['pimpinan',           'Ketua RW',       null, null, 'Active', []],
            ['opkontenrw21',       'Op Konten RW',   null, null, 'Active', []],
            ['opkeuanganrw21',     'Op Keuangan RW', null, null, 'Active', []],
            ['dkm',                'DKM',            null, null, 'Active', []],
            ['ketuart01rw21',      'Ketua RT',       '01', null, 'Active', []],
            ['opkontenrt01rw21',   'Op Konten RT',   '01', null, 'Active', []],
            ['opkeuanganrt01rw21', 'Op Keuangan RT', '01', null, 'Active', []],
            ['opkonten_3',         'Op Konten RT',   '03', null, 'Active', []],
            ['operator_rt3',       'Op Keuangan RT', '03', null, 'Active', []],
            ['budisampurno',       'Ketua RT',       '03', '3217010101010003', 'Active', ['Warga']],
            ['budisantoso',        'Warga',          '03', '3204111122220002', 'Active', []],
            ['donisetiawan',       'Warga',          '03', '3217010101010010', 'Nonaktif', []],
        ];

        foreach ($akun as [$username, $role, $kodeRt, $nik, $status, $tambahan]) {
            $user = User::updateOrCreate(['username' => $username], [
                'nik'             => $nik,
                'password'        => Hash::make('password'),
                'role'            => $role,
                'rt_id'           => $kodeRt ? $rt($kodeRt) : null,
                'status_akun'     => $status,
                'nik_verified_at' => now(),
            ]);

            // Akun nonaktif tidak memegang peran apa pun; lainnya: peran utama + tambahan.
            $peran = $status === 'Nonaktif' ? [] : array_merge([$role], $tambahan);
            foreach ($peran as $r) {
                Role::findOrCreate($r, 'web');
            }
            $user->syncRoles($peran);
        }
    }
}
