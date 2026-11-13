<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Pembuat slug unik untuk berita, agenda, dan pengumuman.
 *
 * Slug unik per tabel (bukan per RT) karena URL tidak memuat RT. Bila bentrok, diberi akhiran
 * -2, -3, dan seterusnya. Slug konten terbit tidak berubah karena konten terbit tidak dapat diedit.
 */
class SlugKonten
{
    /** Kata yang tidak boleh menjadi slug karena bentrok dengan rute (mis. /warga/agenda/rsvp). */
    public const RESERVED = ['rsvp', 'create', 'edit', 'new', 'index', 'api', 'null', 'undefined'];

    public const PANJANG_MAKS = 80;

    /** Dasar slug dari judul (tanpa pengecekan keunikan). */
    public static function dasar(string $judul, string $cadangan = 'konten'): string
    {
        $slug = Str::slug(Str::limit(trim($judul), 200, ''));
        $slug = trim(substr($slug, 0, self::PANJANG_MAKS), '-');

        if ($slug === '') {
            $slug = $cadangan;
        }
        if (in_array($slug, self::RESERVED, true)) {
            $slug .= '-' . $cadangan;
        }

        return $slug;
    }

    /**
     * Slug unik pada tabel tertentu.
     *
     * @param string      $tabel      nama tabel (berita, agenda, pengumuman)
     * @param string|null $abaikanId  id baris yang sedang diedit (boleh mempertahankan slugnya sendiri)
     */
    public static function buat(string $tabel, string $judul, ?string $abaikanId = null): string
    {
        $dasar = self::dasar($judul, $tabel);
        $kandidat = $dasar;
        $n = 2;

        while (self::dipakai($tabel, $kandidat, $abaikanId)) {
            $akhiran = '-' . $n++;
            $kandidat = substr($dasar, 0, 100 - strlen($akhiran)) . $akhiran;
        }

        return $kandidat;
    }

    protected static function dipakai(string $tabel, string $slug, ?string $abaikanId): bool
    {
        return DB::table($tabel)
            ->where('slug', $slug)
            ->when($abaikanId, fn ($q) => $q->where('id', '!=', $abaikanId))
            ->exists();
    }

    /**
     * Cari satu konten lewat slug; UUID lama tetap diterima agar link lama tidak mati.
     */
    public static function cari(Builder $query, string $kunci): ?Model
    {
        $model = (clone $query)->where('slug', $kunci)->first();
        if (! $model && Str::isUuid($kunci)) {
            $model = (clone $query)->where('id', $kunci)->first();
        }

        return $model;
    }
}
