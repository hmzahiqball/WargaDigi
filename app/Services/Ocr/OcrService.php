<?php

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrProvider;

/**
 * Fasad OCR: memilih provider sesuai config lalu membaca & mem-parse dokumen
 * KK/KTP menjadi field terstruktur. Selalu fail-safe (terbaca:false bila gagal).
 */
class OcrService
{
    public function provider(): OcrProvider
    {
        return match (config('ocr.provider')) {
            'ocrspace' => new OcrSpaceProvider(),
            default    => new NullOcrProvider(),
        };
    }

    /**
     * Baca Kartu Keluarga.
     *
     * @return array{terbaca: bool, data: array, raw: ?string}
     */
    public function bacaKk(string $absolutePath, string $mime): array
    {
        $teks = $this->provider()->readText($absolutePath, $mime);
        $data = KkParser::parse($teks);

        if (config('ocr.debug')) {
            \Illuminate\Support\Facades\Log::info("OCR KK RAW:\n" . $teks);
            \Illuminate\Support\Facades\Log::info('OCR KK PARSED: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        // Enrich dengan referensi wilayah dari No. KK (akurat: prov/kab/kec).
        // Teks mentah dikirim sebagai bahan prediksi kelurahan.
        $data = $this->enrichWilayah($data, $data['no_kk'] ?? null, $data['kelurahan'] ?? null, $teks);

        $terbaca = ! empty($teks) && (! empty($data['no_kk']) || ! empty($data['rt']) || ! empty($data['provinsi']));

        return ['terbaca' => $terbaca, 'data' => $data, 'raw' => $teks];
    }

    /**
     * Lengkapi data wilayah dari NIK/No.KK via referensi resmi:
     * - provinsi/kabupaten/kecamatan di-override dari kode (akurat).
     * - sediakan daftar kelurahan + tebakan kelurahan (fuzzy dari teks OCR).
     *
     * @param array $data           data hasil parser
     * @param string|null $nikAtauKk sumber kode (No.KK untuk KK, NIK untuk KTP)
     * @param string|null $teksKelurahanOcr teks kelurahan hasil OCR (opsional)
     */
    protected function enrichWilayah(array $data, ?string $nikAtauKk, ?string $teksKelurahanOcr = null, ?string $teksMentah = null): array
    {
        if (empty($nikAtauKk)) {
            return $data;
        }

        $resolver = new \App\Services\WilayahResolver();
        $w = $resolver->dariKode($nikAtauKk);

        if (! empty($w['kecamatan'])) {
            // Field resmi dari kode — lebih dipercaya daripada OCR.
            $data['provinsi']  = $w['provinsi'];
            $data['kabupaten'] = $w['kabupaten'];
            $data['kecamatan'] = $w['kecamatan'];
            $data['kode_provinsi']  = $w['kode_provinsi'];
            $data['kode_kabupaten'] = $w['kode_kabupaten'];
            $data['kode_kecamatan'] = $w['kode_kecamatan'];

            // Prediksi kelurahan dari teks OCR; bila tak yakin → kosong (pilih manual).
            $kel = $resolver->prediksiKelurahan($teksKelurahanOcr, $w['kode_kecamatan'], $teksMentah);
            $data['kelurahan'] = $kel['nama'] ?? null;
            $data['kode_kelurahan'] = $kel['kode'] ?? null;
        }

        return $data;
    }

    /**
     * Baca KTP.
     *
     * @return array{terbaca: bool, data: array, raw: ?string}
     */
    public function bacaKtp(string $absolutePath, string $mime): array
    {
        $teks = $this->provider()->readText($absolutePath, $mime);
        if (config('ocr.debug')) {
            \Illuminate\Support\Facades\Log::info("OCR KTP RAW:\n" . $teks);
        }
        $data = KtpParser::parse($teks);

        // Verifikasi/isi wilayah dari NIK (prov/kab/kec akurat dari kode)
        // + sediakan daftar kelurahan kecamatan untuk pengisian alamat.
        if (! empty($data['nik'])) {
            $resolver = new \App\Services\WilayahResolver();
            $w = $resolver->dariKode($data['nik']);
            if (! empty($w['kecamatan'])) {
                $data['provinsi']       = $w['provinsi'];
                $data['kabupaten']      = $w['kabupaten'];
                $data['kecamatan']      = $w['kecamatan'];
                $data['kode_provinsi']  = $w['kode_provinsi'];
                $data['kode_kabupaten'] = $w['kode_kabupaten'];
                $data['kode_kecamatan'] = $w['kode_kecamatan'];
            }
        }

        if (config('ocr.debug')) {
            \Illuminate\Support\Facades\Log::info('OCR KTP PARSED: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
        }

        $terbaca = ! empty($teks) && (! empty($data['nik']) || ! empty($data['nama_lengkap']));

        return ['terbaca' => $terbaca, 'data' => $data, 'raw' => $teks];
    }
}
