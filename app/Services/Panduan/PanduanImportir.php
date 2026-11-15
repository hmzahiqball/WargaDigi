<?php

namespace App\Services\Panduan;

use App\Services\KonversiGambarWebp;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Mengimpor folder Manual Book (WargaDigi21_Buku_XX_*.docx) menjadi:
 * - JSON topik Pusat Bantuan (config panduan.sumber), dan
 * - gambar WebP di folder publik (config panduan.folder_gambar).
 * Keduanya ikut Git sehingga ikut ter-deploy; database diisi oleh PanduanRepository.
 */
class PanduanImportir
{
    public function __construct(private PenyesuaianTeksPanduan $penyesuaian)
    {
    }

    /**
     * @return array{versi:string, buku:array<string,int>, topik:int, gambar:int, ukuran_gambar:int}
     */
    public function impor(string $folder, ?string $tujuanJson = null, ?string $folderGambar = null): array
    {
        $tujuanJson ??= config('panduan.sumber');
        $folderGambar ??= config('panduan.folder_gambar');
        $urlGambar = trim(config('panduan.url_gambar', 'panduan'), '/');
        $petaBuku = config('panduan.buku', []);

        $berkas = [];
        foreach (File::glob(rtrim($folder, '\\/') . DIRECTORY_SEPARATOR . '*.docx') as $path) {
            if (preg_match('/Buku_(\d{2})/i', basename($path), $m) && isset($petaBuku[$m[1]]) && ! str_starts_with(basename($path), '~$')) {
                $berkas[$m[1]] = $path;
            }
        }
        if ($berkas === []) {
            throw new RuntimeException("Tidak ada file WargaDigi21_Buku_XX_*.docx di {$folder}");
        }
        ksort($berkas);

        File::deleteDirectory($folderGambar);
        File::ensureDirectoryExists($folderGambar);

        $profil = config('panduan.gambar', ['lebar' => 1366, 'kualitas' => 78]);
        $statistik = ['buku' => [], 'gambar' => 0, 'ukuran_gambar' => 0, 'sisa_nama' => []];
        $semua = [];
        $slugTerpakai = [];
        $urutan = 0;

        foreach ($berkas as $kode => $path) {
            $peran = $petaBuku[$kode];
            $folderBuku = 'b' . $kode;
            File::ensureDirectoryExists($folderGambar . DIRECTORY_SEPARATOR . $folderBuku);

            $simpan = function (string $biner, string $ekstensi) use ($folderGambar, $folderBuku, $urlGambar, $profil, &$statistik): ?array {
                return $this->simpanGambar($biner, $ekstensi, $folderGambar, $folderBuku, $urlGambar, $profil, $statistik);
            };

            // Salin dulu: file yang sedang dibuka di Word tetap bisa dibaca.
            $salinan = tempnam(sys_get_temp_dir(), 'pnd');
            if (! @copy($path, $salinan)) {
                throw new RuntimeException('Tidak dapat membaca ' . basename($path) . '. Tutup file di Word lalu ulangi.');
            }
            try {
                $topik = (new DocxPanduanParser(\Closure::fromCallable($simpan)))->parse($salinan);
            } finally {
                @unlink($salinan);
            }

            $jumlah = 0;
            foreach ($topik as $t) {
                if ($this->penyesuaian->buangTopik($t['judul'])) {
                    continue;
                }
                $t['judul'] = $this->penyesuaian->judul($t['judul']);
                $t['isi_html'] = $this->penyesuaian->html($t['isi_html']);
                $t['kata_kunci'] = array_map(fn ($k) => $this->penyesuaian->judul($k), $t['kata_kunci']);
                if (trim(strip_tags($t['isi_html'])) === '') {
                    continue;
                }
                $lengkap = $this->lengkapi($t, $peran, $kode, ++$urutan, $slugTerpakai);
                if (preg_match(PenyesuaianTeksPanduan::NAMA_DEMO, $lengkap['judul'] . ' ' . $lengkap['isi_teks'], $m)) {
                    $statistik['sisa_nama'][] = "{$kode} {$lengkap['nomor']} {$lengkap['judul']}: {$m[0]}";
                }
                $semua[] = $lengkap;
                $jumlah++;
            }
            $statistik['buku'][$kode . ' ' . $peran] = $jumlah;
        }

        $versi = sha1(json_encode($semua, JSON_UNESCAPED_UNICODE));
        File::ensureDirectoryExists(dirname($tujuanJson));
        File::put($tujuanJson, json_encode([
            'versi' => $versi,
            'dibuat' => now()->toIso8601String(),
            'jumlah' => count($semua),
            'topik' => $semua,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return ['versi' => $versi, 'topik' => count($semua)] + $statistik;
    }

    private function lengkapi(array $t, string $peran, string $kode, int $urutan, array &$slugTerpakai): array
    {
        $dasar = Str::limit(Str::slug(($peran === 'Semua' ? 'umum' : $peran) . ' ' . ($t['jenis'] === 'faq' ? 'faq ' : '') . $t['judul']), 140, '');
        $dasar = rtrim($dasar, '-');
        $slug = $dasar;
        for ($i = 2; isset($slugTerpakai[$slug]); $i++) {
            $slug = $dasar . '-' . $i;
        }
        $slugTerpakai[$slug] = true;

        $teks = $this->teksPolos($t['isi_html']);
        $ringkasan = $this->ringkasan($t['isi_html'], $teks, $t['jenis'] === 'faq' ? [] : $t['kata_kunci']);

        $kata = array_merge([$t['bab_judul']], $t['kata_kunci']);
        $kataKunci = Str::limit(implode(' | ', array_values(array_unique(array_filter(array_map('trim', $kata))))), 1500, '');

        return [
            'peran' => $peran,
            'buku' => $kode,
            'bab_nomor' => $t['bab_nomor'],
            'bab_judul' => $t['bab_judul'],
            'nomor' => $t['nomor'],
            'judul' => $t['judul'],
            'slug' => $slug,
            'jenis' => $t['jenis'],
            'ringkasan' => $ringkasan,
            'isi_html' => $t['isi_html'],
            'isi_teks' => $teks,
            'kata_kunci' => $kataKunci,
            'urutan' => $urutan,
        ];
    }

    private function ringkasan(string $html, string $teksPenuh, array $judulLangkah): string
    {
        // 1) Paragraf pertama di luar bagian langkah/hasil/catatan.
        $tanpaBagian = preg_replace('#<section.*?</section>#s', '', $html);
        if (preg_match('#<p>(.*?)</p>#s', $tanpaBagian, $m)) {
            $kandidat = $this->teksPolos($m[1]);
            if (mb_strlen($kandidat) >= 25) {
                return Str::limit($kandidat, 200);
            }
        }
        // 2) Topik yang langsung berisi langkah: ringkas judul-judul langkahnya.
        $langkah = array_values(array_filter(array_map(fn ($k) => trim($k, " ."), $judulLangkah)));
        if (count($langkah) >= 2) {
            return Str::limit('Langkah: ' . implode(', ', array_slice($langkah, 0, 4)) . (count($langkah) > 4 ? ', dan seterusnya.' : '.'), 200);
        }

        // 3) Awal isi tanpa label bagian.
        return Str::limit(trim(preg_replace('/^(Langkah-langkah|Hasil yang terlihat|Catatan penting)\s*/u', '', $teksPenuh)), 200);
    }

    private function teksPolos(string $html): string
    {
        $html = preg_replace('#<(br|/p|/li|/h3|/h4|/th|/td|/figcaption)>#', ' $0', $html);

        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }

    private function simpanGambar(string $biner, string $ekstensi, string $folderGambar, string $folderBuku, string $urlGambar, array $profil, array &$statistik): ?array
    {
        $info = @getimagesizefromstring($biner);
        if (! $info || $info[0] < 1) {
            return null;
        }
        [$lebar, $tinggi] = $info;
        $nama = substr(sha1($biner), 0, 16);

        $sementara = tempnam(sys_get_temp_dir(), 'pndg');
        file_put_contents($sementara, $biner);
        try {
            $webp = KonversiGambarWebp::encode($sementara, $profil);
        } finally {
            @unlink($sementara);
        }

        if ($webp !== null) {
            $relatif = $folderBuku . '/' . $nama . '.webp';
            if ($lebar > $profil['lebar']) {
                $tinggi = max(1, (int) round($tinggi * $profil['lebar'] / $lebar));
                $lebar = $profil['lebar'];
            }
            $isi = $webp;
        } else {
            $relatif = $folderBuku . '/' . $nama . '.' . ($ekstensi === 'jpeg' ? 'jpg' : ($ekstensi ?: 'png'));
            $isi = $biner;
        }

        $tujuan = $folderGambar . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relatif);
        if (! is_file($tujuan)) {
            File::put($tujuan, $isi);
            $statistik['gambar']++;
            $statistik['ukuran_gambar'] += strlen($isi);
        }

        return ['url' => $urlGambar . '/' . $relatif, 'lebar' => $lebar, 'tinggi' => $tinggi, 'potret' => $info[1] > $info[0]];
    }
}
