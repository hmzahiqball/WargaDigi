<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransparansiController extends Controller
{
    public function index()
    {
        $summary = [
            'saldo' => 'Rp 15.450.000',
            'updated' => '15 Okt 2024',
            'pemasukan' => [
                'total' => 'Rp 4.200.000',
                'bulan' => 'Okt',
                'items' => [
                    ['label' => 'Iuran Warga', 'amount' => 'Rp 3.500.000'],
                    ['label' => 'Donasi Fasilitas', 'amount' => 'Rp 700.000'],
                ],
            ],
            'pengeluaran' => [
                'total' => 'Rp 1.850.000',
                'bulan' => 'Okt',
                'items' => [
                    ['label' => 'Petugas Keamanan', 'amount' => 'Rp 1.200.000'],
                    ['label' => 'Perbaikan Lampu Jalan', 'amount' => 'Rp 650.000'],
                ],
            ],
        ];

        $transactions = [
            ['icon' => 'income', 'title' => 'Iuran RT 01', 'date' => '14 Okt 2024', 'amount' => '+ Rp 500.000', 'type' => 'positive'],
            ['icon' => 'expense', 'title' => 'Perbaikan Gapura', 'date' => '10 Okt 2024', 'amount' => '- Rp 350.000', 'type' => 'negative'],
            ['icon' => 'income', 'title' => 'Iuran RT 02', 'date' => '08 Okt 2024', 'amount' => '+ Rp 450.000', 'type' => 'positive'],
        ];

        $reports = [
            ['title' => 'Laporan Triwulan III – 2024', 'period' => 'Juli - September 2024', 'size' => '1.2 MB', 'color' => 'blue'],
            ['title' => 'Laporan Triwulan II – 2024', 'period' => 'April - Juni 2024', 'size' => '1.5 MB', 'color' => 'green'],
            ['title' => 'Laporan Tahunan 2023', 'period' => 'Januari - Desember 2023', 'size' => '3.8 MB', 'color' => 'red'],
        ];

        return view('pages.transparansi', compact('summary', 'transactions', 'reports'));
    }

    public function downloadPdf($id)
    {
        $reports = [
            ['title' => 'Laporan Triwulan III – 2024', 'period' => 'Juli - September 2024'],
            ['title' => 'Laporan Triwulan II – 2024', 'period' => 'April - Juni 2024'],
            ['title' => 'Laporan Tahunan 2023', 'period' => 'Januari - Desember 2023'],
        ];

        if (!isset($reports[$id])) {
            abort(404);
        }

        $report = $reports[$id];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pages.transparansi_pdf', compact('report'));
        return $pdf->download(str_replace(' ', '_', $report['title']) . '.pdf');
    }
}
