<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
