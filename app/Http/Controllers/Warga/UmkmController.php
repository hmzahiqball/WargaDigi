<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\UmkmUsaha;
use App\Models\UmkmProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    /**
     * Halaman Utama Galeri UMKM Warga
     */
    public function indexGaleri(Request $request)
    {
        $kategori = $request->get('kategori');
        $search = $request->get('q');
        $sort = $request->get('sort', 'terbaru');

        $query = UmkmProduk::with(['usaha.user.penduduk'])
            ->where('status_produk', 'Approved');

        if ($kategori && $kategori !== 'Semua') {
            $query->whereHas('usaha', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        if ($sort === 'termahal') {
            $query->orderBy('harga', 'desc');
        } elseif ($sort === 'termurah') {
            $query->orderBy('harga', 'asc');
        } else {
            $query->latest();
        }

        $produk = $query->get();

        return view('warga.umkm.index', compact('produk', 'kategori', 'search', 'sort'));
    }

    /**
     * Halaman Kelola Produk Saya untuk Warga Pemilik UMKM
     */
    public function kelolaProduk(Request $request)
    {
        $search = $request->get('q');
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $userNik = Auth::user()->nik;

        $query = UmkmProduk::whereHas('usaha', function ($q) use ($userNik) {
            $q->where('nik', $userNik);
        });

        if ($search) {
            $query->where('nama_produk', 'like', "%{$search}%");
        }

        if ($kategori) {
            $query->whereHas('usaha', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        if ($status === 'Aktif') {
            $query->where('stok', '>', 0)->where('status_produk', 'Approved');
        } elseif ($status === 'Stok Habis') {
            $query->where('stok', '<=', 0);
        } elseif ($status === 'Pending') {
            $query->where('status_produk', 'Pending');
        }

        $produk = $query->latest()->get();

        return view('warga.umkm.kelola_produk', compact('produk', 'search', 'kategori', 'status'));
    }

    public function createUsaha()
    {
        return view('warga.umkm.daftar_usaha');
    }

    public function storeUsaha(Request $request)
    {
        $validated = $request->validate([
            'nama_usaha' => 'required|string|max:100',
            'kategori' => 'required|in:Kuliner,Kriya,Jasa,Fashion,Lainnya',
            'alamat_usaha' => 'required|string',
            'no_wa' => 'required|string|max:15',
            'deskripsi' => 'nullable|string',
        ]);

        $validated['nik'] = Auth::user()->nik;
        $validated['status_verifikasi'] = 'Pending';

        UmkmUsaha::create($validated);

        return redirect()->route('warga.umkm.kelola')
            ->with('success', 'Pendaftaran usaha berhasil dikirim dan menunggu verifikasi.');
    }

    public function indexProduk()
    {
        return $this->kelolaProduk(request());
    }

    public function createProduk()
    {
        return view('warga.umkm.create_produk');
    }

    public function storeProduk(Request $request)
    {
        $validated = $request->validate([
            'umkm_usaha_id' => 'required|uuid',
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'link_wa' => 'nullable|string',
        ]);

        $validated['status_produk'] = 'Pending';

        UmkmProduk::create($validated);

        return redirect()->route('warga.umkm.kelola')
            ->with('success', 'Produk UMKM berhasil ditambahkan.');
    }

    public function editProduk($id)
    {
        $produk = UmkmProduk::findOrFail($id);
        return view('warga.umkm.edit_produk', compact('produk'));
    }

    public function updateProduk(Request $request, $id)
    {
        $produk = UmkmProduk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'link_wa' => 'nullable|string',
        ]);

        $produk->update($validated);

        return redirect()->route('warga.umkm.kelola')
            ->with('success', 'Produk UMKM berhasil diperbarui.');
    }

    public function destroyProduk($id)
    {
        $produk = UmkmProduk::findOrFail($id);
        $produk->delete();

        return redirect()->route('warga.umkm.kelola')
            ->with('success', 'Produk UMKM berhasil dihapus.');
    }

    public function showProduk($id)
    {
        $produk = UmkmProduk::with('usaha.user.penduduk')->findOrFail($id);
        return view('warga.umkm.detail_produk', compact('produk'));
    }
}
