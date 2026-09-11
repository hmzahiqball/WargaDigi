<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKeuangan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaksi_keuangan';

    protected $fillable = [
        'kode_transaksi',
        'tipe',
        'kategori',
        'judul',
        'deskripsi',
        'jumlah',
        'tanggal',
        'bukti_file',
        'status',
        'catatan_verifikasi',
        'unit_sumber',
        'rt_id',
        'dicatat_oleh',
        'diverifikasi_oleh',
        'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'jumlah' => 'integer',
    ];

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    public function scopePemasukan($query)
    {
        return $query->where('tipe', 'pemasukan');
    }

    public function scopePengeluaran($query)
    {
        return $query->where('tipe', 'pengeluaran');
    }
}
