<?php

namespace Database\Seeders;

use App\Models\KategoriTransaksi;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\RekeningBendahara;
use App\Models\SetoranPengaturan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder DEMO: pengaturan keuangan terbaru per unit, yaitu rekening bendahara (tanpa QRIS),
 * Iuran Wajib RT (master iuran), setoran RT ke RW per KK, dan contoh Iuran Lainnya (tagihan)
 * beserta daftar tagihan per KK. Tanggal dihitung dari hari seeding. Idempoten.
 */
class KeuanganDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rt = fn (string $kode) => MasterRt::where('kode_rt', $kode)->value('id');
        $user = fn (string $username) => User::where('username', $username)->value('id');
        $kategori = fn (string $nama) => KategoriTransaksi::where('nama', $nama)->value('id');
        $tahun = (int) now()->format('Y');

        // --- Rekening bendahara (QRIS diunggah sendiri oleh operator) ---
        $rekening = [
            ['RT', '01', 'BSI', '7634523890', 'Budi Suseno', 'opkeuanganrt01rw21'],
            ['RT', '03', 'BSI', '0998988997', 'Bendahara RT 03', 'operator_rt3'],
            ['RW', null, 'BSI', '19090980389', 'Bendahara RW 21', 'opkeuanganrw21'],
            ['DKM', null, 'BCA', '34521980', 'DKM RW 21', 'dkm'],
        ];
        foreach ($rekening as [$unit, $kodeRt, $bank, $noRek, $nama, $pembuat]) {
            RekeningBendahara::updateOrCreate(
                ['unit' => $unit, 'rt_id' => $kodeRt ? $rt($kodeRt) : null],
                ['bank' => $bank, 'no_rek' => $noRek, 'nama_rek' => $nama, 'qris_file' => null, 'created_by' => $user($pembuat)]
            );
        }

        // --- Iuran Wajib RT (satu master per RT per tahun; riwayat nominal dibuat otomatis) ---
        foreach (['01', '03'] as $kodeRt) {
            if (! $rt($kodeRt)) {
                continue;
            }
            MasterIuran::firstOrCreate(
                ['rt_id' => $rt($kodeRt), 'tahun' => $tahun, 'nama' => 'Iuran Bulanan Warga'],
                ['nominal' => 60000, 'kategori_id' => $kategori('Iuran Bulanan Warga'), 'aktif' => true]
            );
        }

        // --- Setoran RT ke RW: nominal per KK (berlaku untuk semua RT) ---
        SetoranPengaturan::updateOrCreate(
            ['tahun' => $tahun, 'rt_id' => null],
            ['berlaku_mulai_bulan' => 1, 'nominal_per_kk' => 50000]
        );

        // --- Iuran Lainnya (tagihan insidental) ---
        $tagihan = [
            // judul, unit, kode RT, nominal, via RT, kategori, pembuat, tenggat (hari), pengingat
            ['Iuran Perbaikan Jalan', 'RT', '01', 40000, false, 'Iuran Kebersihan', 'opkeuanganrt01rw21', 55, ['1', '10']],
            ['Perbaikan Toa Masjid', 'RT', '03', 20000, false, 'Sumbangan & Donasi', 'operator_rt3', 17, ['11', '30']],
            ['Iuran Perbaikan Got', 'RW', null, 30000, true, 'Iuran Fasilitas', 'opkeuanganrw21', 48, ['28']],
            ['Iuran Kematian', 'DKM', null, 20000, false, 'Donasi Kematian', 'dkm', 7, []],
        ];
        foreach ($tagihan as [$judul, $unit, $kodeRt, $nominal, $viaRt, $kat, $pembuat, $hari, $pengingat]) {
            $rtId = $kodeRt ? $rt($kodeRt) : null;
            if (Tagihan::where('judul', $judul)->where('unit', $unit)->exists() || ($kodeRt && ! $rtId)) {
                continue;
            }
            $t = Tagihan::create([
                'id' => (string) Str::uuid(), 'judul' => $judul, 'deskripsi' => $viaRt ? 'Dikumpulkan melalui RT masing-masing.' : null,
                'jenis' => 'insidental', 'nominal' => $nominal, 'kategori_id' => $kategori($kat), 'nominal_fix' => false,
                'via_rt' => $viaRt, 'periode_bulan' => (int) now()->format('n'), 'periode_tahun' => $tahun,
                'unit' => $unit, 'rt_id' => $rtId, 'pembuat_id' => $user($pembuat), 'status' => 'Active',
                'tenggat' => now()->addDays($hari)->toDateString(), 'reminder_tanggal' => $pengingat,
            ]);

            // Satu baris tagihan per KK penerima (sama seperti saat operator membuat tagihan).
            $keluarga = $rtId ? Keluarga::where('rt_id', $rtId)->pluck('id') : Keluarga::pluck('id');
            PembayaranTagihan::insert($keluarga->map(fn ($id) => [
                'id' => (string) Str::uuid(), 'tagihan_id' => $t->id, 'keluarga_id' => $id,
                'status' => 'Unpaid', 'created_at' => now(), 'updated_at' => now(),
            ])->all());
        }
    }
}
