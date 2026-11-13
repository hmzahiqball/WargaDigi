<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Konversi gambar unggahan (JPG/PNG/WebP) ke WebP menggunakan GD.
 *
 * - Orientasi EXIF dari foto HP diterapkan sebelum EXIF terbuang.
 * - Gambar lebih lebar dari profil diperkecil (proporsional), tidak pernah diperbesar.
 * - Transparansi PNG dipertahankan; metadata (termasuk lokasi GPS) ikut terbuang.
 * - Fail-safe: bila GD tanpa WebP, file rusak/tidak didukung (GIF, PDF, WebP animasi),
 *   gambar terlalu besar untuk diproses, atau hasilnya justru lebih besar tanpa diperkecil,
 *   file asli yang disimpan. Penyimpanan tidak pernah gagal karena konversi.
 */
class KonversiGambarWebp
{
    /** Foto konten (berita, agenda, galeri, UMKM). */
    public const KONTEN = ['lebar' => 1920, 'kualitas' => 80];

    /** Dokumen identitas (KTP, KK): tulisan kecil, perlu resolusi lebih tinggi. */
    public const DOKUMEN = ['lebar' => 2400, 'kualitas' => 85];

    /** Batas piksel agar memori GD aman (sekitar 50 MP). */
    private const MAKS_PIKSEL = 50_000_000;

    private const MIME_DIDUKUNG = ['image/jpeg', 'image/png', 'image/webp'];

    public static function didukung(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromjpeg');
    }

    /**
     * Hasil WebP (biner) untuk file gambar, atau null bila sebaiknya file asli yang dipakai.
     *
     * @param  array{lebar:int, kualitas:int}  $profil
     */
    public static function encode(string $pathAbsolut, array $profil): ?string
    {
        if (! self::didukung() || ! is_file($pathAbsolut)) {
            return null;
        }

        $info = @getimagesize($pathAbsolut);
        if (! $info || ! in_array($info['mime'] ?? '', self::MIME_DIDUKUNG, true)) {
            return null;
        }
        if ($info[0] * $info[1] > self::MAKS_PIKSEL) {
            return null;
        }

        $img = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($pathAbsolut),
            'image/png'  => @imagecreatefrompng($pathAbsolut),
            'image/webp' => @imagecreatefromwebp($pathAbsolut),
        };
        if (! $img) {
            return null;
        }

        $diubah = false;
        if ($info['mime'] === 'image/jpeg') {
            [$img, $diputar] = self::terapkanOrientasi($img, $pathAbsolut);
            $diubah = $diputar;
        }

        if (! imageistruecolor($img)) {
            imagepalettetotruecolor($img);
        }

        $lebar = imagesx($img);
        $tinggi = imagesy($img);
        if ($lebar > $profil['lebar']) {
            $lebarBaru = $profil['lebar'];
            $tinggiBaru = max(1, (int) round($tinggi * $lebarBaru / $lebar));
            $kecil = imagecreatetruecolor($lebarBaru, $tinggiBaru);
            imagealphablending($kecil, false);
            imagesavealpha($kecil, true);
            imagefill($kecil, 0, 0, imagecolorallocatealpha($kecil, 0, 0, 0, 127));
            imagecopyresampled($kecil, $img, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebar, $tinggi);
            imagedestroy($img);
            $img = $kecil;
            $diubah = true;
        }

        imagealphablending($img, false);
        imagesavealpha($img, true);

        ob_start();
        $ok = imagewebp($img, null, $profil['kualitas']);
        $biner = (string) ob_get_clean();
        imagedestroy($img);

        if (! $ok || $biner === '') {
            return null;
        }

        // Tanpa perubahan dimensi/orientasi, pakai yang lebih kecil (PNG grafik kecil kadang membesar).
        if (! $diubah && strlen($biner) >= filesize($pathAbsolut)) {
            return null;
        }

        return $biner;
    }

    /**
     * Simpan unggahan ke disk Storage. Mengembalikan path relatif terhadap disk.
     */
    public static function simpanKeDisk(UploadedFile $file, string $folder, string $disk, array $profil): string
    {
        $biner = self::encode($file->getRealPath(), $profil);
        if ($biner === null) {
            return $file->store($folder, $disk);
        }

        $path = trim($folder, '/') . '/' . Str::random(40) . '.webp';
        Storage::disk($disk)->put($path, $biner);

        return $path;
    }

    /**
     * Simpan unggahan ke folder di bawah public/ (galeri, banner agenda).
     * Mengembalikan path relatif terhadap public, mis. "assets/images/galeri/galeri_123_abcde.webp".
     */
    public static function simpanKePublik(UploadedFile $file, string $folderPublik, string $prefix, array $profil): string
    {
        $folderPublik = trim($folderPublik, '/');
        $dasar = $prefix . '_' . time() . '_' . Str::random(5);
        $biner = self::encode($file->getRealPath(), $profil);

        if ($biner === null) {
            $nama = $dasar . '.' . strtolower($file->getClientOriginalExtension() ?: $file->extension());
            $file->move(public_path($folderPublik), $nama);
        } else {
            $nama = $dasar . '.webp';
            if (! is_dir(public_path($folderPublik))) {
                mkdir(public_path($folderPublik), 0755, true);
            }
            file_put_contents(public_path($folderPublik . '/' . $nama), $biner);
        }

        return $folderPublik . '/' . $nama;
    }

    /**
     * Salin berkas antar-disk sambil dikonversi bila gambar (PDF disalin apa adanya).
     * Berkas sumber tidak dihapus; pemanggil yang menentukan kapan sumber dibuang.
     */
    public static function salinBerkas(string $diskAsal, string $pathAsal, string $diskTujuan, string $folderTujuan, array $profil): string
    {
        $absolut = Storage::disk($diskAsal)->path($pathAsal);
        $biner = self::encode($absolut, $profil);
        $ekstensi = $biner === null ? strtolower(pathinfo($pathAsal, PATHINFO_EXTENSION)) : 'webp';
        $tujuan = trim($folderTujuan, '/') . '/' . Str::random(40) . '.' . $ekstensi;

        if ($biner === null) {
            $stream = Storage::disk($diskAsal)->readStream($pathAsal);
            Storage::disk($diskTujuan)->writeStream($tujuan, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        } else {
            Storage::disk($diskTujuan)->put($tujuan, $biner);
        }

        return $tujuan;
    }

    /**
     * Terapkan tag Orientation EXIF agar foto HP tidak tersimpan miring setelah EXIF hilang.
     *
     * @return array{0: \GdImage, 1: bool}
     */
    private static function terapkanOrientasi(\GdImage $img, string $path): array
    {
        if (! function_exists('exif_read_data')) {
            return [$img, false];
        }
        $exif = @exif_read_data($path);
        $orientasi = (int) ($exif['Orientation'] ?? 1);

        $hasil = match ($orientasi) {
            2 => self::balik($img, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($img, 180, 0),
            4 => self::balik($img, IMG_FLIP_VERTICAL),
            5 => self::balik(imagerotate($img, -90, 0), IMG_FLIP_HORIZONTAL),
            6 => imagerotate($img, -90, 0),
            7 => self::balik(imagerotate($img, 90, 0), IMG_FLIP_HORIZONTAL),
            8 => imagerotate($img, 90, 0),
            default => null,
        };

        if (! $hasil) {
            return [$img, false];
        }
        if ($hasil !== $img) {
            imagedestroy($img);
        }

        return [$hasil, true];
    }

    private static function balik(\GdImage|false $img, int $mode): \GdImage|false
    {
        if ($img) {
            imageflip($img, $mode);
        }

        return $img;
    }
}
