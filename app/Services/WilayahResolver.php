<?php

namespace App\Services;

use App\Models\Wilayah;

/**
 * Menurunkan wilayah administratif dari NIK/No.KK dan membantu pemilihan
 * kelurahan.
 *
 * 6 digit awal NIK/No.KK = kode wilayah (PP + KK + CC): provinsi, kabupaten/
 * kota, kecamatan. Kelurahan TIDAK ada di NIK, sehingga disediakan daftar
 * kelurahan dalam kecamatan + fuzzy match terhadap teks OCR.
 */
class WilayahResolver
{
    /**
     * Derive provinsi, kabupaten, kecamatan dari 16/6 digit awal NIK/No.KK.
     *
     * @return array{
     *   provinsi: ?string, kabupaten: ?string, kecamatan: ?string,
     *   kode_provinsi: ?string, kode_kabupaten: ?string, kode_kecamatan: ?string
     * }
     */
    public function dariKode(string $nikAtauKk): array
    {
        $kosong = [
            'provinsi' => null, 'kabupaten' => null, 'kecamatan' => null,
            'kode_provinsi' => null, 'kode_kabupaten' => null, 'kode_kecamatan' => null,
        ];

        $digits = preg_replace('/\D/', '', $nikAtauKk);
        if (strlen($digits) < 6) {
            return $kosong;
        }

        $pp = substr($digits, 0, 2);
        $kk = substr($digits, 2, 2);
        $cc = substr($digits, 4, 2);

        $kodeProv = $pp;
        $kodeKab  = "{$pp}.{$kk}";
        $kodeKec  = "{$pp}.{$kk}.{$cc}";

        $map = Wilayah::whereIn('kode', [$kodeProv, $kodeKab, $kodeKec])
            ->pluck('nama', 'kode');

        if (! $map->has($kodeKec)) {
            return $kosong; // kode tidak valid → biar diisi manual
        }

        return [
            'provinsi'       => $map->get($kodeProv),
            'kabupaten'      => $map->get($kodeKab),
            'kecamatan'      => $map->get($kodeKec),
            'kode_provinsi'  => $map->has($kodeProv) ? $kodeProv : null,
            'kode_kabupaten' => $map->has($kodeKab) ? $kodeKab : null,
            'kode_kecamatan' => $kodeKec,
        ];
    }

    /**
     * Daftar kelurahan/desa dalam sebuah kecamatan.
     *
     * @return array<int,array{kode:string,nama:string}>
     */
    public function kelurahanDiKecamatan(?string $kodeKecamatan): array
    {
        if (empty($kodeKecamatan)) {
            return [];
        }

        return Wilayah::where('kode', 'like', $kodeKecamatan . '.%')
            // Hanya level kelurahan (4 segmen): kode kecamatan + '.NNNN'
            ->get(['kode', 'nama'])
            ->filter(fn ($w) => substr_count($w->kode, '.') === 3)
            ->map(fn ($w) => ['kode' => $w->kode, 'nama' => $w->nama])
            ->values()
            ->all();
    }

    /**
     * Prediksi kelurahan dari teks OCR terhadap daftar kelurahan kecamatan.
     *
     * Mencoba dua strategi:
     *  1) Nama kelurahan muncul (persis/mirip) sebagai kata di dalam teks OCR.
     *  2) Fuzzy match keseluruhan terhadap teks kandidat kelurahan.
     * Mengembalikan nama kelurahan bila cukup yakin; bila tidak, null (dikosongkan
     * agar admin memilih sendiri dari dropdown).
     *
     * @param string|null $teksKelurahanOcr teks field kelurahan dari parser (bila ada)
     * @param string|null $kodeKecamatan
     * @param string|null $teksMentahOcr seluruh teks OCR sebagai bahan cadangan
     */
    public function cocokkanKelurahan(?string $teksKelurahanOcr, ?string $kodeKecamatan, ?string $teksMentahOcr = null): ?string
    {
        if (empty($kodeKecamatan)) {
            return null;
        }

        $daftar = $this->kelurahanDiKecamatan($kodeKecamatan);
        if (empty($daftar)) {
            return null;
        }

        $normalisasi = fn ($s) => trim(preg_replace('/\s+/', ' ', strtoupper(preg_replace('/[^A-Za-z\s]/', ' ', (string) $s))));

        // Strategi 1: nama kelurahan muncul utuh di teks OCR mentah (paling andal).
        $mentah = ' ' . $normalisasi($teksMentahOcr) . ' ';
        if (trim($mentah) !== '') {
            foreach ($daftar as $kel) {
                $namaUp = $normalisasi($kel['nama']);
                if ($namaUp !== '' && strlen($namaUp) >= 4 && str_contains($mentah, ' ' . $namaUp . ' ')) {
                    return $kel['nama'];
                }
            }
        }

        // Strategi 2: fuzzy match terhadap teks field kelurahan dari parser.
        $target = $normalisasi($teksKelurahanOcr);
        if ($target !== '') {
            $best = null;
            $bestScore = 0.0;
            foreach ($daftar as $kel) {
                similar_text($target, $normalisasi($kel['nama']), $persen);
                if ($persen > $bestScore) {
                    $bestScore = $persen;
                    $best = $kel['nama'];
                }
            }
            // Ambang lebih longgar (prediksi), tetap cukup untuk menghindari asal pilih.
            if ($bestScore >= 60) {
                return $best;
            }
        }

        return null; // tidak yakin → kosong, admin pilih manual
    }

    /**
     * Seperti cocokkanKelurahan, tetapi mengembalikan [kode, nama] agar bisa
     * dipakai auto-select dropdown. Null bila tidak yakin.
     *
     * @return array{kode:string,nama:string}|null
     */
    public function prediksiKelurahan(?string $teksKelurahanOcr, ?string $kodeKecamatan, ?string $teksMentahOcr = null): ?array
    {
        $nama = $this->cocokkanKelurahan($teksKelurahanOcr, $kodeKecamatan, $teksMentahOcr);
        if (! $nama) {
            return null;
        }

        foreach ($this->kelurahanDiKecamatan($kodeKecamatan) as $kel) {
            if ($kel['nama'] === $nama) {
                return $kel;
            }
        }

        return null;
    }
}
