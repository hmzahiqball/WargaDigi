<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmUsaha extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'umkm_usaha';

    protected $fillable = [
        'nik',
        'nama_usaha',
        'kategori_umkm_id',
        'deskripsi',
        'alamat_usaha',
        'no_wa',
        'foto_usaha',
        'status_verifikasi',
        'catatan_verifikasi',
        'klikWA',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'klikWA' => 'integer',
    ];

    public function kategori_umkm()
    {
        return $this->belongsTo(KategoriUmkm::class, 'kategori_umkm_id');
    }

    public function kategori_produk()
    {
        return $this->hasMany(KategoriProduk::class, 'umkm_usaha_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }


    public function pemilik()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }


    public function produk()
    {
        return $this->hasMany(UmkmProduk::class, 'umkm_usaha_id');
    }

    /**
     * Mutator untuk membersihkan dan memformat nomor WA sebelum disimpan ke database
     */
    public function setNoWaAttribute($value)
    {
        $cleanWa = preg_replace('/[^0-9]/', '', $value);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
            $cleanWa = '62' . $cleanWa;
        }
        $this->attributes['no_wa'] = $cleanWa;
    }

    /**
     * Accessor untuk mendapatkan format nomor WA (+62 xxx)
     */
    public function getNoWaFormattedAttribute()
    {
        if (empty($this->no_wa)) {
            return '-';
        }
        
        $wa = $this->no_wa;
        if (str_starts_with($wa, '62')) {
            return '+62 ' . substr($wa, 2);
        }
        return $wa;
    }
}
