<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UmkmProduk;
use App\Models\UmkmUsaha;

class UmkmController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) ($request->get('q') ?? $request->get('search')));
        $kategori = $request->get('kategori');

        $query = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'kategori_umkm', 'produk'])
            ->where('status_verifikasi', 'Approved')
            ->where('is_active', true);

        if ($kategori && !in_array($kategori, ['Semua', 'Semua Kategori'])) {
            $query->whereHas('kategori_umkm', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_usaha) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(deskripsi) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(alamat_usaha) LIKE ?', [$term]);
                    }
                });
            }
        }

        $daftarUsaha = $query->latest()->paginate(9)->withQueryString();
        $daftarKategoriUmkm = \App\Models\KategoriUmkm::all();

        return view('pages.umkm', compact('daftarUsaha', 'daftarKategoriUmkm', 'search', 'kategori'));
    }

    public function detailUsaha(Request $request, $id = null)
    {
        $userNik = auth()->check() ? auth()->user()->nik : null;

        $usahaQuery = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'produk.kategori_produk', 'kategori_umkm', 'kategori_produk']);

        if ($id) {
            $usaha = (clone $usahaQuery)->where('id', $id)
                ->where(function ($q) use ($userNik) {
                    $q->where('status_verifikasi', 'Approved')
                      ->where('is_active', true);
                    if ($userNik) {
                        $q->orWhere('nik', $userNik);
                    }
                })
                ->first();
        } else {
            $usaha = (clone $usahaQuery)
                ->where('status_verifikasi', 'Approved')
                ->where('is_active', true)
                ->first();
        }

        if (!$usaha) {
            abort(404, 'Profil usaha tidak ditemukan atau belum disetujui.');
        }

        $query = UmkmProduk::with(['usaha.kategori_produk', 'usaha.kategori_umkm', 'kategori_produk'])
            ->where('umkm_usaha_id', $usaha->id);

        if (!$userNik || $usaha->nik !== $userNik) {
            $query->where('status_produk', 'Aktif');
        }

        $search = trim((string) ($request->get('q') ?? $request->get('search')));
        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term])
                          ->orWhereRaw('LOWER(deskripsi) LIKE ?', [$term]);
                    }
                });
            }
        }

        $kategori = $request->get('kategori');
        if ($kategori && $kategori !== 'Semua Kategori') {
            $query->whereHas('kategori_produk', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        $sort = $request->get('sort');
        if ($sort === 'termahal') {
            $query->orderBy('harga', 'desc');
        } elseif ($sort === 'termurah') {
            $query->orderBy('harga', 'asc');
        } else {
            $query->latest();
        }

        $produk = $query->paginate(8)->withQueryString();
        $isDashboard = auth()->check();

        return view('pages.detail_usaha', compact('usaha', 'produk', 'isDashboard', 'search', 'kategori', 'sort'));
    }

    public function detailProduk($id)
    {
        $userNik = auth()->check() ? auth()->user()->nik : null;

        $produk = UmkmProduk::with(['usaha.user.penduduk.keluarga.rt', 'usaha.kategori_umkm', 'kategori_produk'])
            ->where('id', $id)
            ->whereHas('usaha', function ($q) use ($userNik) {
                $q->where(function ($sq) use ($userNik) {
                    $sq->where('status_verifikasi', 'Approved')
                       ->where('is_active', true);
                    if ($userNik) {
                        $sq->orWhere('nik', $userNik);
                    }
                });
            })
            ->where(function ($q) use ($userNik) {
                $q->where('status_produk', 'Aktif');
                if ($userNik) {
                    $q->orWhereHas('usaha', function ($uq) use ($userNik) {
                        $uq->where('nik', $userNik);
                    });
                }
            })
            ->first();

        if (!$produk) {
            abort(404, 'Produk tidak ditemukan atau belum aktif.');
        }

        // Mencegah manipulasi counter jumlah_akses dengan session lock per session
        $sessionKey = 'viewed_umkm_produk_' . $produk->id;
        if (!session()->has($sessionKey)) {
            $produk->increment('jumlah_akses');
            session()->put($sessionKey, true);
        }

        $usaha = $produk->usaha;
        $isDashboard = auth()->check();

        return view('pages.detail_produk', compact('produk', 'usaha', 'isDashboard'));
    }

    public function trackWaClick($id)
    {
        $usaha = UmkmUsaha::find($id);

        if (!$usaha) {
            return response()->json([
                'success' => false,
                'message' => 'Usaha tidak ditemukan',
            ], 404);
        }

        $usaha->increment('klikWA');

        return response()->json([
            'success' => true,
            'message' => 'Klik WhatsApp berhasil dicatat',
            'klikWA'  => $usaha->klikWA,
        ]);
    }
}

