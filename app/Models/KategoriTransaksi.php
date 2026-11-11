<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Master kategori transaksi keuangan. Tiap kategori terikat ke arah transaksi
 * (pemasukan/pengeluaran/keduanya) agar tidak salah pasang, dan ke lingkup unit
 * (RT/RW/DKM) atau umum (unit = null, dikelola Admin Aplikasi).
 */
class KategoriTransaksi extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'kategori_transaksi';

    protected $fillable = [
        'nama',
        'tipe',
        'unit',
        'berlaku_rw',
        'berlaku_rt',
        'berlaku_dkm',
        'rt_id',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'aktif'       => 'boolean',
        'berlaku_rw'  => 'boolean',
        'berlaku_rt'  => 'boolean',
        'berlaku_dkm' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('kategori_transaksi')
            ->logOnly(['nama', 'tipe', 'unit', 'rt_id', 'aktif'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    /** Kategori umum (dikelola Admin), berlaku untuk semua unit. */
    public function scopeUmum($query)
    {
        return $query->whereNull('unit');
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Kategori yang relevan untuk sebuah unit (dan RT bila ada):
     * kategori umum (unit null) + kategori milik unit tsb.
     * Untuk unit RT, kategori RT dibatasi ke rt_id yang sama atau rt_id null.
     */
    public function scopeUntukUnit($query, ?string $unit, ?string $rtId = null)
    {
        return $query->where(function ($q) use ($unit, $rtId) {
            // DKM hanya memakai kategori yang diterapkan untuk DKM; kategori umum tidak ditampilkan.
            if ($unit !== 'DKM') {
                $q->whereNull('unit');
            }
            if (! $unit) {
                return;
            }

            // Kategori milik unit itu sendiri. Kategori RW hanya dipakai RW bila penanda RW aktif.
            $q->orWhere(function ($sub) use ($unit, $rtId) {
                if ($unit === 'RW') {
                    $sub->where('unit', 'RW')->where('berlaku_rw', true);

                    return;
                }
                $sub->where('unit', $unit);
                if ($unit === 'RT') {
                    $sub->where(function ($r) use ($rtId) {
                        $r->whereNull('rt_id');
                        if ($rtId) {
                            $r->orWhere('rt_id', $rtId);
                        }
                    });
                }
            });

            // Kategori buatan RW yang diterapkan juga untuk RT atau DKM.
            if ($unit === 'RT' || $unit === 'DKM') {
                $q->orWhere(fn ($sub) => $sub->where('unit', 'RW')->where($unit === 'RT' ? 'berlaku_rt' : 'berlaku_dkm', true));
            }
        });
    }

    /** Kategori cocok dengan tipe transaksi (termasuk 'keduanya'). */
    public function scopeUntukTipe($query, string $tipe)
    {
        return $query->whereIn('tipe', [$tipe, 'keduanya']);
    }
    /**
     * Unit tempat kategori buatan RW berlaku, mis. ['RW', 'DKM']. Kosong untuk kategori umum atau milik RT.
     *
     * @return string[]
     */
    public function getUnitBerlakuAttribute(): array
    {
        if ($this->unit !== 'RW') {
            return [];
        }

        return array_values(array_filter([
            $this->berlaku_rw ? 'RW' : null,
            $this->berlaku_rt ? 'RT' : null,
            $this->berlaku_dkm ? 'DKM' : null,
        ]));
    }

    /** Label tampilan "Berlaku di". */
    public function getBerlakuDiAttribute(): string
    {
        if ($this->unit === null) {
            return 'Umum';
        }
        if ($this->unit !== 'RW') {
            return 'Unit ' . $this->unit;
        }

        $unit = $this->unit_berlaku;

        return match (count($unit)) {
            0       => '-',
            1       => $unit[0],
            2       => $unit[0] . ' dan ' . $unit[1],
            default => 'Semua (RW, RT, DKM)',
        };
    }
    /** Apakah kategori ini boleh dipakai untuk tipe transaksi tertentu. */
    public function cocokTipe(string $tipe): bool
    {
        return $this->tipe === 'keduanya' || $this->tipe === $tipe;
    }
}
