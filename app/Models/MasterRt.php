<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MasterRt extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'master_rt';

    protected $fillable = [
        'kode_rt',
        'nama_rt',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('master_rt')
            ->logOnly(['kode_rt', 'nama_rt'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Menambahkan data RT baru.',
                'updated' => 'Memperbarui data RT.',
                'deleted' => 'Menghapus data RT.',
                default   => "Data RT di-{$eventName}.",
            });
    }

    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'rt_id');
    }
}
