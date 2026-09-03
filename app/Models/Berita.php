<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'berita';

    protected $fillable = [
        'judul_berita',
        'slug',
        'kategori',
        'isi_berita', 
        'featured_image',
        'status',
        'operator_id',
        'approval_id',
        'tanggal_publish',
    ];

    protected $casts = [
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