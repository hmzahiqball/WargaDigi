<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Agenda;
use App\Models\Pengumuman;
use App\Models\UmkmProduk;
use App\Models\GaleriDokumentasi;
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
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
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
                    'date' => Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('Y-m-d') !== Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('Y-m-d') 
                        ? Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' - ' . Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
                        : Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' - ' . Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB',
                    'location' => $item->lokasi,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'is_rsvp_enabled' => $item->is_rsvp_enabled,
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
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'location' => null,
                    'image' => null,
                    'raw_content' => $item->isi_pengumuman,
                    'kategori' => 'Pengumuman Warga',
                    'is_priority' => $item->is_priority,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get UMKM News
        $umkmProduk = UmkmProduk::with('usaha')
            ->where('status_produk', 'Aktif')
            ->where('status_stok', '!=', 'habis')
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
                    'date' => Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'location' => null,
                    'image' => $imageUrl,
                    'raw_content' => $item->deskripsi,
                    'kategori' => 'Produk UMKM',
                    'updated_at' => $item->created_at
                ];
            });

        // Get Galeri Dokumentasi (AC-06.8)
        $galeriDokumentasi = GaleriDokumentasi::with(['agenda', 'operator'])
            ->whereHas('agenda', function ($q) {
                $q->where('status', 'Publish');
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $agenda = $item->agenda;
                $fotos = is_array($item->foto) ? $item->foto : [];
                $firstImage = count($fotos) > 0 ? asset($fotos[0]) : null;
                $formattedDate = $agenda ? Carbon::parse($agenda->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('d M Y') : Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y');
                
                $imagesGridHtml = '';
                if (count($fotos) > 0) {
                    $imagesGridHtml .= '<div class="row g-3 my-3">';
                    foreach ($fotos as $f) {
                        $imagesGridHtml .= '<div class="col-md-4 col-sm-6"><div class="rounded-3 overflow-hidden shadow-sm border" style="height: 180px;"><img src="' . asset($f) . '" alt="Dokumentasi" style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;" onclick="window.open(\'' . asset($f) . '\', \'_blank\')"></div></div>';
                    }
                    $imagesGridHtml .= '</div>';
                }

                $rawContent = '<p>' . ($agenda->detail_pengumuman ?? ('Dokumentasi kegiatan ' . ($agenda->judul_agenda ?? ''))) . '</p>';
                if ($item->jumlah_peserta) {
                    $rawContent .= '<p><strong><i class="bi bi-people-fill me-1"></i> Jumlah Peserta / Warga Hadir:</strong> ' . $item->jumlah_peserta . ' orang</p>';
                }
                $rawContent .= '<h5 class="fw-bold mt-4 mb-2"><i class="bi bi-images me-2"></i>Foto Dokumentasi Kegiatan:</h5>' . $imagesGridHtml;

                return [
                    'id' => $item->id,
                    'type' => 'Galeri Dokumentasi',
                    'title' => 'Dokumentasi: ' . ($agenda->judul_agenda ?? 'Kegiatan'),
                    'description' => 'Dokumentasi kegiatan ' . ($agenda->judul_agenda ?? '') . ' yang telah selesai dilaksanakan.' . ($item->jumlah_peserta ? ' Dihadiri oleh ' . $item->jumlah_peserta . ' peserta.' : ''),
                    'author' => $item->operator->username ?? 'Operator Konten',
                    'date' => $formattedDate . ', ' . Carbon::parse($item->created_at)->format('H:i'),
                    'location' => $agenda->lokasi ?? null,
                    'image' => $firstImage,
                    'raw_content' => $rawContent,
                    'kategori' => 'Galeri Dokumentasi',
                    'jumlah_peserta' => $item->jumlah_peserta,
                    'fotos' => array_map(fn($f) => asset($f), $fotos),
                    'updated_at' => $item->updated_at
                ];
            });

        $sorotanUmkm = $umkmProduk->first();
        $pengumumanPenting = collect($pengumuman)->where('is_priority', true)->take(3); // First 3 pengumuman

        $semua = collect($berita)->merge($agenda)->merge($pengumuman)->merge($umkmProduk)->merge($galeriDokumentasi)->sortByDesc('updated_at')->values()->all();

        // Unggulan: Try to find a Berita or Agenda with image
        $unggulan = collect($semua)->firstWhere(function ($val) {
            return ($val['type'] === 'Berita' || $val['type'] === 'Agenda' || $val['type'] === 'Galeri Dokumentasi') && $val['image'] !== null;
        }) ?? collect($semua)->first();

        return view('warga.berita.index', compact('berita', 'agenda', 'pengumuman', 'umkmProduk', 'galeriDokumentasi', 'semua', 'unggulan', 'sorotanUmkm', 'pengumumanPenting'));
    }
}
