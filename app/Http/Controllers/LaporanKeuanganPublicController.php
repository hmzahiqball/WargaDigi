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
        $query = LaporanKeuangan::with(['rt', 'pembuat'])
            ->where('is_published', true)
            ->where('status', 'Approved')
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc');

        // Allow everyone to see DKM and RW.
        // For Warga, limit RT to their own RT unless they are admin/rw/etc.
        if ($user && $user->role === 'Warga') {
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

        return view('laporanKeuangan.publik', compact('laporanList', 'filterOptions'));
    }

    /**
     * Download PDF with security checks
     */
    public function downloadPdf($id)
    {
        $user = Auth::user();
        $laporan = LaporanKeuangan::with(['rt'])->findOrFail($id);

        // Security check
        if (!$laporan->is_published) {
            // If not published, only pembuat, ketua RT (for their RT), and RW can view.
            $canView = false;
            if ($user->id === $laporan->dibuat_oleh) {
                $canView = true;
            } else if ($user->role === 'Ketua RT' && $laporan->unit === 'RT' && $laporan->rt_id === $user->rt_id) {
                $canView = true;
            } else if (in_array($user->role, ['Admin RW', 'Pimpinan RW'])) {
                // RW can see all reports that are submitted/disetujui RT or higher
                $canView = true;
            } else if (in_array($user->role, ['Op Keuangan RT', 'Op Keuangan RW', 'DKM'])) {
                 // The creator check above handles this, but just in case for other ops in the same unit
                 if ($laporan->unit === 'RT' && $user->role === 'Op Keuangan RT' && $laporan->rt_id === $user->rt_id) {
                     $canView = true;
                 }
                 if ($laporan->unit === 'RW' && $user->role === 'Op Keuangan RW') {
                     $canView = true;
                 }
                 if ($laporan->unit === 'DKM' && $user->role === 'DKM') {
                     $canView = true;
                 }
            }

            if (!$canView) {
                abort(403, 'Laporan ini belum dipublikasikan dan Anda tidak memiliki hak akses untuk melihatnya.');
            }
        } else {
             // If published, Warga can only view their own RT + RW + DKM
             if ($user && $user->role === 'Warga' && $laporan->unit === 'RT' && $laporan->rt_id !== $user->rt_id) {
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

        $pdf = Pdf::loadView('opKeuangan.laporan-pdf', [
            'laporan' => $laporan,
            'transaksiList' => $transaksiList,
            'rtLabel' => $rtLabel,
            'namaBulan' => $namaBulan,
        ]);

        return $pdf->download("Laporan_Keuangan_{$laporan->unit}_{$laporan->periode_tahun}_{$laporan->periode_bulan}.pdf");
    }
}
