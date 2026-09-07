<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'jumlah_dana',
        'jenis_penerima',
        'rt_id',
        'keluarga_id',
        'tanggal_donasi',
        'status',
        'operator_id',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_dana' => 'decimal:2',
            'tanggal_donasi' => 'date',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
