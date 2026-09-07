<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriUmkm extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori_umkm';

    protected $fillable = [
        'nama_kategori',
    ];

    public function usaha()
    {
        return $this->hasMany(UmkmUsaha::class, 'kategori_umkm_id');
    }
}
