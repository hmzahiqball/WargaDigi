<?php

namespace App\Services;

use App\Models\LaporanKeuangan;
use App\Models\Pejabat;
use Illuminate\Support\Carbon;

/**
 * Nama penandatangan pada dokumen keuangan, diambil dari data Kepengurusan (pejabat)
 * sesuai periode dokumen. Bila jabatan belum ditetapkan, hasilnya null (area nama dikosongkan).
 */
class PenandatanganDokumen
{
    /**
     * Penandatangan laporan keuangan: Ketua (menyetujui) dan Bendahara (membuat),
     * berdasarkan pejabat yang menjabat pada akhir bulan laporan.
     *
     * @return array{ketua:?string, bendahara:?string}
     */
    public static function laporan(LaporanKeuangan $laporan): array
    {
        $akhirPeriode = Carbon::create((int) $laporan->periode_tahun, (int) $laporan->periode_bulan, 1)->endOfMonth();
        $rtId = $laporan->unit === 'RT' ? $laporan->rt_id : null;

        return [
            // Laporan RT disetujui Ketua RT; laporan RW dan DKM disetujui Ketua RW.
            'ketua'     => $laporan->unit === 'RT'
                ? Pejabat::namaPada('ketua_rt', $rtId, $akhirPeriode)
                : Pejabat::namaPada('ketua_rw', null, $akhirPeriode),
            'bendahara' => Pejabat::namaPada(self::jabatanBendahara($laporan->unit), $rtId, $akhirPeriode),
        ];
    }

    /** Nama Bendahara untuk unit (RT/RW/DKM) pada tanggal tertentu. */
    public static function bendahara(string $unit, ?string $rtId, $tanggal): ?string
    {
        return Pejabat::namaPada(self::jabatanBendahara($unit), $unit === 'RT' ? $rtId : null, $tanggal);
    }

    public static function jabatanBendahara(string $unit): string
    {
        return match ($unit) {
            'RT'    => 'bendahara_rt',
            'DKM'   => 'bendahara_dkm',
            default => 'bendahara_rw',
        };
    }
}