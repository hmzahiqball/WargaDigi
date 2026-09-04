<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        // Data dummy grid galeri
        $galeri = [
            [
                'id' => 1,
                'judul' => 'Kerja Bakti Membersihkan Saluran Air',
                'deskripsi' => 'Kegiatan gotong royong warga RW 03 untuk membersihkan saluran air menjelang musim hujan.',
                'tanggal' => '12 Okt 2024',
                'status' => 'SELESAI',
            ],
            [
                'id' => 2,
                'judul' => 'Rapat Koordinasi Siskamling',
                'deskripsi' => 'Evaluasi jadwal ronda malam dan penambahan perlengkapan pos kamling.',
                'tanggal' => '05 Okt 2024',
                'status' => 'SELESAI',
            ],
            [
                'id' => 3,
                'judul' => 'Bantuan Sosial Sembako untuk Warga',
                'deskripsi' => 'Distribusi paket sembako bulanan hasil donasi kas warga.',
                'tanggal' => '28 Sep 2024',
                'status' => 'SELESAI',
            ]
        ];

        return view('opKonten.galeri.index', compact('galeri'));
    }
}
