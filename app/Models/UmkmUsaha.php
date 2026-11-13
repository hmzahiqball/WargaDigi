<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmkmUsaha extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'umkm_usaha';

    protected $fillable = [
        'nik',
        'nama_usaha',
        'kategori_umkm_id',
        'deskripsi',
        'alamat_usaha',
        'no_wa',
        'foto_usaha',
        'status_verifikasi',
        'catatan_verifikasi',
        'klikWA',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'klikWA' => 'integer',
    ];

    public function kategori_umkm()
    {
        return $this->belongsTo(KategoriUmkm::class, 'kategori_umkm_id');
    }

    public function kategori_produk()
    {
        return $this->hasMany(KategoriProduk::class, 'umkm_usaha_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }


    public function pemilik()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }


    public function produk()
    {
        return $this->hasMany(UmkmProduk::class, 'umkm_usaha_id');
    }

    /**
     * Mutator untuk membersihkan dan memformat nomor WA sebelum disimpan ke database
     */
    public function setNoWaAttribute($value)
    {
        $cleanWa = preg_replace('/[^0-9]/', '', $value);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        } elseif (!str_starts_with($cleanWa, '62') && !empty($cleanWa)) {
            $cleanWa = '62' . $cleanWa;
        }
        $this->attributes['no_wa'] = $cleanWa;
    }

    /** Normalisasi nomor WA ke format 62xxxx (sama dengan mutator no_wa). Null bila kosong. */
    public static function normalisasiWa(?string $nomor): ?string
    {
        $bersih = preg_replace('/[^0-9]/', '', (string) $nomor);
        if ($bersih === '') {
            return null;
        }
        if (str_starts_with($bersih, '0')) {
            return '62' . substr($bersih, 1);
        }

        return str_starts_with($bersih, '62') ? $bersih : '62' . $bersih;
    }

    /**
     * Nomor WA di profil pemilik akun: nomor WA penduduk pemilik akun, atau nomor WA keluarga
     * bila kosong. Diambil dari database (bukan dari input klien).
     */
    public static function nomorKontakProfil(?User $user): ?string
    {
        if (! $user || ! $user->nik) {
            return null;
        }
        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();

        return self::normalisasiWa($penduduk?->no_wa) ?? self::normalisasiWa($penduduk?->keluarga?->no_wa);
    }

    /**
     * Alamat di profil pemilik akun (dari database): alamat domisili penduduk, lalu alamat
     * domisili keluarga, lalu alamat KK; dilengkapi RT/RW bila belum tercantum.
     */
    public static function alamatProfil(?User $user): ?string
    {
        if (! $user || ! $user->nik) {
            return null;
        }
        $penduduk = Penduduk::with('keluarga.rt')->where('nik', $user->nik)->first();
        $keluarga = $penduduk?->keluarga;

        $alamat = collect([$penduduk?->alamat_domisili, $keluarga?->alamat_domisili, $keluarga?->alamat, $penduduk?->alamat])
            ->map(fn ($a) => trim((string) $a))
            ->first(fn ($a) => $a !== '');
        if (! $alamat) {
            return null;
        }

        $kodeRt = $keluarga?->rt?->kode_rt;
        if ($kodeRt && ! preg_match('/\bRT\b/i', $alamat)) {
            $alamat .= ', RT ' . str_pad((string) $kodeRt, 2, '0', STR_PAD_LEFT)
                . ($keluarga->rw ? '/RW ' . str_pad((string) $keluarga->rw, 2, '0', STR_PAD_LEFT) : '');
        }

        return $alamat;
    }

    /**
     * Accessor untuk mendapatkan format nomor WA (+62 xxx)
     */
    public function getNoWaFormattedAttribute()
    {
        if (empty($this->no_wa)) {
            return '-';
        }

        $wa = $this->no_wa;
        if (str_starts_with($wa, '62')) {
            return '+62 ' . substr($wa, 2);
        }
        return $wa;
    }
}
