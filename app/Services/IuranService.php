<?php

namespace App\Services;

use App\Models\IuranPembayaran;
use App\Models\KategoriTransaksi;
use App\Models\MasterIuran;
use App\Models\TransaksiKeuangan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Logika inti iuran bulanan: menandai lunas (dan mencatat ke kas), membatalkan,
 * serta mengajukan/memverifikasi pembayaran dari warga.
 */
class IuranService
{
    public const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Tandai beberapa bulan sekaligus (batch) menjadi lunas oleh bendahara,
     * dicatat sebagai SATU transaksi kas gabungan. Bulan yang sudah lunas dilewati.
     *
     * @param int[] $bulanList
     * @return int jumlah bulan yang berhasil dicatat
     */
    public static function tandaiLunasBatch(MasterIuran $master, string $keluargaId, array $bulanList, array $data, ?string $olehUserId = null): int
    {
        return DB::transaction(function () use ($master, $keluargaId, $bulanList, $data, $olehUserId) {
            // Saring bulan yang belum lunas (hindari dobel catat).
            $sudahLunas = IuranPembayaran::where('master_iuran_id', $master->id)
                ->where('keluarga_id', $keluargaId)
                ->where('status', 'lunas')
                ->pluck('bulan')->all();

            $bulanBaru = array_values(array_filter(
                array_unique(array_map('intval', $bulanList)),
                fn ($b) => $b >= 1 && $b <= 12 && ! in_array($b, $sudahLunas, true)
            ));
            sort($bulanBaru);

            if (empty($bulanBaru)) {
                return 0;
            }

            $batchId = (string) Str::uuid();

            // Buat satu transaksi kas gabungan untuk seluruh bulan batch.
            $transaksi = self::catatKasBatch($master, $keluargaId, $bulanBaru, $data, $olehUserId);

            foreach ($bulanBaru as $bulan) {
                $bayar = IuranPembayaran::firstOrNew([
                    'master_iuran_id' => $master->id,
                    'keluarga_id'     => $keluargaId,
                    'bulan'           => $bulan,
                ]);
                $bayar->fill([
                    'status'        => 'lunas',
                    'tanggal_bayar' => $data['tanggal_bayar'] ?? now()->toDateString(),
                    'metode'        => $data['metode'] ?? 'tunai',
                    'bukti_file'    => $data['bukti_file'] ?? $bayar->bukti_file,
                    'sumber'        => $data['sumber'] ?? 'bendahara',
                    'dicatat_oleh'  => $olehUserId,
                    'catatan'       => $data['catatan'] ?? null,
                    'transaksi_id'  => $transaksi->id,
                    'batch_id'      => $batchId,
                ]);
                $bayar->save();
            }

            return count($bulanBaru);
        });
    }

    /**
     * Batalkan pembayaran. Bila sel bagian dari batch, SELURUH batch dibatalkan
     * (semua sel batch dihapus dan transaksi kas gabungannya dihapus).
     */
    public static function batalkanLunas(IuranPembayaran $bayar): void
    {
        DB::transaction(function () use ($bayar) {
            if ($bayar->batch_id) {
                $selBatch = IuranPembayaran::where('batch_id', $bayar->batch_id)->get();
                if ($bayar->transaksi_id) {
                    TransaksiKeuangan::where('id', $bayar->transaksi_id)->delete();
                }
                foreach ($selBatch as $s) {
                    $s->delete();
                }

                return;
            }

            // Sel tunggal (mis. hasil approve warga): hapus sel + transaksinya.
            if ($bayar->transaksi_id) {
                TransaksiKeuangan::where('id', $bayar->transaksi_id)->delete();
            }
            $bayar->delete();
        });
    }

    /**
     * Warga mengajukan beberapa bulan sekaligus (satu batch pengajuan).
     * Semua sel berstatus "menunggu" dengan batch_id sama; belum masuk kas.
     *
     * @param int[] $bulanList
     * @return int jumlah bulan yang diajukan
     */
    public static function ajukanWargaBatch(MasterIuran $master, string $keluargaId, array $bulanList, array $data): int
    {
        return DB::transaction(function () use ($master, $keluargaId, $bulanList, $data) {
            // Lewati bulan yang sudah lunas atau sudah menunggu.
            $terpakai = IuranPembayaran::where('master_iuran_id', $master->id)
                ->where('keluarga_id', $keluargaId)
                ->whereIn('status', ['lunas', 'menunggu'])
                ->pluck('bulan')->all();

            $bulanBaru = array_values(array_filter(
                array_unique(array_map('intval', $bulanList)),
                fn ($b) => $b >= 1 && $b <= 12 && ! in_array($b, $terpakai, true)
            ));
            sort($bulanBaru);

            if (empty($bulanBaru)) {
                return 0;
            }

            $batchId = (string) Str::uuid();
            foreach ($bulanBaru as $bulan) {
                $bayar = IuranPembayaran::firstOrNew([
                    'master_iuran_id' => $master->id,
                    'keluarga_id'     => $keluargaId,
                    'bulan'           => $bulan,
                ]);
                $bayar->fill([
                    'status'        => 'menunggu',
                    'tanggal_bayar' => $data['tanggal_bayar'] ?? now()->toDateString(),
                    'metode'        => $data['metode'] ?? 'transfer',
                    'bukti_file'    => $data['bukti_file'] ?? null,
                    'sumber'        => 'warga',
                    'catatan'       => $data['catatan'] ?? null,
                    'batch_id'      => $batchId,
                ]);
                $bayar->save();
            }

            return count($bulanBaru);
        });
    }

    /**
     * Bendahara menyetujui SELURUH batch pengajuan warga sekaligus: semua sel
     * batch jadi lunas dan dicatat sebagai satu transaksi kas gabungan.
     */
    public static function setujuiBatch(string $batchId, ?string $olehUserId = null): int
    {
        return DB::transaction(function () use ($batchId, $olehUserId) {
            $sel = IuranPembayaran::where('batch_id', $batchId)->where('status', 'menunggu')->get();
            if ($sel->isEmpty()) {
                return 0;
            }
            $master = MasterIuran::find($sel->first()->master_iuran_id);
            $keluargaId = $sel->first()->keluarga_id;
            $bulanList = $sel->pluck('bulan')->sort()->values()->all();

            $transaksi = self::catatKasBatch($master, $keluargaId, $bulanList, [
                'tanggal_bayar' => $sel->first()->tanggal_bayar,
            ], $olehUserId);

            foreach ($sel as $s) {
                $s->fill(['status' => 'lunas', 'dicatat_oleh' => $olehUserId, 'transaksi_id' => $transaksi->id])->save();
            }

            return $sel->count();
        });
    }

    /** Tolak seluruh batch pengajuan warga (hapus sel menunggu dalam batch). */
    public static function tolakBatch(string $batchId, ?string $alasan = null): void
    {
        // Baris dipertahankan berstatus "ditolak" agar warga dapat melihat alasannya
        // dan mengajukan ulang. Tidak masuk kas.
        IuranPembayaran::where('batch_id', $batchId)->where('status', 'menunggu')
            ->update(['status' => 'ditolak', 'catatan' => $alasan]);
    }

    /** Warga mengajukan pembayaran satu bulan (status menunggu, belum masuk kas). */
    public static function ajukanWarga(MasterIuran $master, string $keluargaId, int $bulan, array $data): IuranPembayaran
    {
        $bayar = IuranPembayaran::firstOrNew([
            'master_iuran_id' => $master->id,
            'keluarga_id'     => $keluargaId,
            'bulan'           => $bulan,
        ]);

        // Tidak menimpa yang sudah lunas.
        if ($bayar->status === 'lunas' && $bayar->transaksi_id) {
            return $bayar;
        }

        $bayar->fill([
            'status'        => 'menunggu',
            'tanggal_bayar' => $data['tanggal_bayar'] ?? now()->toDateString(),
            'metode'        => $data['metode'] ?? 'transfer',
            'bukti_file'    => $data['bukti_file'] ?? $bayar->bukti_file,
            'sumber'        => 'warga',
            'catatan'       => $data['catatan'] ?? null,
        ]);
        $bayar->save();

        return $bayar;
    }

    /** Bendahara menyetujui pengajuan warga → jadi lunas + masuk kas. */
    public static function setujuiPengajuan(IuranPembayaran $bayar, ?string $olehUserId = null): IuranPembayaran
    {
        return DB::transaction(function () use ($bayar, $olehUserId) {
            if ($bayar->status === 'lunas') {
                return $bayar;
            }
            $bayar->status = 'lunas';
            $bayar->dicatat_oleh = $olehUserId;
            $bayar->save();
            $bayar->transaksi_id = self::catatKas($bayar->master, $bayar)->id;
            $bayar->save();

            return $bayar;
        });
    }

    /** Bendahara menolak pengajuan warga → baris dihapus (sel kembali kosong). */
    public static function tolakPengajuan(IuranPembayaran $bayar, ?string $catatan = null): void
    {
        $bayar->update(['status' => 'ditolak', 'catatan' => $catatan]);
    }

    /** Buat satu transaksi kas gabungan untuk beberapa bulan sekaligus. */
    private static function catatKasBatch(MasterIuran $master, string $keluargaId, array $bulanList, array $data, ?string $olehUserId): TransaksiKeuangan
    {
        $kategori = $master->kategori ?: self::kategoriDefault();
        $namaKk = self::namaKeluarga($keluargaId);
        $labelBulan = implode(', ', array_map(fn ($b) => self::BULAN[$b] ?? $b, $bulanList));
        $jumlahBulan = count($bulanList);
        $total = array_sum(array_map(fn ($b) => $master->nominalUntukBulan((int) $b), $bulanList));

        return TransaksiKeuangan::create([
            'kode_transaksi' => 'IUR-' . strtoupper(Str::random(8)),
            'tipe'           => 'pemasukan',
            'kategori'       => $kategori?->nama ?? 'Iuran Bulanan Warga',
            'kategori_id'    => $kategori?->id,
            'judul'          => "Iuran {$labelBulan} {$master->tahun}" . ($namaKk ? " - {$namaKk}" : ''),
            'deskripsi'      => "Pembayaran iuran bulanan ({$jumlahBulan} bulan: {$labelBulan} {$master->tahun}).",
            'jumlah'         => $total,
            'tanggal'        => $data['tanggal_bayar'] ?? now()->toDateString(),
            'status'         => 'Verified',
            'unit_sumber'    => 'RT',
            'rt_id'          => $master->rt_id,
            'dicatat_oleh'   => $olehUserId,
        ]);
    }

    /** Buat transaksi kas pemasukan untuk satu pembayaran iuran (per sel). */
    private static function catatKas(MasterIuran $master, IuranPembayaran $bayar): TransaksiKeuangan
    {
        $kategori = $master->kategori ?: self::kategoriDefault();
        $namaBulan = self::BULAN[$bayar->bulan] ?? $bayar->bulan;
        $namaKk = self::namaKeluarga($bayar->keluarga_id);

        return TransaksiKeuangan::create([
            'kode_transaksi' => 'IUR-' . strtoupper(Str::random(8)),
            'tipe'           => 'pemasukan',
            'kategori'       => $kategori?->nama ?? 'Iuran Bulanan Warga',
            'kategori_id'    => $kategori?->id,
            'judul'          => "Iuran {$namaBulan} {$master->tahun}" . ($namaKk ? " - {$namaKk}" : ''),
            'deskripsi'      => "Pembayaran iuran bulanan ({$namaBulan} {$master->tahun}).",
            'jumlah'         => $master->nominalUntukBulan((int) $bayar->bulan),
            'tanggal'        => $bayar->tanggal_bayar ?? now()->toDateString(),
            'status'         => 'Verified',
            'unit_sumber'    => 'RT',
            'rt_id'          => $master->rt_id,
            'dicatat_oleh'   => $bayar->dicatat_oleh,
        ]);
    }

    /** Nama kepala keluarga untuk keterangan kas; fallback ke No. KK. */
    private static function namaKeluarga(string $keluargaId): ?string
    {
        $keluarga = \App\Models\Keluarga::with('kepalaKeluarga')->find($keluargaId);

        return optional(optional($keluarga)->kepalaKeluarga)->nama_lengkap ?: optional($keluarga)->no_kk;
    }

    /** Kategori pemasukan default bila master belum memilih kategori. */
    private static function kategoriDefault(): ?KategoriTransaksi
    {
        return KategoriTransaksi::where('tipe', 'pemasukan')->where('aktif', true)->orderBy('urutan')->first();
    }
}
