<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penduduk extends Model
{
    use HasFactory;

    protected $table = 'penduduk';
    protected $fillable = [
        'keluarga_id', 'nik', 'nama_lengkap', 'jenis_kelamin', 
        'tempat_lahir', 'tanggal_lahir', 'agama', 'pekerjaan', 
        'status_hubungan_keluarga', 'status_perkawinan', 'file_kk', 'file_ktp'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}