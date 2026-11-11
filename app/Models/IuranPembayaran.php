<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Satu sel pembayaran iuran: KK × bulan pada sebuah master iuran.
 * Dibuat lazy (hanya saat ada pembayaran / pengajuan).
 */
class IuranPembayaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'iuran_pembayaran';

    protected $fillable = [
        'master_iuran_id', 'keluarga_id', 'bulan', 'status',
        'tanggal_bayar', 'metode', 'bukti_file', 'sumber',
        'transaksi_id', 'batch_id', 'dicatat_oleh', 'catatan',
    ];

    protected $casts = [
        'bulan'         => 'integer',
        'tanggal_bayar' => 'date',
    ];

    public function master()
    {
        return $this->belongsTo(MasterIuran::class, 'master_iuran_id');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKeuangan::class, 'transaksi_id');
    }

    public function scopeLunas($q)
    {
        return $q->where('status', 'lunas');
    }

    public function scopeMenunggu($q)
    {
        return $q->where('status', 'menunggu');
    }
}
