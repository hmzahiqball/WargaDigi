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
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
        'foto_produk',
        'status_produk',
        'is_tersedia',
        'link_wa',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'is_tersedia' => 'boolean',
    ];

    /**
     * Relasi ke usaha UMKM
     */
    public function usaha()
    {
        return $this->belongsTo(UmkmUsaha::class, 'umkm_usaha_id');
    }
}
