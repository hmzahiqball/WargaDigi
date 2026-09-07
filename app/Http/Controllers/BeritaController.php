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

    public function show($id)
    {
        $articles = [
            [
                'image' => '/images/berita-1.jpg',
                'date' => '12 Juli 2026',
                'title' => 'Jadwal Posbindu dan Cek Kesehatan Gratis Bulan Ini',
                'excerpt' => 'Pemeriksaan rutin untuk warga usia lanjut dan dewasa kembali diadakan di balai warga RW 21. Pastikan Anda membawa...',
                'content' => 'Pemeriksaan rutin untuk warga usia lanjut dan dewasa kembali diadakan di balai warga RW 21. Pastikan Anda membawa KTP dan KMS (Kartu Menuju Sehat) jika ada. Acara akan dimulai pada pukul 08:00 WIB hingga selesai. Layanan meliputi cek gula darah, tensi, asam urat, dan kolesterol.',
                'category' => 'Kesehatan',
            ],
            [
                'image' => '/images/berita-2.jpg',
                'date' => '01 Agustus 2026',
                'title' => 'Laporan Keuangan Kas RW Bulan September',
                'excerpt' => 'Laporan rincian pemasukan dan pengeluaran kas lingkungan untuk...',
                'content' => 'Laporan rincian pemasukan dan pengeluaran kas lingkungan untuk bulan September telah dipublikasikan. Pemasukan terbesar didapatkan dari iuran rutin warga, sedangkan pengeluaran dialokasikan untuk perbaikan lampu jalan dan santunan warga sakit. Rincian lengkap bisa diunduh pada halaman transparansi.',
                'category' => 'Transparansi',
            ],
            [
                'image' => null,
                'date' => '28 Juli 2026',
                'title' => 'Kerja Bakti Rutin Menghadapi Musim Hujan',
                'excerpt' => 'Mari bersama-sama membersihkan saluran air dan fasilitas umum lingkungan kita untuk mencegah genangan air. Bawa alat kebersihan masing-masing.',
                'content' => 'Mari bersama-sama membersihkan saluran air dan fasilitas umum lingkungan kita untuk mencegah genangan air. Kerja bakti ini wajib diikuti oleh perwakilan setiap KK. Harap membawa alat kebersihan masing-masing seperti cangkul, sapu lidi, dan pengki. Konsumsi disediakan oleh ibu-ibu PKK.',
                'category' => 'Kegiatan',
            ],
            [
                'image' => null,
                'date' => '10 Juli 2026',
                'title' => 'Pemberitahuan Pemadaman Listrik Sementara',
                'excerpt' => 'Diberitahukan kepada seluruh warga RW 21, akan ada pemadaman listrik sementara oleh pihak PLN sehubungan dengan perbaikan gardu induk. Pemadaman...',
                'content' => 'Diberitahukan kepada seluruh warga RW 21, akan ada pemadaman listrik sementara oleh pihak PLN sehubungan dengan perbaikan gardu induk. Pemadaman direncanakan berlangsung dari pukul 10:00 hingga 14:00 WIB. Mohon maaf atas ketidaknyamanan ini dan persiapkan segala sesuatunya.',
                'category' => 'Pengumuman',
            ],
        ];

        if (!isset($articles[$id])) {
            abort(404);
        }

        $article = $articles[$id];
        return view('pages.berita_detail', compact('article'));
    }
}
