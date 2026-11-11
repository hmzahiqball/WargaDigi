<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Penduduk extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'penduduk';

    /** Audit perubahan data penduduk (old → new otomatis). */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('penduduk')
            ->logOnly([
                'nama_lengkap', 'nik', 'no_wa', 'jenis_kelamin', 'tempat_lahir',
                'tanggal_lahir', 'agama', 'pekerjaan', 'pendidikan_terakhir',
                'status_hubungan_keluarga', 'status_perkawinan', 'kewarganegaraan',
                'status_warga', 'alamat', 'alamat_domisili',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $e) => match ($e) {
                'created' => 'Data penduduk ditambahkan.',
                'updated' => 'Data penduduk diperbarui.',
                'deleted' => 'Data penduduk dihapus.',
                default   => "Penduduk di-{$e}.",
            });
    }

    protected $fillable = [
        'keluarga_id',
        'nik',
        'no_wa',
        'nama_lengkap',
        'alamat',
        'alamat_domisili',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pekerjaan',
        'status_hubungan_keluarga',
        'status_perkawinan',
        'pendidikan_terakhir',
        'kewarganegaraan',
        'status_warga',
        'nama_ayah',
        'nama_ibu',
        'file_kk',
        'file_ktp',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }

    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class);
    }
}
