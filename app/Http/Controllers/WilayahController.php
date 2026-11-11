<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

/**
 * Endpoint data wilayah untuk dropdown bertingkat (cascading):
 * Provinsi → Kabupaten → Kecamatan → Kelurahan.
 */
class WilayahController extends Controller
{
    /**
     * Daftar wilayah anak dari sebuah kode induk.
     * - kode kosong  → daftar provinsi (level 1).
     * - kode '32'    → daftar kabupaten/kota (level 2).
     * - kode '32.04' → daftar kecamatan (level 3).
     * - kode '32.04.13' → daftar kelurahan (level 4).
     */
    public function anak(Request $request)
    {
        $kode = trim((string) $request->get('kode', ''));

        if ($kode === '') {
            // Provinsi: kode tanpa titik (2 digit).
            $items = Wilayah::whereRaw("kode NOT LIKE '%.%'")
                ->orderBy('nama')
                ->get(['kode', 'nama']);
        } else {
            $levelInduk = substr_count($kode, '.');       // 0=prov,1=kab,2=kec
            $items = Wilayah::where('kode', 'like', $kode . '.%')
                ->get(['kode', 'nama'])
                ->filter(fn ($w) => substr_count($w->kode, '.') === $levelInduk + 1)
                ->sortBy('nama')
                ->values();
        }

        return response()->json(
            $items->map(fn ($w) => ['kode' => $w->kode, 'nama' => $w->nama])->values()
        );
    }
}
