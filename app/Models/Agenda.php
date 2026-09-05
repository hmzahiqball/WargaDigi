<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'agenda';

    protected $fillable = [
        'judul_agenda',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'link_gmaps',
        'detail_pengumuman',
        'banner_flyer',
        'is_rsvp_enabled',
        'foto_dokumentasi',
        'status',
        'catatan_revisi',
        'operator_id',
        'approval_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'is_rsvp_enabled' => 'boolean',
        'foto_dokumentasi' => 'array',
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
