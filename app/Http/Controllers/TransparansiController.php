<?php

namespace App\Http\Controllers;

use App\Models\LaporanKeuangan;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Carbon;

class TransparansiController extends Controller
{
    public function index()
    {
        // Basis transaksi yang dianggap sah (bukan Rejected).
        $base = TransaksiKeuangan::where('status', '!=', 'Rejected');

        // Total Saldo Kas: akumulasi seluruh unit (RT/RW/DKM) = pemasukan - pengeluaran.
        // Keputusan: memakai agregat semua unit agar mencerminkan total kas lingkungan.
        $totalPemasukanAll = (clone $base)->where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaranAll = (clone $base)->where('tipe', 'pengeluaran')->sum('jumlah');
        $saldo = $totalPemasukanAll - $totalPengeluaranAll;

        // Tanggal transaksi terbaru sebagai "diperbarui".
        $latestTanggal = (clone $base)->max('tanggal');
        $updated = $latestTanggal ? Carbon::parse($latestTanggal)->translatedFormat('d M Y') : '-';

        // Rentang bulan berjalan.
        $awalBulan = now()->startOfMonth();
        $akhirBulan = now()->endOfMonth();
        $bulanLabel = now()->translatedFormat('M');

        $pemasukanBulan = (clone $base)
            ->where('tipe', 'pemasukan')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan]);
        $pengeluaranBulan = (clone $base)
            ->where('tipe', 'pengeluaran')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan]);

        $summary = [
            'saldo' => 'Rp ' . number_format($saldo, 0, ',', '.'),
            'updated' => $updated,
            'pemasukan' => [
                'total' => 'Rp ' . number_format((clone $pemasukanBulan)->sum('jumlah'), 0, ',', '.'),
                'bulan' => $bulanLabel,
                'items' => $this->topKategori(clone $pemasukanBulan),
            ],
            'pengeluaran' => [
                'total' => 'Rp ' . number_format((clone $pengeluaranBulan)->sum('jumlah'), 0, ',', '.'),
                'bulan' => $bulanLabel,
                'items' => $this->topKategori(clone $pengeluaranBulan),
            ],
        ];

        // Transaksi terbaru (Verified) untuk daftar singkat.
        $transactions = TransaksiKeuangan::where('status', 'Verified')
            ->orderByDesc('tanggal')
            ->take(5)
            ->get()
            ->map(function (TransaksiKeuangan $t) {
                $isIncome = $t->tipe === 'pemasukan';
                $prefix = $isIncome ? '+ ' : '- ';

                return [
                    'icon' => $isIncome ? 'income' : 'expense',
                    'title' => $t->judul,
                    'date' => $t->tanggal ? $t->tanggal->translatedFormat('d M Y') : '-',
                    'amount' => $prefix . 'Rp ' . number_format($t->jumlah, 0, ',', '.'),
                    'type' => $isIncome ? 'positive' : 'negative',
                ];
            })
            ->all();

        // Laporan resmi yang sudah dipublikasikan & disetujui.
        $colorCycle = ['blue', 'green', 'red'];
        $namaBulan = $this->namaBulanMap();

        $reports = LaporanKeuangan::where('is_published', true)
            ->where('status', 'Approved')
            ->orderByDesc('periode_tahun')
            ->orderByDesc('periode_bulan')
            ->orderByDesc('updated_at')
            ->get()
            ->values()
            ->map(function (LaporanKeuangan $r, int $i) use ($colorCycle, $namaBulan) {
                $periodeBulan = $namaBulan[$r->periode_bulan] ?? $r->periode_bulan;

                return [
                    'id' => $r->id,
                    'title' => $r->judul,
                    'period' => trim($periodeBulan . ' ' . $r->periode_tahun),
                    'size' => '-',
                    'color' => $colorCycle[$i % count($colorCycle)],
                ];
            })
            ->all();

        return view('pages.transparansi', compact('summary', 'transactions', 'reports'));
    }

    public function downloadPdf($id)
    {
        $laporan = LaporanKeuangan::where('is_published', true)->findOrFail($id);

        $namaBulan = $this->namaBulanMap();
        $periodeBulan = $namaBulan[$laporan->periode_bulan] ?? $laporan->periode_bulan;

        $report = [
            'id' => $laporan->id,
            'title' => $laporan->judul,
            'period' => trim($periodeBulan . ' ' . $laporan->periode_tahun),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.transparansi_pdf', compact('report'));
        return $pdf->download(str_replace(' ', '_', $report['title']) . '.pdf');
    }

    /**
     * Ambil top kategori (maks 2) dari query transaksi beserta total nominalnya.
     *
     * @return array<int, array{label: string, amount: string}>
     */
    private function topKategori($query): array
    {
        return $query
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->take(2)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->kategori ?: 'Lainnya',
                'amount' => 'Rp ' . number_format((int) $row->total, 0, ',', '.'),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function namaBulanMap(): array
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }
}
