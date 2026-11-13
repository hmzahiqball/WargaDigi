<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Menyimpan, mengganti, dan mengembalikan gambar yang diatur dari Pengaturan Sistem
 * (logo, logo kop, gambar login, ikon aplikasi). Berkas lama dihapus saat diganti.
 * Ikon aplikasi juga dibuat dalam tiga ukuran PNG persegi untuk PWA dan notifikasi.
 */
class GambarPengaturan
{
    /** Varian ikon aplikasi: kunci => [sisi piksel, maskable]. */
    public const IKON = [
        '192'       => [192, false],
        '512'       => [512, false],
        'maskable'  => [512, true],
    ];

    private const FOLDER = 'settings';

    public static function simpan(string $kunci, UploadedFile $file): string
    {
        abort_unless(isset(SystemSetting::GAMBAR[$kunci]), 500, 'Kunci gambar tidak dikenal.');

        // Berkas lama dibuang setelah yang baru berhasil tersimpan.
        $lama = SystemSetting::get($kunci);
        $path = $file->store(self::FOLDER, 'public');
        SystemSetting::set($kunci, $path);

        if (! empty($lama) && is_string($lama) && $lama !== $path) {
            Storage::disk('public')->delete($lama);
        }

        if ($kunci === 'ikon_aplikasi') {
            self::buatIkon($path);
        }

        return $path;
    }

    /** Kembalikan ke gambar bawaan: hapus berkas unggahan dan turunannya. */
    public static function hapus(string $kunci): void
    {
        $lama = SystemSetting::get($kunci);
        if (! empty($lama) && is_string($lama)) {
            Storage::disk('public')->delete($lama);
        }
        SystemSetting::set($kunci, '');

        if ($kunci === 'ikon_aplikasi') {
            foreach (array_keys(self::IKON) as $varian) {
                Storage::disk('public')->delete(self::pathIkon($varian));
            }
        }
    }

    public static function pathIkon(string|int $varian): string
    {
        return self::FOLDER . '/ikon-' . $varian . '.png';
    }

    /** Path berkas ikon di disk lokal untuk varian ini bila ada ikon kustom, selain itu null. */
    public static function berkasIkon(string|int $varian): ?string
    {
        $varian = (string) $varian;
        if (! isset(self::IKON[$varian]) || ! SystemSetting::get('ikon_aplikasi')) {
            return null;
        }
        $path = self::pathIkon($varian);

        return Storage::disk('public')->exists($path) ? Storage::disk('public')->path($path) : null;
    }

    /** Penanda versi untuk URL ikon (waktu ubah berkas), agar cache browser dan perangkat ikut diperbarui. */
    public static function versiIkon(): string
    {
        $berkas = self::berkasIkon('512');

        return $berkas && is_file($berkas) ? (string) filemtime($berkas) : '0';
    }
    /** Buat ikon persegi 192, 512, dan maskable dari gambar sumber (dipotong dari tengah). */
    public static function buatIkon(string $pathSumber): void
    {
        $biner = Storage::disk('public')->get($pathSumber);
        $sumber = @imagecreatefromstring($biner);
        abort_if($sumber === false, 422, 'Gambar ikon tidak dapat diproses.');

        $lebar = imagesx($sumber);
        $tinggi = imagesy($sumber);
        $sisi = min($lebar, $tinggi);
        $potong = imagecrop($sumber, [
            'x' => (int) (($lebar - $sisi) / 2), 'y' => (int) (($tinggi - $sisi) / 2), 'width' => $sisi, 'height' => $sisi,
        ]);
        imagedestroy($sumber);
        abort_if($potong === false, 422, 'Gambar ikon tidak dapat dipotong.');

        foreach (self::IKON as $varian => [$ukuran, $maskable]) {
            $kanvas = imagecreatetruecolor($ukuran, $ukuran);
            imagealphablending($kanvas, false);
            imagesavealpha($kanvas, true);

            if ($maskable) {
                // Zona aman maskable: isi 80% bagian tengah di atas latar putih agar tidak terpotong bentuk ikon perangkat.
                imagefill($kanvas, 0, 0, imagecolorallocate($kanvas, 255, 255, 255));
                imagealphablending($kanvas, true);
                $isi = (int) round($ukuran * 0.8);
                $geser = (int) (($ukuran - $isi) / 2);
                imagecopyresampled($kanvas, $potong, $geser, $geser, 0, 0, $isi, $isi, $sisi, $sisi);
            } else {
                imagefill($kanvas, 0, 0, imagecolorallocatealpha($kanvas, 0, 0, 0, 127));
                imagecopyresampled($kanvas, $potong, 0, 0, 0, 0, $ukuran, $ukuran, $sisi, $sisi);
            }

            ob_start();
            imagepng($kanvas, null, 6);
            Storage::disk('public')->put(self::pathIkon($varian), (string) ob_get_clean());
            imagedestroy($kanvas);
        }

        imagedestroy($potong);
    }
}
