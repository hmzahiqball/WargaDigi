<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MutasiWarga extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'mutasi_warga';

    protected $fillable = [
        'keluarga_id',
        'penduduk_id',
        'jenis_mutasi',
        'data_pengajuan',
        'data_lama',
        'file_bukti',
        'status',
        'keterangan_tolak',
        'diajukan_oleh',
        'diajukan_at',
        'disetujui_rt_oleh',
        'disetujui_rt_at',
        'diproses_rw_oleh',
        'diproses_rw_at',
        'ditolak_oleh',
    ];

    protected function casts(): array
    {
        return [
            'data_pengajuan'  => 'array',
            'data_lama'       => 'array',
            'diajukan_at'     => 'datetime',
            'disetujui_rt_at' => 'datetime',
            'diproses_rw_at'  => 'datetime',
        ];
    }

    /** Catat perubahan status mutasi ke activity log (audit). */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('mutasi_warga')
            ->logOnly(['status', 'jenis_mutasi', 'keterangan_tolak'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $e) => match ($e) {
                'created' => 'Pengajuan data warga dibuat.',
                'updated' => 'Status pengajuan data warga diperbarui.',
                'deleted' => 'Pengajuan data warga dihapus.',
                default   => "Pengajuan data warga di-{$e}.",
            });
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function approverRt()
    {
        return $this->belongsTo(User::class, 'disetujui_rt_oleh');
    }

    public function approverRw()
    {
        return $this->belongsTo(User::class, 'diproses_rw_oleh');
    }

    public function penolak()
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    /**
     * Label jenis mutasi yang ramah dibaca.
     */
    public function labelJenis(): string
    {
        return match ($this->jenis_mutasi) {
            'tambah'      => 'Tambah Anggota',
            'edit'        => 'Perubahan Data Anggota',
            'edit_kk'     => 'Perubahan Data KK',
            'pindah_akun' => 'Pindah Pemegang Akun',
            default       => ucfirst((string) $this->jenis_mutasi),
        };
    }

    /**
     * Timeline tahap pengajuan untuk ditampilkan di UI (urut kronologis).
     * Tiap item: ['label','at'(Carbon|null),'oleh'(string|null),'done'(bool),'state'].
     *
     * @return array<int,array>
     */
    public function timeline(): array
    {
        $items = [];

        $items[] = [
            'label' => 'Diajukan oleh warga',
            'at'    => $this->diajukan_at ?: $this->created_at,
            'oleh'  => $this->pengaju?->name ?? $this->pengaju?->username,
            'done'  => true,
            'state' => 'done',
        ];

        $ditolak = $this->status === 'ditolak';

        // Tahap RT.
        $rtDone = (bool) $this->disetujui_rt_at
            || in_array($this->status, ['menunggu_rw', 'disetujui'], true);
        $items[] = [
            'label' => 'Persetujuan Ketua RT',
            'at'    => $this->disetujui_rt_at,
            'oleh'  => $this->approverRt?->name ?? $this->approverRt?->username,
            'done'  => $rtDone,
            'state' => $rtDone ? 'done' : ($ditolak && ! $this->disetujui_rt_at ? 'rejected' : 'pending'),
        ];

        // pindah_akun final di RT, jadi tidak ada tahap RW.
        if ($this->jenis_mutasi !== 'pindah_akun') {
            $rwDone = $this->status === 'disetujui';
            $items[] = [
                'label' => 'Persetujuan RW',
                'at'    => $this->diproses_rw_at,
                'oleh'  => $this->approverRw?->name ?? $this->approverRw?->username,
                'done'  => $rwDone,
                'state' => $rwDone ? 'done' : ($ditolak ? 'rejected' : 'pending'),
            ];
        }

        if ($ditolak) {
            $items[] = [
                'label' => 'Ditolak',
                'at'    => $this->diproses_rw_at ?: $this->disetujui_rt_at ?: $this->updated_at,
                'oleh'  => $this->penolak?->name ?? $this->penolak?->username,
                'done'  => true,
                'state' => 'rejected',
                'catatan' => $this->keterangan_tolak,
            ];
        } elseif ($this->status === 'disetujui') {
            $items[] = [
                'label' => 'Selesai — Disetujui',
                'at'    => $this->diproses_rw_at ?: $this->disetujui_rt_at,
                'oleh'  => null,
                'done'  => true,
                'state' => 'done',
            ];
        }

        return $items;
    }
}
