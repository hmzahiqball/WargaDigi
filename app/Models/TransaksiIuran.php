<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiIuran extends Model
{
    use HasFactory;

    protected $table = 'transaksi_iuran';

    protected $fillable = [
        'keluarga_id',
        'rt_id',
        'periode_bulan',
        'periode_tahun',
        'jumlah',
        'metode_pembayaran',
        'bukti_pembayaran',
        'keterangan',
        'status',
        'verified_by',
        'verified_at',
        'operator_id',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
