<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Memproses tanda tangan & stempel saat pengesahan surat (RT/RW), mendukung
 * dua mode: gambar/upload manual, atau memakai TTD/stempel default akun.
 *
 * TTD/stempel yang dipakai selalu disalin ke disk publik 'dokumen/...' agar
 * tersimpan pada pengajuan dan ikut snapshot (tidak berubah bila default diganti).
 */
class TandaTanganPengesahan
{
    /**
     * @param string $peran 'rt' atau 'rw'
     * @return array{ttd?:string, stempel?:string} kolom untuk di-update pada pengajuan_surat
     */
    public static function proses(Request $request, User $user, string $peran): array
    {
        $hasil = [];

        $inputTtd = "signature_{$peran}";      // base64 dari canvas
        $inputStempel = "stempel_{$peran}";     // file upload

        // --- Tanda tangan ---
        if ($request->boolean("pakai_ttd_default_{$peran}") && $user->ttd_default) {
            // Salin TTD default (disk 'local') ke disk publik untuk surat ini.
            if ($salin = self::salinDariPrivat($user->ttd_default)) {
                $hasil["ttd_{$peran}"] = $salin;
            }
        } elseif ($request->filled($inputTtd)) {
            if ($path = self::simpanBase64Publik($request->input($inputTtd))) {
                $hasil["ttd_{$peran}"] = $path;
                // Simpan sebagai default bila diminta.
                if ($request->boolean("simpan_ttd_default_{$peran}")) {
                    self::simpanDefault($user, 'ttd', $request->input($inputTtd));
                }
            }
        }

        // --- Stempel ---
        if ($request->boolean("pakai_stempel_default_{$peran}") && $user->stempel_default) {
            if ($salin = self::salinDariPrivat($user->stempel_default)) {
                $hasil["stempel_{$peran}"] = $salin;
            }
        } elseif ($request->hasFile($inputStempel)) {
            $path = $request->file($inputStempel)->store('dokumen/stempel', 'public');
            $hasil["stempel_{$peran}"] = $path;
            if ($request->boolean("simpan_stempel_default_{$peran}")) {
                self::simpanDefaultDariFile($user, 'stempel', $request->file($inputStempel)->getRealPath());
            }
        }

        return $hasil;
    }

    /** Salin file dari disk privat 'local' ke disk publik; kembalikan path publik. */
    private static function salinDariPrivat(string $pathPrivat): ?string
    {
        if (! Storage::disk('local')->exists($pathPrivat)) {
            return null;
        }
        $ext = pathinfo($pathPrivat, PATHINFO_EXTENSION) ?: 'png';
        $tujuan = 'dokumen/ttd/' . Str::uuid()->toString() . '.' . $ext;
        Storage::disk('public')->put($tujuan, Storage::disk('local')->get($pathPrivat));

        return $tujuan;
    }

    /** Simpan data URL base64 ke disk publik (dokumen/ttd). */
    private static function simpanBase64Publik(string $dataUrl): ?string
    {
        if (! preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $m)) {
            return null;
        }
        $ext = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
        if (! in_array($ext, ['png', 'jpg', 'gif'], true)) {
            return null;
        }
        $isi = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
        if ($isi === false) {
            return null;
        }
        $path = 'dokumen/ttd/' . Str::random(40) . '.' . $ext;
        Storage::disk('public')->put($path, $isi);

        return $path;
    }

    /** Simpan default (TTD) dari base64 ke disk privat 'local'. */
    private static function simpanDefault(User $user, string $jenis, string $dataUrl): void
    {
        if (! preg_match('/^data:image\/\w+;base64,/', $dataUrl)) {
            return;
        }
        $isi = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
        if ($isi === false) {
            return;
        }
        $kolom = $jenis === 'ttd' ? 'ttd_default' : 'stempel_default';
        self::hapusDefaultLama($user->{$kolom});
        $path = 'ttd/' . Str::uuid()->toString() . '.png';
        Storage::disk('local')->put($path, $isi);
        $user->forceFill([$kolom => $path])->save();
    }

    /** Simpan default (stempel) dari file ke disk privat 'local'. */
    private static function simpanDefaultDariFile(User $user, string $jenis, string $realPath): void
    {
        if (! is_file($realPath)) {
            return;
        }
        $kolom = $jenis === 'ttd' ? 'ttd_default' : 'stempel_default';
        self::hapusDefaultLama($user->{$kolom});
        $path = 'ttd/' . Str::uuid()->toString() . '.png';
        Storage::disk('local')->put($path, file_get_contents($realPath));
        $user->forceFill([$kolom => $path])->save();
    }

    private static function hapusDefaultLama(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
