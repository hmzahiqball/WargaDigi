<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriDokumentasi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'galeri_dokumentasi';

    protected $fillable = [
        'agenda_id',
        'jumlah_peserta',
        'foto',
        'operator_id',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
