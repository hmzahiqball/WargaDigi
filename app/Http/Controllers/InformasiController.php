<?php

namespace App\Http\Controllers;

use App\Models\FasilitasUmum;
use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\UmkmUsaha;
use Illuminate\Support\Carbon;

class InformasiController extends Controller
{
    public function index()
    {
        $totalWarga = Penduduk::count();
        $totalKk = Keluarga::count();
        $totalRt = MasterRt::count();
        $totalUmkm = UmkmUsaha::where('status_verifikasi', 'Approved')->count();

        $stats = [
            ['icon' => 'bi-people', 'label' => 'Total Warga', 'value' => number_format($totalWarga, 0, ',', '.'), 'sub' => 'Jiwa terdaftar'],
            ['icon' => 'bi-house-door', 'label' => 'Kepala Keluarga', 'value' => number_format($totalKk, 0, ',', '.'), 'sub' => 'Kartu Keluarga aktif'],
            ['icon' => 'bi-building', 'label' => 'Wilayah RT', 'value' => number_format($totalRt, 0, ',', '.'), 'sub' => 'Rukun Tetangga'],
            ['icon' => 'bi-shop', 'label' => 'Potensi UMKM', 'value' => number_format($totalUmkm, 0, ',', '.'), 'sub' => 'Usaha warga aktif', 'highlight' => true],
        ];

        $totalLaki = Penduduk::where('jenis_kelamin', 'L')->count();
        $totalPerempuan = Penduduk::where('jenis_kelamin', 'P')->count();

        $genderData = [
            'labels' => ['Laki-laki', 'Perempuan'],
            'data' => [$totalLaki, $totalPerempuan],
            'colors' => ['#2E7D32', '#81C784'],
        ];

        // Distribusi kelompok umur dari tanggal_lahir.
        $ageLabels = ['0-4', '5-14', '15-24', '25-34', '35-44', '45-54', '55+'];
        $ageBuckets = array_fill(0, count($ageLabels), 0);

        Penduduk::whereNotNull('tanggal_lahir')
            ->orderBy('tanggal_lahir')
            ->chunk(500, function ($rows) use (&$ageBuckets) {
                foreach ($rows as $p) {
                    if (!$p->tanggal_lahir) {
                        continue;
                    }
                    $usia = Carbon::parse($p->tanggal_lahir)->age;
                    $ageBuckets[$this->ageBucketIndex($usia)]++;
                }
            });

        $ageData = [
            'labels' => $ageLabels,
            'data' => array_values($ageBuckets),
        ];

        // Sebaran warga per RT.
        $rtData = MasterRt::orderBy('nama_rt')
            ->get()
            ->map(function (MasterRt $rt) {
                $kkCount = Keluarga::where('rt_id', $rt->id)->count();
                $jiwaCount = Penduduk::whereHas('keluarga', fn ($q) => $q->where('rt_id', $rt->id))->count();
                $umkmCount = UmkmUsaha::where('status_verifikasi', 'Approved')
                    ->whereHas('user.penduduk.keluarga', fn ($q) => $q->where('rt_id', $rt->id))
                    ->count();

                return [
                    'rt' => 'RT ' . $rt->nama_rt,
                    'kk' => $kkCount,
                    'jiwa' => $jiwaCount,
                    'umkm' => $umkmCount . ' Usaha',
                ];
            })
            ->all();

        $facilities = FasilitasUmum::orderBy('urutan')
            ->get()
            ->map(fn (FasilitasUmum $f) => [
                'icon' => $f->icon,
                'name' => $f->nama,
                'location' => $f->lokasi,
            ])
            ->all();

        return view('pages.informasi', compact('stats', 'genderData', 'ageData', 'rtData', 'facilities'));
    }

    /**
     * Menentukan index bucket usia sesuai label:
     * 0:0-4, 1:5-14, 2:15-24, 3:25-34, 4:35-44, 5:45-54, 6:55+.
     */
    private function ageBucketIndex(int $usia): int
    {
        return match (true) {
            $usia <= 4  => 0,
            $usia <= 14 => 1,
            $usia <= 24 => 2,
            $usia <= 34 => 3,
            $usia <= 44 => 4,
            $usia <= 54 => 5,
            default     => 6,
        };
    }
}
