<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Master kategori konten, dikelola Operator Konten RW dan berlaku untuk semua RT.
 * Tiap kategori dapat dibatasi untuk berita, agenda, dan/atau pengumuman.
 */
class KategoriKonten extends Model
{
    use HasFactory, HasUuids, LogsActivity;

    protected $table = 'kategori_konten';

    public const JENIS = ['berita' => 'Berita', 'agenda' => 'Agenda', 'pengumuman' => 'Pengumuman'];

    /** Palet warna tetap: kunci => [latar, garis, teks]. Dipakai kalender agenda warga. */
    public const WARNA = [
        'hijau'      => ['label' => 'Hijau',      'bg' => '#dcfce7', 'border' => '#86efac', 'text' => '#166534'],
        'biru'       => ['label' => 'Biru',       'bg' => '#dbeafe', 'border' => '#93c5fd', 'text' => '#1e40af'],
        'kuning'     => ['label' => 'Kuning',     'bg' => '#fef9c3', 'border' => '#fde047', 'text' => '#854d0e'],
        'ungu'       => ['label' => 'Ungu',       'bg' => '#f3e8ff', 'border' => '#d8b4fe', 'text' => '#6b21a8'],
        'tosca'      => ['label' => 'Tosca',      'bg' => '#ccfbf1', 'border' => '#5eead4', 'text' => '#115e59'],
        'merah-muda' => ['label' => 'Merah muda', 'bg' => '#fce7f3', 'border' => '#f9a8d4', 'text' => '#9d174d'],
        'oranye'     => ['label' => 'Oranye',     'bg' => '#ffedd5', 'border' => '#fdba74', 'text' => '#9a3412'],
        'abu'        => ['label' => 'Abu-abu',    'bg' => '#f3f4f6', 'border' => '#d1d5db', 'text' => '#374151'],
    ];

    protected $fillable = [
        'nama', 'warna', 'urutan', 'aktif',
        'berlaku_berita', 'berlaku_agenda', 'berlaku_pengumuman',
    ];

    protected $casts = [
        'aktif'              => 'boolean',
        'berlaku_berita'     => 'boolean',
        'berlaku_agenda'     => 'boolean',
        'berlaku_pengumuman' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('kategori_konten')
            ->logOnly(['nama', 'warna', 'aktif', 'berlaku_berita', 'berlaku_agenda', 'berlaku_pengumuman'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /** Kategori yang berlaku untuk satu jenis konten: berita, agenda, atau pengumuman. */
    public function scopeUntuk($query, string $jenis)
    {
        abort_unless(isset(self::JENIS[$jenis]), 500, 'Jenis konten tidak dikenal.');

        return $query->where('berlaku_' . $jenis, true);
    }

    public function scopeUrut($query)
    {
        return $query->orderBy('urutan')->orderBy('nama');
    }

    /** Pilihan untuk form: kategori aktif yang berlaku untuk jenis tersebut. */
    public static function pilihan(string $jenis)
    {
        return static::aktifUntuk($jenis)->get();
    }

    public static function aktifUntuk(string $jenis)
    {
        return static::query()->aktif()->untuk($jenis)->urut();
    }

    /**
     * Aturan validasi kategori_id. Kategori yang sedang dipakai konten ini tetap diterima walau
     * sudah dinonaktifkan agar draf lama tetap bisa disimpan.
     */
    public static function aturan(string $jenis, ?string $idSaatIni = null): array
    {
        return [
            'required',
            'uuid',
            Rule::exists('kategori_konten', 'id')->where(function ($q) use ($jenis, $idSaatIni) {
                $q->where(function ($w) use ($jenis, $idSaatIni) {
                    $w->where('aktif', true)->where('berlaku_' . $jenis, true);
                    if ($idSaatIni) {
                        $w->orWhere('id', $idSaatIni);
                    }
                });
            }),
        ];
    }

    public static function pesan(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.uuid'     => 'Kategori tidak valid.',
            'kategori_id.exists'   => 'Kategori tidak valid atau tidak berlaku untuk jenis konten ini.',
        ];
    }

    /** Jumlah konten yang memakai kategori ini, per jenis dan total. */
    public function pemakaian(): array
    {
        $hasil = [];
        foreach (array_keys(self::JENIS) as $jenis) {
            $hasil[$jenis] = DB::table($jenis)->where('kategori_id', $this->id)->count();
        }
        $hasil['total'] = array_sum($hasil);

        return $hasil;
    }

    /** Warna siap pakai (latar, garis, teks) dengan cadangan abu-abu. */
    public static function warnaDari(?string $kunci): array
    {
        return self::WARNA[$kunci] ?? self::WARNA['abu'];
    }
}
