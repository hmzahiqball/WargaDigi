<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranTagihan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pembayaran_tagihan';

    protected $fillable = [
        'tagihan_id',
        'keluarga_id',
        'warga_id',
        'status',
        'metode',
        'bukti_file',
        'catatan',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'keluarga_id');
    }

    public function warga()
    {
        return $this->belongsTo(User::class, 'warga_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
