<?php

namespace App\Services;

use App\Models\IuranPembayaran;
use App\Models\KategoriTransaksi;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\SetoranPengaturan;
use App\Models\SetoranRt;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Setoran kolektif RT ke RW.
 *
 * Kewajiban setoran satu bulan = nominal setoran per KK x jumlah KK yang LUNAS pada bulan itu.
 * Setoran dicatat RT, dikonfirmasi RW; saat dikonfirmasi dibuat dua transaksi kas
 * (pengeluaran RT dan pemasukan RW) sehingga angka kedua kas selalu cocok.
 */
class SetoranRtService
{
    public const KATEGORI_KELUAR = 'Setoran ke RW';
    public const KATEGORI_MASUK  = 'Setoran Iuran RT';

    // ───────────────────────── Pengaturan (dengan riwayat berlaku-mulai) ─────────────────────────

    /** Baris pengaturan satu tahun (semua RT + default). */
    protected static function barisPengaturan(int $tahun): Collection
    {
        return SetoranPengaturan::where('tahun', $tahun)->get();
    }

    /** Pilih nominal yang berlaku pada bulan tertentu dari kumpulan baris pengaturan. */
    protected static function pilih(Collection $baris, ?string $rtId, int $bulan): int
    {
        if ($rtId) {
            $khusus = $baris->where('rt_id', $rtId)->where('berlaku_mulai_bulan', '<=', $bulan)
                ->sortBy('berlaku_mulai_bulan')->last();
            if ($khusus && $khusus->nominal_per_kk !== null) {
                return (int) $khusus->nominal_per_kk;
            }
        }
        $default = $baris->whereNull('rt_id')->where('berlaku_mulai_bulan', '<=', $bulan)
            ->sortBy('berlaku_mulai_bulan')->last();

        return $default ? (int) $default->nominal_per_kk : 0;
    }

    /** Bulan acuan "sekarang" untuk tahun tertentu: bulan berjalan, 12 (tahun lampau), 1 (tahun depan). */
    protected static function bulanAcuan(int $tahun): int
    {
        $tahunIni = (int) now()->format('Y');

        return $tahun < $tahunIni ? 12 : ($tahun > $tahunIni ? 1 : (int) now()->format('n'));
    }

    /**
     * Nominal setoran per KK untuk RT dan bulan tertentu. Tanpa $bulan: yang berlaku saat ini.
     */
    public static function nominalPerKk(int $tahun, ?string $rtId, ?int $bulan = null): int
    {
        return self::pilih(self::barisPengaturan($tahun), $rtId, $bulan ?? self::bulanAcuan($tahun));
    }

    /**
     * Riwayat pengaturan satu tahun untuk tampilan.
     *
     * @return array{default:array<int,array{bulan:int,nominal:int}>, per_rt:array<string,array<int,array{bulan:int,nominal:?int}>>}
     */
    public static function riwayatPengaturan(int $tahun): array
    {
        $baris = self::barisPengaturan($tahun)->sortBy('berlaku_mulai_bulan');
        $hasil = ['default' => [], 'per_rt' => []];
        foreach ($baris as $r) {
            if ($r->rt_id === null) {
                $hasil['default'][] = ['bulan' => (int) $r->berlaku_mulai_bulan, 'nominal' => (int) $r->nominal_per_kk];
            } else {
                $hasil['per_rt'][$r->rt_id][] = [
                    'bulan'   => (int) $r->berlaku_mulai_bulan,
                    'nominal' => $r->nominal_per_kk === null ? null : (int) $r->nominal_per_kk,
                ];
            }
        }

        return $hasil;
    }

    /**
     * Nominal yang berlaku saat ini (untuk isian form): default dan override per RT.
     *
     * @return array{default:int, per_rt:array<string,int>}
     */
    public static function pengaturan(int $tahun): array
    {
        $baris = self::barisPengaturan($tahun);
        $bulan = self::bulanAcuan($tahun);
        $perRt = [];
        foreach ($baris->whereNotNull('rt_id')->pluck('rt_id')->unique() as $rtId) {
            $khusus = $baris->where('rt_id', $rtId)->where('berlaku_mulai_bulan', '<=', $bulan)
                ->sortBy('berlaku_mulai_bulan')->last();
            if ($khusus && $khusus->nominal_per_kk !== null) {
                $perRt[$rtId] = (int) $khusus->nominal_per_kk;
            }
        }

        return ['default' => self::pilih($baris, null, $bulan), 'per_rt' => $perRt];
    }

    /** True bila tahun tersebut belum punya nominal default (pengaturan awal). */
    public static function belumAdaPengaturan(int $tahun): bool
    {
        return ! SetoranPengaturan::where('tahun', $tahun)->whereNull('rt_id')->exists();
    }

    /**
     * Bulan paling awal yang boleh dipilih sebagai awal berlaku nominal baru (tanpa berlaku surut):
     * bulan berjalan, dan sesudah bulan terakhir yang sudah memiliki setoran. Untuk pengaturan awal
     * (belum ada nominal sama sekali) boleh sejak Januari selama belum ada setoran. >12 = tidak dapat diubah.
     */
    public static function bulanMulaiTerawal(int $tahun): int
    {
        $tahunIni = (int) now()->format('Y');
        if ($tahun < $tahunIni) {
            return 13;
        }
        $terakhir = (int) SetoranRt::whereNull('tagihan_id')->where('periode_tahun', $tahun)
            ->whereIn('status', ['menunggu', 'dikonfirmasi'])->max('periode_bulan');
        if (self::belumAdaPengaturan($tahun)) {
            return max(1, $terakhir + 1);
        }
        $dasar = $tahun > $tahunIni ? 1 : (int) now()->format('n');

        return max($dasar, $terakhir + 1);
    }

    /**
     * Simpan nominal yang berlaku mulai bulan tertentu. Bulan sebelumnya tidak berubah.
     * Untuk RT khusus, nominal null berarti "ikut nominal untuk semua RT" mulai bulan itu.
     */
    public static function simpanPengaturan(int $tahun, ?string $rtId, ?int $nominal, int $bulanMulai = 1): void
    {
        if ($rtId === null && $nominal === null) {
            return;
        }

        SetoranPengaturan::updateOrCreate(
            ['tahun' => $tahun, 'rt_id' => $rtId, 'berlaku_mulai_bulan' => $bulanMulai],
            ['nominal_per_kk' => $nominal]
        );
    }

    // ───────────────────────── Rekap ─────────────────────────

    /**
     * Rekap per RT per bulan untuk satu tahun. Nominal iuran dan nominal setoran
     * mengikuti nominal yang berlaku pada bulan masing-masing.
     *
     * @return array<string, array<int, array{lunas:int, terkumpul:int, kewajiban:int, disetor:int, menunggu:int, sisa:int}>>
     *         [rt_id][bulan] => angka
     */
    public static function hitung(int $tahun, ?string $rtId = null): array
    {
        $baris = self::barisPengaturan($tahun);
        $master = MasterIuran::with('riwayat')->where('tahun', $tahun)
            ->when($rtId, fn ($q) => $q->where('rt_id', $rtId))->get()->keyBy('rt_id');

        $lunas = IuranPembayaran::query()
            ->join('master_iuran', 'master_iuran.id', '=', 'iuran_pembayaran.master_iuran_id')
            ->where('master_iuran.tahun', $tahun)
            ->where('iuran_pembayaran.status', 'lunas')
            ->when($rtId, fn ($q) => $q->where('master_iuran.rt_id', $rtId))
            ->groupBy('master_iuran.rt_id', 'iuran_pembayaran.bulan')
            ->selectRaw('master_iuran.rt_id AS rt_id, iuran_pembayaran.bulan AS bulan,
                COUNT(DISTINCT iuran_pembayaran.keluarga_id) AS jumlah')
            ->get();

        $setoran = SetoranRt::query()
            ->whereNull('tagihan_id')
            ->where('periode_tahun', $tahun)
            ->whereIn('status', ['dikonfirmasi', 'menunggu'])
            ->when($rtId, fn ($q) => $q->where('rt_id', $rtId))
            ->groupBy('rt_id', 'periode_bulan', 'status')
            ->selectRaw('rt_id, periode_bulan AS bulan, status, SUM(nominal) AS total')
            ->get();

        $hasil = [];
        $kosong = ['lunas' => 0, 'terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0];

        foreach ($lunas as $r) {
            $b = (int) $r->bulan;
            $perKk = self::pilih($baris, $r->rt_id, $b);
            $nominalIuran = $master->get($r->rt_id)?->nominalUntukBulan($b) ?? 0;
            $hasil[$r->rt_id][$b] = array_merge($kosong, [
                'lunas'     => (int) $r->jumlah,
                'terkumpul' => (int) $r->jumlah * $nominalIuran,
                'kewajiban' => (int) $r->jumlah * $perKk,
            ]);
        }

        foreach ($setoran as $s) {
            $b = (int) $s->bulan;
            $hasil[$s->rt_id][$b] = $hasil[$s->rt_id][$b] ?? $kosong;
            $kunci = $s->status === 'dikonfirmasi' ? 'disetor' : 'menunggu';
            $hasil[$s->rt_id][$b][$kunci] = (int) $s->total;
        }

        foreach ($hasil as $rt => $perBulan) {
            foreach ($perBulan as $b => $angka) {
                $hasil[$rt][$b]['sisa'] = $angka['kewajiban'] - $angka['disetor'];
            }
            ksort($hasil[$rt]);
        }

        return $hasil;
    }
    /**
     * Rekap satu RT untuk tampilan bendahara RT: baris Januari sampai bulan berjalan.
     *
     * @return array{per_bulan:array, total:array, nominal_per_kk:int}
     */
    public static function rekapRt(string $rtId, int $tahun, int $sampaiBulan): array
    {
        $data = self::hitung($tahun, $rtId)[$rtId] ?? [];
        $perBulan = [];
        $total = ['terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0];

        for ($b = 1; $b <= $sampaiBulan; $b++) {
            $a = $data[$b] ?? ['lunas' => 0, 'terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0];
            $a['nama'] = IuranService::BULAN[$b];
            $perBulan[$b] = $a;
            foreach ($total as $k => $_) {
                $total[$k] += $a[$k];
            }
        }

        return ['per_bulan' => $perBulan, 'total' => $total, 'nominal_per_kk' => self::nominalPerKk($tahun, $rtId, $sampaiBulan)];
    }

    /**
     * Rekap seluruh RT untuk tampilan RW: angka bulan terpilih + kumulatif Januari s.d. bulan itu.
     */
    public static function rekapSemuaRt(int $tahun, int $bulan): array
    {
        $data = self::hitung($tahun);
        $kkPerRt = Keluarga::selectRaw('rt_id, COUNT(*) AS jumlah')->groupBy('rt_id')->pluck('jumlah', 'rt_id');
        $barisPengaturan = self::barisPengaturan($tahun);

        $baris = [];
        foreach (MasterRt::orderBy('kode_rt')->get() as $rt) {
            $perBulan = $data[$rt->id] ?? [];
            $bln = $perBulan[$bulan] ?? ['lunas' => 0, 'terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0];

            $kum = ['kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0, 'terkumpul' => 0];
            for ($b = 1; $b <= $bulan; $b++) {
                foreach ($kum as $k => $_) {
                    $kum[$k] += $perBulan[$b][$k] ?? 0;
                }
            }
            $kum['sisa'] = max(0, $kum['kewajiban'] - $kum['disetor']);

            $baris[] = (object) [
                'rt'             => $rt,
                'kk_total'       => (int) ($kkPerRt[$rt->id] ?? 0),
                'nominal_per_kk' => self::pilih($barisPengaturan, $rt->id, $bulan),
                'bulan'          => $bln,
                'kumulatif'      => $kum,
                'status'         => self::statusBulan($bln),
            ];
        }

        $totalBulan = ['terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0];
        $totalKum = ['kewajiban' => 0, 'disetor' => 0, 'sisa' => 0];
        foreach ($baris as $r) {
            foreach ($totalBulan as $k => $_) {
                $totalBulan[$k] += $r->bulan[$k];
            }
            foreach ($totalKum as $k => $_) {
                $totalKum[$k] += $r->kumulatif[$k];
            }
        }

        return ['baris' => $baris, 'total_bulan' => $totalBulan, 'total_kumulatif' => $totalKum];
    }

    // ───────────────────────── Iuran Lainnya RW yang dikumpulkan lewat RT ─────────────────────────

    /**
     * Angka satu Iuran Lainnya (via RT) untuk satu RT. Kewajiban setoran = 100% dari yang terkumpul.
     *
     * @return array{total_kk:int, lunas:int, terkumpul:int, disetor:int, menunggu:int, sisa:int, bisa_disetor:int}
     */
    public static function angkaTagihan(Tagihan $tagihan, string $rtId): array
    {
        return self::angkaTagihanSemuaRt($tagihan)[$rtId] ?? self::angkaKosongTagihan();
    }

    protected static function angkaKosongTagihan(): array
    {
        return ['total_kk' => 0, 'lunas' => 0, 'terkumpul' => 0, 'disetor' => 0, 'menunggu' => 0, 'sisa' => 0, 'bisa_disetor' => 0];
    }

    /** @return array<string, array{total_kk:int, lunas:int, terkumpul:int, disetor:int, menunggu:int, sisa:int, bisa_disetor:int}> [rt_id] => angka */
    protected static function angkaTagihanSemuaRt(Tagihan $tagihan): array
    {
        $hasil = [];

        $bayar = PembayaranTagihan::query()
            ->join('keluarga', 'keluarga.id', '=', 'pembayaran_tagihan.keluarga_id')
            ->where('pembayaran_tagihan.tagihan_id', $tagihan->id)
            ->groupBy('keluarga.rt_id')
            ->selectRaw("keluarga.rt_id AS rt_id, COUNT(*) AS total_kk,
                SUM(CASE WHEN pembayaran_tagihan.status = 'Paid' THEN 1 ELSE 0 END) AS lunas,
                COALESCE(SUM(CASE WHEN pembayaran_tagihan.status = 'Paid' THEN COALESCE(pembayaran_tagihan.nominal, ?) ELSE 0 END), 0) AS terkumpul", [(int) $tagihan->nominal])
            ->get();

        foreach ($bayar as $r) {
            $hasil[$r->rt_id] = array_merge(self::angkaKosongTagihan(), [
                'total_kk'  => (int) $r->total_kk,
                'lunas'     => (int) $r->lunas,
                'terkumpul' => (int) $r->terkumpul,
            ]);
        }

        $setoran = SetoranRt::query()
            ->where('tagihan_id', $tagihan->id)
            ->whereIn('status', ['dikonfirmasi', 'menunggu'])
            ->groupBy('rt_id', 'status')
            ->selectRaw('rt_id, status, SUM(nominal) AS total')
            ->get();

        foreach ($setoran as $s) {
            $hasil[$s->rt_id] = $hasil[$s->rt_id] ?? self::angkaKosongTagihan();
            $hasil[$s->rt_id][$s->status === 'dikonfirmasi' ? 'disetor' : 'menunggu'] = (int) $s->total;
        }

        foreach ($hasil as $rt => $a) {
            $hasil[$rt]['sisa'] = max(0, $a['terkumpul'] - $a['disetor']);
            $hasil[$rt]['bisa_disetor'] = max(0, $a['terkumpul'] - $a['disetor'] - $a['menunggu']);
        }

        return $hasil;
    }

    /** Tagihan RW via RT yang relevan untuk satu RT, lengkap dengan angka setorannya. */
    public static function rekapTagihanRt(string $rtId): Collection
    {
        return Tagihan::where('unit', 'RW')->where('via_rt', true)->orderByDesc('created_at')->get()
            ->map(fn (Tagihan $t) => (object) array_merge(['tagihan' => $t], self::angkaTagihan($t, $rtId)));
    }

    /** Ringkasan satu Iuran Lainnya (via RT) per RT untuk tampilan RW. */
    public static function rekapTagihanSemuaRt(Tagihan $tagihan): array
    {
        $angka = self::angkaTagihanSemuaRt($tagihan);
        $baris = [];
        $total = self::angkaKosongTagihan();

        foreach (MasterRt::orderBy('kode_rt')->get() as $rt) {
            $a = $angka[$rt->id] ?? self::angkaKosongTagihan();
            $baris[] = (object) array_merge(['rt' => $rt], $a);
            foreach ($total as $k => $_) {
                $total[$k] += $a[$k];
            }
        }

        return ['baris' => $baris, 'total' => $total];
    }

    /** lunas | sebagian | menunggu | belum | tidak_ada (kewajiban 0). */
    protected static function statusBulan(array $a): string
    {
        if ($a['kewajiban'] <= 0) {
            return 'tidak_ada';
        }
        if ($a['disetor'] >= $a['kewajiban']) {
            return 'lunas';
        }
        if ($a['disetor'] > 0) {
            return 'sebagian';
        }

        return $a['menunggu'] > 0 ? 'menunggu' : 'belum';
    }

    // ───────────────────────── Setoran (batch) ─────────────────────────

    /**
     * Sisa yang masih dapat disetor per bulan: kewajiban dikurangi yang sudah dikonfirmasi dan yang menunggu.
     *
     * @return array<int, array{nama:string, lunas:int, terkumpul:int, kewajiban:int, disetor:int, menunggu:int, bisa:int}>
     */
    public static function bisaDisetorBulanan(string $rtId, int $tahun, int $sampaiBulan): array
    {
        $data = self::hitung($tahun, $rtId)[$rtId] ?? [];
        $hasil = [];
        for ($b = 1; $b <= $sampaiBulan; $b++) {
            $a = $data[$b] ?? ['lunas' => 0, 'terkumpul' => 0, 'kewajiban' => 0, 'disetor' => 0, 'menunggu' => 0];
            $hasil[$b] = [
                'nama'      => IuranService::BULAN[$b],
                'lunas'     => (int) $a['lunas'],
                'terkumpul' => (int) $a['terkumpul'],
                'kewajiban' => (int) $a['kewajiban'],
                'disetor'   => (int) $a['disetor'],
                'menunggu'  => (int) $a['menunggu'],
                'bisa'      => max(0, (int) $a['kewajiban'] - (int) $a['disetor'] - (int) $a['menunggu']),
            ];
        }

        return $hasil;
    }

    /**
     * Catat satu setoran iuran bulanan untuk beberapa bulan sekaligus.
     *
     * @param array<int,int> $items [bulan => nominal]
     * @param array{tanggal_setor:string, metode:string, bukti_file:?string, catatan:?string} $data
     * @throws \DomainException bila ada nominal/periode yang tidak valid
     */
    public static function buatBatchBulanan(string $rtId, int $tahun, array $items, array $data, string $userId): string
    {
        $items = array_filter($items, fn ($n) => (int) $n > 0);
        if (empty($items)) {
            throw new \DomainException('Pilih minimal satu bulan dengan nominal setoran.');
        }

        return DB::transaction(function () use ($rtId, $tahun, $items, $data, $userId) {
            $bisa = self::bisaDisetorBulanan($rtId, $tahun, 12);
            $batch = (string) Str::uuid();
            ksort($items);

            foreach ($items as $bulan => $nominal) {
                $bulan = (int) $bulan;
                $nominal = (int) $nominal;
                if ($bulan < 1 || $bulan > 12) {
                    throw new \DomainException('Bulan setoran tidak valid.');
                }
                if ($tahun * 100 + $bulan > (int) now()->format('Ym')) {
                    throw new \DomainException('Bulan ' . IuranService::BULAN[$bulan] . " {$tahun} belum berjalan, belum dapat disetor.");
                }
                if ($nominal > $bisa[$bulan]['bisa']) {
                    throw new \DomainException('Nominal setoran ' . IuranService::BULAN[$bulan] . ' melebihi sisa yang dapat disetor (Rp '
                        . number_format($bisa[$bulan]['bisa'], 0, ',', '.') . ').');
                }

                SetoranRt::create(self::dasarBaris($rtId, $batch, $data, $userId) + [
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                    'nominal'       => $nominal,
                ]);
            }

            return $batch;
        });
    }

    /**
     * Catat satu setoran Iuran Lainnya (RW via RT) untuk beberapa iuran sekaligus.
     *
     * @param array<string,int> $items [tagihan_id => nominal]
     * @throws \DomainException
     */
    public static function buatBatchLainnya(string $rtId, array $items, array $data, string $userId): string
    {
        $items = array_filter($items, fn ($n) => (int) $n > 0);
        if (empty($items)) {
            throw new \DomainException('Pilih minimal satu Iuran Lainnya dengan nominal setoran.');
        }

        return DB::transaction(function () use ($rtId, $items, $data, $userId) {
            $batch = (string) Str::uuid();
            $tanggal = strtotime($data['tanggal_setor']);

            foreach ($items as $tagihanId => $nominal) {
                $tagihan = Tagihan::where('id', $tagihanId)->where('unit', 'RW')->where('via_rt', true)->first();
                if (! $tagihan) {
                    throw new \DomainException('Iuran Lainnya tidak ditemukan atau tidak dikumpulkan lewat RT.');
                }
                $bisa = self::angkaTagihan($tagihan, $rtId)['bisa_disetor'];
                if ((int) $nominal > $bisa) {
                    throw new \DomainException('Nominal melebihi sisa yang dapat disetor untuk "' . $tagihan->judul . '" (Rp ' . number_format($bisa, 0, ',', '.') . ').');
                }

                SetoranRt::create(self::dasarBaris($rtId, $batch, $data, $userId) + [
                    'tagihan_id'    => $tagihan->id,
                    'periode_bulan' => (int) date('n', $tanggal),
                    'periode_tahun' => (int) date('Y', $tanggal),
                    'nominal'       => (int) $nominal,
                ]);
            }

            return $batch;
        });
    }

    protected static function dasarBaris(string $rtId, string $batch, array $data, string $userId): array
    {
        return [
            'rt_id'         => $rtId,
            'batch_id'      => $batch,
            'tanggal_setor' => $data['tanggal_setor'],
            'metode'        => $data['metode'],
            'bukti_file'    => $data['bukti_file'] ?? null,
            'catatan'       => $data['catatan'] ?? null,
            'status'        => 'menunggu',
            'dibuat_oleh'   => $userId,
        ];
    }

    /** Label ringkas satu setoran (batch): "Januari, Februari 2026" atau "Iuran Lainnya: A, B". */
    public static function labelGrup(\Illuminate\Support\Collection $baris): string
    {
        $lainnya = $baris->filter(fn ($s) => $s->tagihan_id);
        if ($lainnya->isNotEmpty() && $lainnya->count() === $baris->count()) {
            return 'Iuran Lainnya: ' . $baris->map(fn ($s) => $s->tagihan->judul ?? 'Lainnya')->unique()->implode(', ');
        }

        return $baris->whereNull('tagihan_id')->sortBy(fn ($s) => $s->periode_tahun * 100 + $s->periode_bulan)
            ->groupBy('periode_tahun')
            ->map(fn ($g, $tahun) => $g->map(fn ($s) => IuranService::BULAN[$s->periode_bulan])->implode(', ') . ' ' . $tahun)
            ->implode(' & ');
    }

    /**
     * Kelompokkan baris setoran menjadi satu entri per batch untuk tampilan.
     * Properti dibuat sejajar dengan SetoranRt agar view mudah memakainya.
     */
    public static function kelompok(\Illuminate\Support\Collection $baris): \Illuminate\Support\Collection
    {
        return $baris->groupBy(fn ($s) => $s->batch_id ?? $s->id)->map(function ($rows) {
            $rows = $rows->sortBy(fn ($s) => $s->periode_tahun * 100 + $s->periode_bulan)->values();
            $pertama = $rows->first();

            return (object) [
                'id'            => $pertama->id,
                'rows'          => $rows,
                'rt'            => $pertama->rt,
                'pembuat'       => $pertama->pembuat,
                'status'        => $pertama->status,
                'nominal'       => (int) $rows->sum('nominal'),
                'periode_label' => self::labelGrup($rows),
                'tanggal_setor' => $pertama->tanggal_setor,
                'metode'        => $pertama->metode,
                'bukti_url'     => $pertama->bukti_url,
                'catatan'       => $pertama->catatan,
                'alasan'        => $pertama->alasan,
                'jenis'         => $rows->every(fn ($s) => $s->tagihan_id) ? 'lainnya' : 'bulanan',
                'updated_at'    => $rows->max('updated_at'),
            ];
        })->values();
    }

    // ───────────────────────── Konfirmasi ─────────────────────────

    /**
     * Konfirmasi seluruh setoran dalam satu batch.
     * - Iuran bulanan: satu pengeluaran RT dan satu pemasukan RW (total batch).
     * - Iuran Lainnya: satu pengeluaran RT (total) dan satu pemasukan RW per iuran (sesuai kategori iuran).
     */
    public static function konfirmasi(SetoranRt $setoran, string $olehUserId): bool
    {
        return DB::transaction(function () use ($setoran, $olehUserId) {
            $ids = $setoran->grup()->pluck('id');
            $baris = SetoranRt::with('tagihan.kategori')->whereIn('id', $ids)->lockForUpdate()->get();
            if ($baris->isEmpty() || $baris->contains(fn ($s) => $s->status !== 'menunggu')) {
                return false;
            }

            $pertama = $baris->first();
            $rt = MasterRt::find($pertama->rt_id);
            $label = 'RT ' . ($rt->kode_rt ?? '');
            $total = (int) $baris->sum('nominal');
            $lainnya = $baris->every(fn ($s) => $s->tagihan_id);
            $periode = self::labelGrup($baris);
            $jenis = $lainnya ? 'Iuran Lainnya' : 'iuran bulanan';

            $keluar = self::kategori(self::KATEGORI_KELUAR, 'pengeluaran', 27);
            $trxRt = TransaksiKeuangan::create([
                'kode_transaksi' => 'SRT-' . strtoupper(Str::random(8)),
                'tipe'           => 'pengeluaran',
                'kategori'       => $keluar->nama,
                'kategori_id'    => $keluar->id,
                'judul'          => $lainnya ? "Setoran {$periode} ke RW" : "Setoran iuran {$periode} ke RW",
                'deskripsi'      => "Setoran kolektif {$jenis} {$periode} dari {$label} ke kas RW.",
                'jumlah'         => $total,
                'tanggal'        => $pertama->tanggal_setor,
                'bukti_file'     => $pertama->bukti_file,
                'status'         => 'Verified',
                'unit_sumber'    => 'RT',
                'rt_id'          => $pertama->rt_id,
                'dicatat_oleh'   => $pertama->dibuat_oleh,
            ]);

            $buatMasuk = function (string $judul, string $deskripsi, int $jumlah, KategoriTransaksi $kat) use ($pertama, $olehUserId) {
                return TransaksiKeuangan::create([
                    'kode_transaksi' => 'SRW-' . strtoupper(Str::random(8)),
                    'tipe'           => 'pemasukan',
                    'kategori'       => $kat->nama,
                    'kategori_id'    => $kat->id,
                    'judul'          => $judul,
                    'deskripsi'      => $deskripsi,
                    'jumlah'         => $jumlah,
                    'tanggal'        => $pertama->tanggal_setor,
                    'bukti_file'     => $pertama->bukti_file,
                    'status'         => 'Verified',
                    'unit_sumber'    => 'RW',
                    'rt_id'          => null,
                    'dicatat_oleh'   => $olehUserId,
                ]);
            };

            $trxRwBulanan = null;
            if (! $lainnya) {
                $trxRwBulanan = $buatMasuk(
                    "Setoran iuran {$periode} - {$label}",
                    "Setoran kolektif iuran bulanan {$periode} dari {$label}.",
                    $total,
                    self::kategori(self::KATEGORI_MASUK, 'pemasukan', 8)
                );
            }

            foreach ($baris as $s) {
                $trxRw = $trxRwBulanan;
                if ($lainnya) {
                    // Pemasukan RW dipecah per iuran agar masuk kategori kas yang benar.
                    $kat = $s->tagihan?->kategori ?: self::kategori(self::KATEGORI_MASUK, 'pemasukan', 8);
                    $judulIuran = $s->tagihan->judul ?? 'Iuran Lainnya';
                    $trxRw = $buatMasuk(
                        "Setoran {$judulIuran} - {$label}",
                        "Setoran kolektif Iuran Lainnya {$judulIuran} dari {$label}.",
                        (int) $s->nominal,
                        $kat
                    );
                }

                $s->update([
                    'status'            => 'dikonfirmasi',
                    'alasan'            => null,
                    'dikonfirmasi_oleh' => $olehUserId,
                    'dikonfirmasi_at'   => now(),
                    'transaksi_rt_id'   => $trxRt->id,
                    'transaksi_rw_id'   => $trxRw->id,
                ]);
            }

            return true;
        });
    }

    public static function tolak(SetoranRt $setoran, string $alasan, string $olehUserId): bool
    {
        return DB::transaction(function () use ($setoran, $alasan, $olehUserId) {
            $baris = SetoranRt::whereIn('id', $setoran->grup()->pluck('id'))->lockForUpdate()->get();
            if ($baris->isEmpty() || $baris->contains(fn ($s) => $s->status !== 'menunggu')) {
                return false;
            }
            SetoranRt::whereIn('id', $baris->pluck('id'))
                ->update(['status' => 'ditolak', 'alasan' => $alasan, 'dikonfirmasi_oleh' => $olehUserId]);

            return true;
        });
    }

    /** Batalkan seluruh batch yang sudah dikonfirmasi: semua transaksi kas terkait dihapus. */
    public static function batalkan(SetoranRt $setoran, string $alasan): bool
    {
        return DB::transaction(function () use ($setoran, $alasan) {
            $baris = SetoranRt::whereIn('id', $setoran->grup()->pluck('id'))->lockForUpdate()->get();
            if ($baris->isEmpty() || $baris->contains(fn ($s) => $s->status !== 'dikonfirmasi')) {
                return false;
            }
            $ids = $baris->flatMap(fn ($s) => [$s->transaksi_rt_id, $s->transaksi_rw_id])->filter()->unique()->all();
            SetoranRt::whereIn('id', $baris->pluck('id'))
                ->update(['status' => 'dibatalkan', 'alasan' => $alasan, 'transaksi_rt_id' => null, 'transaksi_rw_id' => null]);
            TransaksiKeuangan::whereIn('id', $ids)->delete();

            return true;
        });
    }
    protected static function kategori(string $nama, string $tipe, int $urutan): KategoriTransaksi
    {
        return KategoriTransaksi::firstOrCreate(
            ['nama' => $nama, 'unit' => null, 'rt_id' => null],
            ['tipe' => $tipe, 'aktif' => true, 'urutan' => $urutan]
        );
    }
}
