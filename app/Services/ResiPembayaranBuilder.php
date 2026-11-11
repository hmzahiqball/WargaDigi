<?php

namespace App\Services;

use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Menyusun kop dan blok penandatangan untuk resi bukti pembayaran Iuran Lainnya.
 *
 * Penerbit mengikuti siapa yang menerima uangnya:
 * - Iuran RT, atau iuran RW yang dikumpulkan lewat RT  -> Bendahara RT pemilik KK
 * - Iuran RW langsung                                   -> Bendahara RW
 * - Iuran DKM                                           -> Bendahara DKM
 */
class ResiPembayaranBuilder
{
    /**
     * @return array{kop:array, unit:string, jabatan:string, penerima:string, penandatangan:?string,
     *               ttd:?string, stempel:?string, tempat:string, tanggal:string, via_rt:bool}
     */
    public static function bangun(PembayaranTagihan $pembayaran): array
    {
        $pembayaran->loadMissing(['tagihan', 'keluarga']);
        $tagihan = $pembayaran->tagihan;
        $viaRt = $tagihan->unit === 'RW' && (bool) $tagihan->via_rt;
        $unit = $viaRt ? 'RT' : $tagihan->unit;

        $rtId = $unit === 'RT' ? ($viaRt ? $pembayaran->keluarga?->rt_id : $tagihan->rt_id) : null;
        $rt = $rtId ? MasterRt::find($rtId) : null;
        $konfig = config('surat.kop', []);
        $kodeRw = $konfig['kode_rw'] ?? '21';

        $kop = self::kop($unit, $rt, $konfig, $kodeRw);
        $jabatan = match ($unit) {
            'RT'    => 'Bendahara RT ' . ($rt->kode_rt ?? ''),
            'DKM'   => 'Bendahara DKM',
            default => 'Bendahara RW ' . $kodeRw,
        };

        $user = self::penerbit($pembayaran, $unit, $rtId);

        return [
            'kop'           => $kop,
            'unit'          => $unit,
            'jabatan'       => trim($jabatan),
            'penerima'      => trim($jabatan),
            // Nama dari data Kepengurusan (Bendahara yang menjabat saat pembayaran disahkan); TTD/stempel dari akun pengesah.
            'penandatangan' => PenandatanganDokumen::bendahara($unit, $rtId, $pembayaran->approved_at ?? $pembayaran->updated_at),
            'ttd'           => $user ? self::gambarPrivat($user->ttd_default) : null,
            'stempel'       => $user ? self::gambarPrivat($user->stempel_default) : null,
            'tempat'        => $konfig['tempat_surat'] ?? '',
            'tanggal'       => ($pembayaran->approved_at ?? $pembayaran->updated_at)->translatedFormat('d F Y'),
            'via_rt'        => $viaRt,
        ];
    }

    /** Kop sesuai penerbit resi; logo diambil dari konfigurasi kop surat. */
    private static function kop(string $unit, ?MasterRt $rt, array $konfig, string $kodeRw): array
    {
        $baris2 = $konfig['baris_2'] ?? '';
        $baris3 = $konfig['baris_3'] ?? '';

        $isi = match ($unit) {
            'RT' => [
                'baris_1' => 'RUKUN TETANGGA ' . ($rt->kode_rt ?? '') . ' / RUKUN WARGA ' . $kodeRw,
                'baris_2' => $baris2,
                'baris_3' => $baris3,
            ],
            'DKM' => [
                'baris_1' => 'DEWAN KEMAKMURAN MASJID (DKM)',
                'baris_2' => trim('RUKUN WARGA ' . $kodeRw . ' - ' . $baris2, ' -'),
                'baris_3' => $baris3,
            ],
            default => [
                'baris_1' => $konfig['baris_1'] ?? 'RUKUN WARGA ' . $kodeRw,
                'baris_2' => $baris2,
                'baris_3' => $baris3,
            ],
        };

        return $isi + ['logo' => $konfig['logo'] ?? 'images/logo_rw21.png'];
    }

    /**
     * Bendahara yang mengesahkan pembayaran. Bila data lama tidak mencatat pengesahnya,
     * dipakai Operator Keuangan aktif pada unit/RT yang sama.
     */
    private static function penerbit(PembayaranTagihan $pembayaran, string $unit, ?string $rtId): ?User
    {
        if ($pembayaran->approved_by && ($user = User::find($pembayaran->approved_by))) {
            return $user;
        }

        return match ($unit) {
            'RT'    => NotificationService::opKeuangan('RT', $rtId)->first(),
            'RW'    => NotificationService::opKeuangan('RW')->first(),
            default => null,
        };
    }

    /** Ubah gambar TTD/stempel privat menjadi data URI agar tampil stabil di PDF. */
    private static function gambarPrivat(?string $path): ?string
    {
        if (! $path || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode(Storage::disk('local')->get($path));
    }
}
