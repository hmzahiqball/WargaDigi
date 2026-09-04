<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy statistik pengumuman
        $stats = [
            'draft' => 4,
            'pending_rw' => 2,
            'published' => 18,
        ];

        // Data dummy tabel pengumuman
        $pengumuman = [
            [
                'id' => 1,
                'judul' => 'Jadwal Kerja Bakti RT 03',
                'tanggal' => '12 Okt 2023',
                'status' => 'Published',
                'status_bg' => 'rgba(46, 125, 50, 0.10)',
                'status_color' => '#007232',
            ],
            [
                'id' => 2,
                'judul' => 'Pemadaman Listrik Bergilir',
                'tanggal' => '15 Okt 2023',
                'status' => 'Pending RW',
                'status_bg' => '#FFDDB5',
                'status_color' => '#2A1800',
            ],
            [
                'id' => 3,
                'judul' => 'Draft: Lomba 17 Agustus',
                'tanggal' => '-',
                'status' => 'Draft',
                'status_bg' => '#E4E2E2',
                'status_color' => '#40493D',
            ]
        ];

        return view('opKonten.pengumuman.index', compact('stats', 'pengumuman'));
    }
}
