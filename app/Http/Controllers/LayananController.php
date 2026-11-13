<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;

class LayananController extends Controller
{
    /**
     * Layanan Mandiri: satu kartu per jenis surat yang benar-benar dapat diajukan
     * (PengajuanSurat::TIPE_SURAT), dengan ikon dan deskripsi dari config/surat.php.
     * Tombol "Buat Pengajuan" membuka form dengan jenis terpilih; warga wajib login.
     */
    public function index()
    {
        $services = collect(PengajuanSurat::TIPE_SURAT)
            ->map(fn (string $tipe) => [
                'icon'  => config("surat.jenis.{$tipe}.ikon", 'bi-file-earmark-text'),
                'title' => $tipe,
                'desc'  => config("surat.jenis.{$tipe}.deskripsi", ''),
                'url'   => route('warga.surat.index', ['jenis' => $tipe]),
            ])
            ->all();

        return view('pages.layanan-mandiri', compact('services'));
    }
}
