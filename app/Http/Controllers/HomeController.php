<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            ['number' => 150, 'suffix' => '+', 'label' => 'Kepala Keluarga'],
            ['number' => 45, 'suffix' => '', 'label' => 'Jiwa Aktif'],
            ['number' => 10, 'suffix' => '', 'label' => 'Layanan Digital'],
        ];

        $berita = [
            [
                'image' => '/images/berita-1.jpg',
                'date' => '10 Agustus 2024',
                'title' => 'Kerja Bakti Warga: Inti Persiapan Lomba 17-an',
                'excerpt' => 'Hari ini warga RW 21 berkumpul dan saling bekerja sama membersihkan lingkungan untuk mempersiapkan lomba kemerdekaan.',
                'category' => 'kegiatan',
            ],
            [
                'image' => '/images/berita-2.jpg',
                'date' => '08 Agustus 2024',
                'title' => 'Laporan Kas RW Bulan Agt 2024',
                'excerpt' => 'Laporan arus kas terbaru RW 21 dirilis. Transparansi keuangan sebagai kunci kepercayaan masyarakat.',
                'category' => 'transparansi',
            ],
            [
                'image' => '/images/berita-3.jpg',
                'date' => '05 Agustus 2024',
                'title' => 'Jadwal Posyandu Balita & Lansia Bulan Ini',
                'excerpt' => 'Cek jadwal posyandu terdekat dan fasilitas kesehatan yang tersedia di lingkungan sekitar di Balai RW.',
                'category' => 'kesehatan',
            ],
        ];

        $umkm = [
            [
                'image' => '/images/umkm-1.jpg',
                'name' => 'Nasi Kuning Bu Siti',
                'price' => 'Rp 25.000',
                'rt' => 'Warga RT 02',
                'desc' => 'Nasi kuning wangi dengan ayam goreng, telur balado, oreg tempe.',
            ],
            [
                'image' => '/images/umkm-2.jpg',
                'name' => 'Jasa Jahit Pak RT',
                'price' => 'Mulai Rp 50k',
                'rt' => 'Warga RT 05',
                'desc' => 'Menerima jahitan baju, celana, dan perbaikan pakaian.',
            ],
            [
                'image' => '/images/umkm-3.jpg',
                'name' => 'Kripik Tempe Renyah',
                'price' => 'Rp 35.000',
                'rt' => 'Warga RT 01',
                'desc' => 'Kripik tempe renyah buatan rumahan, gurih dan lezat.',
            ],
        ];

        $agenda = [
            [
                'day' => '17',
                'month' => 'Agt',
                'title' => 'Perayaan HUT RI ke-79',
                'time' => '08:00 - 14:00 Balai RW',
            ],
            [
                'day' => '25',
                'month' => 'Agt',
                'title' => 'Rapat Rutin Pengurus',
                'time' => '19:00 - 21:00 Pos RT',
            ],
            [
                'day' => '01',
                'month' => 'Sep',
                'title' => 'Posyandu Balita',
                'time' => '09:00 - 12:00 Balai RW',
            ],
        ];

        return view('pages.home', compact('stats', 'berita', 'umkm', 'agenda'));
    }
}
