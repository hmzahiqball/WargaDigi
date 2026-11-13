<?php

namespace Database\Seeders;

use App\Models\KategoriKonten;
use Illuminate\Database\Seeder;

/**
 * Seeder MASTER: kategori konten (berita, agenda, pengumuman), disamakan dengan data terbaru.
 * Idempoten (updateOrCreate per nama).
 */
class KategoriKontenSeeder extends Seeder
{
    /** [nama, warna, urutan, berita, agenda, pengumuman] */
    private const DATA = [
        ['Sosial',        'hijau',      1, true,  true, true],
        ['Infrastruktur', 'biru',       2, true,  true, true],
        ['Hiburan',       'kuning',     3, true,  true, true],
        ['Kesehatan',     'ungu',       4, true,  true, true],
        ['Keamanan',      'tosca',      5, true,  true, true],
        ['Organisasi',    'merah-muda', 6, false, true, true],
    ];

    public function run(): void
    {
        foreach (self::DATA as [$nama, $warna, $urutan, $berita, $agenda, $pengumuman]) {
            KategoriKonten::updateOrCreate(['nama' => $nama], [
                'warna' => $warna, 'urutan' => $urutan, 'aktif' => true,
                'berlaku_berita' => $berita, 'berlaku_agenda' => $agenda, 'berlaku_pengumuman' => $pengumuman,
            ]);
        }
    }
}
