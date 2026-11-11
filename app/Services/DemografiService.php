<?php

namespace App\Services;

use App\Models\MasterRt;
use App\Models\Penduduk;
use Illuminate\Support\Carbon;

/**
 * Agregasi data demografi penduduk untuk grafik dashboard RT/RW.
 * Semua method menerima $rtId opsional: bila diisi, data dibatasi ke RT tsb
 * (lingkup Ketua RT); bila null, mencakup seluruh RW.
 */
class DemografiService
{
    /**
     * Base query penduduk dengan optional filter RT.
     * Tidak menetapkan select agar aman dipakai untuk count maupun agregat
     * (hindari konflik only_full_group_by saat groupBy).
     */
    protected function query(?string $rtId)
    {
        $q = Penduduk::query()
            ->leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id');

        if ($rtId) {
            $q->where('keluarga.rt_id', $rtId);
        }

        return $q;
    }

    /**
     * Semua dataset demografi untuk dashboard.
     *
     * @return array<string,array{labels:array,values:array}>
     */
    public function semua(?string $rtId = null, bool $sebaranRt = false): array
    {
        $data = [
            'usia'            => $this->usia($rtId),
            'pekerjaan'       => $this->pekerjaan($rtId),
            'pendidikan'      => $this->pendidikan($rtId),
            'jenis_kelamin'   => $this->jenisKelamin($rtId),
            'perkawinan'      => $this->statusPerkawinan($rtId),
            'status_warga'    => $this->statusWarga($rtId),
            'total'           => $this->query($rtId)->count(),
        ];

        if ($sebaranRt) {
            $data['sebaran_rt'] = $this->sebaranRt();
        }

        return $data;
    }

    /** Distribusi kelompok usia dari tanggal_lahir. */
    public function usia(?string $rtId): array
    {
        $kelompok = [
            '0-5'   => 0, '6-12'  => 0, '13-17' => 0, '18-25' => 0,
            '26-40' => 0, '41-60' => 0, '60+'   => 0,
        ];

        $tanggal = $this->query($rtId)
            ->whereNotNull('penduduk.tanggal_lahir')
            ->pluck('penduduk.tanggal_lahir');

        $now = Carbon::now();
        foreach ($tanggal as $tgl) {
            try {
                $umur = Carbon::parse($tgl)->diffInYears($now);
            } catch (\Throwable $e) {
                continue;
            }
            $key = match (true) {
                $umur <= 5  => '0-5',
                $umur <= 12 => '6-12',
                $umur <= 17 => '13-17',
                $umur <= 25 => '18-25',
                $umur <= 40 => '26-40',
                $umur <= 60 => '41-60',
                default     => '60+',
            };
            $kelompok[$key]++;
        }

        return ['labels' => array_keys($kelompok), 'values' => array_values($kelompok)];
    }

    /** Distribusi pekerjaan (top 8 + Lainnya). */
    public function pekerjaan(?string $rtId): array
    {
        $rows = $this->query($rtId)
            ->whereNotNull('penduduk.pekerjaan')
            ->where('penduduk.pekerjaan', '!=', '')
            ->selectRaw('penduduk.pekerjaan as label, COUNT(*) as jumlah')
            ->groupBy('penduduk.pekerjaan')
            ->orderByDesc('jumlah')
            ->get();

        return $this->topNplusLainnya($rows, 8);
    }

    /** Distribusi pendidikan terakhir. */
    public function pendidikan(?string $rtId): array
    {
        $rows = $this->query($rtId)
            ->whereNotNull('penduduk.pendidikan_terakhir')
            ->where('penduduk.pendidikan_terakhir', '!=', '')
            ->selectRaw('penduduk.pendidikan_terakhir as label, COUNT(*) as jumlah')
            ->groupBy('penduduk.pendidikan_terakhir')
            ->orderByDesc('jumlah')
            ->get();

        return [
            'labels' => $rows->pluck('label')->all(),
            'values' => $rows->pluck('jumlah')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /** Jenis kelamin (L/P). */
    public function jenisKelamin(?string $rtId): array
    {
        $l = (clone $this->query($rtId))->where('penduduk.jenis_kelamin', 'L')->count();
        $p = (clone $this->query($rtId))->where('penduduk.jenis_kelamin', 'P')->count();

        return ['labels' => ['Laki-laki', 'Perempuan'], 'values' => [$l, $p]];
    }

    /** Status perkawinan. */
    public function statusPerkawinan(?string $rtId): array
    {
        $rows = $this->query($rtId)
            ->whereNotNull('penduduk.status_perkawinan')
            ->where('penduduk.status_perkawinan', '!=', '')
            ->selectRaw('penduduk.status_perkawinan as label, COUNT(*) as jumlah')
            ->groupBy('penduduk.status_perkawinan')
            ->orderByDesc('jumlah')
            ->get();

        return [
            'labels' => $rows->pluck('label')->all(),
            'values' => $rows->pluck('jumlah')->map(fn ($v) => (int) $v)->all(),
        ];
    }

    /** Status warga (tetap / tidak tetap). */
    public function statusWarga(?string $rtId): array
    {
        $tetap = (clone $this->query($rtId))->where('penduduk.status_warga', 'tetap')->count();
        $tidak = (clone $this->query($rtId))->where('penduduk.status_warga', 'tidak_tetap')->count();

        return ['labels' => ['Warga Tetap', 'Bukan Warga Tetap'], 'values' => [$tetap, $tidak]];
    }

    /** Sebaran jumlah penduduk per RT (khusus dashboard RW). */
    public function sebaranRt(): array
    {
        $rtList = MasterRt::orderBy('kode_rt')->get(['id', 'nama_rt', 'kode_rt']);
        $labels = [];
        $values = [];

        foreach ($rtList as $rt) {
            $labels[] = $rt->nama_rt ?: ('RT ' . $rt->kode_rt);
            $values[] = Penduduk::leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
                ->where('keluarga.rt_id', $rt->id)
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    /**
     * Ambil top-N kategori, sisanya digabung jadi "Lainnya".
     *
     * @param \Illuminate\Support\Collection $rows baris {label, jumlah}
     */
    protected function topNplusLainnya($rows, int $n): array
    {
        $top = $rows->take($n);
        $labels = $top->pluck('label')->all();
        $values = $top->pluck('jumlah')->map(fn ($v) => (int) $v)->all();

        $sisa = $rows->slice($n)->sum('jumlah');
        if ($sisa > 0) {
            $labels[] = 'Lainnya';
            $values[] = (int) $sisa;
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
