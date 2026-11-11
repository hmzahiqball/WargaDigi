<?php

namespace Database\Seeders;

use App\Models\KategoriTransaksi;
use Illuminate\Database\Seeder;

/**
 * Kategori transaksi umum (unit = null) yang dikelola Admin Aplikasi. Operator
 * RT/RW/DKM dapat menambah kategori khusus unitnya lewat halaman masing-masing.
 *
 * Dibuat idempotent (updateOrCreate) agar aman dijalankan ulang.
 */
class KategoriTransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            // --- Pemasukan ---
            ['nama' => 'Iuran Bulanan Warga',   'tipe' => 'pemasukan',   'urutan' => 1],
            ['nama' => 'Iuran Kebersihan',      'tipe' => 'pemasukan',   'urutan' => 2],
            ['nama' => 'Iuran Keamanan',        'tipe' => 'pemasukan',   'urutan' => 3],
            ['nama' => 'Sumbangan & Donasi',    'tipe' => 'pemasukan',   'urutan' => 4],
            ['nama' => 'Dana Sosial / Kematian','tipe' => 'pemasukan',   'urutan' => 5],
            ['nama' => 'Hasil Sewa Fasilitas',  'tipe' => 'pemasukan',   'urutan' => 6],
            ['nama' => 'Bantuan Pemerintah',    'tipe' => 'pemasukan',   'urutan' => 7],
            ['nama' => 'Setoran Iuran RT',      'tipe' => 'pemasukan',   'urutan' => 8],

            // --- Pengeluaran ---
            ['nama' => 'Operasional & ATK',         'tipe' => 'pengeluaran', 'urutan' => 20],
            ['nama' => 'Kebersihan & Sampah',       'tipe' => 'pengeluaran', 'urutan' => 21],
            ['nama' => 'Keamanan & Ronda',          'tipe' => 'pengeluaran', 'urutan' => 22],
            ['nama' => 'Perbaikan & Pemeliharaan',  'tipe' => 'pengeluaran', 'urutan' => 23],
            ['nama' => 'Kegiatan & Acara Warga',    'tipe' => 'pengeluaran', 'urutan' => 24],
            ['nama' => 'Santunan & Bantuan Sosial', 'tipe' => 'pengeluaran', 'urutan' => 25],
            ['nama' => 'Listrik & Air',             'tipe' => 'pengeluaran', 'urutan' => 26],
            ['nama' => 'Setoran ke RW',             'tipe' => 'pengeluaran', 'urutan' => 27],

            // --- Keduanya (netral) ---
            ['nama' => 'Penyesuaian / Koreksi', 'tipe' => 'keduanya', 'urutan' => 40],
            ['nama' => 'Lain-lain',             'tipe' => 'keduanya', 'urutan' => 41],
        ];

        foreach ($kategori as $item) {
            KategoriTransaksi::updateOrCreate(
                ['nama' => $item['nama'], 'unit' => null, 'rt_id' => null],
                [
                    'tipe'   => $item['tipe'],
                    'aktif'  => true,
                    'urutan' => $item['urutan'],
                ]
            );
        }

        // Backfill kategori_id pada transaksi lama berdasarkan kecocokan nama.
        if (\Illuminate\Support\Facades\Schema::hasColumn('transaksi_keuangan', 'kategori_id')) {
            foreach (KategoriTransaksi::all() as $kat) {
                \App\Models\TransaksiKeuangan::whereNull('kategori_id')
                    ->where('kategori', $kat->nama)
                    ->update(['kategori_id' => $kat->id]);
            }
        }
    }
}
