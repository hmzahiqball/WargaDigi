<?php

namespace App\Support;

/**
 * Warna dan ikon kartu bab Pusat Bantuan, ditentukan dari judul bab agar konsisten
 * di semua peran (surat = biru, keuangan = kuning, keluarga = ungu, dst.).
 */
class TemaPanduan
{
    /** Urutan penting: pola pertama yang cocok dipakai. */
    private const TEMA = [
        ['pola' => '/halaman publik/iu', 'nama' => 'tosca', 'ikon' => 'bi-globe2'],
        ['pola' => '/\b(mengenal|mulai|masuk|login|peran|keluar|tampilan|akun)\b/iu', 'nama' => 'hijau', 'ikon' => 'bi-person-check'],
        ['pola' => '/galeri/iu', 'nama' => 'tosca', 'ikon' => 'bi-images'],
        ['pola' => '/\bberita\b/iu', 'nama' => 'tosca', 'ikon' => 'bi-newspaper'],
        ['pola' => '/\bagenda\b/iu', 'nama' => 'tosca', 'ikon' => 'bi-calendar-event'],
        ['pola' => '/surat|\bdokumen\b/iu', 'nama' => 'biru', 'ikon' => 'bi-file-earmark-text'],
        ['pola' => '/umkm|usaha|produk/iu', 'nama' => 'merah-muda', 'ikon' => 'bi-shop'],
        ['pola' => '/rekening/iu', 'nama' => 'indigo', 'ikon' => 'bi-bank'],
        ['pola' => '/laporan|statistik|rekap/iu', 'nama' => 'oranye', 'ikon' => 'bi-bar-chart-line'],
        ['pola' => '/transaksi|\bkas\b/iu', 'nama' => 'biru', 'ikon' => 'bi-journal-text'],
        ['pola' => '/iuran|keuangan|tagihan|setoran|dana|kematian/iu', 'nama' => 'kuning', 'ikon' => 'bi-cash-coin'],
        ['pola' => '/informasi|pengumuman|konten|publik/iu', 'nama' => 'tosca', 'ikon' => 'bi-megaphone'],
        ['pola' => '/pengaturan|sistem|hak akses|log|halaman utama|tanda tangan|stempel/iu', 'nama' => 'indigo', 'ikon' => 'bi-gear'],
        ['pola' => '/keluarga|penduduk|warga|data|mutasi|kepengurusan|pejabat|rt\b/iu', 'nama' => 'ungu', 'ikon' => 'bi-people'],
        ['pola' => '/dashboard|statistik|ringkasan/iu', 'nama' => 'tosca', 'ikon' => 'bi-speedometer2'],
    ];

    /** @return array{nama:string, ikon:string} */
    public static function untuk(string $judulBab): array
    {
        foreach (self::TEMA as $t) {
            if (preg_match($t['pola'], $judulBab)) {
                return ['nama' => $t['nama'], 'ikon' => $t['ikon']];
            }
        }

        return ['nama' => 'hijau', 'ikon' => 'bi-journal-text'];
    }

    /**
     * Beri id pada setiap bagian/subjudul isi topik dan kembalikan daftar isinya.
     *
     * @return array{0:string, 1:list<array{id:string, label:string, jenis:string}>}
     */
    public static function daftarIsi(string $html): array
    {
        $daftar = [];
        $n = 0;
        $html = preg_replace_callback('#<section class="pb-bagian pb-bagian-(\w+)"><h4>(.*?)</h4>|<h3>(.*?)</h3>#s', function ($m) use (&$daftar, &$n) {
            $id = 'bagian-' . (++$n);
            if (! empty($m[3])) {
                $daftar[] = ['id' => $id, 'label' => html_entity_decode(strip_tags($m[3])), 'jenis' => 'subjudul'];

                return '<h3 id="' . $id . '">' . $m[3] . '</h3>';
            }
            $daftar[] = ['id' => $id, 'label' => html_entity_decode(strip_tags($m[2])), 'jenis' => $m[1]];

            return '<section id="' . $id . '" class="pb-bagian pb-bagian-' . $m[1] . '"><h4>' . $m[2] . '</h4>';
        }, $html);

        // Penomoran langkah memakai CSS counter: terjemahkan atribut start agar nomor berlanjut.
        $html = preg_replace_callback('#<ol class="pb-langkah" start="(\d+)">#', fn ($m) => '<ol class="pb-langkah" start="' . $m[1]
            . '" style="counter-reset: langkah ' . ((int) $m[1] - 1) . '">', $html);

        return [$html, $daftar];
    }
}
