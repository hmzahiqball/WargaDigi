<?php

namespace App\Services\Panduan;

use App\Models\PanduanTopik;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sumber data Pusat Bantuan.
 *
 * Tabel panduan_topik diisi dari JSON hasil impor (config panduan.sumber). Bila versi JSON
 * berbeda dengan isi tabel (mis. setelah deploy manual baru), tabel dimuat ulang otomatis
 * saat Pusat Bantuan dibuka, jadi tidak perlu langkah deploy tambahan.
 */
class PanduanRepository
{
    private const CACHE_VERSI = 'panduan.versi_termuat';

    /** Variasi nama peran lama -> nama resmi. */
    private const ALIAS_PERAN = [
        'op. konten rt' => 'Op Konten RT',
        'op. konten rw' => 'Op Konten RW',
        'op. keuangan rt' => 'Op Keuangan RT',
        'op. keuangan rw' => 'Op Keuangan RW',
        'bendahara rt' => 'Op Keuangan RT',
        'bendahara rw' => 'Op Keuangan RW',
        'pimpinan' => 'Ketua RW',
        'superadmin' => 'Admin Aplikasi',
    ];

    /** Nama peran resmi yang punya buku panduan, atau null bila tidak dikenal. */
    public function normalisasiPeran(?string $peran): ?string
    {
        $peran = trim((string) $peran);
        if ($peran === '') {
            return null;
        }
        $peran = self::ALIAS_PERAN[mb_strtolower($peran)] ?? $peran;
        $resmi = array_values(array_diff(config('panduan.buku', []), [PanduanTopik::PERAN_SEMUA]));

        foreach ($resmi as $nama) {
            if (strcasecmp($nama, $peran) === 0) {
                return $nama;
            }
        }

        return null;
    }

    /** Pastikan isi tabel sama dengan JSON hasil impor. Mengembalikan true bila dimuat ulang. */
    public function sinkron(bool $paksa = false): bool
    {
        $sumber = config('panduan.sumber');
        if (! is_string($sumber) || ! is_file($sumber)) {
            return false;
        }

        // Kunci cache memakai waktu ubah file agar JSON tidak dibaca di setiap permintaan.
        $kunciFile = self::CACHE_VERSI . '.' . md5($sumber . '|' . filemtime($sumber) . '|' . filesize($sumber));
        if (! $paksa && Cache::get($kunciFile) === true) {
            return false;
        }

        $data = json_decode((string) file_get_contents($sumber), true);
        if (! is_array($data) || ! isset($data['versi'], $data['topik']) || ! is_array($data['topik'])) {
            return false;
        }
        $versi = (string) $data['versi'];

        if (! $paksa && PanduanTopik::query()->where('versi', $versi)->count() === count($data['topik'])
            && PanduanTopik::query()->where('versi', '!=', $versi)->doesntExist()) {
            Cache::forever($kunciFile, true);

            return false;
        }

        DB::transaction(function () use ($data, $versi) {
            PanduanTopik::query()->delete();
            $kini = now();
            foreach (array_chunk($data['topik'], 50) as $potongan) {
                PanduanTopik::query()->insert(array_map(fn (array $t) => [
                    'id' => (string) Str::uuid(),
                    'peran' => (string) $t['peran'],
                    'buku' => (string) $t['buku'],
                    'bab_nomor' => $t['bab_nomor'] ?? null,
                    'bab_judul' => Str::limit((string) ($t['bab_judul'] ?? ''), 150, ''),
                    'nomor' => $t['nomor'] ?? null,
                    'judul' => Str::limit((string) $t['judul'], 255, ''),
                    'slug' => (string) $t['slug'],
                    'jenis' => $t['jenis'] === 'faq' ? 'faq' : 'topik',
                    'ringkasan' => $t['ringkasan'] ?? null,
                    'isi_html' => (string) $t['isi_html'],
                    'isi_teks' => (string) ($t['isi_teks'] ?? ''),
                    'kata_kunci' => $t['kata_kunci'] ?? null,
                    'urutan' => (int) ($t['urutan'] ?? 0),
                    'versi' => $versi,
                    'created_at' => $kini,
                    'updated_at' => $kini,
                ], $potongan));
            }
        });
        Cache::forever($kunciFile, true);

        return true;
    }

    /** Semua topik untuk peran (panduan perannya lebih dulu, lalu panduan umum). */
    public function untukPeran(?string $peran, array $kolom = ['*']): Collection
    {
        return PanduanTopik::query()->untukPeran($peran)
            ->orderByRaw('CASE WHEN peran = ? THEN 1 ELSE 0 END', [PanduanTopik::PERAN_SEMUA])
            ->orderBy('urutan')
            ->get($kolom);
    }

    public function cariSlug(string $slug, ?string $peran): ?PanduanTopik
    {
        return PanduanTopik::query()->untukPeran($peran)->where('slug', $slug)->first();
    }
}
