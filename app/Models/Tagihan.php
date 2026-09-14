<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tagihan';

    protected $fillable = [
        'judul',
        'deskripsi',
        'jenis',
        'nominal',
        'periode_bulan',
        'periode_tahun',
        'unit',
        'rt_id',
        'pembuat_id',
        'status',
        'tenggat',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'tenggat' => 'date',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranTagihan::class, 'tagihan_id');
    }

    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}
