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

        $this->actingAs($bendahara)
            ->put(route('opkeuangan.transaksi.update', $tx->id), [
                'tipe' => 'pemasukan',
                'judul' => 'Diubah',
                'kategori' => 'Iuran Warga',
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

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan',
                'judul' => 'Transaksi Berformat',
                'kategori' => 'Iuran Warga',
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

    // ───────────────────────── IR39 ─────────────────────────

    public function test_ir39_tagihan_rutin_dapat_dibuat_manual(): void
    {
        $rt = $this->createRt('108', 'RT 108');
        $bendahara = $this->bendaharaRt($rt, '3217010101019908');
        $this->createFamily($rt, '3217010108000001', 'Keluarga Rutin');

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.tagihan.store'), [
                'judul' => 'Iuran Bulanan Manual',
                'nominal' => 50000,
                'tenggat' => '2026-12-31',
                'jenis' => 'rutin',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('tagihan', [
            'judul' => 'Iuran Bulanan Manual',
            'jenis' => 'rutin',
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
}
