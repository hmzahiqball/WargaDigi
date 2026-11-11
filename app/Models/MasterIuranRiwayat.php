<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** Satu entri riwayat nominal iuran bulanan: berlaku mulai bulan tertentu sampai entri berikutnya. */
class MasterIuranRiwayat extends Model
{
    use HasUuids;

    protected $table = 'master_iuran_riwayat';

    protected $fillable = ['master_iuran_id', 'nominal', 'berlaku_mulai_bulan', 'dibuat_oleh'];

    protected $casts = ['nominal' => 'integer', 'berlaku_mulai_bulan' => 'integer'];

    public function master()
    {
        return $this->belongsTo(MasterIuran::class, 'master_iuran_id');
    }
}