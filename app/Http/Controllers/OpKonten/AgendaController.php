<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy statistik agenda
        $stats = [
            'draft' => 12,
            'pending_rw' => 5,
            'published' => 48,
        ];

        // Data dummy tabel agenda
        $agenda = [
            [
                'id' => 1,
                'judul' => 'Rapat Rutin Pengurus RT',
                'deskripsi' => 'Pembahasan kas bulanan',
                'tanggal_waktu' => '15 Okt 2023, 19:30 WIB',
                'lokasi' => 'Balai Warga RW 05',
                'status' => 'Published',
                'status_bg' => '#A3F69C',
                'status_color' => '#005312',
            ],
            [
                'id' => 2,
                'judul' => 'Kerja Bakti Lapangan',
                'deskripsi' => 'Persiapan lomba 17an',
                'tanggal_waktu' => '22 Okt 2023, 07:00 WIB',
                'lokasi' => 'Lapangan Olahraga RW 05',
                'status' => 'Pending RW',
                'status_bg' => '#FFDDB5',
                'status_color' => '#643F00',
            ],
            [
                'id' => 3,
                'judul' => 'Bazar UMKM Warga',
                'deskripsi' => 'Pameran produk lokal',
                'tanggal_waktu' => '05 Nov 2023, 09:00 WIB',
                'lokasi' => 'Jalan Utama Komplek',
                'status' => 'Draft',
                'status_bg' => '#E9E8E7',
                'status_color' => '#40493D',
            ]
        ];

        return view('opKonten.agenda.index', compact('stats', 'agenda'));
    }
}
