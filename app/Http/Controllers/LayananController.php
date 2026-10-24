<?php

namespace App\Http\Controllers;

use App\Models\LayananSurat;

class LayananController extends Controller
{
    public function index()
    {
        // Ambil layanan surat dari DB (MASTER), urut sesuai kolom urutan.
        $services = LayananSurat::orderBy('urutan')
            ->get()
            ->map(fn (LayananSurat $l) => [
                'icon' => $l->icon,
                'title' => $l->judul,
                'desc' => $l->deskripsi,
            ])
            ->all();

        return view('pages.layanan-mandiri', compact('services'));
    }
}
