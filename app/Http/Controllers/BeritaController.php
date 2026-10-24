<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index()
    {
        $publishedQuery = Berita::where('status', 'Publish');

        // Kategori: 'Semua' + daftar kategori distinct dari berita yang Publish.
        $categories = collect(['Semua'])
            ->merge(
                (clone $publishedQuery)
                    ->whereNotNull('kategori')
                    ->distinct()
                    ->orderBy('kategori')
                    ->pluck('kategori')
            )
            ->values()
            ->all();

        $articles = (clone $publishedQuery)
            ->orderByDesc('tanggal_publish')
            ->get()
            ->map(fn (Berita $b) => [
                'id' => $b->id,
                'image' => $b->featured_image,
                'date' => optional($b->tanggal_publish)->format('d M Y') ?? '',
                'title' => $b->judul_berita,
                'excerpt' => Str::limit(strip_tags($b->isi_berita), 160),
                'category' => $b->kategori,
            ])
            ->values()
            ->all();

        return view('pages.berita', compact('categories', 'articles'));
    }

    public function show($id)
    {
        $berita = Berita::where('status', 'Publish')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('slug', $id);
            })
            ->firstOrFail();

        $article = [
            'id' => $berita->id,
            'image' => $berita->featured_image,
            'date' => optional($berita->tanggal_publish)->format('d M Y') ?? '',
            'title' => $berita->judul_berita,
            'excerpt' => Str::limit(strip_tags($berita->isi_berita), 160),
            'content' => $berita->isi_berita,
            'category' => $berita->kategori,
        ];

        return view('pages.berita_detail', compact('article'));
    }
}
