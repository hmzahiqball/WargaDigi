<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmProduk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'umkm_produk';

    protected $fillable = [
        'umkm_usaha_id',
        'kategori_produk_id',
        'nama_produk',
        'deskripsi',
        'harga',
        'status_stok',
        'foto_produk',
        'status_produk',
        'link_wa',
        'jumlah_akses',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'jumlah_akses' => 'integer',
    ];

    public function setStatusStokAttribute($value)
    {
        $this->attributes['status_stok'] = strtolower($value ?? 'tersedia');
    }

    public function getStatusStokFormattedAttribute()
    {
        return ucfirst($this->status_stok ?? 'tersedia');
    }

    public function getStokAttribute()
    {
        return $this->status_stok ?? 'tersedia';
    }

    /**
     * Relasi ke Kategori Produk
     */
    public function kategori_produk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    /**
     * Relasi ke usaha UMKM
     */
    public function usaha()
    {
        return $this->belongsTo(UmkmUsaha::class, 'umkm_usaha_id');
    }
}
