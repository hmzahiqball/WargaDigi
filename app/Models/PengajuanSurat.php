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
        'ttd_rt',
        'stempel_rt',
        'ttd_rw',
        'stempel_rw',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disetujui_rt' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    /**
     * Daftar tipe surat yang tersedia (sesuai format asli RW 21).
     */
    public const TIPE_SURAT = [
        'Kartu Keluarga',
        'Kartu Tanda Penduduk',
        'Surat Keterangan Ahli Waris',
        'Surat Kelahiran',
        'Surat Kematian',
        'Surat Keterangan Domisili',
        'Surat Keterangan Miskin / Tidak Mampu',
        'Surat Keterangan Serbaguna',
        'Surat Keterangan Kelakuan Baik / Catatan Kepolisian',
    ];

    /**
     * Daftar bulan dalam angka Romawi.
     */
    public const BULAN_ROMAWI = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Relasi ke penduduk (pemohon).
     */
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }
}