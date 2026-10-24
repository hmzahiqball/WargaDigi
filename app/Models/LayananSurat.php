<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananSurat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'layanan_surat';

    protected $fillable = [
        'icon',
        'judul',
        'deskripsi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];
}
