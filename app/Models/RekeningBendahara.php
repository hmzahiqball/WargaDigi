<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningBendahara extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rekening_bendahara';

    protected $fillable = [
        'unit',
        'rt_id',
        'bank',
        'no_rek',
        'nama_rek',
        'qris_file',
        'created_by',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
