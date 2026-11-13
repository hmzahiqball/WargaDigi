<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\UmkmProduk;
use App\Support\KontenScope;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        // Statistik jumlah KK, jiwa, dan layanan sengaja tidak ditampilkan di halaman publik.

        // Berita terkini (Publish) terbaru.
        // Berita terbit tingkat RW maupun RT (agenda tetap mengikuti cakupan RT).
        $berita = Berita::with('rt')->where('status', 'Publish')
            ->orderByDesc('tanggal_publish')
            ->take(3)
            ->get()
            ->map(fn (Berita $b) => [
                'id' => $b->id,
                'slug' => $b->kunci_url,
                'image' => $b->featured_image,
                'date' => optional($b->tanggal_publish)->format('d M Y') ?? '',
                'title' => $b->judul_berita,
                'excerpt' => Str::limit(strip_tags($b->isi_berita), 120),
                'category' => $b->kategori,
                'origin' => $b->asal_label,
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

        // Agenda terbit yang sedang berlangsung atau akan datang (belum selesai).
        $agendaTerbit = fn () => KontenScope::untukWarga(Agenda::query(), auth()->user())->where('status', 'Publish');
        $agenda = $agendaTerbit()
            ->where(fn ($q) => $q->where('tanggal_selesai', '>=', now())
                ->orWhere(fn ($w) => $w->whereNull('tanggal_selesai')->where('tanggal_mulai', '>=', today())))
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get()
            ->map(function (Agenda $a) {
                $mulai = $a->tanggal_mulai;
                $waktu = $mulai ? $mulai->format('H:i') : '';
                $lokasi = $a->lokasi ? ' · ' . $a->lokasi : '';
                return [
                    'day' => $mulai ? $mulai->format('d') : '--',
                    'month' => $mulai ? $mulai->translatedFormat('M') : '',
                    'title' => $a->judul_agenda,
                    'time' => trim($waktu . $lokasi),
                    'url' => route('agenda.show', $a->kunci_url),
                ];
            })
            ->all();

        // Tanda tanggal di kalender: agenda terbit dari 3 bulan lalu sampai 6 bulan ke depan.
        $tandaKalender = AgendaPublikController::tandaKalender($agendaTerbit());

        return view('pages.home', compact('berita', 'umkm', 'agenda', 'tandaKalender'));
    }
}
