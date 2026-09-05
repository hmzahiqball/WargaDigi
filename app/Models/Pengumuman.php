<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul_pengumuman',
        'isi_pengumuman',
        'is_priority',
        'status',
        'catatan_revisi',
        'operator_id',
        'approval_id',
        'tanggal_publish',
    ];

    protected $casts = [
        'is_priority' => 'boolean',
        'tanggal_publish' => 'datetime',
    ];

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'approval_id');
    }
}
