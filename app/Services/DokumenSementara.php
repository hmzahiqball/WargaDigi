<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Berkas identitas (KTP/KK) yang diunggah untuk OCR sebelum formulir disimpan.
 *
 * Alur:
 * 1. Saat dipilih/difoto, berkas asli disimpan di disk privat `local` pada
 *    `dokumen-sementara/{user_id}/`. OCR membaca berkas asli (kualitas penuh).
 * 2. Saat formulir disimpan, berkas dikonversi ke WebP (profil DOKUMEN) dan dipindah ke
 *    folder final di disk `public`. Berkas sementara dihapus setelah data tersimpan.
 * 3. Berkas sementara yang tidak pernah disimpan (formulir ditinggal, foto ulang) dihapus
 *    otomatis oleh `dokumen:bersihkan-sementara` setelah UMUR_JAM.
 *
 * Path dari formulir hanya diterima bila berada di folder milik akun yang sama.
 *
 * Instance dipakai sebagai "sesi" satu penyimpanan: selesaikan() membuang berkas sumber,
 * batalkan() membuang berkas final bila penyimpanan data gagal, sehingga tidak ada sampah.
 */
class DokumenSementara
{
    public const DISK = 'local';

    public const FOLDER = 'dokumen-sementara';

    public const UMUR_JAM = 24;

    private const POLA_NAMA = '[A-Za-z0-9]{40}\.(jpg|jpeg|png|pdf)';

    /** @var array<string, string> path sementara => path final */
    private array $difinalkan = [];

    /** @var list<string> berkas final di disk public yang dibuat sesi ini */
    private array $dibuat = [];

    public function __construct(private User $user)
    {
    }

    public static function untuk(User $user): self
    {
        return new self($user);
    }

    /** Simpan unggahan OCR ke folder sementara privat milik akun. */
    public static function simpanUpload(UploadedFile $file, User $user): string
    {
        $ekstensi = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        if ($ekstensi === 'jpe') {
            $ekstensi = 'jpg';
        }

        return $file->storeAs(self::FOLDER . '/' . $user->getKey(), Str::random(40) . '.' . $ekstensi, self::DISK);
    }

    public static function absolut(string $path): string
    {
        return Storage::disk(self::DISK)->path($path);
    }

    /** True bila path adalah berkas sementara milik akun ini dan masih ada. */
    public static function milik(?string $path, ?User $user): bool
    {
        if (! $user || ! is_string($path) || $path === '') {
            return false;
        }
        $pola = '#^' . preg_quote(self::FOLDER . '/' . $user->getKey(), '#') . '/' . self::POLA_NAMA . '$#';

        return preg_match($pola, $path) === 1 && Storage::disk(self::DISK)->exists($path);
    }

    /** Path sementara untuk nama berkas milik akun (dipakai route preview). */
    public static function pathUntukNama(string $nama, User $user): ?string
    {
        $path = self::FOLDER . '/' . $user->getKey() . '/' . $nama;

        return self::milik($path, $user) ? $path : null;
    }

    /**
     * Konversi + pindahkan berkas sementara ke folder final (disk public).
     * Path yang sama dalam satu sesi hanya diproses sekali.
     */
    public function finalisasi(?string $path, string $folderTujuan): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        if (isset($this->difinalkan[$path])) {
            return $this->difinalkan[$path];
        }
        if (! self::milik($path, $this->user)) {
            throw new \InvalidArgumentException('Berkas sementara tidak valid.');
        }

        $final = KonversiGambarWebp::salinBerkas(self::DISK, $path, 'public', $folderTujuan, KonversiGambarWebp::DOKUMEN);
        $this->difinalkan[$path] = $final;
        $this->dibuat[] = $final;

        return $final;
    }

    /** Simpan unggahan langsung (tanpa OCR) ke folder final, dikonversi, dan dicatat sesi. */
    public function simpanLangsung(UploadedFile $file, string $folderTujuan): string
    {
        $final = KonversiGambarWebp::simpanKeDisk($file, $folderTujuan, 'public', KonversiGambarWebp::DOKUMEN);
        $this->dibuat[] = $final;

        return $final;
    }

    /** Data tersimpan: buang berkas sementara yang sudah difinalkan. */
    public function selesaikan(): void
    {
        foreach (array_keys($this->difinalkan) as $sumber) {
            Storage::disk(self::DISK)->delete($sumber);
        }
        $this->difinalkan = [];
        $this->dibuat = [];
    }

    /** Penyimpanan gagal: buang berkas final yang sudah dibuat (berkas sementara tetap untuk dicoba lagi). */
    public function batalkan(): void
    {
        foreach ($this->dibuat as $final) {
            Storage::disk('public')->delete($final);
        }
        $this->difinalkan = [];
        $this->dibuat = [];
    }

    /**
     * Jalankan penyimpanan data; berkas final dibuang bila gagal, berkas sumber dibuang bila berhasil.
     *
     * @template T
     * @param  callable(): T  $simpan
     * @return T
     */
    public function jalankan(callable $simpan): mixed
    {
        try {
            $hasil = $simpan();
        } catch (\Throwable $e) {
            $this->batalkan();
            throw $e;
        }
        $this->selesaikan();

        return $hasil;
    }

    /** Hapus berkas sementara yang lebih tua dari UMUR_JAM dan folder akun yang kosong. */
    public static function bersihkanKedaluwarsa(?int $umurJam = null): int
    {
        $disk = Storage::disk(self::DISK);
        $batas = now()->subHours($umurJam ?? self::UMUR_JAM)->getTimestamp();
        $dihapus = 0;

        foreach ($disk->allFiles(self::FOLDER) as $path) {
            if ($disk->lastModified($path) < $batas) {
                $disk->delete($path);
                $dihapus++;
            }
        }
        foreach ($disk->directories(self::FOLDER) as $folder) {
            if (empty($disk->allFiles($folder))) {
                $disk->deleteDirectory($folder);
            }
        }

        return $dihapus;
    }
}
