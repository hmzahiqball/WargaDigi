<?php

namespace App\Exports;

use App\Models\Penduduk;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Export data penduduk ke Excel dengan kolom yang dapat dipilih.
 *
 * Kolom yang tersedia didefinisikan di self::KOLOM (key => label). Hanya kolom
 * yang dipilih (dan valid) yang diekspor, mengikuti urutan definisi.
 */
class PendudukExport implements FromCollection, WithHeadings, WithMapping
{
    /** Definisi kolom yang dapat diekspor: key => label heading. */
    public const KOLOM = [
        'nik'                      => 'NIK',
        'nama_lengkap'             => 'Nama Lengkap',
        'no_wa'                    => 'Nomor WA',
        'jenis_kelamin'            => 'Jenis Kelamin',
        'tempat_lahir'             => 'Tempat Lahir',
        'tanggal_lahir'            => 'Tanggal Lahir',
        'agama'                    => 'Agama',
        'pekerjaan'                => 'Pekerjaan',
        'pendidikan_terakhir'      => 'Pendidikan Terakhir',
        'status_hubungan_keluarga' => 'Hubungan Keluarga',
        'status_perkawinan'        => 'Status Perkawinan',
        'kewarganegaraan'          => 'Kewarganegaraan',
        'status_warga'             => 'Status Warga',
        'alamat'                   => 'Alamat (KTP)',
        'alamat_domisili'          => 'Alamat Domisili',
        'nama_ayah'                => 'Nama Ayah',
        'nama_ibu'                 => 'Nama Ibu',
        'no_kk'                    => 'No. KK',
        'rt'                       => 'RT',
    ];

    /** @var array<int,string> kolom terpilih (urut sesuai KOLOM). */
    protected array $kolom;

    protected string $search;
    protected ?string $rtId;

    /**
     * @param array<int,string> $kolom kolom yang dipilih user
     */
    public function __construct(array $kolom, string $search = '', ?string $rtId = null)
    {
        // Saring hanya kolom valid, pertahankan urutan definisi KOLOM.
        $this->kolom = array_values(array_filter(
            array_keys(self::KOLOM),
            fn ($key) => in_array($key, $kolom, true)
        ));

        // Fallback: bila tidak ada kolom valid, pakai NIK + Nama.
        if (empty($this->kolom)) {
            $this->kolom = ['nik', 'nama_lengkap'];
        }

        $this->search = $search;
        $this->rtId = $rtId;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        return Penduduk::with(['keluarga.rt'])
            ->leftJoin('keluarga', 'penduduk.keluarga_id', '=', 'keluarga.id')
            ->select('penduduk.*')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('penduduk.nama_lengkap', 'like', "%{$this->search}%")
                        ->orWhere('penduduk.nik', 'like', "%{$this->search}%");
                });
            })
            ->when($this->rtId, fn ($q) => $q->where('keluarga.rt_id', $this->rtId))
            ->orderBy('keluarga.no_kk')
            ->orderBy('penduduk.nama_lengkap')
            ->get();
    }

    public function headings(): array
    {
        return array_map(fn ($key) => self::KOLOM[$key], $this->kolom);
    }

    /**
     * @param Penduduk $p
     */
    public function map($p): array
    {
        $nilai = [
            'nik'                      => (string) $p->nik,
            'nama_lengkap'             => $p->nama_lengkap,
            'no_wa'                    => (string) ($p->no_wa ?? ''),
            'jenis_kelamin'            => $p->jenis_kelamin === 'L' ? 'Laki-laki' : ($p->jenis_kelamin === 'P' ? 'Perempuan' : $p->jenis_kelamin),
            'tempat_lahir'             => $p->tempat_lahir,
            'tanggal_lahir'            => $p->tanggal_lahir ? Carbon::parse($p->tanggal_lahir)->format('d-m-Y') : '',
            'agama'                    => $p->agama,
            'pekerjaan'                => $p->pekerjaan,
            'pendidikan_terakhir'      => $p->pendidikan_terakhir,
            'status_hubungan_keluarga' => $p->status_hubungan_keluarga,
            'status_perkawinan'        => $p->status_perkawinan,
            'kewarganegaraan'          => $p->kewarganegaraan,
            'status_warga'             => $p->status_warga === 'tetap' ? 'Warga Tetap' : ($p->status_warga === 'tidak_tetap' ? 'Bukan Warga Tetap' : ''),
            'alamat'                   => $p->alamat,
            'alamat_domisili'          => $p->alamat_domisili,
            'nama_ayah'                => $p->nama_ayah,
            'nama_ibu'                 => $p->nama_ibu,
            'no_kk'                    => (string) ($p->keluarga?->no_kk ?? ''),
            'rt'                       => $p->keluarga?->rt?->nama_rt ?? '',
        ];

        return array_map(fn ($key) => $nilai[$key] ?? '', $this->kolom);
    }
}
