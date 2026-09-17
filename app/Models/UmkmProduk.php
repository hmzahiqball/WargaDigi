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

    /**
     * Accessor untuk harga terformat (Rp. 10.000)
     */
    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga ?? 0, 0, ',', '.');
    }

    /**
     * Accessor untuk link WA dinamis berdasarkan No WA usaha
     */
    public function getLinkWaAttribute()
    {
        $noWa = $this->usaha->no_wa ?? null;
        if (empty($noWa)) {
            return '#';
        }
        
        $pesan = urlencode('Halo, saya tertarik dengan produk ' . $this->nama_produk . ' yang ada di WargaDigi.');
        return 'https://wa.me/' . $noWa . '?text=' . $pesan;
    }

    /**
     * Scope untuk pencarian berdasar nama produk atau nama usaha
     */
    public function scopeSearch($query, $search)
    {
        if (empty($search)) return $query;
        
        $words = array_filter(preg_split('/[\s,]+/', trim($search)));
        if (empty($words)) return $query;

        return $query->where(function ($q) use ($words) {
            foreach ($words as $word) {
                $term = '%' . mb_strtolower($word, 'UTF-8') . '%';
                $q->orWhereRaw('LOWER(nama_produk) LIKE ?', [$term])
                  ->orWhereHas('usaha', function ($sq) use ($term) {
                      $sq->whereRaw('LOWER(nama_usaha) LIKE ?', [$term]);
                  });
            }
        });
    }

    /**
     * Scope untuk filter berdasar kategori
     */
    public function scopeFilterCategory($query, $kategori)
    {
        if (empty($kategori) || in_array($kategori, ['Semua', 'Semua Kategori'])) {
            return $query;
        }

        return $query->whereHas('kategori_produk', function ($q) use ($kategori) {
            $q->where('nama_kategori', $kategori);
        })->orWhereHas('usaha.kategori_umkm', function ($q) use ($kategori) {
            $q->where('nama_kategori', $kategori);
        });
    }

    /**
     * Scope untuk filter status (Aktif, Pending, dsb)
     */
    public function scopeFilterStatus($query, $status)
    {
        if (empty($status)) return $query;

        if ($status === 'Aktif') return $query->where('status_produk', 'Aktif');
        if (in_array($status, ['Tidak Aktif', 'Non-Aktif'])) return $query->whereIn('status_produk', ['Tidak Aktif', 'Non-Aktif']);
        if ($status === 'Pending') return $query->where('status_produk', 'Pending');
        if (in_array($status, ['Stok Habis', 'Habis'])) return $query->where('status_stok', 'habis');
        if (in_array($status, ['Stok Menipis', 'Menipis'])) return $query->where('status_stok', 'menipis');
        if (in_array($status, ['Stok Tersedia', 'Tersedia'])) return $query->where('status_stok', 'tersedia');

        return $query;
    }

    /**
     * Scope untuk pengurutan
     */
    public function scopeSortBy($query, $sort)
    {
        if ($sort === 'terpopuler') return $query->orderByDesc('jumlah_akses')->latest();
        if ($sort === 'termahal') return $query->orderByDesc('harga');
        if ($sort === 'termurah') return $query->orderBy('harga', 'asc');
        
        return $query->latest();
    }
}
