<?php

namespace App\Http\Controllers;

use App\Services\DokumenSementara;
use App\Services\Ocr\OcrService;
use Illuminate\Http\Request;

/**
 * Endpoint AJAX untuk membaca dokumen (KK/KTP) via OCR saat mengisi form
 * Tambah Keluarga. Mengembalikan field terstruktur + path file tersimpan
 * (agar form tidak perlu upload ulang saat submit).
 */
class OcrController extends Controller
{
    public function __construct(private OcrService $ocr)
    {
    }

    public function bacaKk(Request $request)
    {
        return $this->proses($request, 'kk');
    }

    public function bacaKtp(Request $request)
    {
        return $this->proses($request, 'ktp');
    }

    /**
     * Simpan file KK (tanpa OCR server) — OCR dilakukan di browser (tesseract.js).
     */
    public function simpanKk(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $path = DokumenSementara::simpanUpload($request->file('file'), $request->user());

        return response()->json(['path' => $path, 'url' => self::urlPreview($path)]);
    }

    /**
     * Parse teks KK (header) hasil OCR browser menjadi field profil keluarga.
     */
    public function parseKk(Request $request)
    {
        $request->validate(['text' => ['required', 'string']]);

        $data = \App\Services\Ocr\KkParser::parse($request->input('text'));
        $terbaca = ! empty($data['no_kk']) || ! empty($data['rt']) || ! empty($data['provinsi']);

        return response()->json([
            'terbaca' => $terbaca,
            'data'    => $data,
            'pesan'   => $terbaca
                ? 'Data KK terbaca. Periksa & lengkapi bila perlu.'
                : 'KK tidak dapat dibaca otomatis. Silakan isi manual.',
        ]);
    }

    /**
     * Simpan file KTP (tanpa OCR server) — OCR dilakukan di browser (tesseract.js).
     * Mengembalikan path & url untuk preview dan penyimpanan saat submit.
     */
    public function simpanKtp(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'file.required' => 'File KTP wajib diunggah.',
            'file.mimes'    => 'Format harus JPG, PNG, atau PDF.',
            'file.max'      => 'Ukuran maksimal 5MB.',
        ]);

        $path = DokumenSementara::simpanUpload($request->file('file'), $request->user());

        return response()->json([
            'path' => $path,
            'url'  => self::urlPreview($path),
        ]);
    }

    /**
     * Parse teks KTP hasil OCR browser (tesseract.js) menjadi field terstruktur.
     */
    public function parseKtp(Request $request)
    {
        $request->validate(['text' => ['required', 'string']]);

        $data = \App\Services\Ocr\KtpParser::parse($request->input('text'));

        // Verifikasi/isi wilayah dari NIK (prov/kab/kec akurat dari kode).
        if (! empty($data['nik'])) {
            $resolver = new \App\Services\WilayahResolver();
            $w = $resolver->dariKode($data['nik']);
            if (! empty($w['kecamatan'])) {
                $data['provinsi']  = $w['provinsi'];
                $data['kabupaten'] = $w['kabupaten'];
                $data['kecamatan'] = $w['kecamatan'];
                $data['kode_kecamatan'] = $w['kode_kecamatan'];
                $data['kelurahan_options'] = $resolver->kelurahanDiKecamatan($w['kode_kecamatan']);
            }
        }

        $terbaca = ! empty($data['nik']) || ! empty($data['nama_lengkap']);

        return response()->json([
            'terbaca' => $terbaca,
            'data'    => $data,
            'pesan'   => $terbaca
                ? 'KTP berhasil dibaca. Periksa & lengkapi bila perlu.'
                : 'KTP tidak dapat dibaca otomatis. Silakan isi manual.',
        ]);
    }

    /**
     * Preview berkas sementara milik akun yang login (KTP/KK belum disimpan).
     * Berkas privat: akun lain mendapat 404.
     */
    public function lihatSementara(Request $request, string $nama)
    {
        $path = DokumenSementara::pathUntukNama($nama, $request->user());
        abort_unless($path, 404);

        return response()->file(DokumenSementara::absolut($path), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private static function urlPreview(string $path): string
    {
        return route('dokumen-sementara.lihat', ['nama' => basename($path)]);
    }

    private function proses(Request $request, string $jenis)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'file.required' => 'File dokumen wajib diunggah.',
            'file.mimes'    => 'Format harus JPG, PNG, atau PDF.',
            'file.max'      => 'Ukuran maksimal 5MB.',
        ]);

        // Berkas asli disimpan sementara (privat) agar OCR membaca kualitas penuh;
        // konversi WebP dan pemindahan ke folder final terjadi saat formulir disimpan.
        $path = DokumenSementara::simpanUpload($request->file('file'), $request->user());
        $absolute = DokumenSementara::absolut($path);
        $mime = $request->file('file')->getClientMimeType();

        $hasil = $jenis === 'kk'
            ? $this->ocr->bacaKk($absolute, $mime)
            : $this->ocr->bacaKtp($absolute, $mime);

        return response()->json([
            'terbaca' => $hasil['terbaca'],
            'data'    => $hasil['data'],
            'path'    => $path,
            'url'     => self::urlPreview($path),
            'pesan'   => $hasil['terbaca']
                ? 'Dokumen berhasil dibaca. Periksa & lengkapi data bila perlu.'
                : 'Dokumen tidak dapat dibaca otomatis. Silakan isi data secara manual.',
        ]);
    }
}
