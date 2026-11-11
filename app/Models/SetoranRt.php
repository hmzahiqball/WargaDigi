<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/** Setoran kolektif RT ke RW (dicatat RT, dikonfirmasi RW). */
class SetoranRt extends Model
{
    use HasUuids;

    protected $table = 'setoran_rt';

    protected $fillable = [
        'rt_id', 'tagihan_id', 'batch_id', 'periode_bulan', 'periode_tahun', 'nominal', 'tanggal_setor', 'metode',
        'bukti_file', 'catatan', 'status', 'alasan', 'dibuat_oleh', 'dikonfirmasi_oleh',
        'dikonfirmasi_at', 'transaksi_rt_id', 'transaksi_rw_id',
    ];

    protected $casts = [
        'periode_bulan'   => 'integer',
        'periode_tahun'   => 'integer',
        'nominal'         => 'integer',
        'tanggal_setor'   => 'date',
        'dikonfirmasi_at' => 'datetime',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    /** Seluruh baris dalam satu setoran (batch); setoran lama tanpa batch berisi dirinya sendiri. */
    public function grup(): \Illuminate\Support\Collection
    {
        return $this->batch_id
            ? static::with('tagihan')->where('batch_id', $this->batch_id)->get()
            : collect([$this]);
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    /** Label periode: nama Iuran Lainnya, atau bulan dan tahun untuk iuran bulanan. */
    public function getPeriodeLabelAttribute(): string
    {
        if ($this->tagihan_id) {
            return 'Iuran ' . ($this->tagihan->judul ?? 'Lainnya');
        }

        return (\App\Services\IuranService::BULAN[$this->periode_bulan] ?? $this->periode_bulan) . ' ' . $this->periode_tahun;
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if (! $this->bukti_file) {
            return null;
        }
        $bersih = str_replace('\\', '/', $this->bukti_file);

        return str_starts_with($bersih, 'storage/') ? asset($bersih) : asset('storage/' . $bersih);
    }
}