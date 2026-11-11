<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Referensi wilayah administratif (provinsi/kabupaten/kecamatan/kelurahan).
 * Kode berformat titik, mis. '32.04.13.2001'.
 */
class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'kode';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = ['kode', 'nama'];

    /** Level berdasarkan jumlah segmen kode (1=prov,2=kab,3=kec,4=kelurahan). */
    public function getLevelAttribute(): int
    {
        return substr_count($this->kode, '.') + 1;
    }
}
