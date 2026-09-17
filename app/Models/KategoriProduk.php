<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori_produk';

    protected $fillable = [
        'umkm_usaha_id',
        'nama_kategori',
    ];

    public function usaha()
    {
        return $this->belongsTo(UmkmUsaha::class, 'umkm_usaha_id');
    }

    public function produk()
    {
        return $this->hasMany(UmkmProduk::class, 'kategori_produk_id');
    }
}
