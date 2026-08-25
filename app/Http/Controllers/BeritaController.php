<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index()
    {
        $categories = ['Semua', 'Pengumuman', 'Transparansi', 'Kesehatan', 'Kegiatan'];

        $articles = [
            [
                'image' => '/images/berita-1.jpg',
                'date' => '12 Juli 2026',
                'title' => 'Jadwal Posbindu dan Cek Kesehatan Gratis Bulan Ini',
                'excerpt' => 'Pemeriksaan rutin untuk warga usia lanjut dan dewasa kembali diadakan di balai warga RW 21. Pastikan Anda membawa...',
                'category' => 'Kesehatan',
            ],
            [
                'image' => '/images/berita-2.jpg',
                'date' => '01 Agustus 2026',
                'title' => 'Laporan Keuangan Kas RW Bulan September',
                'excerpt' => 'Laporan rincian pemasukan dan pengeluaran kas lingkungan untuk...',
                'category' => 'Transparansi',
            ],
            [
                'image' => null,
                'date' => '28 Juli 2026',
                'title' => 'Kerja Bakti Rutin Menghadapi Musim Hujan',
                'excerpt' => 'Mari bersama-sama membersihkan saluran air dan fasilitas umum lingkungan kita untuk mencegah genangan air. Bawa alat kebersihan masing-masing.',
                'category' => 'Kegiatan',
            ],
            [
                'image' => null,
                'date' => '10 Juli 2026',
                'title' => 'Pemberitahuan Pemadaman Listrik Sementara',
                'excerpt' => 'Diberitahukan kepada seluruh warga RW 21, akan ada pemadaman listrik sementara oleh pihak PLN sehubungan dengan perbaikan gardu induk. Pemadaman...',
                'category' => 'Pengumuman',
            ],
        ];

        return view('pages.berita', compact('categories', 'articles'));
    }
}
