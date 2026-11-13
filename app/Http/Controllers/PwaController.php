<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\GambarPengaturan;
use Illuminate\Http\Request;

/**
 * Manifest PWA dan ikon aplikasi. Ikon mengikuti Pengaturan Sistem (Ikon Aplikasi); bila belum
 * diunggah dipakai ikon bawaan di public/icons.
 */
class PwaController extends Controller
{
    /** Berkas bawaan per varian di folder public. */
    private const BAWAAN = [
        '192'      => 'icons/icon-192.png',
        '512'      => 'icons/icon-512.png',
        'maskable' => 'icons/icon-maskable-512.png',
    ];

    public function ikon(Request $request, string $varian)
    {
        abort_unless(isset(self::BAWAAN[$varian]), 404);

        $berkas = GambarPengaturan::berkasIkon($varian) ?? public_path(self::BAWAAN[$varian]);
        abort_unless(is_file($berkas), 404);

        // Boleh disimpan lama karena URL membawa versi (?v=); tanpa versi tetap divalidasi ulang.
        $cache = $request->filled('v') ? 'public, max-age=31536000, immutable' : 'public, max-age=0, must-revalidate';

        return response()->file($berkas, ['Content-Type' => 'image/png', 'Cache-Control' => $cache]);
    }

    public function manifest()
    {
        $v = GambarPengaturan::versiIkon();
        $ikon = fn (string $varian, string $ukuran, string $tujuan) => [
            'src'     => route('pwa.ikon', ['varian' => $varian], false) . '?v=' . $v,
            'sizes'   => $ukuran,
            'type'    => 'image/png',
            'purpose' => $tujuan,
        ];

        return response()->json([
            'name'             => 'WargaDigi - Digital Gotong Royong',
            'short_name'       => 'WargaDigi',
            'description'      => 'Aplikasi RW digital: layanan surat, keuangan, UMKM, dan informasi warga.',
            'start_url'        => '/',
            'scope'            => '/',
            'display'          => 'standalone',
            'orientation'      => 'portrait-primary',
            'background_color' => '#ffffff',
            'theme_color'      => '#2E7D32',
            'lang'             => 'id',
            'icons'            => [
                $ikon('192', '192x192', 'any'),
                $ikon('512', '512x512', 'any'),
                $ikon('maskable', '512x512', 'maskable'),
            ],
        ], 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'public, max-age=0, must-revalidate'], JSON_UNESCAPED_SLASHES);
    }
}