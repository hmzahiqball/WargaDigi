<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory, HasUuids, \App\Models\Concerns\PunyaCakupanRt, \App\Models\Concerns\PunyaSlug, \App\Models\Concerns\PunyaKategoriKonten;

    protected $table = 'berita';

    protected $fillable = [
        'judul_berita',
        'slug',
        'kategori',
        'isi_berita',
        'featured_image',
        'status',
        'catatan_revisi',
        'operator_id',
        'rt_id',
        'kategori_id',
        'approval_id',
        'tanggal_publish',
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
    ];

    public function kolomJudulSlug(): string
    {
        return 'judul_berita';
    }

    /**
     * URL foto utama dari nilai featured_image yang formatnya beragam:
     * "https://..." (apa adanya), "/storage/berita/x.webp" (unggahan), atau "berita/x.webp".
     * Null bila kosong.
     */
    public static function urlGambar(?string $gambar): ?string
    {
        $gambar = trim((string) $gambar);
        if ($gambar === '') {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($gambar, ['http://', 'https://'])) {
            return $gambar;
        }
        $gambar = ltrim($gambar, '/');

        return asset(\Illuminate\Support\Str::startsWith($gambar, 'storage/') ? $gambar : 'storage/' . $gambar);
    }

    /** Perkiraan waktu baca (menit) dari isi berita, minimal 1 menit (sekitar 200 kata per menit). */
    public static function menitBaca(?string $isi): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags((string) $isi)) / 200));
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'approval_id');
    }
}
