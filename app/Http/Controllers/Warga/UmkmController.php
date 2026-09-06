<?php

namespace App\Http\Controllers\Warga;

use Illuminate\Http\Request;

/**
 * Controller UMKM Warga (Compatibility Bridge).
 * Fitur telah dipisah secara modular dan terfokus ke dalam:
 * 1. GaleriUmkmController -> Khusus Galeri, Showcase Usaha, Detail Produk, dan Pendaftaran Usaha.
 * 2. KelolaUmkmController -> Khusus Manajemen Profil Usaha, Foto Sampul, Tambah/Edit/Hapus Produk, dan Stok.
 */
class UmkmController extends KelolaUmkmController
{
    protected GaleriUmkmController $galeriController;

    public function __construct()
    {
        $this->galeriController = new GaleriUmkmController();
    }

    public function indexGaleri(Request $request)
    {
        return $this->galeriController->index($request);
    }

    public function detailUsaha($id = null, ?Request $request = null)
    {
        return $this->galeriController->detailUsaha($id, $request);
    }

    public function detailProduk($id)
    {
        return $this->galeriController->detailProduk($id);
    }

    public function createUsaha()
    {
        return $this->galeriController->createUsaha();
    }

    public function storeUsaha(Request $request)
    {
        return $this->galeriController->storeUsaha($request);
    }
}
