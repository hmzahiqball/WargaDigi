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
            ->where('status_produk', 'Aktif')
            ->whereHas('usaha', function ($q) {
                $q->where('status_verifikasi', 'Approved')
                  ->where('is_active', true);
            })
            ->search($search)
            ->filterCategory($kategori)
            ->sortBy($sort);

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
            })
            ->search($search)
            ->filterCategory($kategori)
            ->sortBy($sort);

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
        $userNik = Auth::check() ? Auth::user()->nik : null;

        $usaha = null;
        if ($id) {
            $usaha = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'produk', 'kategori_umkm', 'kategori_produk'])
                ->where('id', $id)
                ->where(function ($q) use ($userNik) {
                    $q->where('status_verifikasi', 'Approved')
                      ->where('is_active', true);
                    if ($userNik) {
                        $q->orWhere('nik', $userNik);
                    }
                })
                ->first();

            if (!$usaha) {
                abort(404, 'Usaha tidak ditemukan atau belum disetujui.');
            }
        } else {
            $usaha = UmkmUsaha::with(['user.penduduk.keluarga.rt', 'produk', 'kategori_umkm', 'kategori_produk'])
                ->where('status_verifikasi', 'Approved')
                ->where('is_active', true)
                ->first();
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
            $kategori = $request->get('kategori');
            $sort = $request->get('sort');

            $query->search($search)->filterCategory($kategori)->sortBy($sort);

            $produk = $query->paginate(8)->withQueryString();
        }

        $isDashboard = true;

        return view('pages.detail_usaha', compact('usaha', 'produk', 'isDashboard', 'search', 'kategori', 'sort'));
    }

    public function detailProduk($id)
    {
        $userNik = Auth::check() ? Auth::user()->nik : null;

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

        $sessionKey = 'viewed_umkm_produk_' . $produk->id;
        if (!session()->has($sessionKey)) {
            $produk->increment('jumlah_akses');
            session()->put($sessionKey, true);
        }

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

        $validated['nama_usaha'] = strip_tags($validated['nama_usaha']);
        $validated['alamat_usaha'] = strip_tags($validated['alamat_usaha']);
        if (!empty($validated['deskripsi'])) {
            $validated['deskripsi'] = strip_tags($validated['deskripsi']);
        }

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

        // no_wa otomatis diformat oleh mutator setNoWaAttribute di model UmkmUsaha
        
        $file = $request->file('foto_sampul') ?? $request->file('foto_usaha');
        if ($file) {
            $path = $file->store('umkm-photos', 'public');
            $validated['foto_usaha'] = $path;
        }
        unset($validated['foto_sampul']);

        $usahaBaru = UmkmUsaha::create($validated);

        if (Auth::check()) {
            Auth::user()->notify(new \App\Notifications\UmkmStatusNotification($usahaBaru, 'Pending'));
        }

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
