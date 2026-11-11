<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Jabatan kepengurusan (Ketua RW / Ketua RT) beserta riwayatnya.
 *
 * Satu jabatan (dibedakan oleh kombinasi jabatan + rt_id) hanya boleh memiliki
 * SATU baris aktif. Pergantian pejabat dilakukan lewat setAktif(), yang menutup
 * periode pejabat lama dan membuat baris baru sebagai pejabat aktif.
 */
class Pejabat extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    /**
     * Daftar jabatan kepengurusan. scope: rw (tanpa RT), rt (per RT), dkm (tanpa RT).
     * Sekretaris disimpan sebagai data; Bendahara dipakai pada dokumen (laporan, resi).
     */
    public const JABATAN = [
        'ketua_rw'      => ['label' => 'Ketua RW',      'scope' => 'rw'],
        'ketua_rt'      => ['label' => 'Ketua RT',      'scope' => 'rt'],
        'sekretaris_rw' => ['label' => 'Sekretaris RW', 'scope' => 'rw'],
        'sekretaris_rt' => ['label' => 'Sekretaris RT', 'scope' => 'rt'],
        'bendahara_rw'  => ['label' => 'Bendahara RW',  'scope' => 'rw'],
        'bendahara_rt'  => ['label' => 'Bendahara RT',  'scope' => 'rt'],
        'bendahara_dkm' => ['label' => 'Bendahara DKM', 'scope' => 'dkm'],
    ];

    public static function labelJabatan(string $jabatan): string
    {
        return self::JABATAN[$jabatan]['label'] ?? $jabatan;
    }

    protected $table = 'pejabat';

    protected $fillable = [
        'jabatan',
        'rt_id',
        'penduduk_nik',
        'mulai_menjabat',
        'selesai_menjabat',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'mulai_menjabat' => 'date',
        'selesai_menjabat' => 'date',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('pejabat')
            ->logOnly(['jabatan', 'rt_id', 'penduduk_nik', 'is_active', 'mulai_menjabat', 'selesai_menjabat'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => 'Menetapkan pejabat baru.',
                'updated' => 'Memperbarui data pejabat.',
                'deleted' => 'Menghapus data pejabat.',
                default   => "Data pejabat di-{$eventName}.",
            });
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_nik', 'nik');
    }

    public function rt()
    {
        return $this->belongsTo(MasterRt::class, 'rt_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Buat baris pejabat BARU (create) dengan tanggal dari input.
     *
     * Tanggal mulai/selesai murni dari parameter (tidak di-generate otomatis).
     * Bila $aktif true, baris ini menjadi aktif dan pejabat aktif lama pada
     * jabatan+RT yang sama hanya DIUBAH flag-nya menjadi non-aktif — tanpa
     * menyentuh/menimpa tanggal selesai mereka.
     *
     * @return self baris pejabat yang baru dibuat.
     */
    public static function buatBaru(
        string $jabatan,
        ?string $rtId,
        string $pendudukNik,
        ?string $mulai = null,
        ?string $selesai = null,
        bool $aktif = true,
        ?string $keterangan = null
    ): self {
        return DB::transaction(function () use ($jabatan, $rtId, $pendudukNik, $mulai, $selesai, $aktif, $keterangan) {
            if ($aktif) {
                // Nonaktifkan pejabat aktif lama (ubah status saja, tanpa isi tanggal selesai).
                static::where('jabatan', $jabatan)
                    ->where('rt_id', $rtId)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            return static::create([
                'jabatan' => $jabatan,
                'rt_id' => $rtId,
                'penduduk_nik' => $pendudukNik,
                'mulai_menjabat' => $mulai,
                'selesai_menjabat' => $selesai,
                'is_active' => $aktif,
                'keterangan' => $keterangan,
            ]);
        });
    }

    /**
     * Jadikan satu baris riwayat (berdasarkan id) menjadi pejabat AKTIF.
     *
     * HANYA mengubah flag is_active: baris target menjadi aktif, baris aktif
     * lain pada jabatan+RT yang sama menjadi non-aktif. Tanggal mulai/selesai
     * TIDAK diubah sama sekali (patokan tetap data yang sudah tersimpan).
     *
     * @return self baris yang kini aktif.
     */
    public static function aktifkanById(string $id): self
    {
        return DB::transaction(function () use ($id) {
            /** @var self $target */
            $target = static::findOrFail($id);

            // Nonaktifkan semua baris aktif pada jabatan + RT yang sama.
            static::where('jabatan', $target->jabatan)
                ->where('rt_id', $target->rt_id)
                ->where('is_active', true)
                ->where('id', '!=', $target->id)
                ->update(['is_active' => false]);

            // Aktifkan baris target (ubah status saja).
            $target->is_active = true;
            $target->save();

            return $target;
        });
    }

    /**
     * Tetapkan pejabat aktif untuk sebuah jabatan (+RT bila ketua_rt).
     *
     * Dipertahankan untuk kompatibilitas (mis. seeder/test lama). Membuat baris
     * baru sebagai pejabat aktif dan menonaktifkan pejabat aktif lama. Tanggal
     * selesai pejabat lama TIDAK lagi diisi otomatis.
     *
     * @return self baris pejabat aktif yang baru.
     */
    public static function setAktif(string $jabatan, ?string $rtId, string $pendudukNik, ?string $mulai = null, ?string $keterangan = null): self
    {
        return static::buatBaru($jabatan, $rtId, $pendudukNik, $mulai ?: now()->toDateString(), null, true, $keterangan);
    }

    /**
     * Nama lengkap Ketua RW aktif, atau null bila belum ditetapkan.
     */
    public static function namaKetuaRw(): ?string
    {
        $pejabat = static::with('penduduk')->aktif()->where('jabatan', 'ketua_rw')->first();

        return $pejabat?->penduduk?->nama_lengkap;
    }

    /**
     * Nama lengkap Ketua RT aktif untuk RT tertentu, atau null.
     */
    public static function namaKetuaRt(?string $rtId): ?string
    {
        if (! $rtId) {
            return null;
        }

        $pejabat = static::with('penduduk')->aktif()
            ->where('jabatan', 'ketua_rt')
            ->where('rt_id', $rtId)
            ->first();

        return $pejabat?->penduduk?->nama_lengkap;
    }

    /**
     * Pejabat yang menjabat pada tanggal tertentu untuk jabatan (+RT bila per RT).
     *
     * Dipilih baris dengan mulai menjabat terbaru yang tidak melewati tanggal tersebut
     * (dan belum berakhir). Bila tidak ada yang cocok, dipakai pejabat aktif saat ini.
     */
    public static function pada(string $jabatan, ?string $rtId, $tanggal = null): ?self
    {
        $query = static::with('penduduk')->where('jabatan', $jabatan);
        $rtId ? $query->where('rt_id', $rtId) : $query->whereNull('rt_id');

        if ($tanggal) {
            $hari = \Illuminate\Support\Carbon::parse($tanggal)->toDateString();
            $cocok = (clone $query)->whereNotNull('mulai_menjabat')->whereDate('mulai_menjabat', '<=', $hari)
                ->orderByDesc('mulai_menjabat')->orderByDesc('created_at')->first();

            if ($cocok && (! $cocok->selesai_menjabat || $cocok->selesai_menjabat->toDateString() >= $hari)) {
                return $cocok;
            }
        }

        return (clone $query)->where('is_active', true)->first();
    }

    /** Nama lengkap pejabat pada tanggal tertentu, atau null bila belum ada. */
    public static function namaPada(string $jabatan, ?string $rtId, $tanggal = null): ?string
    {
        return static::pada($jabatan, $rtId, $tanggal)?->penduduk?->nama_lengkap;
    }}
