<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu topik Pusat Bantuan (sub-bab panduan atau pertanyaan FAQ) untuk satu peran.
 * Isi dibangkitkan dari hasil impor Manual Book, bukan diketik lewat aplikasi.
 */
class PanduanTopik extends Model
{
    use HasUuids;

    public const PERAN_SEMUA = 'Semua';

    protected $table = 'panduan_topik';

    protected $fillable = [
        'peran', 'buku', 'bab_nomor', 'bab_judul', 'nomor', 'judul', 'slug', 'jenis',
        'ringkasan', 'isi_html', 'isi_teks', 'kata_kunci', 'urutan', 'versi',
    ];

    /** Topik yang boleh dilihat peran tertentu: panduan perannya + panduan umum. */
    public function scopeUntukPeran(Builder $query, ?string $peran): Builder
    {
        $daftar = array_values(array_filter([self::PERAN_SEMUA, $peran]));

        return $query->whereIn('peran', $daftar);
    }

    public function isFaq(): bool
    {
        return $this->jenis === 'faq';
    }
}
