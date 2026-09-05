<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluarga';
    protected $fillable = [
        'no_kk', 'nik_kepala_keluarga', 'alamat',
        'rt_id', 'no_wa', 'status_aktivasi',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function anggota()
    {
        return $this->hasMany(Penduduk::class);
    }
}
