<?php

namespace App\Services;

use App\Models\LaporanKeuangan;
use App\Models\TransaksiKeuangan;

/**
 * Menyusun data transparansi keuangan untuk tampilan Warga.
 *
 * Prinsip:
 *  - Hanya laporan yang sudah DISETUJUI dan DIPUBLIKASIKAN yang dipakai.
 *  - Rekap kategori hanya menghitung bulan yang laporannya sudah terbit
 *    (data draf / belum disetujui tidak bocor ke warga).
 *  - Hanya angka agregat. Tidak ada nama pembayar, nominal per KK, maupun
 *    daftar transaksi individual.
 */
class TransparansiKeuanganService
{
    public const NAMA_BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /** Query dasar: laporan terbit untuk satu unit (dan RT bila unit = RT). */
    protected static function laporanTerbit(string $unit, ?string $rtId)
    {
        $q = LaporanKeuangan::where('is_published', true)
            ->where('status', 'Approved')
            ->where('unit', $unit);

        if ($unit === 'RT') {
            // RT tanpa rt_id tidak boleh menarik laporan RT lain.
            $rtId ? $q->where('rt_id', $rtId) : $q->whereRaw('1 = 0');
        }

        return $q;
    }

    /** Daftar tahun yang punya laporan terbit pada unit tersebut (terbaru dulu). */
    public static function daftarTahun(string $unit, ?string $rtId): array
    {
        return self::laporanTerbit($unit, $rtId)
            ->select('periode_tahun')->distinct()
            ->orderByDesc('periode_tahun')
            ->pluck('periode_tahun')->map(fn ($t) => (int) $t)->all();
    }

    /** Unit mana saja yang punya laporan terbit (untuk pemilih unit di UI). */
    public static function unitTersedia(?string $rtId): array
    {
        $hasil = [];
        foreach (['RT', 'RW', 'DKM'] as $unit) {
            $hasil[$unit] = self::laporanTerbit($unit, $rtId)->exists();
        }

        return $hasil;
    }

    /**
     * @return array{
     *   unit:string, tahun:int,
     *   per_bulan:array<int,array{bulan:int,nama:string,pemasukan:int,pengeluaran:int,saldo_akhir:?int,ada:bool}>,
     *   total_pemasukan:int, total_pengeluaran:int, selisih:int,
     *   saldo_terkini:?int, periode_terkini:?string,
     *   kategori_masuk:array<string,int>, kategori_keluar:array<string,int>,
     *   jumlah_laporan:int
     * }
     */
    public static function ringkasan(string $unit, ?string $rtId, int $tahun): array
    {
        $laporan = self::laporanTerbit($unit, $rtId)
            ->where('periode_tahun', $tahun)
            ->orderBy('periode_bulan')
            ->get()
            ->keyBy('periode_bulan');

        $perBulan = [];
        $totalMasuk = 0;
        $totalKeluar = 0;
        foreach (self::NAMA_BULAN as $no => $nama) {
            $lap = $laporan->get($no);
            $masuk = $lap ? (int) $lap->total_pemasukan : 0;
            $keluar = $lap ? (int) $lap->total_pengeluaran : 0;
            $totalMasuk += $masuk;
            $totalKeluar += $keluar;
            $perBulan[$no] = [
                'bulan'       => $no,
                'nama'        => $nama,
                'pemasukan'   => $masuk,
                'pengeluaran' => $keluar,
                'saldo_akhir' => $lap ? (int) $lap->saldo_akhir : null,
                'ada'         => (bool) $lap,
            ];
        }

        // Saldo terkini = saldo akhir laporan terbit paling baru (lintas tahun).
        $terbaru = self::laporanTerbit($unit, $rtId)
            ->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->first();

        // Rekap per kategori, hanya untuk bulan yang laporannya sudah terbit.
        $kategoriMasuk = [];
        $kategoriKeluar = [];
        $bulanTerbit = $laporan->keys()->all();
        if (! empty($bulanTerbit)) {
            $tx = TransaksiKeuangan::where('unit_sumber', $unit)
                ->where('status', '!=', 'Rejected')
                ->whereYear('tanggal', $tahun)
                ->whereRaw('MONTH(tanggal) IN (' . implode(',', array_map('intval', $bulanTerbit)) . ')');
            if ($unit === 'RT') {
                $rtId ? $tx->where('rt_id', $rtId) : $tx->whereRaw('1 = 0');
            }

            $baris = $tx->selectRaw('tipe, COALESCE(NULLIF(kategori, \'\'), \'Lainnya\') as kat, SUM(jumlah) as total')
                ->groupBy('tipe', 'kat')
                ->orderByDesc('total')
                ->get();

            foreach ($baris as $b) {
                if ($b->tipe === 'pemasukan') {
                    $kategoriMasuk[$b->kat] = (int) $b->total;
                } else {
                    $kategoriKeluar[$b->kat] = (int) $b->total;
                }
            }
        }

        return [
            'unit'              => $unit,
            'tahun'             => $tahun,
            'per_bulan'         => $perBulan,
            'total_pemasukan'   => $totalMasuk,
            'total_pengeluaran' => $totalKeluar,
            'selisih'           => $totalMasuk - $totalKeluar,
            'saldo_terkini'     => $terbaru ? (int) $terbaru->saldo_akhir : null,
            'periode_terkini'   => $terbaru
                ? (self::NAMA_BULAN[(int) $terbaru->periode_bulan] ?? $terbaru->periode_bulan) . ' ' . $terbaru->periode_tahun
                : null,
            'kategori_masuk'    => $kategoriMasuk,
            'kategori_keluar'   => $kategoriKeluar,
            'jumlah_laporan'    => $laporan->count(),
        ];
    }

    /**
     * Status iuran bulanan sebuah KK pada tahun tertentu.
     *
     * @return array{nama:string,nominal:int,lunas:int,menunggu:int}|null  null bila RT belum menetapkan iuran
     */
    public static function statusIuranSaya(?string $keluargaId, ?string $rtId, int $tahun): ?array
    {
        if (! $keluargaId || ! $rtId) {
            return null;
        }
        $master = \App\Models\MasterIuran::where('rt_id', $rtId)->where('tahun', $tahun)->first();
        if (! $master) {
            return null;
        }
        $sel = \App\Models\IuranPembayaran::where('master_iuran_id', $master->id)
            ->where('keluarga_id', $keluargaId)->get();

        return [
            'nama'     => $master->nama,
            'nominal'  => (int) $master->nominal,
            'lunas'    => $sel->where('status', 'lunas')->count(),
            'menunggu' => $sel->where('status', 'menunggu')->count(),
        ];
    }
}
