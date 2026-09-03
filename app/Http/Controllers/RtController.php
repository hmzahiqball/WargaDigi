<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RtController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_warga' => [
                'total' => '1.248',
                'change' => '+12 bulan ini',
                'laki_laki' => 610,
                'perempuan' => 638,
            ],
            'dokumen_menunggu' => [
                'total' => 24,
                'status' => 'Perlu ditinjau segera',
                'surat_domisili' => 15,
                'kartu_keluarga' => 9,
            ],
            'persetujuan_keuangan' => [
                'total' => 5,
                'status' => 'Menunggu tanda tangan RT',
                'iuran_bulanan' => 3,
                'laporan_kas' => 2,
            ],
        ];

        $chartData = [
            'labels' => ['Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov'],
            'values' => [1205, 1218, 1220, 1228, 1236, 1240, 1248],
        ];

        $aktivitasKeuangan = [
            [
                'title' => 'Laporan Iuran Keamanan Bulanan untuk Blok A diserahkan.',
                'time' => '5 jam yang lalu',
                'badge' => 'Disetujui',
                'badge_class' => 'bg-success text-white',
                'icon' => 'bi-check-circle-fill text-success',
                'bg_icon' => 'bg-success bg-opacity-10',
            ],
            [
                'title' => 'Pengajuan Dana Perbaikan Jalan Blok C.',
                'time' => 'Kemarin',
                'badge' => 'Menunggu',
                'badge_class' => 'bg-warning bg-opacity-20 text-dark',
                'icon' => 'bi-three-dots text-warning',
                'bg_icon' => 'bg-warning bg-opacity-10',
            ],
        ];

        $aktivitasDokumen = [
            [
                'title' => 'Budi Santoso mengajukan Surat Keterangan Domisili.',
                'time' => '2 jam yang lalu',
                'badge' => 'Menunggu',
                'badge_class' => 'bg-warning bg-opacity-20 text-dark',
                'icon' => 'bi-person-vcard text-primary',
                'bg_icon' => 'bg-primary bg-opacity-10',
            ],
            [
                'title' => 'Keluarga Ahmad memperbarui Kartu Keluarga.',
                'time' => '1 hari yang lalu',
                'badge' => 'Selesai',
                'badge_class' => 'bg-success text-white',
                'icon' => 'bi-file-earmark-text text-success',
                'bg_icon' => 'bg-success bg-opacity-10',
            ],
        ];

        return view('rt.dashboard', compact('stats', 'chartData', 'aktivitasKeuangan', 'aktivitasDokumen'));
    }
}
