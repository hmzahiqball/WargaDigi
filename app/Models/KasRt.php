<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KasRt extends Model
{
    use HasFactory;

    protected $table = 'kas_rt';

    protected $fillable = [
        'rt_id',
        'periode_bulan',
        'periode_tahun',
        'total_pemasukan',
        'total_pengeluaran',
        'saldo',
        'catatan',
        'status_laporan',
        'approved_by',
        'approved_at',
        'operator_id',
    ];

    protected function casts(): array
    {
        return [
            'total_pemasukan' => 'decimal:2',
            'total_pengeluaran' => 'decimal:2',
            'saldo' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function kasRw()
    {
        return $this->hasMany(KasRw::class, 'kas_rt_id');
    }
}
