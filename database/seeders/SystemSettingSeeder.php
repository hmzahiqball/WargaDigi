<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Services\HalamanUtama;
use Illuminate\Database\Seeder;

/**
 * Seeder MASTER: pengaturan sistem dari SystemSetting::defaults() ditimpa nilai
 * terbaru yang dipakai (domain, persetujuan konten).
 *
 * Halaman Utama diisi teks terbaru (banner, footer, partner) tanpa gambar unggahan.
 * Gambar (logo, logo kop, gambar login, ikon) tidak diisi: memakai bawaan aplikasi
 * sampai diunggah Admin Aplikasi (berkas unggahan tidak ikut Git).
 * Idempoten (SystemSetting::setMany memakai updateOrCreate per key).
 */
class SystemSettingSeeder extends Seeder
{
    /** Nilai terbaru yang berbeda dari bawaan. */
    private const NILAI_TERBARU = [
        'domain'                     => 'wargadigi21.com',
        'konten_approval_berita'     => true,
        'konten_approval_agenda'     => false,
        'konten_approval_pengumuman' => false,
    ];

    public function run(): void
    {
        $lewati = array_merge(array_keys(SystemSetting::GAMBAR), ['halaman_utama']);
        $pairs = [];

        foreach (SystemSetting::defaults() as $key => $meta) {
            if (in_array($key, $lewati, true)) {
                continue;
            }
            $pairs[$key] = self::NILAI_TERBARU[$key] ?? ($meta['value'] ?? null);
        }

        SystemSetting::setMany($pairs);

        HalamanUtama::simpanData(self::halamanUtama());
    }

    /**
     * Teks Halaman Utama terbaru (banner, footer, partner). Gambar banner memakai
     * gambar bawaan dan logo partner dikosongkan (partner tampil sebagai nama),
     * karena berkas unggahan tidak ikut Git. Admin Aplikasi dapat mengunggah ulang.
     */
    private static function halamanUtama(): array
    {
        $data = HalamanUtama::bawaan();
        $data['footer']['copyright'] = '© {tahun} WargaDigi 21 Desa Tanimulya. Supported by PkM POLBAN';
        $data['footer']['partner'] = [
            ['nama' => 'Politeknik Negeri Bandung', 'url' => 'https://polban.ac.id', 'logo' => null],
        ];

        return $data;
    }
}
