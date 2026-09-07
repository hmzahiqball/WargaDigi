<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\KategoriUmkm;
use App\Models\KategoriProduk;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KelolaUmkmController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('q');
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $userNik = Auth::user()->nik ?? null;
        $selectedUsahaId = $request->get('usaha_id') ?? session('selected_umkm_usaha_id');

        $daftarUsaha = collect();
        if ($userNik) {
            $daftarUsaha = UmkmUsaha::with(['kategori_umkm', 'user.penduduk.keluarga.rt'])
                ->where('nik', $userNik)
                ->get();
        }

        if ($daftarUsaha->isEmpty()) {
            $daftarUsaha = UmkmUsaha::with(['kategori_umkm', 'user.penduduk.keluarga.rt'])->get();
        }

        $usaha = null;
        $approvedUsaha = $daftarUsaha->where('status_verifikasi', 'Approved');

        if ($selectedUsahaId) {
            $candidate = $daftarUsaha->firstWhere('id', $selectedUsahaId);
            if ($candidate && $candidate->status_verifikasi === 'Approved') {
                $usaha = $candidate;
            } elseif ($candidate && $candidate->status_verifikasi === 'Pending' && $approvedUsaha->isEmpty()) {
                $usaha = $candidate;
            }
        }

        if (!$usaha) {
            $usaha = $approvedUsaha->first();
        }

        if (!$usaha) {
            $usaha = $daftarUsaha->first();
        }

        if ($usaha) {
            session(['selected_umkm_usaha_id' => $usaha->id]);
        }

        $query = UmkmProduk::with(['usaha.kategori_umkm','usaha.kategori_produk']);

        if ($usaha) {
            $query->where('umkm_usaha_id', $usaha->id);
        } elseif ($userNik) {
            $query->whereHas('usaha', function ($q) use ($userNik) {
                $q->where('nik', $userNik);
            });
        }

        $allUsahaProduk = (clone $query)->get();
        $jumlahProdukAktif = $allUsahaProduk->where('status_produk', 'Aktif')->count();
        $jumlahProdukTidakAktif = $allUsahaProduk->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif'])->count();
        $jumlahProdukPending = $jumlahProdukTidakAktif;
        $jumlahKategoriProduk = $allUsahaProduk->groupBy(fn($item) => $item->usaha->kategori_umkm->nama_kategori ?? 'Lainnya')->count();
        $jumlahProdukStokMenipis = $allUsahaProduk->filter(fn($item) => strtolower($item->status_stok ?? '') === 'menipis')->count();

        if ($search) {
            $words = array_filter(preg_split('/[\s,]+/', trim($search)));
            if (!empty($words)) {
                $query->where(function ($q) use ($words) {
                    foreach ($words as $word) {
                        $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                        $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term]);
                    }
                });
            }
        }

        if ($kategori && $kategori !== 'Semua' && $kategori !== 'Semua Kategori') {
            $query->whereHas('kategori_produk', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        if ($status === 'Aktif') {
            $query->where('status_produk', 'Aktif');
        } elseif ($status === 'Tidak Aktif' || $status === 'Non-Aktif') {
            $query->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif']);
        } elseif ($status === 'Pending') {
            $query->where('status_produk', 'Pending');
        } elseif ($status === 'Stok Habis' || $status === 'Habis') {
            $query->where('status_stok', 'habis');
        } elseif ($status === 'Stok Menipis' || $status === 'Menipis') {
            $query->where('status_stok', 'menipis');
        } elseif ($status === 'Stok Tersedia' || $status === 'Tersedia') {
            $query->where('status_stok', 'tersedia');
        }

        $produk = $query->latest()->paginate(8)->withQueryString();

        $daftarKategoriUmkm = KategoriUmkm::all();
        $kategoriProdukList = $usaha ? KategoriProduk::where('umkm_usaha_id', $usaha->id)->get() : collect();
        if ($kategoriProdukList->isEmpty() && $usaha) {
            $kategoriProdukList = collect([
                KategoriProduk::firstOrCreate(['umkm_usaha_id' => $usaha->id, 'nama_kategori' => 'Umum']),
                KategoriProduk::firstOrCreate(['umkm_usaha_id' => $usaha->id, 'nama_kategori' => 'Unggulan']),
            ]);
        }

        return view('warga.umkm.kelola_umkm', compact(
            'usaha',
            'daftarUsaha',
            'produk',
            'search',
            'kategori',
            'status',
            'jumlahProdukAktif',
            'jumlahProdukTidakAktif',
            'jumlahProdukPending',
            'jumlahKategoriProduk',
            'jumlahProdukStokMenipis',
            'daftarKategoriUmkm',
            'kategoriProdukList'
        ));
    }

    public function updateUsaha(Request $request, $id)
    {
        $usaha = UmkmUsaha::findOrFail($id);

        $userNik = Auth::user()->nik ?? null;
        if (!$userNik || $usaha->nik !== $userNik) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah data profil usaha ini.');
        }

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
        ]);

        $validated['nama_usaha'] = strip_tags($validated['nama_usaha']);
        $validated['alamat_usaha'] = strip_tags($validated['alamat_usaha']);
        if (!empty($validated['deskripsi'])) {
            $validated['deskripsi'] = strip_tags($validated['deskripsi']);
        }

        if ($kategoriId) {
            $validated['kategori_umkm_id'] = $kategoriId;
        }

        $cleanWa = preg_replace('/[^0-9]/', '', $validated['no_wa']);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
            $cleanWa = '62' . $cleanWa;
        }
        $validated['no_wa'] = $cleanWa;

        $usaha->update($validated);

        if (!empty($cleanWa)) {
            foreach ($usaha->produk as $prod) {
                $prod->update([
                    'link_wa' => 'https://wa.me/' . $cleanWa . '?text=' . urlencode('Halo, saya tertarik dengan produk ' . $prod->nama_produk),
                ]);
            }
        }

        return redirect()->route('warga.umkm.kelola', ['usaha_id' => $usaha->id])
            ->with('success', 'Data profil UMKM berhasil diperbarui.');
    }

    public function updateSampulUsaha(Request $request, $id)
    {
        $usaha = UmkmUsaha::findOrFail($id);

        $userNik = Auth::user()->nik ?? null;
        if (!$userNik || $usaha->nik !== $userNik) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah foto sampul usaha ini.');
        }

        $request->validate([
            'foto_sampul' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        if ($request->hasFile('foto_sampul')) {
            $file = $request->file('foto_sampul');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('umkm-photos', $fileName, 'public');
            $usaha->update(['foto_usaha' => $path]);
        }

        return redirect()->route('warga.umkm.kelola', ['usaha_id' => $usaha->id])
            ->with('success', 'Foto sampul UMKM berhasil diperbarui.');
    }

    public function storeProduk(Request $request)
    {
        $userNik = Auth::user()->nik ?? null;
        if (!$userNik) {
            return back()->with('error', 'Silakan login terlebih dahulu untuk menambah produk.');
        }

        $selectedUsahaId = $request->get('umkm_usaha_id') ?? session('selected_umkm_usaha_id');
        $umkm = null;
        if ($selectedUsahaId) {
            $umkm = UmkmUsaha::where('id', $selectedUsahaId)->where('nik', $userNik)->first();
        }
        if (!$umkm) {
            $umkm = UmkmUsaha::where('nik', $userNik)->first();
        }

        if (!$umkm) {
            return back()->with('error', 'Silakan daftarkan usaha terlebih dahulu sebelum menambah produk.');
        }

        if ($umkm->nik !== $userNik) {
            abort(403, 'Anda tidak memiliki izin untuk menambahkan produk pada usaha ini.');
        }

        if ($umkm->status_verifikasi === 'Pending') {
            return back()->with('error', 'Usaha ' . $umkm->nama_usaha . ' masih dalam status Pending. Produk baru dapat ditambahkan setelah usaha disetujui oleh Pengurus RW.');
        }

        $kategoriProdukId = $request->get('kategori_produk_id');
        if (!$kategoriProdukId || !KategoriProduk::where('id', $kategoriProdukId)->exists()) {
            $katDefault = KategoriProduk::firstOrCreate([
                'umkm_usaha_id' => $umkm->id,
                'nama_kategori' => 'Umum',
            ]);
            $kategoriProdukId = $katDefault->id;
        }

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'status_stok' => 'nullable|string',
            'stok' => 'nullable',
            'deskripsi' => 'nullable|string',
            'link_wa' => 'nullable|string',
            'foto_produk' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $validated['nama_produk'] = strip_tags($validated['nama_produk']);
        if (!empty($validated['deskripsi'])) {
            $validated['deskripsi'] = strip_tags($validated['deskripsi']);
        }

        $validated['kategori_produk_id'] = $kategoriProdukId;
        $validated['umkm_usaha_id'] = $umkm->id;
        $validated['status_produk'] = $request->get('status_produk', 'Aktif');

        if (empty($validated['status_stok'])) {
            if ($request->filled('stok') && is_numeric($request->stok)) {
                $val = (int)$request->stok;
                $validated['status_stok'] = $val <= 0 ? 'habis' : ($val <= 5 ? 'menipis' : 'tersedia');
            } else {
                $validated['status_stok'] = 'tersedia';
            }
        } else {
            $validated['status_stok'] = strtolower(strip_tags($validated['status_stok']));
        }
        unset($validated['stok']);

        if (empty($validated['link_wa']) && !empty($umkm->no_wa)) {
            $validated['link_wa'] = 'https://wa.me/' . $umkm->no_wa . '?text=' . urlencode('Halo, saya tertarik dengan produk ' . $validated['nama_produk']);
        }

        if ($request->hasFile('foto_produk')) {
            $file = $request->file('foto_produk');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('umkm-photos', $fileName, 'public');
            $validated['foto_produk'] = $path;
        }

        UmkmProduk::create($validated);

        return redirect()->route('warga.umkm.kelola', ['usaha_id' => $umkm->id])
            ->with('success', 'Produk UMKM "' . $validated['nama_produk'] . '" berhasil ditambahkan.');
    }

    public function updateProduk(Request $request, $id)
    {
        $produk = UmkmProduk::with('usaha')->findOrFail($id);

        $userNik = Auth::user()->nik ?? null;
        if (!$userNik || !$produk->usaha || $produk->usaha->nik !== $userNik) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah produk ini.');
        }

        $kategoriProdukId = $request->get('kategori_produk_id');
        if ($kategoriProdukId && !KategoriProduk::where('id', $kategoriProdukId)->exists() && $produk->umkm_usaha_id) {
            $katDefault = KategoriProduk::firstOrCreate([
                'umkm_usaha_id' => $produk->umkm_usaha_id,
                'nama_kategori' => $kategoriProdukId,
            ]);
            $request->merge(['kategori_produk_id' => $katDefault->id]);
        }

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'kategori_produk_id' => 'nullable|exists:kategori_produk,id',
            'harga' => 'required|numeric|min:0',
            'status_stok' => 'nullable|string',
            'status_produk' => 'nullable|string|in:Aktif,Tidak Aktif,Non-Aktif',
            'stok' => 'nullable',
            'deskripsi' => 'nullable|string',
            'no_wa' => 'nullable|string',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ]);

        $validated['nama_produk'] = strip_tags($validated['nama_produk']);
        if (!empty($validated['deskripsi'])) {
            $validated['deskripsi'] = strip_tags($validated['deskripsi']);
        }

        if (isset($validated['status_produk']) && $validated['status_produk'] === 'Non-Aktif') {
            $validated['status_produk'] = 'Tidak Aktif';
        }

        if (empty($validated['status_stok'])) {
            if ($request->filled('stok') && is_numeric($request->stok)) {
                $val = (int)$request->stok;
                $validated['status_stok'] = $val <= 0 ? 'habis' : ($val <= 5 ? 'menipis' : 'tersedia');
            }
        } else {
            $validated['status_stok'] = strtolower(strip_tags($validated['status_stok']));
        }
        unset($validated['stok']);

        if ($request->filled('no_wa')) {
            $cleanWa = preg_replace('/[^0-9]/', '', $request->no_wa);
            if (str_starts_with($cleanWa, '0')) {
                $cleanWa = '62' . substr($cleanWa, 1);
            } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
                $cleanWa = '62' . $cleanWa;
            }
            if ($cleanWa) {
                $validated['link_wa'] = 'https://wa.me/' . $cleanWa . '?text=' . urlencode('Halo, saya tertarik dengan produk ' . $validated['nama_produk']);
            }
        } elseif ($produk->usaha && !empty($produk->usaha->no_wa)) {
            $validated['link_wa'] = 'https://wa.me/' . $produk->usaha->no_wa . '?text=' . urlencode('Halo, saya tertarik dengan produk ' . $validated['nama_produk']);
        }
        unset($validated['no_wa']);

        if (empty($validated['kategori_produk_id'])) {
            unset($validated['kategori_produk_id']);
        }

        if ($request->hasFile('foto_produk')) {
            $file = $request->file('foto_produk');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('umkm-photos', $fileName, 'public');
            $validated['foto_produk'] = $path;
        }

        $produk->update($validated);

        return back()->with('success', 'Produk UMKM "' . $produk->nama_produk . '" berhasil diperbarui.');
    }

    public function destroyProduk($id)
    {
        $produk = UmkmProduk::with('usaha')->findOrFail($id);

        $userNik = Auth::user()->nik ?? null;
        if (!$userNik || !$produk->usaha || $produk->usaha->nik !== $userNik) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus produk ini.');
        }

        $namaProduk = $produk->nama_produk;
        $produk->delete();

        return back()->with('success', 'Produk "' . $namaProduk . '" berhasil dihapus.');
    }

    public function kelolaProduk(Request $request)
    {
        $search = $request->get('q');
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $userNik = Auth::user()->nik ?? null;
        $selectedUsahaId = $request->get('usaha_id') ?? session('selected_umkm_usaha_id');

        $daftarUsaha = collect();
        if ($userNik) {
            $daftarUsaha = UmkmUsaha::with(['kategori_umkm', 'user.penduduk.keluarga.rt'])
                ->where('nik', $userNik)
                ->get();
        }

        if ($daftarUsaha->isEmpty()) {
            $daftarUsaha = UmkmUsaha::with(['kategori_umkm', 'user.penduduk.keluarga.rt'])->get();
        }

        $usaha = null;
        if ($selectedUsahaId) {
            $usaha = $daftarUsaha->firstWhere('id', $selectedUsahaId);
        }
        if (!$usaha) {
            $usaha = $daftarUsaha->first();
        }

        if ($usaha) {
            session(['selected_umkm_usaha_id' => $usaha->id]);
        }

        $query = UmkmProduk::with(['usaha.kategori_produk', 'usaha.kategori_umkm']);

        if ($usaha) {
            $query->where('umkm_usaha_id', $usaha->id);
        } elseif ($userNik) {
            $query->whereHas('usaha', function ($q) use ($userNik) {
                $q->where('nik', $userNik);
            });
        }

        if ($search) {
            $words = array_filter(preg_split('/[\s,]+/', trim($search)));
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

        if ($kategori && $kategori !== 'Semua' && $kategori !== 'Semua Kategori') {
            $query->whereHas('kategori_produk', function ($q) use ($kategori) {
                $q->where('nama_kategori', $kategori);
            });
        }

        if ($status === 'Aktif') {
            $query->where('status_produk', 'Aktif');
        } elseif ($status === 'Tidak Aktif' || $status === 'Non-Aktif') {
            $query->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif']);
        } elseif ($status === 'Pending') {
            $query->where('status_produk', 'Pending');
        } elseif ($status === 'Stok Habis' || $status === 'Habis') {
            $query->where('status_stok', 'habis');
        } elseif ($status === 'Stok Menipis' || $status === 'Menipis') {
            $query->where('status_stok', 'menipis');
        } elseif ($status === 'Stok Tersedia' || $status === 'Tersedia') {
            $query->where('status_stok', 'tersedia');
        }

        $produk = $query->latest()->paginate(8)->withQueryString();

        return view('warga.umkm.kelola_produk', compact(
            'produk', 
            'search', 
            'kategori', 
            'status', 
            'usaha', 
            'daftarUsaha'
        ));
    }

    public function createProduk()
    {
        return redirect()->route('warga.umkm.kelola');
    }
}
