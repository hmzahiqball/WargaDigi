<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Keluarga;
use App\Models\LayananSurat;
use App\Models\Penduduk;
use App\Models\UmkmProduk;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        // Statistik ringkas (struktur lama: number, suffix, label).
        $stats = [
            ['number' => Keluarga::count(), 'suffix' => '+', 'label' => 'Kepala Keluarga'],
            ['number' => Penduduk::count(), 'suffix' => '', 'label' => 'Jiwa Aktif'],
            ['number' => LayananSurat::count(), 'suffix' => '', 'label' => 'Layanan Digital'],
        ];

        // Berita terkini (Publish) terbaru.
        $berita = Berita::where('status', 'Publish')
            ->orderByDesc('tanggal_publish')
            ->take(3)
            ->get()
            ->map(fn (Berita $b) => [
                'id' => $b->id,
                'image' => $b->featured_image,
                'date' => optional($b->tanggal_publish)->format('d M Y') ?? '',
                'title' => $b->judul_berita,
                'excerpt' => Str::limit(strip_tags($b->isi_berita), 120),
                'category' => $b->kategori,
            ])
            ->all();

        // Produk UMKM dari usaha yang sudah di-Approve.
        $umkm = UmkmProduk::with(['usaha.user.penduduk.keluarga.rt'])
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', fn ($q) => $q->where('status_verifikasi', 'Approved'))
            ->take(3)
            ->get()
            ->map(function (UmkmProduk $p) {
                $namaRt = optional(optional(optional(optional(optional($p->usaha)->user)->penduduk)->keluarga)->rt)->nama_rt;

                return [
                    'image' => $p->foto_produk,
                    'name' => $p->nama_produk,
                    'price' => 'Rp ' . number_format((float) $p->harga, 0, ',', '.'),
                    'rt' => $namaRt ? 'Warga RT ' . $namaRt : 'Warga RW 21',
                    'desc' => Str::limit(strip_tags($p->deskripsi), 90),
                ];
            })
            ->all();

        // Agenda mendatang (Publish) yang belum lewat.
        $agenda = Agenda::where('status', 'Publish')
            ->whereDate('tanggal_mulai', '>=', today())
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get()
            ->map(function (Agenda $a) {
                $mulai = $a->tanggal_mulai;
                $waktu = $mulai ? $mulai->format('H:i') : '';
                $lokasi = $a->lokasi ? ' ' . $a->lokasi : '';

                return [
                    'day' => $mulai ? $mulai->format('d') : '--',
                    'month' => $mulai ? $mulai->translatedFormat('M') : '',
                    'title' => $a->judul_agenda,
                    'time' => trim($waktu . $lokasi),
                ];
            })
            ->all();

        return view('pages.home', compact('stats', 'berita', 'umkm', 'agenda'));
    }
}
