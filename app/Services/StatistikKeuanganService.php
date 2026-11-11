<?php

namespace App\Services;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Statistik keuangan untuk pimpinan (Ketua RT, Ketua RW, Admin RW).
 *
 * Dasar angka: laporan berstatus Approved (sudah terbit atau belum). Bila `$hanyaTerbit` aktif
 * (pimpinan melihat unit lain), hanya laporan yang sudah dipublikasikan yang dipakai dan data
 * operasional (transaksi Pending, bukti, laporan ditolak) tidak ditampilkan.
 * Hanya angka agregat: tidak ada nama warga maupun nominal per KK.
 */
class StatistikKeuanganService
{
    /** Pengeluaran sebesar ini atau lebih tanpa bukti dianggap perlu perhatian. */
    public const BATAS_TANPA_BUKTI = 500000;

    /** Pengeluaran bulan terakhir lebih dari kelipatan ini dari rata-rata bulan sebelumnya dianggap lonjakan. */
    public const LONJAKAN = 1.5;

    /** Laporan dianggap terlambat bila disetujui setelah tanggal ini pada bulan berikutnya. */
    public const BATAS_TERLAMBAT_HARI = 15;

    private const URUTAN_LEVEL = ['baik' => 0, 'waspada' => 1, 'kritis' => 2];

    protected static function rp(int $n): string
    {
        return 'Rp ' . number_format($n, 0, ',', '.');
    }

    protected static function scopeRt($q, string $unit, ?string $rtId, bool $semuaRt)
    {
        if ($unit === 'RT' && ! $semuaRt) {
            $rtId ? $q->where('rt_id', $rtId) : $q->whereRaw('1 = 0');
        }

        return $q;
    }

    protected static function laporanQuery(string $unit, ?string $rtId, bool $semuaRt, bool $hanyaTerbit, bool $semuaStatus = false)
    {
        $q = self::scopeRt(LaporanKeuangan::where('unit', $unit), $unit, $rtId, $semuaRt);
        if (! $semuaStatus) {
            $q->where('status', 'Approved');
            if ($hanyaTerbit) {
                $q->where('is_published', true);
            }
        }

        return $q;
    }

    protected static function transaksi(string $unit, ?string $rtId, bool $semuaRt)
    {
        return self::scopeRt(
            TransaksiKeuangan::where('unit_sumber', $unit)->where('status', '!=', 'Rejected'),
            $unit, $rtId, $semuaRt
        );
    }

    /** Tahun yang punya laporan pada lingkup ini (terbaru dulu). */
    public static function daftarTahun(string $unit, ?string $rtId, bool $semuaRt, bool $hanyaTerbit): array
    {
        return self::laporanQuery($unit, $rtId, $semuaRt, $hanyaTerbit)
            ->select('periode_tahun')->distinct()->orderByDesc('periode_tahun')
            ->pluck('periode_tahun')->map(fn ($t) => (int) $t)->all();
    }

    public static function data(string $unit, ?string $rtId, bool $semuaRt, int $tahun, bool $hanyaTerbit, bool $bolehRincianRt): array
    {
        $now = Carbon::now();
        $penuh = ! $hanyaTerbit;
        $nama = TransparansiKeuanganService::NAMA_BULAN;

        // 1. Per bulan dari laporan (gabungan bila beberapa RT).
        $laporan = self::laporanQuery($unit, $rtId, $semuaRt, $hanyaTerbit)
            ->where('periode_tahun', $tahun)->orderBy('periode_bulan')->get()->groupBy('periode_bulan');

        $perBulan = [];
        foreach ($nama as $no => $namaBulan) {
            $g = $laporan->get($no);
            $perBulan[$no] = [
                'bulan'       => $no,
                'nama'        => $namaBulan,
                'ada'         => (bool) $g,
                'pemasukan'   => $g ? (int) $g->sum('total_pemasukan') : 0,
                'pengeluaran' => $g ? (int) $g->sum('total_pengeluaran') : 0,
                'saldo_awal'  => $g ? (int) $g->sum('saldo_awal') : 0,
                'saldo_akhir' => $g ? (int) $g->sum('saldo_akhir') : 0,
            ];
        }

        $ada = collect($perBulan)->where('ada', true)->values();
        $n = $ada->count();
        $terakhir = $ada->last();
        $sebelum = $n >= 2 ? $ada[$n - 2] : null;

        // 2. Ringkasan.
        $totalMasuk = (int) $ada->sum('pemasukan');
        $totalKeluar = (int) $ada->sum('pengeluaran');
        $rataKeluar = $n > 0 ? (int) round($totalKeluar / $n) : 0;
        $saldo = $terakhir ? (int) $terakhir['saldo_akhir'] : null;
        $saldoAwal = $n > 0 ? (int) $ada->first()['saldo_awal'] : null;
        $cadangan = ($saldo !== null && $rataKeluar > 0) ? round(max(0, $saldo) / $rataKeluar, 1) : null;
        $rasio = $totalMasuk > 0 ? (int) round($totalKeluar / $totalMasuk * 100) : null;
        $perubahanKeluar = ($sebelum && $sebelum['pengeluaran'] > 0)
            ? (int) round(($terakhir['pengeluaran'] - $sebelum['pengeluaran']) / $sebelum['pengeluaran'] * 100)
            : null;

        $ringkasan = [
            'saldo'            => $saldo,
            'periode'          => $terakhir ? $terakhir['nama'] . ' ' . $tahun : null,
            'saldo_awal'       => $saldoAwal,
            'perubahan_saldo'  => $n > 0 ? $totalMasuk - $totalKeluar : null,
            'total_masuk'      => $totalMasuk,
            'total_keluar'     => $totalKeluar,
            'selisih'          => $totalMasuk - $totalKeluar,
            'rasio'            => $rasio,
            'rata_keluar'      => $rataKeluar,
            'cadangan'         => $cadangan,
            'perubahan_keluar' => $perubahanKeluar,
            'pembanding'       => $sebelum ? $sebelum['nama'] : null,
        ];

        // 3. Komposisi kategori hanya pada bulan yang laporannya dipakai.
        $bulanList = $ada->pluck('bulan')->map(fn ($b) => (int) $b)->all();
        $kategori = $n > 0
            ? self::kategori($unit, $rtId, $semuaRt, $tahun, $bulanList, $terakhir, $sebelum)
            : ['masuk' => [], 'keluar' => [], 'perubahan' => null];

        // 4. Iuran wajib dan setoran (RT/RW saja).
        $iuran = self::iuran($unit, $rtId, $semuaRt, $tahun, $now, $bolehRincianRt);

        // 5. Data operasional (hanya untuk unit sendiri).
        $tata = $penuh ? self::tataKelola($unit, $rtId, $semuaRt, $tahun, $now) : null;
        $berjalan = $penuh ? self::berjalan($unit, $rtId, $semuaRt, $tahun, $now, $perBulan) : null;

        $perhatian = self::perhatian($ringkasan, $ada, $tata, $iuran, $penuh);
        $penilaian = self::penilaian($ringkasan, $n, $tahun, $iuran);

        return [
            'unit'        => $unit,
            'tahun'       => $tahun,
            'penuh'       => $penuh,
            'n_bulan'     => $n,
            'per_bulan'   => array_values($perBulan),
            'berjalan'    => $berjalan,
            'ringkasan'   => $ringkasan,
            'penilaian'   => $penilaian,
            'kategori'    => $kategori,
            'iuran'       => $iuran,
            'tata_kelola' => $tata,
            'perhatian'   => $perhatian,
        ];
    }

    // ───────────────────────── Komponen ─────────────────────────

    protected static function berjalan(string $unit, ?string $rtId, bool $semuaRt, int $tahun, Carbon $now, array $perBulan): ?array
    {
        if ($tahun !== $now->year || $perBulan[$now->month]['ada']) {
            return null;
        }
        $r = self::transaksi($unit, $rtId, $semuaRt)
            ->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
            ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) AS masuk,
                         COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) AS keluar")->first();
        if ((int) $r->masuk + (int) $r->keluar === 0) {
            return null;
        }

        return [
            'nama'        => TransparansiKeuanganService::NAMA_BULAN[$now->month] . ' ' . $now->year,
            'pemasukan'   => (int) $r->masuk,
            'pengeluaran' => (int) $r->keluar,
        ];
    }

    protected static function kategori(string $unit, ?string $rtId, bool $semuaRt, int $tahun, array $bulanList, array $terakhir, ?array $sebelum): array
    {
        $baris = self::transaksi($unit, $rtId, $semuaRt)
            ->whereYear('tanggal', $tahun)->whereIn(DB::raw('MONTH(tanggal)'), $bulanList)
            ->selectRaw("tipe, COALESCE(NULLIF(kategori, ''), 'Lainnya') AS kat, SUM(jumlah) AS total")
            ->groupBy('tipe', 'kat')->orderByDesc('total')->get();

        $susun = function (string $tipe) use ($baris) {
            $rows = $baris->where('tipe', $tipe)->values();
            $total = (int) $rows->sum('total');

            return $rows->take(6)->map(fn ($r) => [
                'nama'   => $r->kat,
                'total'  => (int) $r->total,
                'persen' => $total > 0 ? (int) round($r->total / $total * 100) : 0,
            ])->all();
        };

        // Kategori pengeluaran dengan perubahan terbesar antara dua bulan terakhir.
        $perubahan = null;
        if ($sebelum) {
            $perBulanKat = function (int $bulan) use ($unit, $rtId, $semuaRt, $tahun) {
                return self::transaksi($unit, $rtId, $semuaRt)->where('tipe', 'pengeluaran')
                    ->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)
                    ->selectRaw("COALESCE(NULLIF(kategori, ''), 'Lainnya') AS kat, SUM(jumlah) AS total")
                    ->groupBy('kat')->pluck('total', 'kat')->map(fn ($v) => (int) $v)->all();
            };
            $kini = $perBulanKat((int) $terakhir['bulan']);
            $lalu = $perBulanKat((int) $sebelum['bulan']);
            $maks = null;
            foreach (array_unique(array_merge(array_keys($kini), array_keys($lalu))) as $kat) {
                $selisih = ($kini[$kat] ?? 0) - ($lalu[$kat] ?? 0);
                if ($selisih !== 0 && (! $maks || abs($selisih) > abs($maks['selisih']))) {
                    $maks = ['nama' => $kat, 'sekarang' => $kini[$kat] ?? 0, 'sebelumnya' => $lalu[$kat] ?? 0, 'selisih' => $selisih];
                }
            }
            $perubahan = $maks ? $maks + ['bulan' => $terakhir['nama'], 'pembanding' => $sebelum['nama']] : null;
        }

        return ['masuk' => $susun('pemasukan'), 'keluar' => $susun('pengeluaran'), 'perubahan' => $perubahan];
    }

    /**
     * Kesehatan iuran wajib dan kewajiban setoran RT ke RW. Null bila tidak relevan
     * (DKM, atau RW tanpa hak melihat rincian per RT).
     */
    protected static function iuran(string $unit, ?string $rtId, bool $semuaRt, int $tahun, Carbon $now, bool $bolehRincianRt): ?array
    {
        if ($unit === 'DKM') {
            return null;
        }
        if ($unit === 'RT' && ! $semuaRt) {
            $ids = $rtId ? [$rtId] : [];
        } elseif ($unit === 'RT' || ($unit === 'RW' && $bolehRincianRt)) {
            $ids = MasterRt::pluck('id')->all();
        } else {
            return null;
        }
        if (empty($ids)) {
            return null;
        }

        $sampai = $tahun < $now->year ? 12 : ($tahun > $now->year ? 0 : $now->month);
        $sampaiTagih = $tahun < $now->year ? 12 : ($tahun > $now->year ? 0 : $now->month - 1);

        $rts = MasterRt::whereIn('id', $ids)->orderBy('kode_rt')->get();
        $kkPerRt = Keluarga::whereIn('rt_id', $ids)->get(['id', 'rt_id'])->groupBy('rt_id');
        $adaMaster = DB::table('master_iuran')->where('tahun', $tahun)->whereIn('rt_id', $ids)->pluck('id', 'rt_id');
        $hitung = SetoranRtService::hitung($tahun);

        $lunasPerKk = $sampaiTagih > 0
            ? IuranPembayaran::query()
                ->join('master_iuran', 'master_iuran.id', '=', 'iuran_pembayaran.master_iuran_id')
                ->where('master_iuran.tahun', $tahun)->whereIn('master_iuran.rt_id', $ids)
                ->where('iuran_pembayaran.status', 'lunas')->where('iuran_pembayaran.bulan', '<=', $sampaiTagih)
                ->groupBy('iuran_pembayaran.keluarga_id')
                ->selectRaw('iuran_pembayaran.keluarga_id AS kk, COUNT(DISTINCT iuran_pembayaran.bulan) AS n')
                ->pluck('n', 'kk')
            : collect();

        $baris = [];
        $tot = ['kk' => 0, 'lunas_kk_bulan' => 0, 'terkumpul' => 0, 'tepat' => 0, 'tunggak1' => 0, 'tunggak2' => 0,
                'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0];
        $lunasBulan = array_fill(1, 12, 0);
        $kkBermaster = 0;

        foreach ($rts as $rt) {
            $kkList = $kkPerRt->get($rt->id, collect());
            $kk = $kkList->count();
            $punyaMaster = $adaMaster->has($rt->id);
            $perBulan = $hitung[$rt->id] ?? [];

            $lunasKkBulan = $terkumpul = $kewajiban = $disetor = $menunggu = 0;
            for ($b = 1; $b <= $sampai; $b++) {
                $lunasKkBulan += $perBulan[$b]['lunas'] ?? 0;
                $terkumpul += $perBulan[$b]['terkumpul'] ?? 0;
                $kewajiban += $perBulan[$b]['kewajiban'] ?? 0;
                $disetor += $perBulan[$b]['disetor'] ?? 0;
                $menunggu += $perBulan[$b]['menunggu'] ?? 0;
                if ($punyaMaster) {
                    $lunasBulan[$b] += $perBulan[$b]['lunas'] ?? 0;
                }
            }

            $tepat = $t1 = $t2 = 0;
            if ($punyaMaster) {
                foreach ($kkList as $k) {
                    $tunggak = max(0, $sampaiTagih - (int) ($lunasPerKk[$k->id] ?? 0));
                    $tunggak === 0 ? $tepat++ : ($tunggak === 1 ? $t1++ : $t2++);
                }
                $kkBermaster += $kk;
            }

            $row = [
                'rt'        => $rt->kode_rt,
                'kk'        => $kk,
                'ada_iuran' => $punyaMaster,
                'tingkat'   => ($punyaMaster && $kk > 0 && $sampai > 0) ? min(100, (int) round($lunasKkBulan / ($kk * $sampai) * 100)) : null,
                'terkumpul' => $terkumpul,
                'tepat'     => $tepat, 'tunggak1' => $t1, 'tunggak2' => $t2,
                'kewajiban' => $kewajiban, 'disetor' => $disetor, 'menunggu' => $menunggu,
                'sisa'      => max(0, $kewajiban - $disetor),
            ];
            $baris[] = $row;

            if ($punyaMaster) {
                $tot['kk'] += $kk;
                $tot['lunas_kk_bulan'] += $lunasKkBulan;
            }
            foreach (['terkumpul', 'tepat', 'tunggak1', 'tunggak2', 'kewajiban', 'disetor', 'menunggu', 'sisa'] as $k) {
                $tot[$k] += $row[$k];
            }
        }

        $tren = [];
        for ($b = 1; $b <= $sampai; $b++) {
            $tren[] = ['bulan' => TransparansiKeuanganService::NAMA_BULAN[$b], 'persen' => $kkBermaster > 0 ? min(100, (int) round($lunasBulan[$b] / $kkBermaster * 100)) : null];
        }

        return [
            'sampai'       => $sampai,
            'sampai_nama'  => $sampai > 0 ? TransparansiKeuanganService::NAMA_BULAN[$sampai] : null,
            'sampai_tagih' => $sampaiTagih,
            'baris'        => $baris,
            'bandingkan'   => count($baris) > 1,
            'total'        => $tot + [
                'tingkat' => ($tot['kk'] > 0 && $sampai > 0) ? min(100, (int) round($tot['lunas_kk_bulan'] / ($tot['kk'] * $sampai) * 100)) : null,
            ],
            'tren'         => $tren,
        ];
    }

    protected static function tataKelola(string $unit, ?string $rtId, bool $semuaRt, int $tahun, Carbon $now): array
    {
        $keluar = self::transaksi($unit, $rtId, $semuaRt)->where('tipe', 'pengeluaran')->whereYear('tanggal', $tahun);
        $jumlahKeluar = (clone $keluar)->count();
        $denganBukti = (clone $keluar)->whereNotNull('bukti_file')->where('bukti_file', '<>', '')->count();
        $tanpaBuktiBesar = (clone $keluar)->where(fn ($q) => $q->whereNull('bukti_file')->orWhere('bukti_file', ''))
            ->where('jumlah', '>=', self::BATAS_TANPA_BUKTI)->count();
        $pending = (clone $keluar)->where('status', 'Pending');

        // Laporan: ditolak, terlambat, dan bulan yang belum dibuat.
        $semua = self::laporanQuery($unit, $rtId, $semuaRt, false, true)->where('periode_tahun', $tahun)->get();
        $disetujui = $semua->where('status', 'Approved');
        $terlambat = $disetujui->filter(function ($l) {
            if (! $l->tanggal_disetujui) {
                return false;
            }
            $batas = Carbon::create((int) $l->periode_tahun, (int) $l->periode_bulan, 1)->addMonth()
                ->addDays(self::BATAS_TERLAMBAT_HARI - 1)->endOfDay();

            return $l->tanggal_disetujui->gt($batas);
        })->count();

        $belumDibuat = [];
        if (! ($unit === 'RT' && $semuaRt) && $semua->isNotEmpty()) {
            $terakhirSelesai = $tahun < $now->year ? 12 : ($tahun > $now->year ? 0 : $now->month - 1);
            $awal = (int) $semua->min('periode_bulan');
            $punya = $semua->pluck('periode_bulan')->map(fn ($b) => (int) $b)->all();
            for ($b = $awal; $b <= $terakhirSelesai; $b++) {
                if (! in_array($b, $punya, true)) {
                    $belumDibuat[] = TransparansiKeuanganService::NAMA_BULAN[$b];
                }
            }
        }

        return [
            'pengeluaran'       => $jumlahKeluar,
            'bukti_persen'      => $jumlahKeluar > 0 ? (int) round($denganBukti / $jumlahKeluar * 100) : null,
            'tanpa_bukti_besar' => $tanpaBuktiBesar,
            'pending_jumlah'    => (clone $pending)->count(),
            'pending_nominal'   => (int) (clone $pending)->sum('jumlah'),
            'laporan_ditolak'   => $semua->where('status', 'Rejected')->count(),
            'laporan_terlambat' => $terlambat,
            'laporan_total'     => $disetujui->count(),
            'belum_dibuat'      => $belumDibuat,
        ];
    }

    protected static function naikkan(string $level, string $minimal): string
    {
        return self::URUTAN_LEVEL[$minimal] > self::URUTAN_LEVEL[$level] ? $minimal : $level;
    }

    protected static function penilaian(array $r, int $n, int $tahun, ?array $iuran): array
    {
        if ($n === 0) {
            return ['level' => 'kosong', 'kalimat' => ["Belum ada laporan yang disetujui pada tahun {$tahun}, sehingga statistik belum dapat dihitung."]];
        }

        $level = 'baik';
        $k = [];

        $kalimat = 'Saldo kas ' . self::rp($r['saldo']) . ' per ' . $r['periode'];
        if ($r['cadangan'] !== null) {
            $kalimat .= ', cukup untuk ' . number_format($r['cadangan'], 1, ',', '.') . ' bulan pengeluaran rata-rata.';
        } else {
            $kalimat .= '. Belum ada pengeluaran tercatat.';
        }
        $k[] = $kalimat;

        if ($r['saldo'] < 0 || ($r['cadangan'] !== null && $r['cadangan'] < 1)) {
            $level = 'kritis';
        } elseif ($r['cadangan'] !== null && $r['cadangan'] < 3) {
            $level = self::naikkan($level, 'waspada');
        }

        if ($r['rasio'] !== null) {
            if ($r['rasio'] > 100) {
                $k[] = 'Pengeluaran melebihi pemasukan (rasio ' . $r['rasio'] . '%).';
                $level = self::naikkan($level, 'waspada');
            } else {
                $k[] = 'Pemasukan masih lebih besar dari pengeluaran (surplus ' . self::rp($r['selisih']) . ').';
            }
        }

        if ($r['perubahan_keluar'] !== null && abs($r['perubahan_keluar']) >= 20) {
            $arah = $r['perubahan_keluar'] > 0 ? 'naik' : 'turun';
            $k[] = 'Pengeluaran ' . $arah . ' ' . abs($r['perubahan_keluar']) . '% dibanding ' . $r['pembanding'] . '.';
            if ($r['perubahan_keluar'] >= 50) {
                $level = self::naikkan($level, 'waspada');
            }
        }

        $tingkat = $iuran['total']['tingkat'] ?? null;
        if ($tingkat !== null && $tingkat < 60) {
            $k[] = 'Pengumpulan iuran wajib baru ' . $tingkat . '% sampai ' . $iuran['sampai_nama'] . '.';
            $level = self::naikkan($level, 'waspada');
        }

        if ($n < 3) {
            $k[] = "Data baru {$n} bulan, tren belum bermakna.";
        }

        return ['level' => $level, 'kalimat' => $k];
    }

    protected static function perhatian(array $r, $ada, ?array $tata, ?array $iuran, bool $penuh): array
    {
        $item = [];
        $tambah = function (string $level, string $teks) use (&$item) {
            $item[] = ['level' => $level, 'teks' => $teks];
        };

        if ($r['saldo'] !== null && $r['saldo'] < 0) {
            $tambah('danger', 'Saldo kas negatif (' . self::rp($r['saldo']) . ').');
        } elseif ($r['cadangan'] !== null && $r['cadangan'] < 1) {
            $tambah('warning', 'Cadangan kas kurang dari 1 bulan pengeluaran rata-rata.');
        }

        if ($ada->count() >= 3) {
            $terakhir = $ada->last();
            $sebelumnya = $ada->slice(0, -1);
            $rata = $sebelumnya->avg('pengeluaran');
            if ($rata > 0 && $terakhir['pengeluaran'] > $rata * self::LONJAKAN) {
                $tambah('warning', 'Pengeluaran ' . $terakhir['nama'] . ' ' . self::rp($terakhir['pengeluaran'])
                    . ' jauh di atas rata-rata bulan sebelumnya (' . self::rp((int) round($rata)) . ').');
            }
        }

        if ($tata) {
            if ($tata['pending_jumlah'] > 0) {
                $tambah('warning', $tata['pending_jumlah'] . ' pengeluaran (' . self::rp($tata['pending_nominal']) . ') belum diputuskan.');
            }
            if ($tata['tanpa_bukti_besar'] > 0) {
                $tambah('warning', $tata['tanpa_bukti_besar'] . ' pengeluaran sebesar ' . self::rp(self::BATAS_TANPA_BUKTI) . ' atau lebih tidak memiliki bukti.');
            }
            if (! empty($tata['belum_dibuat'])) {
                $tambah('warning', 'Laporan belum dibuat untuk: ' . implode(', ', $tata['belum_dibuat']) . '.');
            }
            if ($tata['laporan_ditolak'] > 0) {
                $tambah('info', $tata['laporan_ditolak'] . ' laporan ditolak dan perlu revisi.');
            }
        }

        if ($iuran) {
            if ($iuran['total']['tunggak2'] > 0) {
                $tambah('warning', $iuran['total']['tunggak2'] . ' KK menunggak iuran 2 bulan atau lebih.');
            }
            if ($iuran['total']['sisa'] > 0) {
                $tambah('warning', 'Kewajiban setoran ke RW belum terpenuhi sebesar ' . self::rp($iuran['total']['sisa']) . '.');
            }
        }

        usort($item, fn ($a, $b) => ['danger' => 0, 'warning' => 1, 'info' => 2][$a['level']] <=> ['danger' => 0, 'warning' => 1, 'info' => 2][$b['level']]);

        return $item;
    }
}
