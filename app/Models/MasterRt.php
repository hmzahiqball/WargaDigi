<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterRt extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_rt';
    
    protected $fillable = [
        'kode_rt', 
        'nama_rt',
    ];

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'rt_id');
    }
}