<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiKeuangan;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
            if (!$rt_id) {
                $penduduk = \App\Models\Penduduk::with('keluarga')->where('nik', $user->nik)->first();
                if ($penduduk && $penduduk->keluarga) {
                    $rt_id = $penduduk->keluarga->rt_id;
                }
                if (!$rt_id) {
                    $rt01 = MasterRt::where('kode_rt', '01')->first() ?? MasterRt::first();
                    $rt_id = $rt01 ? $rt01->id : null;
                }
            }
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
            $query->where('unit_sumber', 'RT')->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
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
            $kasRtQuery->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
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

        // Sheet Saldo: terisolasi per unit kerja aktif agar aset unit lain tidak nyasar ke neraca saldo
        if ($unit === 'RT') {
            $totalAssets = $totalKasRt;
        } elseif ($unit === 'DKM') {
            $totalAssets = $totalDanaDkm;
        } else {
            // Unit RW
            $totalAssets = $totalKasRw;
        }

        // Stat Cards: Tetap transparan & terlihat oleh ketiga bendahara (EV-001: Breakdown Pemasukan & Pengeluaran)
        $stats = [
            'kas_rw' => [
                'total' => 'Rp ' . number_format($totalKasRw, 0, ',', '.'),
                'masuk' => 'Rp ' . number_format($kasRwPemasukan, 0, ',', '.'),
                'keluar' => 'Rp ' . number_format($kasRwPengeluaran, 0, ',', '.'),
                'subtext' => $unit === 'RW' ? 'Saldo kas operasional RW' : 'Dikelola oleh Bendahara RW',
            ],
            'kas_rt' => [
                'total' => 'Rp ' . number_format($totalKasRt, 0, ',', '.'),
                'masuk' => 'Rp ' . number_format($kasRtPemasukan, 0, ',', '.'),
                'keluar' => 'Rp ' . number_format($kasRtPengeluaran, 0, ',', '.'),
                'subtext' => $unit === 'RT' ? 'Saldo kas RT Anda' : 'Total dana seluruh RT',
            ],
            'dana_kematian' => [
                'total' => 'Rp ' . number_format($totalDanaDkm, 0, ',', '.'),
                'masuk' => 'Rp ' . number_format($danaDkmPemasukan, 0, ',', '.'),
                'keluar' => 'Rp ' . number_format($danaDkmPengeluaran, 0, ',', '.'),
                'subtext' => $unit === 'DKM' ? 'Saldo kas DKM Anda' : 'Alokasi santunan warga (DKM)',
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
            $queryTx->where('unit_sumber', 'RT')->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
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
                'time' => $tx->tanggal->format('d M Y') . ' • ' . $tx->created_at->timezone('Asia/Jakarta')->format('H:i'),
                'amount' => $amountSign . ' Rp ' . number_format($tx->jumlah, 0, ',', '.'),
                'type' => $tx->tipe == 'pemasukan' ? 'income' : 'expense',
                'icon' => $icon,
                'icon_bg' => $iconBg,
            ];
        });

        // ──────────────────────────────
        // Sheet Saldo (EV-003 Revisi IR06: Dinamis Liabilitas & Status Icon)
        // ──────────────────────────────
        $pendingExpenses = (clone $this->scopedTransaksiQuery($unit, $rt_id))
            ->where('tipe', 'pengeluaran')
            ->where('status', 'Pending')
            ->sum('jumlah');

        if ($totalAssets < 0) {
            // Kondisi Defisit: Nilai liabilitas mencerminkan defisit kas + kewajiban tertunda
            $calculatedLiabilitas = abs($totalAssets) + $pendingExpenses;
            $calculatedEkuitas = 0;
            $statusSaldo = 'Defisit';
            $statusIcon = 'bi-exclamation-triangle-fill text-danger';
            $statusTextClass = 'text-danger';
            $statusBoxClass = 'bg-danger bg-opacity-10 border border-danger-subtle';
        } else {
            // Kondisi Balanced / Surplus
            $calculatedLiabilitas = $pendingExpenses;
            $calculatedEkuitas = $totalAssets - $calculatedLiabilitas;
            $statusSaldo = 'Balanced';
            $statusIcon = 'bi-check-circle-fill text-success';
            $statusTextClass = 'text-success';
            $statusBoxClass = 'bg-success bg-opacity-10 border border-success-subtle';
        }

        $sheetSaldo = [
            'assets' => 'Rp ' . number_format($totalAssets, 0, ',', '.'),
            'liabilitas' => 'Rp ' . number_format($calculatedLiabilitas, 0, ',', '.'),
            'ekuitas' => 'Rp ' . number_format($calculatedEkuitas, 0, ',', '.'),
            'status' => $statusSaldo,
            'status_icon' => $statusIcon,
            'status_text_class' => $statusTextClass,
            'status_box_class' => $statusBoxClass,
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

    /**
     * Helper to save base64 uploaded image/pdf directly to storage (dual write)
     * Solves Windows PHP built-in server temporary upload directory issues (EV-006, EV-016, EV-021)
     */
    private function saveBase64Bukti(?string $base64Data, string $folder = 'bukti_transaksi'): ?string
    {
        if (!$base64Data) {
            return null;
        }

        if (preg_match('/^data:(image\/(\w+)|application\/(pdf));base64,/', $base64Data, $matches)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            if (!empty($matches[3])) {
                $ext = 'pdf';
            } else {
                $ext = strtolower($matches[2]);
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $ext = 'jpg';
                }
            }

            $decoded = base64_decode($data);
            if ($decoded === false) {
                return null;
            }

            $filename = $folder . '/' . Str::uuid() . '.' . $ext;
            Storage::disk('public')->put($filename, $decoded);

            // Dual write to public/storage for immediate web serving
            try {
                $dst = public_path('storage/' . $filename);
                $dstDir = dirname($dst);
                if (!is_dir($dstDir)) {
                    @mkdir($dstDir, 0755, true);
                }
                @file_put_contents($dst, $decoded);
            } catch (\Throwable $e) {
                Log::warning('Dual-storage sync failed for base64 bukti: ' . $e->getMessage());
            }

            return $filename;
        }

        return null;
    }

    /**
     * EV-009: Cek apakah periode (bulan & tahun) untuk unit transaksi terkait terkunci.
     * Periode dianggap terkunci jika laporan keuangannya berstatus 'Submitted', 'Approved',
     * atau 'is_published' == true.
     */
    private function isPeriodLocked(string $unit, ?string $rt_id, int $bulan, int $tahun): bool
    {
        $query = LaporanKeuangan::where('unit', $unit)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->where(function ($q) {
                $q->whereIn('status', ['Submitted', 'Approved'])
                  ->orWhere('is_published', true);
            });

        if ($unit === 'RT') {
            $query->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
        }

        return $query->exists();
    }

    public function transaksiIndex(Request $request)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $query = TransaksiKeuangan::with(['rt', 'pencatat'])->orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($unit === 'RT') {
            // Auto-heal legacy RT transactions that have rt_id = null so they are stamped with proper rt_id
            if ($rt_id) {
                TransaksiKeuangan::where('unit_sumber', 'RT')->whereNull('rt_id')->update(['rt_id' => $rt_id]);
            }

            $query->where('unit_sumber', 'RT')->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
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

        // EV-009: Ambil semua periode terkunci (Submitted, Approved, Published) untuk unit ini
        $lockedPeriodsQuery = LaporanKeuangan::where('unit', $unit)
            ->where(function ($q) {
                $q->whereIn('status', ['Submitted', 'Approved'])
                  ->orWhere('is_published', true);
            });

        if ($unit === 'RT') {
            $lockedPeriodsQuery->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
        }

        $lockedPeriods = $lockedPeriodsQuery->get()->map(function ($lap) {
            return sprintf('%04d-%02d', $lap->periode_tahun, $lap->periode_bulan);
        })->unique()->toArray();

        // Tandai is_locked pada setiap transaksi di halaman aktif
        $transaksi->getCollection()->transform(function ($tx) use ($lockedPeriods) {
            $txPeriod = $tx->tanggal->format('Y-m');
            $tx->is_locked = in_array($txPeriod, $lockedPeriods);
            return $tx;
        });

        return view('opKeuangan.transaksi', compact('transaksi', 'unit'));
    }

    public function laporanIndex(Request $request)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $query = LaporanKeuangan::with(['rt', 'pembuat', 'penyetuju'])->orderBy('periode_tahun', 'desc')->orderBy('periode_bulan', 'desc');

        if ($unit === 'RT') {
            $query->where('unit', 'RT');
            if ($rt_id) {
                $query->where(function ($q) use ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                });
            }
        } elseif ($unit === 'DKM') {
            $query->where('unit', 'DKM');
        } elseif ($unit === 'RW') {
            $query->where('unit', 'RW');
        }

        if ($request->filled('tahun')) {
            $query->where('periode_tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Pending') {
                $query->where('status', 'Submitted');
            } else {
                $query->where('status', $request->status);
            }
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
        ]);

        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'] ?? 'RW';
        $rt_id = $roleData['rt_id'];

        // EV-009: Cegah penambahan transaksi pada periode yang laporannya sudah terkunci
        $bulanTx = (int) Carbon::parse($request->tanggal)->format('m');
        $tahunTx = (int) Carbon::parse($request->tanggal)->format('Y');

        if ($this->isPeriodLocked($unit, $rt_id, $bulanTx, $tahunTx)) {
            $periodeLabel = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('F Y');
            return back()->with('error', "Tidak dapat mencatat transaksi pada periode {$periodeLabel} karena laporan keuangan periode tersebut sudah diajukan, disetujui, atau dipublikasikan.")->withInput();
        }

        // EV-006: Prioritas 1 - Base64 payload (anti-gagal jika PHP temp directory Windows bermasalah)
        $buktiPath = null;
        if ($request->filled('bukti_base64')) {
            $buktiPath = $this->saveBase64Bukti($request->bukti_base64, 'bukti_transaksi');
        } elseif ($request->hasFile('bukti_file') && $request->file('bukti_file')->isValid()) {
            $buktiPath = $request->file('bukti_file')->store('bukti_transaksi', 'public');
            try {
                $destDir = public_path('storage/bukti_transaksi');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                copy(storage_path('app/public/' . $buktiPath), public_path('storage/' . $buktiPath));
            } catch (\Exception $e) {
                Log::warning('Dual-storage sync failed for bukti_transaksi store: ' . $e->getMessage());
            }
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
        ]);

        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $transaksi = TransaksiKeuangan::findOrFail($id);

        // Verify ownership: Op RT can only edit their RT's transactions
        if ($unit === 'RT') {
            if ($transaksi->unit_sumber !== 'RT') {
                abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
            }
            if ($rt_id && $transaksi->rt_id && $transaksi->rt_id !== $rt_id) {
                abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi RT lain.');
            }
        } elseif ($unit === 'DKM' && $transaksi->unit_sumber !== 'DKM') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        } elseif ($unit === 'RW' && $transaksi->unit_sumber !== 'RW') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit transaksi ini.');
        }

        // EV-009: Cek apakah transaksi asal berada dalam periode yang terkunci
        $oldBulan = (int) Carbon::parse($transaksi->tanggal)->format('m');
        $oldTahun = (int) Carbon::parse($transaksi->tanggal)->format('Y');

        if ($this->isPeriodLocked($transaksi->unit_sumber, $transaksi->rt_id, $oldBulan, $oldTahun)) {
            $periodeLabel = Carbon::parse($transaksi->tanggal)->locale('id')->translatedFormat('F Y');
            return back()->with('error', "Transaksi tidak dapat diubah karena laporan keuangan periode {$periodeLabel} sudah diajukan, disetujui, atau dipublikasikan.");
        }

        // Cek juga jika tanggal baru dipindahkan ke periode lain yang juga terkunci
        $newBulan = (int) Carbon::parse($request->tanggal)->format('m');
        $newTahun = (int) Carbon::parse($request->tanggal)->format('Y');

        if (($newBulan !== $oldBulan || $newTahun !== $oldTahun) && $this->isPeriodLocked($transaksi->unit_sumber, $transaksi->rt_id, $newBulan, $newTahun)) {
            $periodeBaruLabel = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('F Y');
            return back()->with('error', "Tanggal transaksi tidak dapat dipindahkan ke periode {$periodeBaruLabel} karena laporan periode tersebut sudah diajukan, disetujui, atau dipublikasikan.");
        }

        $data = [
            'tipe' => $request->tipe,
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi ?? '',
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
        ];

        if ($unit === 'RT' && !$transaksi->rt_id && $rt_id) {
            $data['rt_id'] = $rt_id;
        }

        // EV-006: Prioritas Base64 payload lalu standard file
        if ($request->filled('bukti_base64')) {
            $data['bukti_file'] = $this->saveBase64Bukti($request->bukti_base64, 'bukti_transaksi');
        } elseif ($request->hasFile('bukti_file') && $request->file('bukti_file')->isValid()) {
            $data['bukti_file'] = $request->file('bukti_file')->store('bukti_transaksi', 'public');
            try {
                $destDir = public_path('storage/bukti_transaksi');
                if (!file_exists($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                copy(storage_path('app/public/' . $data['bukti_file']), public_path('storage/' . $data['bukti_file']));
            } catch (\Exception $e) {
                Log::warning('Dual-storage sync failed for bukti_transaksi update: ' . $e->getMessage());
            }
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

        // Cek apakah ada laporan sebelumnya untuk periode ini
        $existingQuery = LaporanKeuangan::where('periode_bulan', $request->bulan)
            ->where('periode_tahun', $request->tahun)
            ->where('unit', $unit);

        if ($unit === 'RT') {
            $existingQuery->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
        }

        $existing = $existingQuery->first();

        if ($existing) {
            if ($existing->status === 'Rejected') {
                // Auto-cleanup laporan yang ditolak agar laporan revisi baru dapat langsung diajukan tanpa hapus manual
                $existing->delete();
            } else {
                return back()->with('error', 'Laporan untuk periode ini sudah ada (Status: ' . $existing->status . ').');
            }
        }

        // Hitung total dari transaksi valid (semua kecuali Rejected)
        $queryTx = TransaksiKeuangan::whereMonth('tanggal', $request->bulan)
            ->whereYear('tanggal', $request->tahun)
            ->where('unit_sumber', $unit)
            ->where('status', '!=', 'Rejected');

        if ($unit === 'RT') {
            $queryTx->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
        }

        $pemasukan = (clone $queryTx)->pemasukan()->sum('jumlah');
        $pengeluaran = (clone $queryTx)->pengeluaran()->sum('jumlah');

        // Hitung saldo awal = total saldo sebelum bulan ini
        $firstDayOfMonth = Carbon::createFromDate($request->tahun, $request->bulan, 1)->startOfDay();
        $saldoAwalQuery = TransaksiKeuangan::where('tanggal', '<', $firstDayOfMonth)
            ->where('unit_sumber', $unit)
            ->where('status', '!=', 'Rejected');

        if ($unit === 'RT') {
            $saldoAwalQuery->where(function ($q) use ($rt_id) {
                if ($rt_id) {
                    $q->where('rt_id', $rt_id)->orWhereNull('rt_id');
                } else {
                    $q->whereNull('rt_id');
                }
            });
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
            'status' => 'Draft', // EV-007: Menggunakan status awal Draft untuk review mandiri
            'dibuat_oleh' => Auth::id(),
        ]);

        return back()->with('success', 'Laporan berhasil dibuat dalam status Draft. Silakan review laporan terlebih dahulu, lalu klik tombol "Ajukan" untuk mengirimkannya ke Ketua.');
    }

    public function submitLaporan($id)
    {
        $roleData = $this->getRoleFilterData();
        $unit = $roleData['unit'];
        $rt_id = $roleData['rt_id'];

        $laporan = LaporanKeuangan::findOrFail($id);

        // Verify ownership
        if ($unit === 'RT' && $laporan->rt_id && $rt_id && $laporan->rt_id !== $rt_id) {
            abort(403, 'Anda tidak memiliki akses ke laporan RT ini.');
        }
        if ($unit === 'DKM' && $laporan->unit !== 'DKM') {
            abort(403, 'Anda tidak memiliki akses ke laporan DKM.');
        }
        if ($unit === 'RW' && $laporan->unit !== 'RW') {
            abort(403, 'Anda tidak memiliki akses ke laporan RW.');
        }

        if (!in_array($laporan->status, ['Draft', 'Rejected'])) {
            return back()->with('error', 'Hanya laporan berstatus Draft atau Rejected yang dapat diajukan.');
        }

        // Sinkronisasi data mutakhir sebelum submit
        $this->syncLaporanKeuangan($laporan);

        $laporan->update([
            'status' => 'Submitted',
            'catatan' => null, // Reset catatan jika sebelumnya ada revisi penolakan
        ]);

        return back()->with('success', 'Laporan berhasil diajukan ke Ketua untuk peninjauan dan persetujuan.');
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

        if ($unit === 'RT') {
            if ($tx->unit_sumber !== 'RT') {
                abort(403, 'Unauthorized action.');
            }
            if ($rt_id && $tx->rt_id && $tx->rt_id !== $rt_id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($unit === 'DKM' && $tx->unit_sumber !== 'DKM') {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'RW' && $tx->unit_sumber !== 'RW') {
            abort(403, 'Unauthorized action.');
        }

        // EV-009: Cek apakah transaksi berada dalam periode yang terkunci
        $bulanTx = (int) Carbon::parse($tx->tanggal)->format('m');
        $tahunTx = (int) Carbon::parse($tx->tanggal)->format('Y');

        if ($this->isPeriodLocked($tx->unit_sumber, $tx->rt_id, $bulanTx, $tahunTx)) {
            $periodeLabel = Carbon::parse($tx->tanggal)->locale('id')->translatedFormat('F Y');
            return back()->with('error', "Transaksi tidak dapat dihapus karena laporan keuangan periode {$periodeLabel} sudah diajukan, disetujui, atau dipublikasikan.");
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

        if ($unit === 'RT') {
            if ($laporan->unit !== 'RT') {
                abort(403, 'Unauthorized action.');
            }
            if ($rt_id && $laporan->rt_id && $laporan->rt_id !== $rt_id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif ($unit === 'DKM' && $laporan->unit !== 'DKM') {
            abort(403, 'Unauthorized action.');
        } elseif ($unit === 'RW' && $laporan->unit !== 'RW') {
            abort(403, 'Unauthorized action.');
        }

        if ($laporan->status === 'Approved') {
            return back()->with('error', 'Laporan yang sudah disetujui tidak dapat dihapus.');
        }
        if ($laporan->is_published) {
            return back()->with('error', 'Laporan yang sudah dipublikasikan tidak dapat dihapus.');
        }

        $laporan->delete();

        return back()->with('success', 'Laporan berhasil dihapus.');
    }
}
