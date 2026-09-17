<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SuratPengajuan extends Model
{
    use HasUuids;

    protected $fillable = [
        'nik',
        'jenis_surat',
        'keperluan',
        'status',
        'file_lampiran',
        'nomor_surat'
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'nik', 'nik');
    }
}
