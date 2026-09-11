<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKeuangan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'laporan_keuangan';

    protected $fillable = [
        'judul',
        'periode_bulan',
        'periode_tahun',
        'unit',
        'rt_id',
        'total_pemasukan',
        'total_pengeluaran',
        'saldo_awal',
        'saldo_akhir',
        'status',
        'catatan',
        'file_pdf',
        'dibuat_oleh',
        'disetujui_oleh',
        'tanggal_disetujui',
    ];

    protected $casts = [
        'tanggal_disetujui' => 'datetime',
        'total_pemasukan' => 'integer',
        'total_pengeluaran' => 'integer',
        'saldo_awal' => 'integer',
        'saldo_akhir' => 'integer',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function getFormattedTotalPemasukanAttribute()
    {
        return 'Rp ' . number_format($this->total_pemasukan, 0, ',', '.');
    }

    public function getFormattedTotalPengeluaranAttribute()
    {
        return 'Rp ' . number_format($this->total_pengeluaran, 0, ',', '.');
    }

    public function getFormattedSaldoAkhirAttribute()
    {
        return 'Rp ' . number_format($this->saldo_akhir, 0, ',', '.');
    }
}
