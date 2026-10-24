<?php

namespace Database\Seeders;

use App\Models\LayananSurat;
use Illuminate\Database\Seeder;

/**
 * Seeder MASTER (produksi): daftar layanan surat pengantar.
 * Deskripsi diambil persis dari LayananController sebelumnya.
 * Idempoten (updateOrCreate by judul).
 */
class LayananSuratSeeder extends Seeder
{
    public function run(): void
    {
        $layanan = [
            [
                'icon' => 'bi-house-door',
                'judul' => 'Pengantar Domisili',
                'deskripsi' => 'Untuk keperluan perbankan, administrasi kependudukan, atau domisili usaha.',
                'urutan' => 1,
            ],
            [
                'icon' => 'bi-person-vcard',
                'judul' => 'Pengantar KTP/KK',
                'deskripsi' => 'Pembuatan baru, perpanjangan, atau perubahan data KTP dan Kartu Keluarga.',
                'urutan' => 2,
            ],
            [
                'icon' => 'bi-shield-check',
                'judul' => 'Pengantar SKCK',
                'deskripsi' => 'Surat keterangan catatan kepolisian untuk melamar kerja atau keperluan lain.',
                'urutan' => 3,
            ],
            [
                'icon' => 'bi-people',
                'judul' => 'Izin Keramaian',
                'deskripsi' => 'Pemberitahuan acara pernikahan, khitanan, atau kegiatan yang mengundang massa.',
                'urutan' => 4,
            ],
        ];

        foreach ($layanan as $item) {
            LayananSurat::updateOrCreate(
                ['judul' => $item['judul']],
                $item
            );
        }
    }
}
