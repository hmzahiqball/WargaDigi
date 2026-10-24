<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FasilitasUmum extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'fasilitas_umum';

    protected $fillable = [
        'icon',
        'nama',
        'lokasi',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];
}
