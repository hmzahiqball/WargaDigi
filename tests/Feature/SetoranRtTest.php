<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\SetoranRt;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use App\Services\SetoranRtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Setoran kolektif RT ke RW: dicatat RT, dikonfirmasi RW, jurnal kas otomatis di kedua sisi.
 */
class SetoranRtTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;
    private MasterRt $rtLain;
    private User $opRt;
    private User $opRw;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt = MasterRt::create(['kode_rt' => '601', 'nama_rt' => 'RT 601']);
        $this->rtLain = MasterRt::create(['kode_rt' => '602', 'nama_rt' => 'RT 602']);
        $this->opRt = User::create([
            'nik' => '3217010101017701', 'username' => 'oprt601', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $this->rt->id, 'password' => 'password123',
        ]);
        $this->opRw = User::create([
            'nik' => '3217010101017702', 'username' => 'oprw', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Carbon::setTestNow();
        parent::tearDown();
    }

    private function kk(MasterRt $rt, string $nik, string $nama): Keluarga
    {
        $k = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id, 'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta', 'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $k;
    }

    /** RT punya master iuran 60.000 dan 3 KK lunas pada bulan berjalan. */
    private function siapkanIuranLunas(int $jumlahKk = 3): MasterIuran
    {
        $master = MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => (int) now()->format('Y'), 'nama' => 'Iuran Wajib',
            'nominal' => 60000, 'aktif' => true,
        ]);
        for ($i = 1; $i <= $jumlahKk; $i++) {
            $kk = $this->kk($this->rt, '32170101010177' . str_pad((string) (10 + $i), 2, '0', STR_PAD_LEFT), 'Warga ' . $i);
            IuranPembayaran::create([
                'master_iuran_id' => $master->id, 'keluarga_id' => $kk->id, 'bulan' => (int) now()->format('n'),
                'status' => 'lunas', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara',
            ]);
        }

        return $master;
    }

    private function setoran(int $nominal = 50000, string $status = 'menunggu'): SetoranRt
    {
        return SetoranRt::create([
            'rt_id' => $this->rt->id, 'periode_bulan' => (int) now()->format('n'), 'periode_tahun' => (int) now()->format('Y'),
            'nominal' => $nominal, 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
            'status' => $status, 'dibuat_oleh' => $this->opRt->id,
        ]);
    }

    public function test_kewajiban_dihitung_dari_nominal_per_kk_dan_kk_lunas(): void
    {
        $this->siapkanIuranLunas(3);
        $tahun = (int) now()->format('Y');
        SetoranRtService::simpanPengaturan($tahun, null, 20000);

        $rekap = SetoranRtService::rekapRt($this->rt->id, $tahun, (int) now()->format('n'));
        $bulan = $rekap['per_bulan'][(int) now()->format('n')];

        $this->assertSame(3, $bulan['lunas']);
        $this->assertSame(180000, $bulan['terkumpul']);
        $this->assertSame(60000, $bulan['kewajiban']); // 3 KK x 20.000
        $this->assertSame(60000, $bulan['sisa']);
    }

    public function test_override_nominal_per_rt_mengalahkan_default(): void
    {
        $tahun = (int) now()->format('Y');
        SetoranRtService::simpanPengaturan($tahun, null, 20000);
        SetoranRtService::simpanPengaturan($tahun, $this->rt->id, 15000);

        $this->assertSame(15000, SetoranRtService::nominalPerKk($tahun, $this->rt->id));
        $this->assertSame(20000, SetoranRtService::nominalPerKk($tahun, $this->rtLain->id));

        // Override dihapus bila dikosongkan.
        SetoranRtService::simpanPengaturan($tahun, $this->rt->id, null);
        $this->assertSame(20000, SetoranRtService::nominalPerKk($tahun, $this->rt->id));
    }

    public function test_rt_mencatat_setoran_menunggu_dan_belum_masuk_kas(): void
    {
        $this->siapkanIuranLunas(3);
        SetoranRtService::simpanPengaturan((int) now()->format('Y'), null, 20000, 1);

        $res = $this->actingAs($this->opRt)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'bulanan', 'tahun' => (int) now()->format('Y'),
            'items' => [(int) now()->format('n') => '50.000'],
            'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ]);

        $res->assertRedirect()->assertSessionHas('success');
        $s = SetoranRt::first();
        $this->assertSame('menunggu', $s->status);
        $this->assertSame(50000, $s->nominal);
        $this->assertSame($this->rt->id, $s->rt_id);
        $this->assertSame(0, TransaksiKeuangan::count());
    }

    public function test_konfirmasi_rw_membuat_dua_transaksi_yang_cocok(): void
    {
        $s = $this->setoran(50000);

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $s->id))
            ->assertRedirect()->assertSessionHas('success');

        $s->refresh();
        $this->assertSame('dikonfirmasi', $s->status);

        $keluar = TransaksiKeuangan::find($s->transaksi_rt_id);
        $masuk = TransaksiKeuangan::find($s->transaksi_rw_id);
        $this->assertSame('pengeluaran', $keluar->tipe);
        $this->assertSame('RT', $keluar->unit_sumber);
        $this->assertSame($this->rt->id, $keluar->rt_id);
        $this->assertSame('pemasukan', $masuk->tipe);
        $this->assertSame('RW', $masuk->unit_sumber);
        $this->assertSame(50000, $keluar->jumlah);
        $this->assertSame($keluar->jumlah, $masuk->jumlah);

        // Konfirmasi ulang tidak menggandakan transaksi.
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $s->id))->assertSessionHas('error');
        $this->assertSame(2, TransaksiKeuangan::count());
    }

    public function test_transaksi_setoran_terkunci_dari_hapus_manual(): void
    {
        $s = $this->setoran(50000);
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $s->id));
        $s->refresh();

        $this->actingAs($this->opRt)->delete(route('opkeuangan.transaksi.destroy', $s->transaksi_rt_id))
            ->assertSessionHas('error');
        $this->assertNotNull(TransaksiKeuangan::find($s->transaksi_rt_id));

        $this->actingAs($this->opRw)->delete(route('opkeuangan.transaksi.destroy', $s->transaksi_rw_id))
            ->assertSessionHas('error');
        $this->assertNotNull(TransaksiKeuangan::find($s->transaksi_rw_id));
    }

    public function test_tolak_wajib_alasan_dan_tidak_membuat_transaksi(): void
    {
        $s = $this->setoran(50000);

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.tolak', $s->id), [])
            ->assertSessionHasErrors('alasan');
        $this->assertSame('menunggu', $s->fresh()->status);

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.tolak', $s->id), ['alasan' => 'Bukti tidak sesuai'])
            ->assertSessionHas('success');

        $s->refresh();
        $this->assertSame('ditolak', $s->status);
        $this->assertSame('Bukti tidak sesuai', $s->alasan);
        $this->assertSame(0, TransaksiKeuangan::count());

        // RT melihat alasan penolakan di halamannya.
        $this->actingAs($this->opRt)->get(route('opkeuangan.setoran.index'))
            ->assertOk()->assertSee('Bukti tidak sesuai');
    }

    public function test_batalkan_menghapus_kedua_transaksi(): void
    {
        $s = $this->setoran(50000);
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $s->id));
        $this->assertSame(2, TransaksiKeuangan::count());

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.batalkan', $s->id), ['alasan' => 'Salah nominal'])
            ->assertSessionHas('success');

        $this->assertSame(0, TransaksiKeuangan::count());
        $this->assertSame('dibatalkan', $s->fresh()->status);
    }

    public function test_hak_akses_rt_dan_rw_dipisah(): void
    {
        $s = $this->setoran(50000);

        // RT tidak boleh mengonfirmasi/menolak/membatalkan/mengatur nominal.
        $this->actingAs($this->opRt)->put(route('opkeuangan.setoran.konfirmasi', $s->id))->assertForbidden();
        $this->actingAs($this->opRt)->put(route('opkeuangan.setoran.tolak', $s->id), ['alasan' => 'x'])->assertForbidden();
        $this->actingAs($this->opRt)->post(route('opkeuangan.setoran.pengaturan'), ['tahun' => 2026, 'default' => 1000])->assertForbidden();

        // RW tidak mencatat setoran atas nama RT.
        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'bulanan', 'tahun' => (int) now()->format('Y'), 'items' => [1 => '1.000'],
            'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertForbidden();

        // RT lain tidak dapat menghapus setoran RT ini.
        $opLain = User::create([
            'nik' => '3217010101017703', 'username' => 'oprt602', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $this->rtLain->id, 'password' => 'password123',
        ]);
        $this->actingAs($opLain)->delete(route('opkeuangan.setoran.destroy', $s->id))->assertForbidden();
        $this->assertNotNull(SetoranRt::find($s->id));
    }

    public function test_rt_hanya_boleh_hapus_setoran_yang_belum_dikonfirmasi(): void
    {
        $menunggu = $this->setoran(1000);
        $this->actingAs($this->opRt)->delete(route('opkeuangan.setoran.destroy', $menunggu->id))->assertSessionHas('success');
        $this->assertNull(SetoranRt::find($menunggu->id));

        $ok = $this->setoran(2000, 'dikonfirmasi');
        $this->actingAs($this->opRt)->delete(route('opkeuangan.setoran.destroy', $ok->id))->assertSessionHas('error');
        $this->assertNotNull(SetoranRt::find($ok->id));
    }

    public function test_periode_masa_depan_ditolak(): void
    {
        $this->actingAs($this->opRt)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'bulanan', 'tahun' => (int) now()->format('Y') + 1, 'items' => [12 => '1.000'],
            'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('error');
        $this->assertSame(0, SetoranRt::count());
    }

    public function test_rw_melihat_rekap_semua_rt_dan_pengaturan_tersimpan(): void
    {
        $this->siapkanIuranLunas(2);
        $tahun = (int) now()->format('Y');

        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => $tahun, 'default' => '20.000', 'berlaku_mulai' => 1, 'per_rt' => [$this->rtLain->id => '', $this->rt->id => '25.000'],
        ])->assertSessionHas('success');

        $this->assertSame(25000, SetoranRtService::nominalPerKk($tahun, $this->rt->id));
        $this->assertSame(20000, SetoranRtService::nominalPerKk($tahun, $this->rtLain->id));

        $this->actingAs($this->opRw)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('RT 601')
            ->assertSee('RT 602')
            ->assertSee('Rp 50.000'); // kewajiban RT 601: 2 KK x 25.000
    }

    public function test_admin_rw_hanya_melihat_rekap_tanpa_aksi(): void
    {
        $adminRw = User::create([
            'nik' => '3217010101017704', 'username' => 'adminrw', 'role' => 'Admin RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $s = $this->setoran(50000);

        $this->actingAs($adminRw)->get(route('rw.rekap-iuran'))
            ->assertOk()
            ->assertSee('Rekap per RT')
            ->assertDontSee('Nominal Setoran')
            ->assertDontSee('data-testid="periksa-setoran"', false);

        // Admin RW tidak punya akses aksi (ditolak middleware peran: 403 atau dialihkan).
        $status = $this->actingAs($adminRw)->put(route('opkeuangan.setoran.konfirmasi', $s->id))->getStatusCode();
        $this->assertContains($status, [302, 403]);
        $this->assertSame('menunggu', $s->fresh()->status);
    }

    public function test_dashboard_menampilkan_pengingat_setoran(): void
    {
        $this->siapkanIuranLunas(3);
        SetoranRtService::simpanPengaturan((int) now()->format('Y'), null, 20000);
        $this->setoran(20000); // menunggu konfirmasi

        // Dashboard RT: kewajiban setoran belum lunas tampil di Perlu Tindakan.
        $this->actingAs($this->opRt)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Setoran ke RW belum lunas');

        // Dashboard RW: ada setoran yang menunggu konfirmasi.
        $this->actingAs($this->opRw)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Setoran RT menunggu konfirmasi');
    }

    public function test_halaman_rt_menampilkan_sisa_kewajiban(): void
    {
        $this->siapkanIuranLunas(3);
        SetoranRtService::simpanPengaturan((int) now()->format('Y'), null, 20000);
        $this->setoran(20000, 'dikonfirmasi'); // sebagian

        $this->actingAs($this->opRt)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('Rp 40.000'); // sisa: 60.000 - 20.000
    }

    // ───────────────────────── Setoran batch (beberapa bulan sekaligus) ─────────────────────────

    /** Sejak Maret: 3 KK lunas pada Maret, April, Mei 2026; setoran 20.000 per KK (kewajiban 60.000 per bulan). */
    private function siapkanTigaBulan(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-05-15 09:00:00');
        $master = MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => 2026, 'nama' => 'Iuran Wajib', 'nominal' => 60000, 'aktif' => true,
        ]);
        for ($i = 1; $i <= 3; $i++) {
            $kk = $this->kk($this->rt, '32170101010177' . (40 + $i), 'Warga ' . $i);
            foreach ([3, 4, 5] as $bulan) {
                IuranPembayaran::create([
                    'master_iuran_id' => $master->id, 'keluarga_id' => $kk->id, 'bulan' => $bulan,
                    'status' => 'lunas', 'tanggal_bayar' => '2026-05-10', 'metode' => 'tunai', 'sumber' => 'bendahara',
                ]);
            }
        }
        SetoranRtService::simpanPengaturan(2026, null, 20000, 1);
    }

    private function postBatch(array $items, array $tambahan = [])
    {
        return $this->actingAs($this->opRt)->post(route('opkeuangan.setoran.store'), array_merge([
            'jenis' => 'bulanan', 'tahun' => 2026, 'items' => $items,
            'tanggal_setor' => '2026-05-15', 'metode' => 'transfer',
        ], $tambahan));
    }

    public function test_setor_beberapa_bulan_sekaligus_menjadi_satu_batch(): void
    {
        $this->siapkanTigaBulan();

        $this->postBatch([3 => '60.000', 4 => '60.000', 5 => '30.000'], ['catatan' => 'Transfer satu kali'])
            ->assertSessionHas('success');

        $baris = SetoranRt::orderBy('periode_bulan')->get();
        $this->assertCount(3, $baris);
        $this->assertSame(1, $baris->pluck('batch_id')->unique()->count());
        $this->assertSame([60000, 60000, 30000], $baris->pluck('nominal')->all());
        $this->assertSame(['menunggu'], $baris->pluck('status')->unique()->all());

        // Yang menunggu mengurangi sisa yang dapat disetor lagi (Mei: 60.000 - 30.000).
        $bisa = SetoranRtService::bisaDisetorBulanan($this->rt->id, 2026, 5);
        $this->assertSame(0, $bisa[3]['bisa']);
        $this->assertSame(30000, $bisa[5]['bisa']);
    }

    public function test_nominal_melebihi_sisa_atau_bulan_belum_berjalan_ditolak_tanpa_setoran_tersimpan(): void
    {
        $this->siapkanTigaBulan();

        // Satu bulan melebihi sisa: seluruh batch dibatalkan (tidak ada yang tersimpan).
        $this->postBatch([3 => '60.000', 4 => '70.000'])->assertSessionHas('error');
        $this->assertSame(0, SetoranRt::count());

        // Juni belum berjalan.
        $this->postBatch([6 => '10.000'])->assertSessionHas('error');
        $this->assertSame(0, SetoranRt::count());

        // Bulan tanpa kewajiban (Februari) tidak dapat disetor.
        $this->postBatch([2 => '10.000'])->assertSessionHas('error');
        $this->assertSame(0, SetoranRt::count());

        // Tidak memilih apa pun.
        $this->postBatch([3 => '0'])->assertSessionHas('error');
    }

    public function test_setoran_menunggu_menahan_sisa_agar_tidak_disetor_dua_kali(): void
    {
        $this->siapkanTigaBulan();
        $this->postBatch([5 => '60.000'])->assertSessionHas('success');

        $this->postBatch([5 => '1.000'])->assertSessionHas('error');
        $this->assertSame(1, SetoranRt::count());
    }

    public function test_konfirmasi_batch_membuat_satu_pasang_transaksi_total_dan_rekap_per_bulan_tetap(): void
    {
        $this->siapkanTigaBulan();
        $this->postBatch([3 => '60.000', 4 => '60.000', 5 => '30.000']);
        $satu = SetoranRt::where('periode_bulan', 4)->first();

        // Konfirmasi lewat salah satu baris mengonfirmasi seluruh batch.
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $satu->id))->assertSessionHas('success');

        $baris = SetoranRt::all();
        $this->assertSame(['dikonfirmasi'], $baris->pluck('status')->unique()->all());
        $this->assertSame(2, TransaksiKeuangan::count()); // satu pengeluaran RT + satu pemasukan RW
        $keluar = TransaksiKeuangan::where('unit_sumber', 'RT')->where('tipe', 'pengeluaran')->first();
        $masuk = TransaksiKeuangan::where('unit_sumber', 'RW')->where('tipe', 'pemasukan')->first();
        $this->assertSame(150000, $keluar->jumlah);
        $this->assertSame(150000, $masuk->jumlah);
        $this->assertStringContainsString('Maret, April, Mei 2026', $keluar->judul);

        // Rekap per bulan tetap benar.
        $rekap = SetoranRtService::rekapRt($this->rt->id, 2026, 5)['per_bulan'];
        $this->assertSame(60000, $rekap[3]['disetor']);
        $this->assertSame(30000, $rekap[5]['disetor']);
        $this->assertSame(30000, $rekap[5]['sisa']);
        $this->assertSame(0, $rekap[3]['sisa']);
    }

    public function test_batalkan_batch_menghapus_transaksi_dan_menolak_batch_menolak_semua(): void
    {
        $this->siapkanTigaBulan();
        $this->postBatch([3 => '60.000', 4 => '60.000']);
        $satu = SetoranRt::first();

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $satu->id));
        $this->assertSame(2, TransaksiKeuangan::count());
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.batalkan', $satu->id), ['alasan' => 'Salah catat'])
            ->assertSessionHas('success');
        $this->assertSame(0, TransaksiKeuangan::count());
        $this->assertSame(['dibatalkan'], SetoranRt::pluck('status')->unique()->all());

        // Batch baru ditolak: semua baris ikut ditolak lalu dapat dihapus RT sekaligus.
        SetoranRt::query()->delete();
        $this->postBatch([3 => '60.000', 4 => '60.000']);
        $baru = SetoranRt::first();
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.tolak', $baru->id), ['alasan' => 'Bukti buram'])
            ->assertSessionHas('success');
        $this->assertSame(['ditolak'], SetoranRt::pluck('status')->unique()->all());
        $this->assertSame('Bukti buram', SetoranRt::first()->alasan);

        $this->actingAs($this->opRt)->delete(route('opkeuangan.setoran.destroy', $baru->id))->assertSessionHas('success');
        $this->assertSame(0, SetoranRt::count());
    }

    public function test_halaman_rt_dan_rw_menampilkan_rincian_batch(): void
    {
        $this->siapkanTigaBulan();

        $this->actingAs($this->opRt)->get(route('opkeuangan.setoran.index', ['tahun' => 2026]))
            ->assertOk()
            ->assertSee('Setor Iuran Bulanan')
            ->assertSee('Setor Iuran Lainnya')
            ->assertSee('rincian-setor-bulanan', false)
            ->assertSee('Setor Seluruhnya')
            ->assertDontSee('Catat Setoran');

        $this->postBatch([3 => '60.000', 4 => '60.000']);
        $this->actingAs($this->opRw)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('rincian-setoran', false)
            ->assertSee('Maret, April 2026')
            ->assertSee('Rp 120.000');
    }
    public function test_riwayat_setoran_dipaginasi_sepuluh_per_halaman(): void
    {
        for ($b = 1; $b <= 12; $b++) {
            SetoranRt::create([
                'rt_id' => $this->rt->id, 'periode_bulan' => $b, 'periode_tahun' => (int) now()->format('Y'),
                'nominal' => 1000 * $b, 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
                'status' => 'dikonfirmasi', 'dibuat_oleh' => $this->opRt->id,
            ]);
        }

        // RT: halaman 1 berisi 10, halaman 2 berisi sisanya.
        $hal1 = $this->actingAs($this->opRt)->get(route('opkeuangan.setoran.index'));
        $hal1->assertOk();
        $this->assertSame(12, $hal1->viewData('riwayat')->total());
        $this->assertCount(10, $hal1->viewData('riwayat')->items());
        $hal1->assertSee('12 setoran');

        $hal2 = $this->actingAs($this->opRt)->get(route('opkeuangan.setoran.index', ['halaman' => 2]));
        $this->assertCount(2, $hal2->viewData('riwayat')->items());

        // RW: riwayat juga dipaginasi.
        $rw1 = $this->actingAs($this->opRw)->get(route('opkeuangan.setoran.index'));
        $this->assertSame(12, $rw1->viewData('riwayat')->total());
        $this->assertCount(10, $rw1->viewData('riwayat')->items());
        $rw2 = $this->actingAs($this->opRw)->get(route('opkeuangan.setoran.index', ['halaman' => 2]));
        $this->assertCount(2, $rw2->viewData('riwayat')->items());
    }}
