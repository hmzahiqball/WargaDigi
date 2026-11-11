<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Program iuran bulanan (wajib) satu RT untuk satu tahun. Nominal per bulan
 * diatur di sini; grid ceklis mengikuti master yang aktif.
 */
class MasterIuran extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'master_iuran';

    protected $fillable = [
        'rt_id', 'tahun', 'nama', 'nominal', 'kategori_id', 'aktif',
    ];

    protected $casts = [
        'nominal' => 'integer',
        'tahun'   => 'integer',
        'aktif'   => 'boolean',
    ];

    protected static function booted(): void
    {
        // Setiap master selalu punya nominal awal yang berlaku sejak Januari.
        static::created(function (self $master) {
            $master->riwayat()->create([
                'nominal'             => (int) ($master->getAttributes()['nominal'] ?? 0),
                'berlaku_mulai_bulan' => 1,
            ]);
            $master->unsetRelation('riwayat');
        });
    }

    /**
     * Nominal yang berlaku SAAT INI (bulan berjalan untuk tahun ini, Desember untuk tahun lampau,
     * Januari untuk tahun depan). Nominal per bulan tertentu: nominalUntukBulan().
     */
    public function getNominalAttribute($value): int
    {
        if (! $this->exists) {
            return (int) $value;
        }
        $tahunIni = (int) now()->format('Y');
        $bulan = $this->tahun < $tahunIni ? 12 : ($this->tahun > $tahunIni ? 1 : (int) now()->format('n'));

        return $this->nominalUntukBulan($bulan, (int) $value);
    }

    public function riwayat()
    {
        return $this->hasMany(MasterIuranRiwayat::class, 'master_iuran_id');
    }

    /** Riwayat terurut menurut bulan mulai berlaku. */
    public function riwayatUrut(): Collection
    {
        return $this->riwayat->sortBy('berlaku_mulai_bulan')->values();
    }

    /** Nominal yang berlaku untuk iuran bulan tertentu (1..12). */
    public function nominalUntukBulan(int $bulan, ?int $fallback = null): int
    {
        $urut = $this->riwayatUrut();
        $entri = $urut->where('berlaku_mulai_bulan', '<=', $bulan)->last() ?? $urut->first();

        return $entri ? (int) $entri->nominal : (int) ($fallback ?? ($this->getAttributes()['nominal'] ?? 0));
    }

    /** Bulan terakhir (1..12) yang sudah memiliki pembayaran/pengajuan atau laporan RT; 0 bila belum ada. */
    public function bulanTerkunciTerakhir(): int
    {
        $bayar = (int) IuranPembayaran::where('master_iuran_id', $this->id)
            ->whereIn('status', ['lunas', 'menunggu'])->max('bulan');
        $laporan = (int) LaporanKeuangan::where('unit', 'RT')->where('rt_id', $this->rt_id)
            ->where('periode_tahun', $this->tahun)->where('status', '!=', 'Rejected')->max('periode_bulan');

        return max($bayar, $laporan);
    }

    /**
     * Bulan paling awal yang boleh dijadikan awal berlaku nominal baru (tanpa berlaku surut):
     * bulan berjalan, atau sesudah bulan terakhir yang sudah ada pembayaran/laporan.
     * Nilai > 12 berarti nominal tahun ini sudah tidak dapat diubah lagi.
     */
    public function bulanMulaiTerawal(): int
    {
        $tahunIni = (int) now()->format('Y');
        if ($this->tahun < $tahunIni) {
            return 13;
        }
        $dasar = $this->tahun > $tahunIni ? 1 : (int) now()->format('n');

        return max($dasar, $this->bulanTerkunciTerakhir() + 1);
    }

    /** Terapkan nominal baru yang berlaku mulai bulan tertentu. Tidak menyentuh bulan sebelumnya. */
    public function ubahNominal(int $nominal, int $bulanMulai, ?string $olehUserId = null): void
    {
        $this->riwayat()->updateOrCreate(
            ['berlaku_mulai_bulan' => $bulanMulai],
            ['nominal' => $nominal, 'dibuat_oleh' => $olehUserId]
        );
        $this->unsetRelation('riwayat');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('master_iuran')
            ->logOnly(['rt_id', 'tahun', 'nama', 'nominal', 'aktif'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriTransaksi::class, 'kategori_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(IuranPembayaran::class, 'master_iuran_id');
    }
}
