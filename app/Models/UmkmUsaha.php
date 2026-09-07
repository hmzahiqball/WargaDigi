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
        'kategori',
        'deskripsi',
        'alamat_usaha',
        'no_wa',
        'foto_usaha',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    /**
     * Relasi ke Pemilik (User berdasarkan NIK)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

    /**
     * Alias relasi pemilik ke User
     */
    public function pemilik()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

    /**
     * Relasi ke daftar produk UMKM
     */
    public function produk()
    {
        return $this->hasMany(UmkmProduk::class, 'umkm_usaha_id');
    }
}
