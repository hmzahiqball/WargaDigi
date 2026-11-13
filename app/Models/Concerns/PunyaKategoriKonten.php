<?php

namespace App\Models\Concerns;

use App\Models\KategoriKonten;

/** Konten yang memakai master kategori (nama disalin ke kolom `kategori` agar tampilan lama tetap bekerja). */
trait PunyaKategoriKonten
{
    public function kategoriMaster()
    {
        return $this->belongsTo(KategoriKonten::class, 'kategori_id');
    }
}
