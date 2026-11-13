<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory, HasUuids, \App\Models\Concerns\PunyaCakupanRt, \App\Models\Concerns\PunyaSlug, \App\Models\Concerns\PunyaKategoriKonten;

    protected $table = 'pengumuman';

    protected $fillable = [
        'judul_pengumuman',
        'isi_pengumuman',
        'is_priority',
        'status',
        'catatan_revisi',
        'operator_id',
        'rt_id',
        'kategori_id',
        'kategori',
        'slug',
        'approval_id',
        'tanggal_publish',
    ];

    protected $casts = [
        'is_priority' => 'boolean',
        'tanggal_publish' => 'datetime',
    ];

    public function kolomJudulSlug(): string
    {
        return 'judul_pengumuman';
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approval()
    {
        return $this->belongsTo(User::class, 'approval_id');
    }
}
