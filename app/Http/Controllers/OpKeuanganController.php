<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeuangan;
use App\Models\LaporanKeuangan;
use Illuminate\Support\Facades\Auth;

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
            $unit = 'RW'; // but can see aggregates
        } elseif ($user->role === 'DKM') {
            $unit = 'DKM';
        }

        return compact('unit', 'rt_id');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        // Get Kas Balances based on Verified/Approved status
        // For simplicity, we just sum pemasukan - pengeluaran
        $kasRw = TransaksiKeuangan::where('unit_sumber', 'RW')->whereIn('status', ['Verified', 'Approved'])->sum('jumlah'); // adjust logic if pengeluaran/pemasukan is different column
        
        $kasRwPemasukan = TransaksiKeuangan::where('unit_sumber', 'RW')->pemasukan()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah');
        $kasRwPengeluaran = TransaksiKeuangan::where('unit_sumber', 'RW')->pengeluaran()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah');
        $totalKasRw = $kasRwPemasukan - $kasRwPengeluaran;

        $kasRtPemasukan = TransaksiKeuangan::where('unit_sumber', 'RT');
        $kasRtPengeluaran = TransaksiKeuangan::where('unit_sumber', 'RT');
        if ($unit === 'RT') {
            $kasRtPemasukan = $kasRtPemasukan->where('rt_id', $rt_id);
            $kasRtPengeluaran = $kasRtPengeluaran->where('rt_id', $rt_id);
        }
        $totalKasRt = $kasRtPemasukan->pemasukan()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah') - $kasRtPengeluaran->pengeluaran()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah');

        $danaDkmPemasukan = TransaksiKeuangan::where('unit_sumber', 'DKM')->pemasukan()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah');
        $danaDkmPengeluaran = TransaksiKeuangan::where('unit_sumber', 'DKM')->pengeluaran()->whereIn('status', ['Verified', 'Approved'])->sum('jumlah');
        $totalDanaDkm = $danaDkmPemasukan - $danaDkmPengeluaran;

        $stats = [
            'kas_rw' => [
                'total' => 'Rp ' . number_format($totalKasRw, 0, ',', '.'),
                'change' => '+0%',
                'subtext' => 'Total saldo terkini',
            ],
            'kas_rt' => [
                'total' => 'Rp ' . number_format($totalKasRt, 0, ',', '.'),
                'subtext' => 'Total dana RT terkumpul',
            ],
            'dana_kematian' => [
                'total' => 'Rp ' . number_format($totalDanaDkm, 0, ',', '.'),
                'subtext' => 'Alokasi santunan warga',
            ],
        ];

        // Fetch recent transactions based on role
        $queryTx = TransaksiKeuangan::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc')->take(3);
        if ($unit === 'RT') {
            $queryTx->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $queryTx->where('unit_sumber', 'DKM');
        }

        $transaksiTerbaru = $queryTx->get()->map(function($tx) {
            $icon = $tx->tipe == 'pemasukan' ? 'bi-wallet2 text-success' : 'bi-receipt text-danger';
            $iconBg = $tx->tipe == 'pemasukan' ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10';
            $amountSign = $tx->tipe == 'pemasukan' ? '+' : '-';
            
            $statusClass = 'bg-secondary bg-opacity-10 text-secondary';
            if ($tx->status == 'Verified' || $tx->status == 'Approved') {
                $statusClass = 'bg-success bg-opacity-10 text-success';
            } elseif ($tx->status == 'Pending') {
                $statusClass = 'bg-warning bg-opacity-10 text-warning';
            }

            return [
                'title' => $tx->judul,
                'time' => $tx->tanggal->format('d M Y') . ' • ' . $tx->created_at->format('H:i'),
                'amount' => $amountSign . ' Rp ' . number_format($tx->jumlah, 0, ',', '.'),
                'type' => $tx->tipe == 'pemasukan' ? 'income' : 'expense',
                'status' => strtoupper($tx->status),
                'status_class' => $statusClass,
                'icon' => $icon,
                'icon_bg' => $iconBg,
            ];
        });

        // Hardcode some UI specific variables for dashboard rendering temporarily
        $sheetSaldo = [
            'assets' => 'Rp ' . number_format($totalKasRw + $totalKasRt + $totalDanaDkm, 0, ',', '.'),
            'liabilitas' => 'Rp 0',
            'ekuitas' => 'Rp ' . number_format($totalKasRw + $totalKasRt + $totalDanaDkm, 0, ',', '.'),
            'status' => 'Balanced',
        ];

        $statusLaporan = [
            [
                'step' => 1,
                'title' => 'Drafting',
                'subtitle' => 'Selesai oleh Operator',
                'status' => 'completed',
                'icon' => 'bi-check-circle-fill text-success',
            ],
            [
                'step' => 2,
                'title' => 'Pending RT/RW',
                'subtitle' => 'Menunggu Verifikasi',
                'status' => 'current',
                'icon' => 'bi-record-circle-fill text-success',
            ],
            [
                'step' => 3,
                'title' => 'Selesai',
                'subtitle' => 'Laporan Diterima',
                'status' => 'upcoming',
                'icon' => 'bi-circle text-muted',
            ],
        ];

        $chartData = [
            'labels' => ['Agt', 'Sep', 'Okt', 'Nov'],
            'rw' => [65, 72, 78, 80],
            'rt' => [25, 28, 32, 35],
            'kematian' => [15, 16, 18, 20],
        ];

        return view('opKeuangan.dashboard', compact('stats', 'sheetSaldo', 'transaksiTerbaru', 'statusLaporan', 'chartData', 'unit'));
    }

    public function transaksiIndex()
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $query = TransaksiKeuangan::with(['rt', 'pencatat', 'verifikator'])->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($unit === 'RT') {
            $query->where('rt_id', $rt_id);
        } elseif ($unit === 'DKM') {
            $query->where('unit_sumber', 'DKM');
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
        }

        $laporan = $query->paginate(10);

        return view('opKeuangan.laporan', compact('laporan', 'unit'));
    }

    public function transaksiStore(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pemasukan,pengeluaran',
            'judul' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
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
            'status' => 'Pending',
            'unit_sumber' => $unit,
            'rt_id' => $rt_id,
            'dicatat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Transaksi berhasil dicatat dan menunggu verifikasi.');
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

        // Hitung total dari transaksi Verified/Approved
        $queryTx = TransaksiKeuangan::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('unit_sumber', $unit)
            ->whereIn('status', ['Verified', 'Approved']);

        if ($unit === 'RT') {
            $queryTx->where('rt_id', $rt_id);
        }

        $pemasukan = (clone $queryTx)->pemasukan()->sum('jumlah');
        $pengeluaran = (clone $queryTx)->pengeluaran()->sum('jumlah');

        $judul = "Laporan Keuangan {$unit} - " . date('F', mktime(0, 0, 0, $request->bulan, 10)) . " {$request->tahun}";

        LaporanKeuangan::create([
            'judul' => $judul,
            'periode_bulan' => $request->bulan,
            'periode_tahun' => $request->tahun,
            'unit' => $unit,
            'rt_id' => $rt_id,
            'total_pemasukan' => $pemasukan,
            'total_pengeluaran' => $pengeluaran,
            'saldo_awal' => 0, // Simplified
            'saldo_akhir' => $pemasukan - $pengeluaran,
            'status' => 'Draft',
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Laporan berhasil di-generate.');
    }
}
