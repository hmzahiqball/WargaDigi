<?php

namespace Database\Seeders;

use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;

/**
 * Seeder DEMO: kepengurusan aktif (dipakai pada tanda tangan surat, resi, dan laporan).
 *
 * - Ketua RW   : Dedi Mulyadi (RT 02)
 * - Ketua RT 01: Ahmad Zaki
 * - Ketua RT 03: Budi Sampurno (akun budisampurno)
 * - Bendahara RW   : Hendra Gunawan (RT 01)
 * - Bendahara RT 03: Siti Aminah
 *
 * Idempoten: jabatan yang sudah terisi pejabat aktif tidak diganti.
 */
class PejabatDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rt = fn (string $kode) => MasterRt::where('kode_rt', $kode)->value('id');
        $mulai = now()->subYear()->toDateString();

        $daftar = [
            // jabatan, kode RT (null = tingkat RW), NIK
            ['ketua_rw',     null, '3217010101010004'],
            ['ketua_rt',     '01', '3217010101010008'],
            ['ketua_rt',     '03', '3217010101010003'],
            ['bendahara_rw', null, '3217010101010002'],
            ['bendahara_rt', '03', '3217010101010011'],
        ];

        foreach ($daftar as [$jabatan, $kodeRt, $nik]) {
            $rtId = $kodeRt ? $rt($kodeRt) : null;
            if (($kodeRt && ! $rtId) || ! Penduduk::where('nik', $nik)->exists()) {
                continue;
            }
            $sudahAda = Pejabat::aktif()->where('jabatan', $jabatan)
                ->when($rtId, fn ($q) => $q->where('rt_id', $rtId), fn ($q) => $q->whereNull('rt_id'))
                ->exists();
            if (! $sudahAda) {
                Pejabat::setAktif($jabatan, $rtId, $nik, $mulai);
            }
        }
    }
}
