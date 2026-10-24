<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Jabatan kepengurusan (Ketua RW / Ketua RT) beserta riwayatnya.
 *
 * Satu jabatan (dibedakan oleh kombinasi jabatan + rt_id) hanya boleh memiliki
 * SATU baris aktif. Pergantian pejabat dilakukan lewat setAktif(), yang menutup
 * periode pejabat lama dan membuat baris baru sebagai pejabat aktif.
 */
class Pejabat extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'pejabat';

    protected $fillable = [
        'jabatan',
        'rt_id',
        'penduduk_nik',
        'mulai_menjabat',
        'selesai_menjabat',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'mulai_menjabat' => 'date',
        'selesai_menjabat' => 'date',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('pejabat')
            ->logOnly(['jabatan', 'rt_id', 'penduduk_nik', 'is_active', 'mulai_menjabat', 'selesai_menjabat'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Menetapkan pejabat baru.',
                'updated' => 'Memperbarui data pejabat.',
                'deleted' => 'Menghapus data pejabat.',
                default   => "Data pejabat di-{$eventName}.",
            });
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_nik', 'nik');
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Tetapkan pejabat aktif untuk sebuah jabatan (+RT bila ketua_rt).
     *
     * Menutup periode pejabat aktif lama pada jabatan+RT yang sama lalu membuat
     * baris baru sebagai pejabat aktif. Seluruhnya dalam satu transaksi.
     *
     * @return self baris pejabat aktif yang baru.
     */
    public static function setAktif(string $jabatan, ?string $rtId, string $pendudukNik, ?string $mulai = null, ?string $keterangan = null): self
    {
        return DB::transaction(function () use ($jabatan, $rtId, $pendudukNik, $mulai, $keterangan) {
            // Tutup pejabat aktif lama pada jabatan + RT yang sama.
            static::where('jabatan', $jabatan)
                ->where('rt_id', $rtId)
                ->where('is_active', true)
                ->get()
                ->each(function (self $lama) use ($mulai) {
                    $lama->is_active = false;
                    if (! $lama->selesai_menjabat) {
                        $lama->selesai_menjabat = $mulai ?: now()->toDateString();
                    }
                    $lama->save();
                });

            return static::create([
                'jabatan' => $jabatan,
                'rt_id' => $rtId,
                'penduduk_nik' => $pendudukNik,
                'mulai_menjabat' => $mulai ?: now()->toDateString(),
                'selesai_menjabat' => null,
                'is_active' => true,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Nama lengkap Ketua RW aktif, atau null bila belum ditetapkan.
     */
    public static function namaKetuaRw(): ?string
    {
        $pejabat = static::with('penduduk')->aktif()->where('jabatan', 'ketua_rw')->first();

        return $pejabat?->penduduk?->nama_lengkap;
    }

    /**
     * Nama lengkap Ketua RT aktif untuk RT tertentu, atau null.
     */
    public static function namaKetuaRt(?string $rtId): ?string
    {
        if (! $rtId) {
            return null;
        }

        $pejabat = static::with('penduduk')->aktif()
            ->where('jabatan', 'ketua_rt')
            ->where('rt_id', $rtId)
            ->first();

        return $pejabat?->penduduk?->nama_lengkap;
    }
}
