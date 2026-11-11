<?php

namespace App\Services;

use App\Models\LaporanKeuangan;
use App\Models\PembayaranTagihan;
use App\Models\SetoranRt;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Data dashboard Operator Keuangan RW. Hanya kas RW (unit_sumber = RW); kas RT dan DKM
 * tidak dicampur. Aturan saldo sama dengan buku kas dan laporan (semua status kecuali Rejected).
 */
class DashboardKeuanganRwService
{
    protected static function transaksi()
    {
        return TransaksiKeuangan::where('unit_sumber', 'RW')->where('status', '!=', 'Rejected');
    }

    protected static function agregat($query): array
    {
        $row = $query->selectRaw(
            "COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) AS masuk,
             COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) AS keluar"
        )->first();

        return ['masuk' => (int) $row->masuk, 'keluar' => (int) $row->keluar];
    }

    public static function data(): array
    {
        $sekarang = Carbon::now();
        $setoran = SetoranRtService::rekapSemuaRt($sekarang->year, $sekarang->month);

        return [
            'ringkasan' => self::ringkasan($sekarang),
            'tindakan'  => self::tindakan($sekarang, $setoran),
            'setoran'   => $setoran,
            'tagihan'   => self::tagihanAktif(),
            'grafik'    => self::grafik($sekarang),
            'masuk'     => self::perKategori('pemasukan', $sekarang),
            'keluar'    => self::perKategori('pengeluaran', $sekarang),
            'terbaru'   => self::transaksiTerbaru(),
            'laporan'   => LaporanKeuangan::where('unit', 'RW')
                ->orderByDesc('periode_tahun')->orderByDesc('periode_bulan')->first(),
            'nama_bulan' => IuranService::BULAN[$sekarang->month] . ' ' . $sekarang->year,
        ];
    }

    protected static function ringkasan(Carbon $now): array
    {
        $semua = self::agregat(self::transaksi());
        $bulanIni = self::agregat(
            self::transaksi()->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
        );

        return [
            'saldo'             => $semua['masuk'] - $semua['keluar'],
            'pemasukan_bulan'   => $bulanIni['masuk'],
            'pengeluaran_bulan' => $bulanIni['keluar'],
            'selisih_bulan'     => $bulanIni['masuk'] - $bulanIni['keluar'],
        ];
    }

    protected static function tindakan(Carbon $now, array $setoran): array
    {
        $menunggu = SetoranRtService::kelompok(
            SetoranRt::with(['rt', 'tagihan'])->where('status', 'menunggu')->get()
        );

        // Iuran Lainnya yang dibayar langsung ke RW (yang lewat RT diverifikasi oleh RT).
        $pembayaranLangsung = PembayaranTagihan::where('status', 'Pending')
            ->whereHas('tagihan', fn ($q) => $q->where('unit', 'RW')->where('via_rt', false))
            ->count();

        $bulanLalu = $now->copy()->startOfMonth()->subMonth();
        $laporanBulanLalu = LaporanKeuangan::where('unit', 'RW')
            ->where('periode_bulan', $bulanLalu->month)->where('periode_tahun', $bulanLalu->year)->exists();

        return [
            'setoran_menunggu'      => $menunggu->count(),
            'setoran_menunggu_rp'   => (int) $menunggu->sum('nominal'),
            'pembayaran_langsung'   => $pembayaranLangsung,
            'pengeluaran_menunggu'  => (int) TransaksiKeuangan::where('unit_sumber', 'RW')
                ->where('tipe', 'pengeluaran')->where('status', 'Pending')->sum('jumlah'),
            // RT yang masih punya kewajiban setoran (Januari s.d. bulan ini) belum terpenuhi.
            'rt_belum_setor'        => collect($setoran['baris'])->filter(fn ($r) => $r->kumulatif['sisa'] > 0)->count(),
            'laporan_lalu_label'    => IuranService::BULAN[$bulanLalu->month] . ' ' . $bulanLalu->year,
            'laporan_lalu_belum'    => ! $laporanBulanLalu,
            'laporan_ditolak'       => LaporanKeuangan::where('unit', 'RW')->where('status', 'Rejected')
                ->orderByDesc('updated_at')->first(),
            'laporan_draft'         => LaporanKeuangan::where('unit', 'RW')->where('status', 'Draft')->count(),
        ];
    }

    /** Iuran Lainnya milik RW yang masih aktif: kemajuan pengumpulan dan penyetoran. */
    protected static function tagihanAktif(): Collection
    {
        return Tagihan::where('unit', 'RW')->where('jenis', 'insidental')->where('status', 'Active')
            ->orderByRaw('tenggat IS NULL, tenggat ASC')->take(4)->get()
            ->map(function (Tagihan $t) {
                if ($t->via_rt) {
                    $tot = SetoranRtService::rekapTagihanSemuaRt($t)['total'];

                    return (object) [
                        'judul' => $t->judul, 'via_rt' => true, 'tenggat' => $t->tenggat?->translatedFormat('d M Y'),
                        'lunas' => $tot['lunas'], 'total' => $tot['total_kk'],
                        'terkumpul' => $tot['terkumpul'], 'disetor' => $tot['disetor'], 'sisa' => $tot['sisa'],
                        'persen' => $tot['total_kk'] > 0 ? (int) round($tot['lunas'] / $tot['total_kk'] * 100) : 0,
                    ];
                }

                $pembayaran = $t->pembayaran()->get();
                $lunas = $pembayaran->where('status', 'Paid');

                return (object) [
                    'judul' => $t->judul, 'via_rt' => false, 'tenggat' => $t->tenggat?->translatedFormat('d M Y'),
                    'lunas' => $lunas->count(), 'total' => $pembayaran->count(),
                    'terkumpul' => (int) $lunas->sum(fn ($p) => $p->nominal ?: $t->nominal), 'disetor' => null, 'sisa' => null,
                    'persen' => $pembayaran->count() > 0 ? (int) round($lunas->count() / $pembayaran->count() * 100) : 0,
                ];
            });
    }

    protected static function grafik(Carbon $now): array
    {
        $labels = $masuk = $keluar = [];
        for ($i = 5; $i >= 0; $i--) {
            $bulan = $now->copy()->startOfMonth()->subMonths($i);
            $a = self::agregat(self::transaksi()->whereMonth('tanggal', $bulan->month)->whereYear('tanggal', $bulan->year));
            $labels[] = $bulan->translatedFormat('M');
            $masuk[] = $a['masuk'];
            $keluar[] = $a['keluar'];
        }

        return compact('labels', 'masuk', 'keluar');
    }

    protected static function perKategori(string $tipe, Carbon $now): Collection
    {
        $rows = self::transaksi()->where('tipe', $tipe)
            ->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
            ->selectRaw('kategori, SUM(jumlah) AS total')->groupBy('kategori')->orderByDesc('total')->take(5)->get();
        $total = (int) $rows->sum('total');

        return $rows->map(fn ($r) => (object) [
            'nama'   => $r->kategori ?: 'Lainnya',
            'total'  => (int) $r->total,
            'persen' => $total > 0 ? (int) round($r->total / $total * 100) : 0,
        ]);
    }

    protected static function transaksiTerbaru(): Collection
    {
        $dariSetoran = SetoranRt::whereNotNull('transaksi_rw_id')->pluck('transaksi_rw_id')->flip();
        $dariIuran = PembayaranTagihan::whereNotNull('transaksi_id')->pluck('transaksi_id')->flip();

        return self::transaksi()->orderByDesc('tanggal')->orderByDesc('created_at')->take(5)->get()
            ->map(fn ($t) => (object) [
                'judul'     => $t->judul,
                'tanggal'   => $t->tanggal->format('d M Y'),
                'jumlah'    => (int) $t->jumlah,
                'pemasukan' => $t->tipe === 'pemasukan',
                'label'     => $dariSetoran->has($t->id) ? 'Setoran RT' : ($dariIuran->has($t->id) ? 'Iuran' : null),
            ]);
    }
}
