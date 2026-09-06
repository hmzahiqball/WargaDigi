<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KategoriUmkm;
use App\Models\KategoriProduk;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GaleriUmkmController extends Controller
{

    public function index(Request $request)
    {
        $kategori = $request->get('kategori');
        $search = trim((string) ($request->get('q') ?? $request->get('search')));
        $sort = $request->get('sort', 'terbaru');

        $query = UmkmProduk::with(['usaha.kategori_umkm', 'usaha.user.penduduk'])
            ->where('status_produk', 'Aktif');

        if ($kategori && !in_array($kategori, ['Semua', 'Semua Kategori'])) {
            $query->whereHas('usaha.kategori_umkm', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term])
                          ->orWhereHas('usaha', function ($sq) use ($term) {
                              $sq->whereRaw('LOWER(nama_usaha) LIKE ?', [$term]);
                          });
                    }
                });
            }
        }

        if ($sort === 'termahal') {
            $query->orderBy('harga', 'desc');
        } elseif ($sort === 'termurah') {
            $query->orderBy('harga', 'asc');
        } else {
            $query->latest();
        }

        $produk = $query->paginate(8)->withQueryString();
        $daftarKategoriUmkm = KategoriUmkm::all();

        $produkUnggulan = UmkmProduk::with(['usaha.kategori_umkm', 'usaha.user.penduduk.keluarga.rt', 'kategori_produk'])
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', function ($q) {
                $q->where('status_verifikasi', 'Approved');
            })
            ->orderByDesc('jumlah_akses')
            ->latest()
            ->get()
            ->unique('umkm_usaha_id')
            ->take(3)
            ->values();

        $produkTerbaru = UmkmProduk::with(['usaha.kategori_umkm', 'usaha.user.penduduk.keluarga.rt', 'kategori_produk'])
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', function ($q) {
                $q->where('status_verifikasi', 'Approved');
            })
            ->latest()
            ->get()
            ->unique('umkm_usaha_id')
            ->values();

        $pendingUsahaCount = (Auth::check() && Auth::user()->nik)
            ? UmkmUsaha::where('nik', Auth::user()->nik)->where('status_verifikasi', 'Pending')->count()
            : 0;

        return view('warga.umkm.galeri', compact(
            'produk', 
            'kategori', 
            'search', 
            'sort', 
            'daftarKategoriUmkm', 
            'produkUnggulan', 
            'produkTerbaru', 
            'pendingUsahaCount'
        ));
    }

    public function koleksiProduk(Request $request, $tipe = 'unggulan')
    {
        $tipe = strtolower($tipe);
        if (!in_array($tipe, ['unggulan', 'terbaru'])) {
            $tipe = 'unggulan';
        }

        $kategori = $request->get('kategori');
        $search = trim((string) ($request->get('q') ?? $request->get('search')));
        $sort = $request->get('sort');

        if ($tipe === 'unggulan') {
            $pageTitle = 'Produk Unggulan RW 21';
            $pageSubtitle = 'Koleksi produk UMKM pilihan paling diminati dengan interaksi dan popularitas tertinggi dari warga.';
            $defaultSort = 'terpopuler';
        } else {
            $pageTitle = 'Produk Terbaru Warga';
            $pageSubtitle = 'Jelajahi inovasi dan produk UMKM teranyar yang baru saja didaftarkan oleh warga RW 21.';
            $defaultSort = 'terbaru';
        }

        $sort = $sort ?: $defaultSort;

        $query = UmkmProduk::with(['usaha.kategori_umkm', 'usaha.user.penduduk.keluarga.rt', 'kategori_produk'])
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', function ($q) {
                $q->where('status_verifikasi', 'Approved');
            });

        if ($kategori && !in_array($kategori, ['Semua', 'Semua Kategori'])) {
            $query->whereHas('usaha.kategori_umkm', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        if ($search !== '') {
            $words = array_filter(preg_split('/[\s,]+/', $search));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term])
                          ->orWhereHas('usaha', function ($sq) use ($term) {
                              $sq->whereRaw('LOWER(nama_usaha) LIKE ?', [$term]);
                          });
                    }
                });
            }
        }

        if ($sort === 'terpopuler') {
            $query->orderByDesc('jumlah_akses')->latest();
        } elseif ($sort === 'termahal') {
            $query->orderByDesc('harga');
        } elseif ($sort === 'termurah') {
            $query->orderBy('harga', 'asc');
        } else {
            // terbaru
            $query->latest();
        }

        $produk = $query->paginate(12)->withQueryString();
        $daftarKategoriUmkm = KategoriUmkm::all();

        return view('warga.umkm.koleksi', compact(
            'tipe',
            'pageTitle',
            'pageSubtitle',
            'produk',
            'kategori',
            'search',
            'sort',
            'daftarKategoriUmkm'
        ));
    }


    public function detailUsaha($id = null, ?Request $request = null)
    {
        $request = $request ?? request();
        $usaha = null;
        if ($id) {
            $usaha = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'produk', 'kategori_umkm', 'kategori_produk'])->find($id);
        }

        if (!$usaha) {
            $usaha = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'produk', 'kategori_umkm', 'kategori_produk'])->first();
        }

        if (!$usaha) {
            $kategoriKerajinan = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Kerajinan']);
            $usaha = new UmkmUsaha([
                'nama_usaha' => 'Pahatan Kayu Jati Custom',
                'kategori_umkm_id' => $kategoriKerajinan->id,
                'deskripsi' => 'Karya tangan Pak Budi, RT 03. Cocok untuk hiasan rumah atau hadiah eksklusif. Dibuat dengan dedikasi tinggi menggunakan kayu jati pilihan terbaik.',
                'no_wa' => '628123456789',
            ]);
            $produk = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 8);
            $search = null;
            $kategori = null;
            $sort = null;
        } else {
            $query = UmkmProduk::with(['usaha.kategori_produk', 'usaha.kategori_umkm', 'kategori_produk'])
                ->where('umkm_usaha_id', $usaha->id)
                ->where('status_produk', 'Aktif');

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
        }

        $isDashboard = true;

        return view('pages.detail_usaha', compact('usaha', 'produk', 'isDashboard', 'search', 'kategori', 'sort'));
    }

    public function detailProduk($id)
    {
        $produk = UmkmProduk::with(['usaha.user.penduduk.keluarga.rt', 'usaha.kategori_umkm', 'kategori_produk'])->find($id);

        if (!$produk) {
            $produk = UmkmProduk::with(['usaha.user.penduduk.keluarga.rt', 'usaha.kategori_umkm', 'kategori_produk'])->first();
        }

        if (!$produk) {
            abort(404, 'Produk tidak ditemukan');
        }

        $produk->increment('jumlah_akses');

        $usaha = $produk->usaha;
        $isDashboard = true;
        $isFromKelola = request('from') === 'kelola_umkm' 
            || request('from') === 'kelola' 
            || str_contains(url()->previous(), 'galeri/kelola') 
            || str_contains(request()->header('referer', ''), 'galeri/kelola');

        return view('pages.detail_produk', compact('produk', 'usaha', 'isDashboard', 'isFromKelola'));
    }


    public function createUsaha()
    {
        return redirect()->route('warga.umkm.galeri');
    }

    public function storeUsaha(Request $request)
    {
        $kategoriId = $request->get('kategori_umkm_id');
        if ($kategoriId && !KategoriUmkm::where('id', $kategoriId)->exists()) {
            $kat = KategoriUmkm::firstOrCreate(['nama_kategori' => $kategoriId]);
            $kategoriId = $kat->id;
        } elseif (!$kategoriId && $request->filled('kategori')) {
            $kat = KategoriUmkm::firstOrCreate(['nama_kategori' => $request->kategori]);
            $kategoriId = $kat->id;
        }

        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:100',
            'alamat_usaha' => 'required|string',
            'no_wa' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'foto_sampul' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'foto_usaha' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if (!$kategoriId) {
            $defaultKat = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Lainnya']);
            $kategoriId = $defaultKat->id;
        }
        $validated['kategori_umkm_id'] = $kategoriId;

        $userNik = Auth::user()->nik ?? null;
        if (!$userNik) {
            return back()->with('error', 'Anda harus login untuk mendaftarkan usaha.');
        }
        $validated['nik'] = $userNik;
        $validated['status_verifikasi'] = 'Pending';
        $validated['is_active'] = false;


        $cleanWa = preg_replace('/[^0-9]/', '', $validated['no_wa']);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
            $cleanWa = '62' . $cleanWa;
        }
        $validated['no_wa'] = $cleanWa;

        
        $file = $request->file('foto_sampul') ?? $request->file('foto_usaha');
        if ($file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('umkm-photos', $fileName, 'public');
            $validated['foto_usaha'] = $path;
        }
        unset($validated['foto_sampul']);

        $usahaBaru = UmkmUsaha::create($validated);

        KategoriProduk::firstOrCreate([
            'umkm_usaha_id' => $usahaBaru->id,
            'nama_kategori' => 'Umum',
        ]);
        KategoriProduk::firstOrCreate([
            'umkm_usaha_id' => $usahaBaru->id,
            'nama_kategori' => 'Unggulan',
        ]);

        return redirect()->route('warga.umkm.kelola')
            ->with('success', 'Pendaftaran usaha ' . $usahaBaru->nama_usaha . ' berhasil dikirim dan berstatus Pending menunggu verifikasi dari Pengurus RW.');
    }
}
