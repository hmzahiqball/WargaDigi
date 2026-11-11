<?php

namespace App\Models\Concerns;

use App\Models\MasterRt;

/** Konten dengan cakupan: rt_id kosong = RW, terisi = RT tertentu. */
trait PunyaCakupanRt
{
    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    /** "RW" atau "RT 02" untuk lencana asal konten. */
    public function getAsalLabelAttribute(): string
    {
        if (empty($this->rt_id)) {
            return 'RW';
        }

        return 'RT ' . ($this->rt?->kode_rt ?? '');
    }
}
