<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasRw extends Model
{
    use HasFactory;

    protected $table = 'kas_rw';

    protected $fillable = [
        'kas_rt_id',
        'jenis',
        'jumlah',
        'saldo_berjalan',
        'keterangan',
        'tanggal_transaksi',
        'operator_id',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'saldo_berjalan' => 'decimal:2',
            'tanggal_transaksi' => 'date',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function kasRt()
    {
        return $this->belongsTo(KasRt::class, 'kas_rt_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
