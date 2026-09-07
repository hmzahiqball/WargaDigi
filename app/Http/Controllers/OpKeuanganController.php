<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TransaksiIuran;
use App\Models\Keluarga;
use App\Models\MasterRt;

class OpKeuanganController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'kas_rw' => [
                'total' => 'Rp 124.500.000',
                'change' => '+2.4%',
                'subtext' => 'Total saldo terkini',
            ],
            'kas_rt' => [
                'total' => 'Rp 32.150.000',
                'subtext' => 'Total dana RT terkumpul',
            ],
            'dana_kematian' => [
                'total' => 'Rp 18.400.000',
                'subtext' => 'Alokasi santunan warga',
            ],
        ];

        $sheetSaldo = [
            'assets' => 'Rp 175.M',
            'liabilitas' => 'Rp 12.M',
            'ekuitas' => 'Rp 163.M',
            'status' => 'Balanced',
        ];

        $transaksiTerbaru = [
            [
                'title' => 'Iuran Warga Bulanan - Blok A',
                'time' => '12 Okt 2023 • 09:45',
                'amount' => '+ Rp 250.000',
                'type' => 'income',
                'status' => 'SELESAI',
                'status_class' => 'bg-success bg-opacity-10 text-success',
                'icon' => 'bi-wallet2 text-success',
                'icon_bg' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => 'Pembayaran Listrik Fasum',
                'time' => '11 Okt 2023 • 14:20',
                'amount' => '- Rp 1.240.000',
                'type' => 'expense',
                'status' => 'SELESAI',
                'status_class' => 'bg-success bg-opacity-10 text-success',
                'icon' => 'bi-receipt text-danger',
                'icon_bg' => 'bg-danger bg-opacity-10',
            ],
            [
                'title' => 'Sumbangan Dana Kematian',
                'time' => '10 Okt 2023 • 11:00',
                'amount' => '+ Rp 500.000',
                'type' => 'income',
                'status' => 'PENDING',
                'status_class' => 'bg-secondary bg-opacity-10 text-secondary',
                'icon' => 'bi-heart-pulse text-success',
                'icon_bg' => 'bg-success bg-opacity-10',
            ],
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
                'title' => 'Pending RT',
                'subtitle' => 'Menunggu Verifikasi Ketua RT',
                'status' => 'current',
                'icon' => 'bi-record-circle-fill text-success',
            ],
            [
                'step' => 3,
                'title' => 'Pending RW',
                'subtitle' => 'Belum Dimulai',
                'status' => 'upcoming',
                'icon' => 'bi-circle text-muted',
            ],
        ];

        $chartData = [
            'labels' => ['Periode 1', 'Periode 2', 'Periode 3', 'Periode 4'],
            'rw' => [65, 72, 78, 62],
            'rt' => [25, 28, 32, 30],
            'kematian' => [15, 16, 18, 17],
        ];

        return view('opKeuangan.dashboard', compact('stats', 'sheetSaldo', 'transaksiTerbaru', 'statusLaporan', 'chartData'));
    }

    public function transaksi()
    {
        $transaksi = TransaksiIuran::with(['keluarga', 'rt', 'operator', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Data untuk dropdown form
        $keluargaList = Keluarga::orderBy('no_kk')->get();
        $rtList = MasterRt::orderBy('kode_rt')->get();

        return view('opKeuangan.transaksi.index', compact('transaksi', 'keluargaList', 'rtList'));
    }

    public function storeTransaksi(Request $request)
    {
        $user = Auth::user();

        // Authorization: Block Op. Keuangan RW dari posting transaksi iuran RT
        if ($user->role === 'Op. Keuangan RW') {
            abort(403, 'Op. Keuangan RW tidak diizinkan menambah transaksi iuran di tingkat RT.');
        }

        // Hanya Op. Keuangan RT yang boleh mencatat iuran
        if (!in_array($user->role, ['Op. Keuangan RT'])) {
            abort(403, 'Anda tidak memiliki akses untuk mencatat transaksi iuran.');
        }

        $validated = $request->validate([
            'keluarga_id'       => 'required|exists:keluarga,id',
            'rt_id'             => 'required|exists:master_rt,id',
            'periode_bulan'     => 'required|integer|between:1,12',
            'periode_tahun'     => 'required|integer|min:2020|max:2030',
            'jumlah'            => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|in:Tunai,Transfer',
            'bukti_pembayaran'  => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:10240',
            'keterangan'        => 'nullable|string|max:500',
        ]);

        // Handle file upload
        if ($request->hasFile('bukti_pembayaran')) {
            $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')
                ->store('bukti-pembayaran', 'public');
        }

        $validated['operator_id'] = $user->id;
        $validated['status'] = 'Pending';

        TransaksiIuran::create($validated);

        return redirect()->route('opkeuangan.transaksi')
            ->with('success', 'Transaksi iuran berhasil dicatat.');
    }

    public function laporan()
    {
        $ringkasan = [
            'total_pemasukan' => 45750000,
            'total_pengeluaran' => 13600000,
            'saldo_bersih' => 32150000,
        ];

        // Data chart: KK sudah/belum bayar per RT
        $kkPembayaran = [
            'labels' => ['RT 01', 'RT 02', 'RT 03', 'RT 04', 'RT 05'],
            'sudah_bayar' => [85, 72, 90, 65, 78],
            'belum_bayar' => [15, 28, 10, 35, 22],
        ];

        $laporanRt = [
            [
                'rt' => 'RT 01',
                'periode' => 'Juli 2026',
                'pemasukan' => 8500000,
                'pengeluaran' => 2100000,
                'saldo' => 6400000,
                'status' => 'Disetujui',
            ],
            [
                'rt' => 'RT 02',
                'periode' => 'Juli 2026',
                'pemasukan' => 7200000,
                'pengeluaran' => 3500000,
                'saldo' => 3700000,
                'status' => 'Diajukan',
            ],
            [
                'rt' => 'RT 03',
                'periode' => 'Juli 2026',
                'pemasukan' => 9800000,
                'pengeluaran' => 1800000,
                'saldo' => 8000000,
                'status' => 'Draft',
            ],
            [
                'rt' => 'RT 04',
                'periode' => 'Juli 2026',
                'pemasukan' => 6500000,
                'pengeluaran' => 2900000,
                'saldo' => 3600000,
                'status' => 'Ditolak',
            ],
            [
                'rt' => 'RT 05',
                'periode' => 'Juli 2026',
                'pemasukan' => 13750000,
                'pengeluaran' => 3300000,
                'saldo' => 10450000,
                'status' => 'Disetujui',
            ],
        ];

        $donasiTerbaru = [
            [
                'judul' => 'Santunan Yatim Piatu',
                'jumlah' => 5000000,
                'jenis_penerima' => 'Semua Warga',
                'status' => 'Disalurkan',
                'tanggal' => '15 Juli 2026',
            ],
            [
                'judul' => 'Bantuan Bencana RT 03',
                'jumlah' => 2500000,
                'jenis_penerima' => 'RT Tertentu',
                'status' => 'Selesai',
                'tanggal' => '10 Juli 2026',
            ],
        ];

        return view('opKeuangan.laporan.index', compact('ringkasan', 'kkPembayaran', 'laporanRt', 'donasiTerbaru'));
    }
}
