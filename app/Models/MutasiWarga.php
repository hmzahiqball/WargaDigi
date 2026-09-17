<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MutasiWarga extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mutasi_warga';

    protected $fillable = [
        'keluarga_id',
        'penduduk_id',
        'jenis_mutasi',
        'data_pengajuan',
        'file_bukti',
        'status',
        'keterangan_tolak',
    ];

    protected function casts(): array
    {
        return [
            'data_pengajuan' => 'array',
        ];
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}
