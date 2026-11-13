<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Rw\Concerns\MemetakanKonten;
use App\Support\KontenScope;

/**
 * Pusat Informasi: daftar konten yang sudah terbit dan tampil bagi warga (hanya dilihat).
 * Persetujuan konten ada di menu Persetujuan (PersetujuanKontenController).
 */
class PusatInformasiController extends Controller
{
    use MemetakanKonten;

    public function index()
    {
        $user = auth()->user();

        // Ketua RT memantau konten yang dilihat warga RT-nya; pimpinan RW memantau seluruh konten terbit.
        $data = $this->daftarKonten(
            fn (string $kelas) => KontenScope::untukPantau($kelas::query(), $user),
            ['Publish']
        );

        $untukRt = KontenScope::peranRt($user);

        return view('rw.pusat_informasi.index', $data + [
            'mode'        => 'publikasi',
            'basePath'    => '',
            'tingkat'     => $untukRt ? 'RT' : 'RW',
            'bolehBerita' => true,
            'keterangan'  => $untukRt
                ? 'Konten RW dan konten RT Anda yang sudah terbit dan tampil bagi warga.'
                : 'Seluruh konten (RW dan semua RT) yang sudah terbit dan tampil bagi warga.',
        ]);
    }
}