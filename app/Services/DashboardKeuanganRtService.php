<?php

namespace App\Services;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterIuran;
use App\Models\PembayaranTagihan;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Data dashboard Operator Keuangan RT. Seluruh angka dibatasi ketat pada satu RT
 * dan memakai aturan saldo yang sama dengan buku kas/laporan (semua status kecuali Rejected).
 */
class DashboardKeuanganRtService
{
    /** Query transaksi valid khusus RT ini. */
    protected static function transaksi(string $rtId)
    {
        return TransaksiKeuangan::where('unit_sumber', 'RT')
            ->where('rt_id', $rtId)
            ->where('status', '!=', 'Rejected');
    }

    /** Selisih pemasukan - pengeluaran dari sebuah query transaksi. */
    protected static function agregat($query): array
    {
        $row = $query->selectRaw(
            "COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) AS masuk,
             COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) AS keluar"
        )->first();

        return ['masuk' => (int) $row->masuk, 'keluar' => (int) $row->keluar];
    }

    public static function data(string $rtId): array
    {
        $sekarang = Carbon::now();

        return [
            'ringkasan' => self::ringkasan($rtId, $sekarang),
            'tindakan'  => self::tindakan($rtId, $sekarang),
            'iuran'     => self::iuranBulanIni($rtId, $sekarang),
            'tagihan'   => self::tagihanAktif($rtId),
            'grafik'    => self::grafik($rtId, $sekarang),
            'kategori'  => self::pengeluaranPerKategori($rtId, $sekarang),
            'terbaru'   => self::transaksiTerbaru($rtId),
            'laporan'   => self::laporanTerbaru($rtId),
        ];
    }

    protected static function ringkasan(string $rtId, Carbon $now): array
    {
        $semua = self::agregat(self::transaksi($rtId));
        $bulanIni = self::agregat(
            self::transaksi($rtId)->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
        );

        return [
            'saldo'              => $semua['masuk'] - $semua['keluar'],
            'pemasukan_bulan'    => $bulanIni['masuk'],
            'pengeluaran_bulan'  => $bulanIni['keluar'],
            'selisih_bulan'      => $bulanIni['masuk'] - $bulanIni['keluar'],
            'nama_bulan'         => IuranService::BULAN[$now->month] . ' ' . $now->year,
        ];
    }

    /** Hal yang menunggu tindakan bendahara. */
    protected static function tindakan(string $rtId, Carbon $now): array
    {
        // Pengajuan iuran bulanan: satu batch dihitung satu pengajuan.
        $pengajuanIuran = IuranPembayaran::where('status', 'menunggu')
            ->whereHas('master', fn ($q) => $q->where('rt_id', $rtId))
            ->get()
            ->groupBy(fn ($p) => $p->batch_id ?? $p->id)
            ->count();

        // Termasuk Iuran Lainnya RW yang dikumpulkan lewat RT (hanya KK di RT ini).
        $pembayaranLainnya = PembayaranTagihan::where('status', 'Pending')
            ->whereHas('keluarga', fn ($k) => $k->where('rt_id', $rtId))
            ->whereHas('tagihan', fn ($q) => $q->where(function ($w) use ($rtId) {
                $w->where(fn ($x) => $x->where('unit', 'RT')->where('rt_id', $rtId))
                    ->orWhere(fn ($x) => $x->where('unit', 'RW')->where('via_rt', true));
            }))
            ->count();

        $pengeluaranMenunggu = (int) TransaksiKeuangan::where('unit_sumber', 'RT')
            ->where('rt_id', $rtId)->where('tipe', 'pengeluaran')->where('status', 'Pending')->sum('jumlah');

        // Laporan bulan lalu: sudah dibuat atau belum.
        $bulanLalu = $now->copy()->startOfMonth()->subMonth();
        $laporanBulanLalu = LaporanKeuangan::where('unit', 'RT')->where('rt_id', $rtId)
            ->where('periode_bulan', $bulanLalu->month)->where('periode_tahun', $bulanLalu->year)
            ->first();

        $laporanDitolak = LaporanKeuangan::where('unit', 'RT')->where('rt_id', $rtId)
            ->where('status', 'Rejected')->orderByDesc('updated_at')->first();

        $laporanDraft = LaporanKeuangan::where('unit', 'RT')->where('rt_id', $rtId)
            ->where('status', 'Draft')->count();

        // Kewajiban setoran ke RW yang belum terpenuhi (Januari s.d. bulan ini) + setoran yang ditolak RW.
        $setoranSisa = max(0, SetoranRtService::rekapRt($rtId, $now->year, $now->month)['total']['sisa']);
        $setoranTagihanSisa = (int) self::rekapTagihanSisa($rtId);
        $setoranDitolak = \App\Models\SetoranRt::where('rt_id', $rtId)->where('status', 'ditolak')->count();

        return [
            'setoran_sisa'          => $setoranSisa,
            'setoran_tagihan_sisa'  => $setoranTagihanSisa,
            'setoran_ditolak'       => $setoranDitolak,
            'pengajuan_iuran'       => $pengajuanIuran,
            'pembayaran_lainnya'    => $pembayaranLainnya,
            'pengeluaran_menunggu'  => $pengeluaranMenunggu,
            'laporan_lalu_label'    => IuranService::BULAN[$bulanLalu->month] . ' ' . $bulanLalu->year,
            'laporan_lalu_belum'    => $laporanBulanLalu === null,
            'laporan_ditolak'       => $laporanDitolak,
            'laporan_draft'         => $laporanDraft,
        ];
    }

    /** Progres iuran bulanan pada bulan berjalan + 5 KK dengan tunggakan terbanyak. */
    protected static function iuranBulanIni(string $rtId, Carbon $now): ?array
    {
        $master = MasterIuran::where('rt_id', $rtId)->where('tahun', $now->year)->first();
        $jumlahKk = Keluarga::where('rt_id', $rtId)->count();
        if (! $master) {
            return ['ada_master' => false, 'jumlah_kk' => $jumlahKk];
        }

        $lunas = IuranPembayaran::where('master_iuran_id', $master->id)
            ->where('status', 'lunas')->where('bulan', '<=', $now->month)
            ->get(['keluarga_id', 'bulan']);

        $kkLunasBulanIni = $lunas->where('bulan', $now->month)->pluck('keluarga_id')->unique()->count();
        $nominal = $master->nominalUntukBulan($now->month);

        // Tunggakan = bulan (Jan s.d. bulan ini) yang belum lunas, per KK.
        $lunasPerKk = $lunas->groupBy('keluarga_id')->map->count();
        $penunggak = Keluarga::where('rt_id', $rtId)->with('kepalaKeluarga')->get()
            ->map(function ($k) use ($lunasPerKk, $now) {
                $belum = $now->month - (int) ($lunasPerKk[$k->id] ?? 0);

                return (object) [
                    'nama'  => optional($k->kepalaKeluarga)->nama_lengkap ?? ('KK ' . $k->no_kk),
                    'bulan' => max(0, $belum),
                ];
            })
            ->filter(fn ($x) => $x->bulan > 0)
            ->sortByDesc('bulan')->values();

        return [
            'ada_master'    => true,
            'nama'          => $master->nama,
            'nominal'       => $nominal,
            'jumlah_kk'     => $jumlahKk,
            'kk_lunas'      => $kkLunasBulanIni,
            'kk_belum'      => max(0, $jumlahKk - $kkLunasBulanIni),
            'persen'        => $jumlahKk > 0 ? (int) round($kkLunasBulanIni / $jumlahKk * 100) : 0,
            'target'        => $nominal * $jumlahKk,
            'terkumpul'     => $nominal * $kkLunasBulanIni,
            'nama_bulan'    => IuranService::BULAN[$now->month],
            'total_penunggak' => $penunggak->count(),
            'penunggak'     => $penunggak->take(5),
        ];
    }

    /** Total sisa setoran Iuran Lainnya (RW via RT) yang belum disetor RT ini. */
    protected static function rekapTagihanSisa(string $rtId): int
    {
        return (int) SetoranRtService::rekapTagihanRt($rtId)->sum('sisa');
    }

    /** Tagihan Iuran Lainnya yang masih aktif (milik RT + dari RW via RT): terkumpul dibanding target KK di RT ini. */
    protected static function tagihanAktif(string $rtId): Collection
    {
        return Tagihan::where('jenis', 'insidental')->where('status', 'Active')
            ->where(function ($q) use ($rtId) {
                $q->where(fn ($x) => $x->where('unit', 'RT')->where('rt_id', $rtId))
                    ->orWhere(fn ($x) => $x->where('unit', 'RW')->where('via_rt', true));
            })
            ->with('pembayaran.keluarga')
            ->orderByRaw('tenggat IS NULL, tenggat ASC')
            ->take(4)->get()
            ->map(function ($t) use ($rtId) {
                $pembayaran = $t->pembayaran->filter(fn ($p) => optional($p->keluarga)->rt_id === $rtId);
                $total = $pembayaran->count();
                $lunas = $pembayaran->where('status', 'Paid');
                $tenggat = $t->tenggat;

                return (object) [
                    'judul'     => $t->judul,
                    'dari_rw'   => $t->unit === 'RW',
                    'target'    => (int) $t->nominal * $total,
                    'terkumpul' => (int) $lunas->sum(fn ($p) => $p->nominal ?: $t->nominal),
                    'lunas'     => $lunas->count(),
                    'total'     => $total,
                    'persen'    => $total > 0 ? (int) round($lunas->count() / $total * 100) : 0,
                    'tenggat'   => $tenggat?->translatedFormat('d M Y'),
                    'sisa_hari' => $tenggat ? (int) now()->startOfDay()->diffInDays($tenggat, false) : null,
                ];
            });
    }
    /** Pemasukan vs pengeluaran 6 bulan terakhir (khusus RT ini). */
    protected static function grafik(string $rtId, Carbon $now): array
    {
        $labels = $masuk = $keluar = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = $now->copy()->startOfMonth()->subMonths($i);
            $a = self::agregat(
                self::transaksi($rtId)->whereMonth('tanggal', $bulan->month)->whereYear('tanggal', $bulan->year)
            );
            $labels[] = $bulan->translatedFormat('M');
            $masuk[] = $a['masuk'];
            $keluar[] = $a['keluar'];
        }

        return compact('labels', 'masuk', 'keluar');
    }

    /** Pengeluaran bulan ini per kategori (maksimal 5 teratas). */
    protected static function pengeluaranPerKategori(string $rtId, Carbon $now): Collection
    {
        $rows = self::transaksi($rtId)->pengeluaran()
            ->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
            ->selectRaw('kategori, SUM(jumlah) AS total')
            ->groupBy('kategori')->orderByDesc('total')->take(5)->get();

        $total = (int) $rows->sum('total');

        return $rows->map(fn ($r) => (object) [
            'nama'   => $r->kategori ?: 'Lainnya',
            'total'  => (int) $r->total,
            'persen' => $total > 0 ? (int) round($r->total / $total * 100) : 0,
        ]);
    }

    protected static function transaksiTerbaru(string $rtId): Collection
    {
        $dariIuran = IuranPembayaran::whereNotNull('transaksi_id')->pluck('transaksi_id')
            ->merge(PembayaranTagihan::whereNotNull('transaksi_id')->pluck('transaksi_id'))->flip();

        return self::transaksi($rtId)
            ->orderByDesc('tanggal')->orderByDesc('created_at')->take(5)->get()
            ->map(fn ($t) => (object) [
                'judul'      => $t->judul,
                'tanggal'    => $t->tanggal->format('d M Y'),
                'jumlah'     => (int) $t->jumlah,
                'pemasukan'  => $t->tipe === 'pemasukan',
                'dari_iuran' => $dariIuran->has($t->id),
            ]);
    }

    protected static function laporanTerbaru(string $rtId): ?LaporanKeuangan
    {
        return LaporanKeuangan::where('unit', 'RT')->where('rt_id', $rtId)
            ->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->first();
    }
}
