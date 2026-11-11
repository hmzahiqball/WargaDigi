<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanKeuangan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanKeuanganPublicController extends Controller
{
    /**
     * Display public/published reports
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Warga mendapat tampilan transparansi ringkas (angka agregat, tanpa file).
        if ($user && $user->activeRole() === 'Warga') {
            return $this->tampilanWarga($request, $user);
        }

        // Pimpinan (Ketua RT, Ketua RW, Admin RW) mendapat tab Statistik.
        $role = trim($user?->activeRole() ?? '');
        $bolehStatistik = in_array($role, ['Ketua RT', 'Ketua RW', 'Admin RW'], true);
        if ($bolehStatistik && $request->input('tab') === 'statistik') {
            return $this->tampilanStatistik($request, $user, $role);
        }

        $query = LaporanKeuangan::with(['rt', 'pembuat'])
            ->where('is_published', true)
            ->where('status', 'Approved')
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        // Allow everyone to see DKM and RW.
        // For Warga, limit RT to their own RT unless they are admin/rw/etc.
        if ($user && $user->activeRole() === 'Warga') {
            $wargaRtId = $user->rt_id;

            // Coba ambil dari data Penduduk -> Keluarga jika rt_id di User null
            if (!$wargaRtId) {
                $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
                if ($penduduk && $penduduk->keluarga) {
                    $wargaRtId = $penduduk->keluarga->rt_id;
                }
            }

            $query->where(function ($q) use ($wargaRtId) {
                $q->whereIn('unit', ['RW', 'DKM'])
                  ->orWhere(function ($q2) use ($wargaRtId) {
                      $q2->where('unit', 'RT');
                      if ($wargaRtId) {
                          $q2->where('rt_id', $wargaRtId);
                      }
                  });
            });
        }

        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->where('periode_bulan', $request->bulan);
        }
        if ($request->filled('unit') && $request->unit !== 'all') {
            $query->where('unit', $request->unit);
        }

        $laporanList = $query->paginate(12);

        $filterOptions = [
            'tahun' => LaporanKeuangan::where('is_published', true)->select('periode_tahun')->distinct()->orderBy('periode_tahun', 'desc')->pluck('periode_tahun'),
            'bulan' => [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'],
        ];

        $tab = 'daftar';

        return view('laporanKeuangan.publik', compact('laporanList', 'filterOptions', 'bolehStatistik', 'tab'));
    }

    /**
     * Tab Statistik untuk pimpinan.
     * - Ketua RT: RT-nya sendiri (laporan disetujui), RW/DKM hanya laporan yang sudah terbit.
     * - Ketua RW / Admin RW: seluruh laporan disetujui, termasuk rincian per RT.
     */
    protected function tampilanStatistik(Request $request, $user, string $role)
    {
        $pimpinanRw = in_array($role, ['Ketua RW', 'Admin RW'], true);

        $rtSaya = null;
        if ($role === 'Ketua RT') {
            $rtSaya = $user->rt_id;
            if (! $rtSaya) {
                $rtSaya = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first()?->keluarga?->rt_id;
            }
        }

        $unit = strtoupper((string) $request->input('unit', $pimpinanRw ? 'RW' : 'RT'));
        if (! in_array($unit, ['RT', 'RW', 'DKM'], true)) {
            $unit = $pimpinanRw ? 'RW' : 'RT';
        }

        // Pemilihan RT: Ketua RT terkunci pada RT-nya; pimpinan RW dapat memilih satu RT atau semua RT.
        $rtId = $rtSaya;
        $semuaRt = false;
        $daftarRt = collect();
        if ($pimpinanRw) {
            $daftarRt = \App\Models\MasterRt::orderBy('kode_rt')->get(['id', 'kode_rt']);
            $rtId = $daftarRt->firstWhere('id', $request->input('rt'))?->id;
            $semuaRt = $rtId === null;
        }

        $hanyaTerbit = $role === 'Ketua RT' && $unit !== 'RT';

        $daftarTahun = \App\Services\StatistikKeuanganService::daftarTahun($unit, $rtId, $semuaRt, $hanyaTerbit);
        $tahun = (int) $request->input('tahun', $daftarTahun[0] ?? now()->year);
        if (! empty($daftarTahun) && ! in_array($tahun, $daftarTahun, true)) {
            $tahun = $daftarTahun[0];
        }

        $statistik = \App\Services\StatistikKeuanganService::data($unit, $rtId, $semuaRt, $tahun, $hanyaTerbit, $pimpinanRw);

        return view('laporanKeuangan.publik', [
            'tab'            => 'statistik',
            'bolehStatistik' => true,
            'statistik'      => $statistik,
            'unit'           => $unit,
            'tahun'          => $tahun,
            'daftarTahun'    => $daftarTahun ?: [now()->year],
            'daftarRt'       => $daftarRt,
            'rtTerpilih'     => $rtId,
            'pimpinanRw'     => $pimpinanRw,
            'hanyaTerbit'    => $hanyaTerbit,
        ]);
    }

    /**
     * Tampilan transparansi keuangan untuk Warga: ringkasan, rekap kategori,
     * tren bulanan, dan status iuran milik KK yang bersangkutan.
     */
    protected function tampilanWarga(Request $request, $user)
    {
        $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
        $keluarga = $penduduk?->keluarga;
        $rtId = $user->rt_id ?: $keluarga?->rt_id;

        $tersedia = \App\Services\TransparansiKeuanganService::unitTersedia($rtId);

        // Unit terpilih: dari query bila valid dan punya laporan, selain itu unit pertama yang tersedia.
        $unit = strtoupper((string) $request->input('unit', ''));
        if (! in_array($unit, ['RT', 'RW', 'DKM'], true)) {
            $unit = collect($tersedia)->filter()->keys()->first() ?? 'RT';
        }

        $daftarTahun = \App\Services\TransparansiKeuanganService::daftarTahun($unit, $rtId);
        $tahun = (int) $request->input('tahun', $daftarTahun[0] ?? now()->year);
        if (! empty($daftarTahun) && ! in_array($tahun, $daftarTahun, true)) {
            $tahun = $daftarTahun[0];
        }

        $data = \App\Services\TransparansiKeuanganService::ringkasan($unit, $rtId, $tahun);
        $iuranSaya = \App\Services\TransparansiKeuanganService::statusIuranSaya($keluarga?->id, $rtId, $tahun);

        return view('laporanKeuangan.warga', [
            'data'        => $data,
            'unit'        => $unit,
            'tahun'       => $tahun,
            'tersedia'    => $tersedia,
            'daftarTahun' => $daftarTahun,
            'iuranSaya'   => $iuranSaya,
            'kodeRt'      => $keluarga?->rt?->kode_rt,
        ]);
    }

    /**
     * Download PDF with security checks
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();
        $laporan = LaporanKeuangan::with(['rt'])->findOrFail($id);

        // Warga hanya melihat ringkasan angka; file PDF tidak disediakan untuk Warga.
        abort_if($user && $user->activeRole() === 'Warga', 403, 'Unduhan laporan tidak tersedia untuk Warga.');

        // Security check
        if (!$laporan->is_published) {
            // If not published, only pembuat, ketua RT (for their RT), and RW can view.
            $canView = false;
            if ($user->id === $laporan->dibuat_oleh) {
                $canView = true;
            } else if ($user->activeRole() === 'Ketua RT' && $laporan->unit === 'RT' && $laporan->rt_id === $user->rt_id) {
                $canView = true;
            } else if (in_array($user->activeRole(), ['Admin RW', 'Ketua RW'])) {
                // RW can see all reports that are submitted/disetujui RT or higher
                $canView = true;
            } else if (in_array($user->activeRole(), ['Op Keuangan RT', 'Op Keuangan RW', 'DKM'])) {
                 // The creator check above handles this, but just in case for other ops in the same unit
                 if ($laporan->unit === 'RT' && $user->activeRole() === 'Op Keuangan RT' && $laporan->rt_id === $user->rt_id) {
                     $canView = true;
                 }
                 if ($laporan->unit === 'RW' && $user->activeRole() === 'Op Keuangan RW') {
                     $canView = true;
                 }
                 if ($laporan->unit === 'DKM' && $user->activeRole() === 'DKM') {
                     $canView = true;
                 }
            }

            if (!$canView) {
                abort(403, 'Laporan ini belum dipublikasikan dan Anda tidak memiliki hak akses untuk melihatnya.');
            }
        } else {
             // If published, Warga can only view their own RT + RW + DKM
             if ($user && $user->activeRole() === 'Warga' && $laporan->unit === 'RT' && $laporan->rt_id !== $user->rt_id) {
                 abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan dari RT lain.');
             }
        }

        $queryTx = TransaksiKeuangan::whereMonth('tanggal', $laporan->periode_bulan)
            ->whereYear('tanggal', $laporan->periode_tahun)
            ->where('unit_sumber', $laporan->unit)
            ->where('status', '!=', 'Rejected')
            ->orderBy('tanggal', 'asc');

        if ($laporan->unit === 'RT') {
            $queryTx->where('rt_id', $laporan->rt_id);
        }

        $transaksiList = $queryTx->get();

        $rtLabel = '';
        if ($laporan->unit === 'RT' && $laporan->rt) {
            $rtLabel = $laporan->rt->kode_rt ?? '01';
        }
        $namaBulan = date('F', mktime(0, 0, 0, $laporan->periode_bulan, 10));

        // Rekap per kategori (pemasukan & pengeluaran) untuk ringkasan laporan.
        $rekapPemasukan = $transaksiList->where('tipe', 'pemasukan')
            ->groupBy(fn ($t) => $t->kategori ?: 'Lainnya')
            ->map(fn ($g) => $g->sum('jumlah'))->sortDesc();
        $rekapPengeluaran = $transaksiList->where('tipe', 'pengeluaran')
            ->groupBy(fn ($t) => $t->kategori ?: 'Lainnya')
            ->map(fn ($g) => $g->sum('jumlah'))->sortDesc();

        $pdf = Pdf::loadView('opKeuangan.laporan-pdf', [
            'ttd' => \App\Services\PenandatanganDokumen::laporan($laporan),
            'laporan' => $laporan,
            'transaksiList' => $transaksiList,
            'rtLabel' => $rtLabel,
            'namaBulan' => $namaBulan,
            'rekapPemasukan' => $rekapPemasukan,
            'rekapPengeluaran' => $rekapPengeluaran,
        ]);

        return $pdf->download("Laporan_Keuangan_{$laporan->unit}_{$laporan->periode_tahun}_{$laporan->periode_bulan}.pdf");
    }

    /**
     * Ekspor laporan keuangan ke Excel (format buku kas).
     * Memakai kontrol akses yang sama dengan downloadPdf.
     */
    public function downloadExcel($id)
    {
        $user = Auth::user();
        $laporan = LaporanKeuangan::with(['rt'])->findOrFail($id);

        abort_if($user && $user->activeRole() === 'Warga', 403, 'Unduhan laporan tidak tersedia untuk Warga.');

        if (! $laporan->is_published) {
            $canView = $user->id === $laporan->dibuat_oleh
                || ($user->activeRole() === 'Ketua RT' && $laporan->unit === 'RT' && $laporan->rt_id === $user->rt_id)
                || in_array($user->activeRole(), ['Admin RW', 'Ketua RW'])
                || ($laporan->unit === 'RT' && $user->activeRole() === 'Op Keuangan RT' && $laporan->rt_id === $user->rt_id)
                || ($laporan->unit === 'RW' && $user->activeRole() === 'Op Keuangan RW')
                || ($laporan->unit === 'DKM' && $user->activeRole() === 'DKM');
            abort_unless($canView, 403, 'Laporan ini belum dipublikasikan dan Anda tidak memiliki hak akses.');
        } elseif ($user && $user->activeRole() === 'Warga' && $laporan->unit === 'RT' && $laporan->rt_id !== $user->rt_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan dari RT lain.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanKeuanganExport($laporan),
            "Laporan_Keuangan_{$laporan->unit}_{$laporan->periode_tahun}_{$laporan->periode_bulan}.xlsx"
        );
    }
}
