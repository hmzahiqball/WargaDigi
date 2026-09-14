<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeuangan;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class OpKeuanganController extends Controller
{
    private function getRoleFilterData()
    {
        $user = Auth::user();
        $unit = null;
        $rt_id = null;

        if ($user->role === 'Op Keuangan RT') {
            $unit = 'RT';
            $rt_id = $user->rt_id;
        } elseif ($user->role === 'Op Keuangan RW') {
            $unit = 'RW';
        } elseif ($user->role === 'DKM') {
            $unit = 'DKM';
        }

        return compact('unit', 'rt_id');
    }

    /**
     * Build a base query for transactions scoped by role.
     * Excludes 'Rejected' transactions — all other statuses are considered valid.
     */
    private function scopedTransaksiQuery($unit, $rt_id)
    {
        $query = TransaksiKeuangan::where('status', '!=', 'Rejected');

        if ($unit === 'RT') {
            $query->where('unit_sumber', 'RT')->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $query->where('unit_sumber', 'DKM');
        } elseif ($unit === 'RW') {
            $query->where('unit_sumber', 'RW');
        }

        return $query;
    }

    public function dashboard()
    {
        $user = Auth::user();
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        // ──────────────────────────────
        // Stat Cards — calculated dynamically
        // ──────────────────────────────

        // Kas RW
        $kasRwPemasukan = TransaksiKeuangan::where('unit_sumber', 'RW')->where('status', '!=', 'Rejected')->pemasukan()->sum('jumlah');
        $kasRwPengeluaran = TransaksiKeuangan::where('unit_sumber', 'RW')->where('status', '!=', 'Rejected')->pengeluaran()->sum('jumlah');
        $totalKasRw = $kasRwPemasukan - $kasRwPengeluaran;

        // Kas RT (scoped by rt_id if Op RT)
        $kasRtQuery = TransaksiKeuangan::where('unit_sumber', 'RT')->where('status', '!=', 'Rejected');
        if ($unit === 'RT') {
            $kasRtQuery->where('rt_id', $rt_id);
        }
        $kasRtPemasukan = (clone $kasRtQuery)->pemasukan()->sum('jumlah');
        $kasRtPengeluaran = (clone $kasRtQuery)->pengeluaran()->sum('jumlah');
        $totalKasRt = $kasRtPemasukan - $kasRtPengeluaran;

        // Dana DKM
        $danaDkmPemasukan = TransaksiKeuangan::where('unit_sumber', 'DKM')->where('status', '!=', 'Rejected')->pemasukan()->sum('jumlah');
        $danaDkmPengeluaran = TransaksiKeuangan::where('unit_sumber', 'DKM')->where('status', '!=', 'Rejected')->pengeluaran()->sum('jumlah');
        $totalDanaDkm = $danaDkmPemasukan - $danaDkmPengeluaran;

        // Pemasukan & Pengeluaran bulan ini (for change badge)
        $now = Carbon::now();
        $pemasukanBulanIni = $this->scopedTransaksiQuery($unit, $rt_id)
            ->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
            ->pemasukan()->sum('jumlah');
        $pengeluaranBulanIni = $this->scopedTransaksiQuery($unit, $rt_id)
            ->whereMonth('tanggal', $now->month)->whereYear('tanggal', $now->year)
            ->pengeluaran()->sum('jumlah');

        $stats = [
            'kas_rw' => [
                'total' => 'Rp ' . number_format($totalKasRw, 0, ',', '.'),
                'subtext' => 'Total saldo terkini',
            ],
            'kas_rt' => [
                'total' => 'Rp ' . number_format($totalKasRt, 0, ',', '.'),
                'subtext' => $unit === 'RT' ? 'Saldo kas RT Anda' : 'Total dana seluruh RT',
            ],
            'dana_kematian' => [
                'total' => 'Rp ' . number_format($totalDanaDkm, 0, ',', '.'),
                'subtext' => 'Alokasi santunan warga',
            ],
            'pemasukan_bulan_ini' => 'Rp ' . number_format($pemasukanBulanIni, 0, ',', '.'),
            'pengeluaran_bulan_ini' => 'Rp ' . number_format($pengeluaranBulanIni, 0, ',', '.'),
        ];

        // ──────────────────────────────
        // Fetch recent transactions based on role
        // ──────────────────────────────
        $queryTx = TransaksiKeuangan::where('status', '!=', 'Rejected')
            ->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->take(3);

        if ($unit === 'RT') {
            $queryTx->where('unit_sumber', 'RT')->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $queryTx->where('unit_sumber', 'DKM');
        } elseif ($unit === 'RW') {
            $queryTx->where('unit_sumber', 'RW');
        }

        $transaksiTerbaru = $queryTx->get()->map(function($tx) {
            $icon = $tx->tipe == 'pemasukan' ? 'bi-wallet2 text-success' : 'bi-receipt text-danger';
            $iconBg = $tx->tipe == 'pemasukan' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10';
            $amountSign = $tx->tipe == 'pemasukan' ? '+' : '-';

            return [
                'title' => $tx->judul,
                'time' => $tx->tanggal->format('d M Y') . ' • ' . $tx->created_at->format('H:i'),
                'amount' => $amountSign . ' Rp ' . number_format($tx->jumlah, 0, ',', '.'),
                'type' => $tx->tipe == 'pemasukan' ? 'income' : 'expense',
                'icon' => $icon,
                'icon_bg' => $iconBg,
            ];
        });

        // ──────────────────────────────
        // Sheet Saldo
        // ──────────────────────────────
        $totalAssets = $totalKasRw + $totalKasRt + $totalDanaDkm;
        $sheetSaldo = [
            'assets' => 'Rp ' . number_format($totalAssets, 0, ',', '.'),
            'liabilitas' => 'Rp 0',
            'ekuitas' => 'Rp ' . number_format($totalAssets, 0, ',', '.'),
            'status' => 'Balanced',
        ];

        // ──────────────────────────────
        // Status Laporan — dynamic from DB
        // ──────────────────────────────
        $laporanTerbaru = LaporanKeuangan::orderBy('created_at', 'desc');
        if ($unit === 'RT') {
            $laporanTerbaru->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $laporanTerbaru->where('unit', 'DKM');
        } elseif ($unit === 'RW') {
            $laporanTerbaru->where('unit', 'RW');
        }
        $laporanTerbaru = $laporanTerbaru->first();

        if ($laporanTerbaru) {
            $statusLaporan = $this->buildStatusSteps($laporanTerbaru);
            $laporanTitle = $laporanTerbaru->judul;
        } else {
            $statusLaporan = [
                ['step' => 1, 'title' => 'Belum Ada Laporan', 'subtitle' => 'Generate laporan terlebih dahulu', 'status' => 'upcoming', 'icon' => 'bi-circle text-muted'],
            ];
            $laporanTitle = 'Belum ada laporan';
        }

        // ──────────────────────────────
        // Chart Tren Kas — dynamic last 4 months
        // ──────────────────────────────
        $chartLabels = [];
        $chartRw = [];
        $chartRt = [];
        $chartKematian = [];

        for ($i = 3; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M');

            $rwSaldo = TransaksiKeuangan::where('unit_sumber', 'RW')->where('status', '!=', 'Rejected')
                ->where('tanggal', '<=', $month->endOfMonth())
                ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as saldo")
                ->value('saldo');
            $chartRw[] = (int)$rwSaldo;

            $rtQuery = TransaksiKeuangan::where('unit_sumber', 'RT')->where('status', '!=', 'Rejected')
                ->where('tanggal', '<=', $month->endOfMonth());
            if ($unit === 'RT') {
                $rtQuery->where('rt_id', $rt_id);
            }
            $rtSaldo = $rtQuery->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as saldo")
                ->value('saldo');
            $chartRt[] = (int)$rtSaldo;

            $dkmSaldo = TransaksiKeuangan::where('unit_sumber', 'DKM')->where('status', '!=', 'Rejected')
                ->where('tanggal', '<=', $month->endOfMonth())
                ->selectRaw("COALESCE(SUM(CASE WHEN tipe = 'pemasukan' THEN jumlah ELSE 0 END), 0) - COALESCE(SUM(CASE WHEN tipe = 'pengeluaran' THEN jumlah ELSE 0 END), 0) as saldo")
                ->value('saldo');
            $chartKematian[] = (int)$dkmSaldo;
        }

        $chartData = [
            'labels' => $chartLabels,
            'rw' => $chartRw,
            'rt' => $chartRt,
            'kematian' => $chartKematian,
        ];

        return view('opKeuangan.dashboard', compact(
            'stats', 'sheetSaldo', 'transaksiTerbaru', 'statusLaporan',
            'chartData', 'unit', 'laporanTitle'
        ));
    }

    /**
     * Build status step array from a LaporanKeuangan model.
     */
    private function buildStatusSteps(LaporanKeuangan $laporan): array
    {
        $steps = [];

        // Step 1: Drafting / Generated
        $steps[] = [
            'step' => 1,
            'title' => 'Laporan Di-generate',
            'subtitle' => 'Oleh ' . ($laporan->pembuat->name ?? 'Operator'),
            'status' => 'completed',
            'icon' => 'bi-check-circle-fill text-success',
        ];

        // Step 2: Submitted / Pending Approval
        if (in_array($laporan->status, ['Submitted', 'Approved'])) {
            $steps[] = [
                'step' => 2,
                'title' => 'Diajukan ke Ketua',
                'subtitle' => 'Menunggu approval',
                'status' => $laporan->status === 'Submitted' ? 'current' : 'completed',
                'icon' => $laporan->status === 'Submitted' ? 'bi-record-circle-fill text-warning' : 'bi-check-circle-fill text-success',
            ];
        } else {
            $steps[] = [
                'step' => 2,
                'title' => 'Pending Pengajuan',
                'subtitle' => 'Belum diajukan',
                'status' => $laporan->status === 'Draft' ? 'current' : 'upcoming',
                'icon' => $laporan->status === 'Draft' ? 'bi-record-circle-fill text-secondary' : 'bi-circle text-muted',
            ];
        }

        // Step 3: Approved
        if ($laporan->status === 'Approved') {
            $steps[] = [
                'step' => 3,
                'title' => 'Disetujui',
                'subtitle' => $laporan->tanggal_disetujui ? $laporan->tanggal_disetujui->format('d M Y') : 'Laporan diterima',
                'status' => 'completed',
                'icon' => 'bi-check-circle-fill text-success',
            ];
        } else {
            $steps[] = [
                'step' => 3,
                'title' => 'Selesai',
                'subtitle' => 'Laporan Diterima',
                'status' => 'upcoming',
                'icon' => 'bi-circle text-muted',
            ];
        }

        return $steps;
    }

    public function transaksiIndex(Request $request)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $query = TransaksiKeuangan::with(['rt', 'pencatat'])->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($unit === 'RT') {
            $query->where('unit_sumber', 'RT')->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $query->where('unit_sumber', 'DKM');
        } elseif ($unit === 'RW') {
            $query->where('unit_sumber', 'RW');
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $transaksi = $query->paginate(10);

        return view('opKeuangan.transaksi', compact('transaksi', 'unit'));
    }

    public function laporanIndex()
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $query = LaporanKeuangan::with(['rt', 'pembuat', 'penyetuju'])->orderBy('periode_tahun', 'desc')->orderBy('periode_bulan', 'desc');

        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $query->where('unit', 'DKM');
        } elseif ($unit === 'RW') {
            $query->where('unit', 'RW');
        }

        $laporans = $query->paginate(10);

        foreach ($laporans as $lap) {
            if (in_array($lap->status, ['Draft', 'Submitted'])) {
                $this->syncLaporanKeuangan($lap);
            }
        }

        return view('opKeuangan.laporan', ['laporan' => $laporans, 'unit' => $unit]);
    }

    public function transaksiStore(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'] ?? 'RW';
        $rt_id = $roleData['rt_id'];

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('bukti_transaksi', 'public');
        }

        // Generate Kode Transaksi
        $prefix = $unit === 'RT' ? 'RT' . str_pad($rt_id ? 1 : 0, 2, '0', STR_PAD_LEFT) : $unit;
        $kode = 'TRX-' . $prefix . '-' . date('ym') . '-' . rand(1000, 9999);

        TransaksiKeuangan::create([
            'kode_transaksi' => $kode,
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi ?? '',
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'bukti_file' => $buktiPath,
            'status' => 'Verified', // Transaksi otomatis valid
            'unit_sumber' => $unit,
            'rt_id' => $rt_id,
            'dicatat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function transaksiUpdate(Request $request, $id)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $transaksi = TransaksiKeuangan::findOrFail($id);

        // Verify ownership: Op RT can only edit their RT's transactions
        if ($unit === 'RT' && $transaksi->rt_id !== $rt_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        }
        if ($unit === 'DKM' && $transaksi->unit_sumber !== 'DKM') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        }

        $data = [
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi ?? '',
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ];

        if ($request->hasFile('bukti_file')) {
            $data['bukti_file'] = $request->file('bukti_file')->store('bukti_transaksi', 'public');
        }

        $transaksi->update($data);

        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function laporanStore(Request $request)
    {
        $request->validate([
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2000',
        ]);

        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'] ?? 'RW';
        $rt_id = $roleData['rt_id'];

        // Cek apakah laporan sudah ada
        $exists = LaporanKeuangan::where('periode_bulan', $request->bulan)
            ->where('periode_tahun', $request->tahun)
            ->where('unit', $unit);

        if ($unit === 'RT') {
            $exists->where('rt_id', $rt_id);
        }

        if ($exists->exists()) {
            return back()->with('error', 'Laporan untuk periode ini sudah ada.');
        }

        // Hitung total dari transaksi valid (semua kecuali Rejected)
        $queryTx = TransaksiKeuangan::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('unit_sumber', $unit)
            ->where('status', '!=', 'Rejected');

        if ($unit === 'RT') {
            $queryTx->where('rt_id', $rt_id);
        }

        $pemasukan = (clone $queryTx)->pemasukan()->sum('jumlah');
        $pengeluaran = (clone $queryTx)->pengeluaran()->sum('jumlah');

        // Hitung saldo awal = total saldo sebelum bulan ini
        $firstDayOfMonth = Carbon::createFromDate($request->tahun, $request->bulan, 1)->startOfDay();
        $saldoAwalQuery = TransaksiKeuangan::where('tanggal', '<', $firstDayOfMonth)
            ->where('unit_sumber', $unit)
            ->where('status', '!=', 'Rejected');

        if ($unit === 'RT') {
            $saldoAwalQuery->where('rt_id', $rt_id);
        }

        $saldoAwalPemasukan = (clone $saldoAwalQuery)->pemasukan()->sum('jumlah');
        $saldoAwalPengeluaran = (clone $saldoAwalQuery)->pengeluaran()->sum('jumlah');
        $saldoAwal = $saldoAwalPemasukan - $saldoAwalPengeluaran;

        $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;

        $judul = "Laporan Keuangan {$unit} - " . date('F', mktime(0, 0, 0, $request->bulan, 10)) . " {$request->tahun}";

        LaporanKeuangan::create([
            'judul' => $judul,
            'periode_bulan' => $request->bulan,
            'periode_tahun' => $request->tahun,
            'unit' => $unit,
            'rt_id' => $rt_id,
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo_awal' => max(0, $saldoAwal),
            'saldo_akhir' => max(0, $saldoAkhir),
            'status' => 'Submitted', // Langsung diajukan untuk approval
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Laporan berhasil di-generate dan diajukan untuk approval.');
    }

    public function publishLaporan($id)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $laporan = LaporanKeuangan::findOrFail($id);

        // Verify ownership
        if ($unit === 'RT' && $laporan->rt_id !== $rt_id) {
            abort(403);
        }
        if ($unit === 'DKM' && $laporan->unit !== 'DKM') {
            abort(403);
        }

        if ($laporan->status !== 'Approved') {
            return back()->with('error', 'Hanya laporan yang sudah Approved yang bisa di-publish.');
        }

        if ($laporan->is_published) {
            return back()->with('error', 'Laporan ini sudah di-publish.');
        }

        $laporan->update([
            'is_published' => true
        ]);

        return back()->with('success', 'Laporan berhasil di-publish dan kini dapat dilihat oleh publik.');
    }

    /**
     * Sinkronisasi data laporan keuangan (total pemasukan, pengeluaran, saldo)
     * berdasarkan transaksi riil bulan tersebut.
     */
    private function syncLaporanKeuangan(LaporanKeuangan $laporan)
    {
        $queryTx = TransaksiKeuangan::whereMonth('tanggal', $laporan->periode_bulan)
            ->whereYear('tanggal', $laporan->periode_tahun)
            ->where('unit_sumber', $laporan->unit)
            ->where('status', '!=', 'Rejected');

        if ($laporan->unit === 'RT') {
            $queryTx->where('rt_id', $laporan->rt_id);
        }

        $pemasukan = (clone $queryTx)->pemasukan()->sum('jumlah');
        $pengeluaran = (clone $queryTx)->pengeluaran()->sum('jumlah');

        // Hitung Saldo Awal (total semua transaksi sebelum bulan laporan)
        $firstDayOfMonth = Carbon::createFromDate($laporan->periode_tahun, $laporan->periode_bulan, 1)->startOfDay();
        $saldoAwalQuery = TransaksiKeuangan::where('tanggal', '<', $firstDayOfMonth)
            ->where('unit_sumber', $laporan->unit)
            ->where('status', '!=', 'Rejected');

        if ($laporan->unit === 'RT') {
            $saldoAwalQuery->where('rt_id', $laporan->rt_id);
        }

        $saldoAwalPemasukan = (clone $saldoAwalQuery)->pemasukan()->sum('jumlah');
        $saldoAwalPengeluaran = (clone $saldoAwalQuery)->pengeluaran()->sum('jumlah');
        $saldoAwal = $saldoAwalPemasukan - $saldoAwalPengeluaran;
        $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran;

        $laporan->update([
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo_awal' => max(0, $saldoAwal),
            'saldo_akhir' => max(0, $saldoAkhir),
        ]);
    }

    public function transaksiDestroy($id)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'] ?? 'RW';
        $rt_id = $roleData['rt_id'];

        $tx = TransaksiKeuangan::findOrFail($id);

        if ($unit === 'RT' && $tx->rt_id !== $rt_id) {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'DKM' && $tx->unit_sumber !== 'DKM') {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'RW' && $tx->unit_sumber !== 'RW') {
            abort(403, 'Unauthorized action.');
        }

        // Hard delete
        $tx->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function laporanDestroy($id)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'] ?? 'RW';
        $rt_id = $roleData['rt_id'];

        $laporan = LaporanKeuangan::findOrFail($id);

        if ($unit === 'RT' && $laporan->rt_id !== $rt_id) {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'DKM' && $laporan->unit !== 'DKM') {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'RW' && $laporan->unit !== 'RW') {
            abort(403, 'Unauthorized action.');
        }

        if ($laporan->status === 'Approved') {
            return back()->with('error', 'Laporan yang sudah disetujui tidak dapat dihapus.');
        }

        $laporan->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}
