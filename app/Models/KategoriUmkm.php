<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Master kategori UMKM. Warga hanya memilih dari master; tidak bisa menambah kategori baru.
 */
class KategoriUmkm extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori_umkm';

    /** Kategori yang sementara tidak bisa dipilih untuk usaha baru. */
    public const DINONAKTIFKAN = ['koperasi'];

    protected $fillable = [
        'nama_kategori',
    ];

    public function usaha()
    {
        return $this->hasMany(UmkmUsaha::class, 'kategori_umkm_id');
    }

    /**
     * Aturan validasi kategori_umkm_id: wajib ID yang ada di master dan bukan kategori yang
     * dinonaktifkan. Kategori yang sedang dipakai usaha ini tetap diterima saat edit.
     */
    public static function aturan(?string $idSaatIni = null): array
    {
        return [
            'required',
            'uuid',
            Rule::exists('kategori_umkm', 'id')->where(function ($q) use ($idSaatIni) {
                $q->where(function ($w) use ($idSaatIni) {
                    $w->whereRaw('LOWER(TRIM(nama_kategori)) NOT IN (' . implode(',', array_fill(0, count(self::DINONAKTIFKAN), '?')) . ')', self::DINONAKTIFKAN);
                    if ($idSaatIni) {
                        $w->orWhere('id', $idSaatIni);
                    }
                });
            }),
        ];
    }

    public static function pesan(): array
    {
        return [
            'kategori_umkm_id.required' => 'Kategori usaha wajib dipilih.',
            'kategori_umkm_id.uuid'     => 'Kategori usaha tidak valid.',
            'kategori_umkm_id.exists'   => 'Kategori usaha tidak valid atau sedang tidak dapat dipilih.',
        ];
    }
}
