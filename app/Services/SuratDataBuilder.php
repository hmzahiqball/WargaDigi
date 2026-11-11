<?php

namespace App\Services;

use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\PengajuanSurat;
use Illuminate\Support\Carbon;

/**
 * Menyusun data surat (objek $data untuk pdf/surat.blade.php) dari satu
 * PengajuanSurat. Dipakai bersama oleh pratinjau RT, pratinjau RW, dan
 * unduhan PDF warga agar isinya selalu konsisten.
 *
 * Semua data diambil dari DB; tata letak per jenis surat mengikuti
 * config/surat.php.
 */
class SuratDataBuilder
{
    /** Penanda isian kosong yang dicetak agar bisa ditulis tangan. */
    public const TITIK = '..........................';

    public static function build(PengajuanSurat $item, bool $preview = false): object
    {
        // Surat yang sudah terbit memakai snapshot agar isinya tidak berubah
        // walaupun data penduduk diedit setelahnya.
        if ($item->status === 'Selesai' && ! $preview && is_array($item->snapshot_surat) && $item->snapshot_surat) {
            $snap = $item->snapshot_surat;
            $snap['is_preview'] = false;

            return (object) $snap;
        }

        return self::susun($item, $preview);
    }

    /** Menyimpan snapshot data surat saat status menjadi Selesai. */
    public static function simpanSnapshot(PengajuanSurat $item): void
    {
        $data = (array) self::susun($item->fresh(), false);
        unset($data['is_preview']);
        $item->forceFill(['snapshot_surat' => $data])->save();
    }

    protected static function susun(PengajuanSurat $item, bool $preview): object
    {
        $item->loadMissing('penduduk.keluarga.rt');
        $p = $item->penduduk;
        $k = $p?->keluarga;

        // --- RT / pejabat ---------------------------------------------------
        $rtId = $k?->rt_id ?? (auth()->check() ? auth()->user()->rt_id : null);
        $rt = $rtId ? MasterRt::find($rtId) : null;
        $kodeRt = $rt?->kode_rt ?? '01';

        $namaKetuaRt = Pejabat::namaKetuaRt($rtId) ?? self::TITIK;
        $namaKetuaRw = Pejabat::namaKetuaRw() ?? self::TITIK;

        // --- Tanggal surat --------------------------------------------------
        // Surat yang sudah selesai memakai tanggal penyelesaian; pratinjau
        // sebelum terbit memakai hari ini (bukan updated_at yang bergeser).
        $tanggal = $item->tanggal_selesai
            ?? ($item->status === 'Selesai' ? $item->updated_at : Carbon::now());

        // --- Konfigurasi jenis surat ---------------------------------------
        $semuaJenis = config('surat.jenis', []);
        $cfg = $semuaJenis[$item->tipe_surat] ?? [
            'judul'  => 'SURAT KETERANGAN',
            'tujuan' => 'Surat keterangan ini diperlukan untuk {keperluan}.',
            'alamat' => 'kk',
            'fields' => [],
        ];
        $kop = config('surat.kop', []);

        // --- Alamat ---------------------------------------------------------
        $alamatKk = $k ? ($k->alamatLengkap() ?: '-') : '-';
        $alamatDomisili = $p?->alamat_domisili ?: ($k?->alamat_domisili ?: $alamatKk);
        $pakaiDomisili = ($cfg['alamat'] ?? 'kk') === 'domisili';

        // --- Keperluan ------------------------------------------------------
        $keperluan = trim((string) $item->keterangan_tambahan);
        $tujuan = str_replace('{keperluan}', $keperluan !== '' ? $keperluan : self::TITIK, $cfg['tujuan'] ?? '');

        // --- RW kode (tanpa nol di depan) -----------------------------------
        $kodeRw = $kop['kode_rw'] ?? '21';

        return (object) [
            'tipe_surat'  => $item->tipe_surat,
            'judul'       => $cfg['judul'] ?? 'SURAT KETERANGAN',
            'tujuan'      => $tujuan,
            'sebagai_pelapor' => (bool) ($cfg['sebagai_pelapor'] ?? false),

            // Kop & penomoran
            'kop'          => $kop,
            'kode_rt'      => $kodeRt,
            'kode_rw'      => $kodeRw,
            'nomor_urut'   => $item->nomor_urut,
            'bulan_romawi' => PengajuanSurat::BULAN_ROMAWI[(int) $tanggal->format('n')] ?? 'I',
            'tahun'        => $tanggal->format('y'),
            'tanggal_surat'=> $tanggal->copy()->locale('id')->translatedFormat('j F Y'),

            // Biodata (langsung dari DB)
            'nama_lengkap'     => $p->nama_lengkap ?? '-',
            'tempat_tgl_lahir' => self::ttl($p),
            'nik'              => $p->nik ?? '-',
            'no_kk'            => $k->no_kk ?? '-',
            'jenis_kelamin'    => $p ? ($p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan') : '-',
            'agama'            => $p->agama ?? '-',
            'status_perkawinan'=> $p->status_perkawinan ?? '-',
            'kewarganegaraan'  => $p->kewarganegaraan ?? 'WNI',
            'pekerjaan'        => $p->pekerjaan ?? '-',
            'pendidikan'       => $p->pendidikan_terakhir ?? null,
            'status_hubungan_keluarga' => $p->status_hubungan_keluarga ?? '-',
            'label_alamat'     => $pakaiDomisili ? 'Alamat Domisili' : 'Alamat',
            'alamat'           => $pakaiDomisili ? $alamatDomisili : $alamatKk,

            // Isian tambahan khusus jenis surat
            'tambahan_rows' => self::barisTambahan($item, $cfg['fields'] ?? []),

            // Pejabat & pengesahan
            'nama_ketua_rt' => $namaKetuaRt,
            'nama_ketua_rw' => $namaKetuaRw,
            'ttd_rt'        => $item->ttd_rt,
            'stempel_rt'    => $item->stempel_rt,
            'ttd_rw'        => $item->ttd_rw,
            'stempel_rw'    => $item->stempel_rw,

            'is_preview' => $preview,
        ];
    }

    /** "Bandung, 6 Januari 1993" (aman bila tanggal lahir kosong). */
    protected static function ttl($p): string
    {
        if (! $p) {
            return '-';
        }
        $tempat = $p->tempat_lahir ?: '-';
        if (! $p->tanggal_lahir) {
            return $tempat;
        }

        return $tempat . ', ' . $p->tanggal_lahir->copy()->locale('id')->translatedFormat('j F Y');
    }

    /**
     * Baris isian tambahan: [['label'=>..,'nilai'=>..|null,'multi'=>bool], ...].
     * Nilai diambil dari kolom data_tambahan (bila sudah ada pada tabel).
     * Isian wajib yang kosong tetap dicetak (nilai null -> titik-titik),
     * isian opsional yang kosong dilewati.
     */
    protected static function barisTambahan(PengajuanSurat $item, array $fields): array
    {
        $tersimpan = [];
        if (array_key_exists('data_tambahan', $item->getAttributes())) {
            $mentah = $item->getAttributes()['data_tambahan'];
            $tersimpan = is_array($mentah) ? $mentah : (json_decode((string) $mentah, true) ?: []);
        }

        $baris = [];
        foreach ($fields as $f) {
            $nilai = trim((string) ($tersimpan[$f['key']] ?? ''));
            $opsional = (bool) ($f['opsional'] ?? false);

            if ($nilai === '' && $opsional) {
                continue;
            }

            $tampil = null;
            if ($nilai !== '') {
                $tampil = match ($f['type'] ?? 'text') {
                    'date'  => self::formatTanggal($nilai),
                    'time'  => $nilai . ' WIB',
                    default => $nilai,
                };
            }

            $baris[] = [
                'label' => $f['label'],
                'nilai' => $tampil,
                'multi' => ($f['type'] ?? 'text') === 'textarea',
            ];
        }

        return $baris;
    }

    protected static function formatTanggal(string $nilai): string
    {
        try {
            return Carbon::parse($nilai)->locale('id')->translatedFormat('j F Y');
        } catch (\Throwable $e) {
            return $nilai;
        }
    }
}
