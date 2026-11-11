<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Nominal setoran per KK lunas yang berlaku mulai bulan tertentu dalam satu tahun
 * (rt_id null = default semua RT; nominal null pada baris RT = ikut default).
 */
class SetoranPengaturan extends Model
{
    use HasUuids;

    protected $table = 'setoran_pengaturan';

    protected $fillable = ['tahun', 'rt_id', 'berlaku_mulai_bulan', 'nominal_per_kk'];

    protected $casts = ['tahun' => 'integer', 'berlaku_mulai_bulan' => 'integer', 'nominal_per_kk' => 'integer'];
}