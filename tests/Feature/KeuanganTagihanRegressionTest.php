<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression tests untuk perbaikan bug keuangan & tagihan:
 * - IR26: transaksi terkunci bila periode sudah masuk laporan Submitted/Approved
 * - IR27: isolasi laporan keuangan antar unit (RT tidak bisa hapus laporan DKM/RW)
 * - IR39: pembuatan tagihan rutin manual + command generate rutin
 * - IR44: tagihan insidental RT menyebar ke keluarga RT; ditolak bila 0 keluarga
 */
class KeuanganTagihanRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function createRt(string $kode, string $nama): MasterRt
    {
        return MasterRt::create(['kode_rt' => $kode, 'nama_rt' => $nama]);
    }

    private function createFamily(MasterRt $rt, string $nik, string $nama): Keluarga
    {
        $family = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $family->id,
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $family;
    }

    private function bendaharaRt(MasterRt $rt, string $nik = '3217010101019901'): User
    {
        return User::create([
            'nik' => $nik,
            'username' => 'Bendahara RT',
            'role' => 'Op Keuangan RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
    }

    // ───────────────────────── IR26 ─────────────────────────

    public function test_ir26_transaksi_terkunci_tidak_dapat_dihapus_bila_periode_sudah_dilaporkan(): void
    {
        $rt = $this->createRt('101', 'RT 101');
        $bendahara = $this->bendaharaRt($rt);

        $tx = TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-TEST-1',
            'tipe' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'judul' => 'Iuran Terkunci',
            'jumlah' => 100000,
            'tanggal' => '2026-03-10',
            'status' => 'Verified',
            'unit_sumber' => 'RT',
            'rt_id' => $rt->id,
        ]);

        // Laporan periode Maret 2026 sudah diajukan (Submitted)
        LaporanKeuangan::create([
            'judul' => 'Laporan RT Maret 2026',
            'periode_bulan' => 3,
            'periode_tahun' => 2026,
            'unit' => 'RT',
            'rt_id' => $rt->id,
            'status' => 'Submitted',
        ]);

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.transaksi.destroy', $tx->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('transaksi_keuangan', ['id' => $tx->id]);
    }

    public function test_ir26_transaksi_terkunci_tidak_dapat_diedit(): void
    {
        $rt = $this->createRt('102', 'RT 102');
        $bendahara = $this->bendaharaRt($rt, '3217010101019902');

        $tx = TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-TEST-2',
            'tipe' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'judul' => 'Iuran Edit',
            'jumlah' => 100000,
            'tanggal' => '2026-03-10',
            'status' => 'Verified',
            'unit_sumber' => 'RT',
            'rt_id' => $rt->id,
        ]);

        LaporanKeuangan::create([
            'judul' => 'Laporan RT Maret 2026',
            'periode_bulan' => 3,
            'periode_tahun' => 2026,
            'unit' => 'RT',
            'rt_id' => $rt->id,
            'status' => 'Approved',
        ]);

        $kat = \App\Models\KategoriTransaksi::create(['nama' => 'Iuran Warga', 'tipe' => 'pemasukan', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->put(route('opkeuangan.transaksi.update', $tx->id), [
                'tipe' => 'pemasukan',
                'judul' => 'Diubah',
                'kategori_id' => $kat->id,
                'tanggal' => '2026-03-10',
                'jumlah' => 999999,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('transaksi_keuangan', [
            'id' => $tx->id,
            'jumlah' => 100000,
            'judul' => 'Iuran Edit',
        ]);
    }

    public function test_ir26_transaksi_periode_tanpa_laporan_masih_dapat_dihapus(): void
    {
        $rt = $this->createRt('103', 'RT 103');
        $bendahara = $this->bendaharaRt($rt, '3217010101019903');

        $tx = TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-TEST-3',
            'tipe' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'judul' => 'Iuran Bebas',
            'jumlah' => 100000,
            'tanggal' => '2026-05-10',
            'status' => 'Verified',
            'unit_sumber' => 'RT',
            'rt_id' => $rt->id,
        ]);

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.transaksi.destroy', $tx->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('transaksi_keuangan', ['id' => $tx->id]);
    }

    public function test_transaksi_dengan_nominal_berformat_tersimpan_sebagai_angka_murni(): void
    {
        $rt = $this->createRt('120', 'RT 120');
        $bendahara = $this->bendaharaRt($rt, '3217010101019920');

        $kat = \App\Models\KategoriTransaksi::create(['nama' => 'Iuran Warga', 'tipe' => 'pemasukan', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan',
                'judul' => 'Transaksi Berformat',
                'kategori_id' => $kat->id,
                'tanggal' => '2026-07-10',
                'jumlah' => '100.000',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('transaksi_keuangan', [
            'judul' => 'Transaksi Berformat',
            'jumlah' => 100000,
        ]);
    }

    // ───────────────────────── IR27 ─────────────────────────

    public function test_ir27_bendahara_rt_tidak_dapat_menghapus_laporan_dkm(): void
    {
        $rt = $this->createRt('104', 'RT 104');
        $bendahara = $this->bendaharaRt($rt, '3217010101019904');

        $laporanDkm = LaporanKeuangan::create([
            'judul' => 'Laporan DKM',
            'periode_bulan' => 3,
            'periode_tahun' => 2026,
            'unit' => 'DKM',
            'status' => 'Submitted',
        ]);

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.laporan.destroy', $laporanDkm->id))
            ->assertForbidden();

        $this->assertDatabaseHas('laporan_keuangan', ['id' => $laporanDkm->id]);
    }

    public function test_ir27_daftar_laporan_bendahara_rt_hanya_menampilkan_unit_rt(): void
    {
        $rt = $this->createRt('105', 'RT 105');
        $bendahara = $this->bendaharaRt($rt, '3217010101019905');

        LaporanKeuangan::create([
            'judul' => 'Laporan RT Sendiri',
            'periode_bulan' => 4, 'periode_tahun' => 2026,
            'unit' => 'RT', 'rt_id' => $rt->id, 'status' => 'Submitted',
        ]);
        LaporanKeuangan::create([
            'judul' => 'Laporan DKM Rahasia',
            'periode_bulan' => 4, 'periode_tahun' => 2026,
            'unit' => 'DKM', 'status' => 'Submitted',
        ]);

        $this->actingAs($bendahara)
            ->get(route('opkeuangan.laporan.index'))
            ->assertOk()
            ->assertSee('Laporan RT Sendiri')
            ->assertDontSee('Laporan DKM Rahasia');
    }

    // ───────────────────────── IR44 ─────────────────────────

    public function test_ir44_tagihan_insidental_rt_menyebar_ke_keluarga_rt(): void
    {
        $rt = $this->createRt('106', 'RT 106');
        $bendahara = $this->bendaharaRt($rt, '3217010101019906');
        $this->createFamily($rt, '3217010106000001', 'Keluarga A');
        $this->createFamily($rt, '3217010106000002', 'Keluarga B');

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.tagihan.store'), [
                'judul' => 'Iuran Kerja Bakti',
                'nominal' => 25000,
                'tenggat' => '2026-12-31',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $tagihan = Tagihan::where('judul', 'Iuran Kerja Bakti')->first();
        $this->assertNotNull($tagihan);
        $this->assertSame('insidental', $tagihan->jenis);
        $this->assertSame('RT', $tagihan->unit);
        $this->assertSame($rt->id, $tagihan->rt_id);
        $this->assertSame(2, $tagihan->pembayaran()->count());
    }

    public function test_nominal_berformat_ribuan_dinormalisasi_menjadi_angka_murni(): void
    {
        $rt = $this->createRt('110', 'RT 110');
        $bendahara = $this->bendaharaRt($rt, '3217010101019910');
        $this->createFamily($rt, '3217010110000001', 'Keluarga Format');

        // Nominal dikirim dengan pemisah ribuan ala number formatter ("1.250.000").
        $this->actingAs($bendahara)
            ->post(route('opkeuangan.tagihan.store'), [
                'judul' => 'Iuran Berformat',
                'nominal' => '1.250.000',
                'tenggat' => '2026-12-31',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tagihan', [
            'judul' => 'Iuran Berformat',
            'nominal' => 1250000,
        ]);
    }

    public function test_ir44_tagihan_rt_ditolak_bila_tidak_ada_keluarga(): void
    {
        $rt = $this->createRt('107', 'RT 107');
        $bendahara = $this->bendaharaRt($rt, '3217010101019907');
        // Tidak membuat keluarga apa pun di RT ini.

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.tagihan.store'), [
                'judul' => 'Tagihan Kosong',
                'nominal' => 25000,
                'tenggat' => '2026-12-31',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('tagihan', ['judul' => 'Tagihan Kosong']);
    }

    // ───────────────────────── Laporan Keuangan ─────────────────────────

    public function test_laporan_saldo_boleh_negatif_saat_defisit(): void
    {
        $rt = $this->createRt('140', 'RT 140');
        $bendahara = $this->bendaharaRt($rt, '3217010101019940');

        // Pengeluaran > pemasukan pada periode → saldo akhir negatif.
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-DEF-1', 'tipe' => 'pengeluaran', 'kategori' => 'Operasional',
            'judul' => 'Belanja Besar', 'jumlah' => 500000, 'tanggal' => '2026-04-10',
            'status' => 'Verified', 'unit_sumber' => 'RT', 'rt_id' => $rt->id,
        ]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.laporan.store'), ['bulan' => 4, 'tahun' => 2026])
            ->assertRedirect();

        $lap = \App\Models\LaporanKeuangan::where('unit', 'RT')->where('rt_id', $rt->id)
            ->where('periode_bulan', 4)->first();
        $this->assertSame(-500000, (int) $lap->saldo_akhir);
    }

    public function test_warga_tidak_melihat_dan_tidak_bisa_unduh_pdf_laporan(): void
    {
        $rt = $this->createRt('143', 'RT 143');
        $bendahara = $this->bendaharaRt($rt, '3217010101019943');
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-WRG-1', 'tipe' => 'pemasukan', 'kategori' => 'Iuran',
            'judul' => 'Iuran', 'jumlah' => 100000, 'tanggal' => '2026-07-05',
            'status' => 'Verified', 'unit_sumber' => 'RT', 'rt_id' => $rt->id,
        ]);
        $this->actingAs($bendahara)->post(route('opkeuangan.laporan.store'), ['bulan' => 7, 'tahun' => 2026]);
        $lap = \App\Models\LaporanKeuangan::where('rt_id', $rt->id)->where('periode_bulan', 7)->first();
        $lap->update(['status' => 'Approved', 'is_published' => true]);

        \Spatie\Permission\Models\Role::findOrCreate('Warga', 'web');
        $warga = \App\Models\User::create([
            'nik' => '3217010101019944', 'username' => 'wargalap', 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $warga->syncRoles(['Warga']);

        // Halaman tampil tanpa tombol PDF, tetapi angka tetap ada.
        $this->actingAs($warga)
            ->get(route('laporan-keuangan.publik'))
            ->assertOk()
            ->assertDontSee(route('laporan-keuangan.pdf', $lap->id))
            ->assertSee('Pemasukan')
            ->assertSee('Rp 100.000')
            ->assertSee('Uang masuk dari mana')
            ->assertDontSee($bendahara->username);

        // Akses langsung ke URL PDF/Excel ditolak untuk Warga.
        $this->actingAs($warga)->get(route('laporan-keuangan.pdf', $lap->id))->assertForbidden();
        $this->actingAs($warga)->get(route('laporan-keuangan.excel', $lap->id))->assertForbidden();

        // Peran pengurus tetap dapat mengunduh.
        $this->actingAs($bendahara)->get(route('laporan-keuangan.pdf', $lap->id))->assertOk();
    }

    public function test_download_excel_laporan(): void
    {
        $rt = $this->createRt('141', 'RT 141');
        $bendahara = $this->bendaharaRt($rt, '3217010101019941');
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-XLS-1', 'tipe' => 'pemasukan', 'kategori' => 'Iuran Bulanan Warga',
            'judul' => 'Iuran', 'jumlah' => 90000, 'tanggal' => '2026-05-10',
            'status' => 'Verified', 'unit_sumber' => 'RT', 'rt_id' => $rt->id,
        ]);
        $this->actingAs($bendahara)->post(route('opkeuangan.laporan.store'), ['bulan' => 5, 'tahun' => 2026]);
        $lap = \App\Models\LaporanKeuangan::where('rt_id', $rt->id)->where('periode_bulan', 5)->first();

        $res = $this->actingAs($bendahara)->get(route('laporan-keuangan.excel', $lap->id));
        $res->assertOk();
        $this->assertStringContainsString('spreadsheetml', $res->headers->get('content-type'));
    }

    public function test_pdf_laporan_menampilkan_saldo_berjalan(): void
    {
        $rt = $this->createRt('142', 'RT 142');
        $bendahara = $this->bendaharaRt($rt, '3217010101019942');
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-PDF-1', 'tipe' => 'pemasukan', 'kategori' => 'Iuran',
            'judul' => 'Iuran Masuk', 'jumlah' => 100000, 'tanggal' => '2026-06-05',
            'status' => 'Verified', 'unit_sumber' => 'RT', 'rt_id' => $rt->id,
        ]);
        $this->actingAs($bendahara)->post(route('opkeuangan.laporan.store'), ['bulan' => 6, 'tahun' => 2026]);
        $lap = \App\Models\LaporanKeuangan::where('rt_id', $rt->id)->where('periode_bulan', 6)->first();

        $this->actingAs($bendahara)
            ->get(route('laporan-keuangan.pdf', $lap->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    // ───────────────────────── Iuran Lainnya ─────────────────────────

    public function test_ceklis_bayar_iuran_lainnya_masuk_kas(): void
    {
        $rt = $this->createRt('130', 'RT 130');
        $bendahara = $this->bendaharaRt($rt, '3217010101019930');
        $fam = $this->createFamily($rt, '3217011300000001', 'Keluarga Lainnya');

        $this->actingAs($bendahara)->post(route('opkeuangan.tagihan.store'), [
            'judul' => 'Iuran 17 Agustus', 'nominal' => 50000, 'tenggat' => '2026-08-20',
        ])->assertRedirect();

        $tagihan = \App\Models\Tagihan::where('judul', 'Iuran 17 Agustus')->first();
        $bayar = \App\Models\PembayaranTagihan::where('tagihan_id', $tagihan->id)->first();

        // Ceklis bayar dengan nominal lebih (75000, di atas minimal 50000).
        $this->actingAs($bendahara)->put(route('opkeuangan.pembayaran.ceklis', $bayar->id), [
            'nominal' => '75.000', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'Cash',
        ])->assertRedirect();

        $bayar->refresh();
        $this->assertSame('Paid', $bayar->status);
        $this->assertSame(75000, (int) $bayar->nominal);
        $this->assertNotNull($bayar->transaksi_id);
        $this->assertDatabaseHas('transaksi_keuangan', ['id' => $bayar->transaksi_id, 'jumlah' => 75000, 'tipe' => 'pemasukan']);
    }

    public function test_ceklis_nominal_kurang_dari_minimal_ditolak(): void
    {
        $rt = $this->createRt('131', 'RT 131');
        $bendahara = $this->bendaharaRt($rt, '3217010101019931');
        $this->createFamily($rt, '3217011310000001', 'Keluarga Min');

        $this->actingAs($bendahara)->post(route('opkeuangan.tagihan.store'), [
            'judul' => 'Iuran Minimal', 'nominal' => 50000,
        ])->assertRedirect();
        $tagihan = \App\Models\Tagihan::where('judul', 'Iuran Minimal')->first();
        $bayar = \App\Models\PembayaranTagihan::where('tagihan_id', $tagihan->id)->first();

        $this->actingAs($bendahara)->put(route('opkeuangan.pembayaran.ceklis', $bayar->id), [
            'nominal' => '30.000', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'Cash',
        ])->assertSessionHas('error');

        $this->assertSame('Unpaid', $bayar->fresh()->status);
    }

    public function test_transaksi_iuran_lainnya_terkunci_di_buku_kas(): void
    {
        $rt = $this->createRt('132', 'RT 132');
        $bendahara = $this->bendaharaRt($rt, '3217010101019932');
        $this->createFamily($rt, '3217011320000001', 'Keluarga Kunci');

        $this->actingAs($bendahara)->post(route('opkeuangan.tagihan.store'), [
            'judul' => 'Iuran Kunci', 'nominal' => 50000, 'nominal_fix' => '1',
        ])->assertRedirect();
        $tagihan = \App\Models\Tagihan::where('judul', 'Iuran Kunci')->first();
        $bayar = \App\Models\PembayaranTagihan::where('tagihan_id', $tagihan->id)->first();
        $this->actingAs($bendahara)->put(route('opkeuangan.pembayaran.ceklis', $bayar->id), [
            'nominal' => '50.000', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'Cash',
        ]);
        $txId = $bayar->fresh()->transaksi_id;

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.transaksi.destroy', $txId))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('transaksi_keuangan', ['id' => $txId]);
    }

    // ───────────────────────── IR39 ─────────────────────────

    public function test_ir39_tagihan_rutin_dapat_dibuat_manual(): void
    {
        $rt = $this->createRt('108', 'RT 108');
        $bendahara = $this->bendaharaRt($rt, '3217010101019908');
        $this->createFamily($rt, '3217010108000001', 'Keluarga Rutin');

        // Jalur "rutin" di form tagihan dihapus; iuran rutin kini ditangani modul
        // Iuran Wajib. Tagihan yang dibuat lewat store selalu 'insidental'.
        $this->actingAs($bendahara)
            ->post(route('opkeuangan.tagihan.store'), [
                'judul' => 'Iuran Bulanan Manual',
                'nominal' => 50000,
                'tenggat' => '2026-12-31',
                'jenis' => 'rutin', // diabaikan controller
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tagihan', [
            'judul' => 'Iuran Bulanan Manual',
            'jenis' => 'insidental',
            'unit' => 'RT',
        ]);
    }

    public function test_ir39_command_generate_rutin_membuat_tagihan_dan_menyebar(): void
    {
        $rt = $this->createRt('109', 'RT 109');
        $this->createFamily($rt, '3217010109000001', 'Keluarga Command');

        $this->artisan('tagihan:generate-rutin')->assertExitCode(0);

        $tagihanRt = Tagihan::where('unit', 'RT')->where('jenis', 'rutin')->first();
        $this->assertNotNull($tagihanRt, 'Tagihan rutin RT harus dibuat oleh command.');
        $this->assertGreaterThan(0, $tagihanRt->pembayaran()->count());

        $this->assertDatabaseHas('tagihan', ['unit' => 'DKM', 'jenis' => 'rutin']);
    }

    public function test_daftar_laporan_keuangan_pengurus_berbentuk_tabel_berpaginasi_dengan_tombol_pdf(): void
    {
        $rt = $this->createRt('144', 'RT 144');
        $bendahara = $this->bendaharaRt($rt, '3217010101019944');
        for ($b = 1; $b <= 13; $b++) {
            \App\Models\LaporanKeuangan::create([
                'judul' => 'Laporan Uji ' . $b, 'periode_bulan' => ($b % 12) + 1, 'periode_tahun' => 2020 + intdiv($b, 12),
                'unit' => 'RT', 'rt_id' => $rt->id, 'total_pemasukan' => 100000 * $b, 'total_pengeluaran' => 25000,
                'saldo_awal' => 0, 'saldo_akhir' => 100000 * $b - 25000, 'status' => 'Approved', 'is_published' => true,
                'dibuat_oleh' => $bendahara->id,
            ]);
        }

        $res = $this->actingAs($bendahara)->get(route('laporan-keuangan.publik'));
        $res->assertOk()
            ->assertSee('tabel-laporan-keuangan', false)
            ->assertSee('daftar-laporan-keuangan', false)
            ->assertSee('Unduh PDF')
            ->assertDontSee('hover-lift', false);
        $this->assertSame(13, $res->viewData('laporanList')->total());
        $this->assertCount(12, $res->viewData('laporanList')->items());
    }}
