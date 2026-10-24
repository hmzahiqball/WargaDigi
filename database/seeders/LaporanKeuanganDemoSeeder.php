<?php

namespace Database\Seeders;

use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder DEMO (showcase): membuat beberapa LaporanKeuangan yang sudah
 * Approved & is_published=true untuk mengisi halaman Transparansi publik.
 *
 * Mencakup 2-3 periode terakhir untuk unit RW, DKM, dan RT 01.
 * Idempoten: updateOrCreate by (unit, periode_bulan, periode_tahun, rt_id).
 */
class LaporanKeuanganDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rt01 = MasterRt::where('kode_rt', '01')->first();

        // Pembuat laporan: operator keuangan. Penyetuju: pimpinan/ketua.
        $pembuat = User::where('username', 'opkeuanganrw21')->first()
            ?? User::where('role', 'Op Keuangan RW')->first()
            ?? User::first();

        $penyetuju = User::where('username', 'pimpinan')->first()
            ?? User::where('role', 'Pimpinan RW')->first()
            ?? User::where('role', 'Admin RW')->first()
            ?? User::first();

        $pembuatId = $pembuat?->id;
        $penyetujuId = $penyetuju?->id;

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        // Ambil 3 periode terakhir (bulan ini dan dua bulan sebelumnya).
        $periode = [];
        $cursor = now()->startOfMonth();
        for ($i = 0; $i < 3; $i++) {
            $periode[] = ['bulan' => (int) $cursor->format('n'), 'tahun' => (int) $cursor->format('Y')];
            $cursor = $cursor->copy()->subMonth();
        }

        // Definisi unit beserta angka yang wajar & konsisten.
        $units = [
            [
                'unit' => 'RW',
                'rt_id' => null,
                'saldo_awal' => 15_000_000,
                'pemasukan' => 4_200_000,
                'pengeluaran' => 1_850_000,
            ],
            [
                'unit' => 'DKM',
                'rt_id' => null,
                'saldo_awal' => 8_500_000,
                'pemasukan' => 2_300_000,
                'pengeluaran' => 1_100_000,
            ],
            [
                'unit' => 'RT',
                'rt_id' => $rt01?->id,
                'saldo_awal' => 3_200_000,
                'pemasukan' => 1_250_000,
                'pengeluaran' => 600_000,
            ],
        ];

        foreach ($units as $u) {
            // Lewati laporan RT bila RT 01 tidak tersedia.
            if ($u['unit'] === 'RT' && empty($u['rt_id'])) {
                continue;
            }

            $saldoAwal = $u['saldo_awal'];

            foreach ($periode as $p) {
                $pemasukan = $u['pemasukan'];
                $pengeluaran = $u['pengeluaran'];
                $saldoAkhir = $saldoAwal + $pemasukan - $pengeluaran; // konsisten

                $bulanLabel = $namaBulan[$p['bulan']] ?? $p['bulan'];
                $judul = 'Laporan Keuangan ' . $u['unit'] . ' ' . $bulanLabel . ' ' . $p['tahun'];

                LaporanKeuangan::updateOrCreate(
                    [
                        'unit' => $u['unit'],
                        'periode_bulan' => $p['bulan'],
                        'periode_tahun' => $p['tahun'],
                        'rt_id' => $u['rt_id'],
                    ],
                    [
                        'judul' => $judul,
                        'is_published' => true,
                        'total_pemasukan' => $pemasukan,
                        'total_pengeluaran' => $pengeluaran,
                        'saldo_awal' => $saldoAwal,
                        'saldo_akhir' => $saldoAkhir,
                        'status' => 'Approved',
                        'catatan' => 'Laporan keuangan periode ' . $bulanLabel . ' ' . $p['tahun'] . ' telah diverifikasi.',
                        'dibuat_oleh' => $pembuatId,
                        'disetujui_oleh' => $penyetujuId,
                        'tanggal_disetujui' => now(),
                    ]
                );

                // Saldo akhir periode ini menjadi saldo awal periode berikutnya
                // (periode diurut dari terbaru ke terlama, jadi mundur).
                $saldoAwal = $saldoAkhir - $pemasukan + $pengeluaran;
            }
        }
    }
}
