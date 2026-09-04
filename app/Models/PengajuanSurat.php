<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSurat extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_surat';

    protected $fillable = [
        'penduduk_id',
        'tipe_surat',
        'keterangan_tambahan',
        'file_ktp',
        'file_kk',
        'status',
        'catatan_rt',
        'catatan_rw',
        'file_surat_resmi',
        'tanggal_disetujui_rt',
        'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disetujui_rt' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    /**
     * Daftar tipe surat yang tersedia.
     */
    public const TIPE_SURAT = [
        'Surat Keterangan Domisili (SKD)',
        'Surat Keterangan Tidak Mampu (SKTM)',
        'Surat Keterangan Usaha (SKU)',
        'Surat Keterangan Belum Menikah',
        'Surat Keterangan Kematian',
        'Surat Keterangan Kelahiran',
        'Surat Pengantar RT/RW',
        'Surat Keterangan Pindah',
        'Surat Keterangan Lainnya',
    ];

    /**
     * Relasi ke penduduk (pemohon).
     */
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}
