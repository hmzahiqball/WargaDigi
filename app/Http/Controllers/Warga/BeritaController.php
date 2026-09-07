<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Agenda;
use App\Models\Pengumuman;
use App\Models\UmkmProduk;
use Carbon\Carbon;

class BeritaController extends Controller
{
    public function index()
    {
        // Get Berita
        $berita = Berita::with('operator')
            ->where('status', 'Publish')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Berita',
                    'title' => $item->judul_berita,
                    'description' => strip_tags($item->isi_berita),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->format('d M Y, H:i'),
                    'location' => null,
                    'image' => $item->featured_image ? asset($item->featured_image) : null,
                    'raw_content' => $item->isi_berita,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Agenda
        $agenda = Agenda::with('operator')
            ->where('status', 'Publish')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Agenda',
                    'title' => $item->judul_agenda,
                    'description' => strip_tags($item->detail_pengumuman ?? ''),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_mulai)->format('d M Y, H:i') . ' WIB',
                    'location' => $item->lokasi,
                    'image' => $item->banner_flyer ? asset($item->banner_flyer) : null,
                    'raw_content' => $item->detail_pengumuman,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Pengumuman
        $pengumuman = Pengumuman::with('operator')
            ->where('status', 'Publish')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Pengumuman',
                    'title' => $item->judul_pengumuman,
                    'description' => strip_tags($item->isi_pengumuman ?? ''),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->format('d M Y, H:i'),
                    'location' => null,
                    'image' => null,
                    'raw_content' => $item->isi_pengumuman,
                    'kategori' => 'Pengumuman Warga',
                    'updated_at' => $item->updated_at
                ];
            });

        // Get UMKM News
        $umkmProduk = UmkmProduk::with('usaha')
            ->where('status_produk', 'Aktif')
            ->where('is_tersedia', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                // Determine image path (might need adjustment based on how UMKM saves images)
                $imageUrl = null;
                if ($item->foto_produk) {
                    $imageUrl = str_starts_with($item->foto_produk, 'http') 
                        ? $item->foto_produk 
                        : asset('storage/' . $item->foto_produk);
                }

                return [
                    'id' => $item->id,
                    'type' => 'UMKM News',
                    'title' => $item->nama_produk,
                    'description' => strip_tags($item->deskripsi ?? ''),
                    'author' => $item->usaha->nama_usaha ?? 'Unknown',
                    'date' => Carbon::parse($item->created_at)->format('d M Y, H:i'),
                    'location' => null,
                    'image' => $imageUrl,
                    'raw_content' => $item->deskripsi,
                    'kategori' => 'Produk UMKM',
                    'updated_at' => $item->created_at
                ];
            });

        $sorotanUmkm = $umkmProduk->first();
        $pengumumanPenting = $pengumuman->take(3); // First 3 pengumuman

        $semua = collect($berita)->merge($agenda)->merge($pengumuman)->merge($umkmProduk)->sortByDesc('updated_at')->values()->all();

        // Unggulan: Try to find a Berita or Agenda with image
        $unggulan = collect($semua)->firstWhere(function ($val) {
            return ($val['type'] === 'Berita' || $val['type'] === 'Agenda') && $val['image'] !== null;
        }) ?? collect($semua)->first();

        return view('warga.berita.index', compact('berita', 'agenda', 'pengumuman', 'umkmProduk', 'semua', 'unggulan', 'sorotanUmkm', 'pengumumanPenting'));
    }
}
