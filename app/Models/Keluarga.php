<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Keluarga extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'keluarga';

    /** Audit perubahan data keluarga (old → new otomatis). */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('keluarga')
            ->logOnly([
                'no_kk', 'nik_kepala_keluarga', 'nik_pemegang_akun', 'alamat',
                'alamat_domisili', 'rw', 'kelurahan', 'kecamatan', 'kabupaten',
                'provinsi', 'kode_pos', 'pemilik_rumah', 'status_warga', 'no_wa',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $e) => match ($e) {
                'created' => 'Data keluarga ditambahkan.',
                'updated' => 'Data keluarga diperbarui.',
                'deleted' => 'Data keluarga dihapus.',
                default   => "Keluarga di-{$e}.",
            });
    }

    protected $fillable = [
        'no_kk',
        'nik_kepala_keluarga',
        'nik_pemegang_akun',
        'alamat',
        'alamat_domisili',
        'rw',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'pemilik_rumah',
        'status_warga',
        'rt_id',
        'no_wa',
        'status_aktivasi',
    ];

    protected $casts = [
        'pemilik_rumah' => 'boolean',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function anggota()
    {
        return $this->hasMany(Penduduk::class, 'keluarga_id');
    }

    public function kepalaKeluarga()
    {
        return $this->hasOne(Penduduk::class, 'nik', 'nik_kepala_keluarga');
    }

    /**
     * Alamat sesuai KK dalam satu baris:
     * "<alamat>, RT 003/RW 021, Kel. X, Kec. Y, Kab. Z, Provinsi 40552".
     * Bagian yang kosong dilewati.
     */
    public function alamatLengkap(): string
    {
        $pad = fn ($v) => ($v = preg_replace('/\D/', '', (string) $v)) !== '' ? str_pad($v, 3, '0', STR_PAD_LEFT) : '';
        $rt = $pad($this->rt?->kode_rt ?? '');
        $rw = $pad($this->rw ?? '');

        $bagian = [];
        if (! empty($this->alamat)) {
            $bagian[] = $this->alamat;
        }
        if ($rt || $rw) {
            $bagian[] = 'RT ' . ($rt ?: '000') . '/RW ' . ($rw ?: '000');
        }
        if (! empty($this->kelurahan)) {
            $bagian[] = 'Kel. ' . $this->kelurahan;
        }
        if (! empty($this->kecamatan)) {
            $bagian[] = 'Kec. ' . $this->kecamatan;
        }
        if (! empty($this->kabupaten)) {
            $bagian[] = $this->kabupaten;
        }
        $prov = $this->provinsi;
        if (! empty($this->kode_pos)) {
            $prov = ($prov ? $prov . ' ' : '') . $this->kode_pos;
        }
        if (! empty($prov)) {
            $bagian[] = $prov;
        }

        return implode(', ', $bagian);
    }

    /**
     * NIK pemegang akun login keluarga. Fallback ke kepala keluarga bila belum di-set.
     */
    public function nikPemegangAkun(): ?string
    {
        return $this->nik_pemegang_akun ?: $this->nik_kepala_keluarga;
    }

    /**
     * Penduduk pemegang akun (relasi).
     */
    public function pemegangAkun()
    {
        return $this->hasOne(Penduduk::class, 'nik', 'nik_pemegang_akun');
    }
}
