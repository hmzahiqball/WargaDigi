<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Support\SlugKonten;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        // Berita terbit tingkat RW maupun RT tampil untuk publik (asal ditandai lencana RW / RT xx).
        $publishedQuery = Berita::with('rt')->where('status', 'Publish');

        // Kategori: 'Semua' + master kategori berita (urutan master). Kategori nonaktif tetap
        // ditampilkan bila masih dipakai berita terbit agar berita lama tetap bisa difilter.
        $kategoriTerpakai = (clone $publishedQuery)->whereNotNull('kategori_id')->distinct()->pluck('kategori_id');
        $categories = collect(['Semua'])
            ->merge(
                \App\Models\KategoriKonten::query()->untuk('berita')
                    ->where(fn ($q) => $q->where('aktif', true)->orWhereIn('id', $kategoriTerpakai))
                    ->urut()
                    ->pluck('nama')
            )
            ->values()
            ->all();

        $articles = (clone $publishedQuery)
            ->orderByDesc('tanggal_publish')
            ->get()
            ->map(fn (Berita $b) => [
                'id' => $b->id,
                'slug' => $b->kunci_url,
                'image' => Berita::urlGambar($b->featured_image),
                'date' => optional($b->tanggal_publish)->translatedFormat('d M Y') ?? '',
                'title' => $b->judul_berita,
                'excerpt' => Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($b->isi_berita)))), 160),
                'category' => $b->kategori,
                'minutes' => Berita::menitBaca($b->isi_berita),
                'origin' => $b->asal_label,
            ])
            ->values()
            ->all();

        return view('pages.berita', compact('categories', 'articles'));
    }

    public function show($id)
    {
        $berita = SlugKonten::cari(
            Berita::with('rt')->where('status', 'Publish'), // berita RT juga publik
            (string) $id
        );
        abort_if(! $berita, 404);

        // Link lama berbasis id dialihkan ke URL ber-slug.
        if ($berita->slug && $id !== $berita->slug) {
            return redirect()->route('berita.show', $berita->slug, 301);
        }

        $article = [
            'id' => $berita->id,
            'image' => $berita->featured_image,
            'image_url' => Berita::urlGambar($berita->featured_image),
            'date' => optional($berita->tanggal_publish)->format('d M Y') ?? '',
            'title' => $berita->judul_berita,
            'excerpt' => Str::limit(strip_tags($berita->isi_berita), 160),
            'content' => $berita->isi_berita,
            'category' => $berita->kategori,
            'origin' => $berita->asal_label,
        ];

        return view('pages.berita_detail', compact('article'));
    }
}
