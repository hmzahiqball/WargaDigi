<?php

namespace Database\Seeders;

use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use Illuminate\Database\Seeder;

/**
 * Seeder DEMO untuk data kepengurusan (Pejabat).
 *
 * Menetapkan Ketua RW dan Ketua RT aktif dari penduduk demo yang sudah ada,
 * sehingga surat menampilkan nama asli. Bukan bagian MasterSeeder.
 */
class PejabatDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Ketua RW: Dedi Mulyadi.
        $dedi = Penduduk::where('nik', '3217010101010004')->first();
        if ($dedi && ! Pejabat::aktif()->where('jabatan', 'ketua_rw')->exists()) {
            Pejabat::setAktif('ketua_rw', null, $dedi->nik, now()->subYear()->toDateString());
        }

        // Ketua RT 01: Ahmad Zaki.
        $rt01 = MasterRt::where('kode_rt', '01')->first();
        $zaki = Penduduk::where('nik', '3217010101010008')->first();
        if ($rt01 && $zaki && ! Pejabat::aktif()->where('jabatan', 'ketua_rt')->where('rt_id', $rt01->id)->exists()) {
            Pejabat::setAktif('ketua_rt', $rt01->id, $zaki->nik, now()->subYear()->toDateString());
        }
    }
}
