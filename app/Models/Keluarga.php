<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';

    protected $fillable = [
        'no_kk',
        'nik_kepala_keluarga',
        'alamat',
        'rt_id',
        'no_wa',
        'status_aktivasi',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function penduduk()
    {
        return $this->hasMany(Penduduk::class);
    }

    public function transaksiIuran()
    {
        return $this->hasMany(TransaksiIuran::class);
    }

    public function donasi()
    {
        return $this->hasMany(Donasi::class);
    }
}
