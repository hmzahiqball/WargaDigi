<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransaksiKeuangan;
use App\Models\MasterRt;
use App\Models\User;

class TransaksiKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $rt01 = MasterRt::where('kode_rt', '01')->first();
        $opRw = User::where('username', 'opkeuanganrw21')->first();
        $opRt = User::where('username', 'opkeuanganrt01rw21')->first();
        $dkm = User::where('username', 'dkm')->first();

        // Transaksi RT
        if ($rt01 && $opRt) {
            TransaksiKeuangan::create([
                'kode_transaksi' => 'TRX-RT01-2609-001',
                'tipe' => 'pemasukan',
                'kategori' => 'Iuran Warga',
                'judul' => 'Iuran Warga Bulanan - Blok A',
                'deskripsi' => 'Iuran bulanan warga RT 01 Blok A untuk bulan September',
                'jumlah' => 250000,
                'tanggal' => now()->subDays(2),
                'status' => 'Verified',
                'unit_sumber' => 'RT',
                'rt_id' => $rt01->id,
                'dicatat_oleh' => $opRt->id,
                'diverifikasi_oleh' => $opRt->id,
                'tanggal_verifikasi' => now()->subDays(1),
            ]);
            
            TransaksiKeuangan::create([
                'kode_transaksi' => 'TRX-RT01-2609-002',
                'tipe' => 'pengeluaran',
                'kategori' => 'Setoran ke RW',
                'judul' => 'Setoran Kas RT ke RW',
                'deskripsi' => 'Setoran persentase kas ke bendahara RW',
                'jumlah' => 100000,
                'tanggal' => now()->subDays(1),
                'status' => 'Pending',
                'unit_sumber' => 'RT',
                'rt_id' => $rt01->id,
                'dicatat_oleh' => $opRt->id,
            ]);
        }

        // Transaksi RW
        if ($opRw) {
            TransaksiKeuangan::create([
                'kode_transaksi' => 'TRX-RW21-2609-001',
                'tipe' => 'pengeluaran',
                'kategori' => 'Operasional',
                'judul' => 'Pembayaran Listrik Fasum',
                'deskripsi' => 'Bayar listrik fasilitas umum RW 21',
                'jumlah' => 1240000,
                'tanggal' => now()->subDays(5),
                'status' => 'Verified',
                'unit_sumber' => 'RW',
                'dicatat_oleh' => $opRw->id,
                'diverifikasi_oleh' => clone $opRw->id, // dummy verifikator
                'tanggal_verifikasi' => now()->subDays(4),
            ]);
        }

        // Transaksi DKM
        if ($dkm) {
            TransaksiKeuangan::create([
                'kode_transaksi' => 'TRX-DKM-2609-001',
                'tipe' => 'pemasukan',
                'kategori' => 'Dana Kematian',
                'judul' => 'Sumbangan Dana Kematian',
                'deskripsi' => 'Sumbangan dari donatur anonim',
                'jumlah' => 500000,
                'tanggal' => now()->subDays(1),
                'status' => 'Pending',
                'unit_sumber' => 'DKM',
                'dicatat_oleh' => $dkm->id,
            ]);
        }
    }
}
