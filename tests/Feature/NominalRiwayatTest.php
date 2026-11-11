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
use App\Services\IuranService;
use App\Services\SetoranRtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Perubahan nominal iuran/setoran di tengah tahun: berlaku mulai bulan tertentu (cut off),
 * tidak berlaku surut, wajib konfirmasi, dan tidak mengubah perhitungan bulan sebelumnya.
 */
class NominalRiwayatTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;
    private User $opRt;
    private User $opRw;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-05-15 09:00:00');

        $this->rt = MasterRt::create(['kode_rt' => '701', 'nama_rt' => 'RT 701']);
        $this->opRt = User::create([
            'nik' => '3217010101016601', 'username' => 'oprt701', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $this->rt->id, 'password' => 'password123',
        ]);
        $this->opRw = User::create([
            'nik' => '3217010101016602', 'username' => 'oprw701', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function kk(string $nik, string $nama): Keluarga
    {
        $k = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji',
            'rt_id' => $this->rt->id, 'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta', 'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $k;
    }

    private function master(int $nominal = 60000): MasterIuran
    {
        return MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => 2026, 'nama' => 'Iuran Wajib',
            'nominal' => $nominal, 'aktif' => true,
        ]);
    }

    private function bayar(MasterIuran $master, Keluarga $kk, array $bulan): void
    {
        IuranService::tandaiLunasBatch($master, $kk->id, $bulan, [
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara',
        ], $this->opRt->id);
    }

    private function simpanMaster(int|string $nominal, array $tambahan = [])
    {
        return $this->actingAs($this->opRt)->post(route('opkeuangan.iuran-bulanan.master'), array_merge([
            'tahun' => 2026, 'nama' => 'Iuran Wajib', 'nominal' => (string) $nominal, 'aktif' => 1,
        ], $tambahan));
    }

    // ───────────────────────── Iuran bulanan ─────────────────────────

    public function test_master_baru_punya_riwayat_awal_sejak_januari(): void
    {
        $master = $this->master(60000);

        $this->assertSame(1, $master->riwayat()->count());
        $this->assertSame(60000, $master->nominalUntukBulan(1));
        $this->assertSame(60000, $master->nominalUntukBulan(12));
        $this->assertSame(60000, $master->fresh()->nominal);
    }

    public function test_perubahan_nominal_wajib_konfirmasi(): void
    {
        $master = $this->master(60000);

        $this->simpanMaster('70.000', ['berlaku_mulai' => 6])->assertSessionHas('error');
        $this->assertSame(60000, $master->fresh()->nominalUntukBulan(6));
        $this->assertSame(1, $master->fresh()->riwayat()->count());
    }

    public function test_perubahan_nominal_tidak_boleh_berlaku_surut(): void
    {
        $master = $this->master(60000);
        $kk = $this->kk('3217010101016611', 'Warga Satu');
        $this->bayar($master, $kk, [5]); // Mei sudah ada pembayaran

        // Mei sudah terkunci: awal berlaku paling cepat Juni.
        $this->assertSame(6, $master->fresh()->bulanMulaiTerawal());

        $this->simpanMaster('70.000', ['berlaku_mulai' => 5, 'konfirmasi_cutoff' => 1])->assertSessionHas('error');
        $this->simpanMaster('70.000', ['berlaku_mulai' => 3, 'konfirmasi_cutoff' => 1])->assertSessionHas('error');
        $this->assertSame(60000, $master->fresh()->nominalUntukBulan(5));
        $this->assertSame(1, $master->fresh()->riwayat()->count());
    }

    public function test_cut_off_tidak_mengubah_bulan_sebelumnya_dan_kas_lama(): void
    {
        $master = $this->master(60000);
        $kk = $this->kk('3217010101016621', 'Warga Dua');
        $this->bayar($master, $kk, [5]);
        $trxMei = TransaksiKeuangan::where('rt_id', $this->rt->id)->first();
        $this->assertSame(60000, $trxMei->jumlah);

        $this->simpanMaster('70.000', ['berlaku_mulai' => 6, 'konfirmasi_cutoff' => 1])
            ->assertSessionHas('success');

        $master = $master->fresh();
        $this->assertSame(60000, $master->nominalUntukBulan(5));
        $this->assertSame(60000, $master->nominalUntukBulan(1));
        $this->assertSame(70000, $master->nominalUntukBulan(6));
        $this->assertSame(70000, $master->nominalUntukBulan(12));
        $this->assertSame(60000, $master->nominal); // bulan berjalan (Mei) masih nominal lama

        // Bulan berganti: nominal berlaku saat ini mengikuti riwayat.
        Carbon::setTestNow('2026-06-10 09:00:00');
        $this->assertSame(70000, MasterIuran::find($master->id)->nominal);

        // Pembayaran Juni memakai nominal baru; transaksi Mei tetap nominal lama.
        $this->bayar(MasterIuran::find($master->id), $kk, [6]);
        $this->assertSame(60000, $trxMei->fresh()->jumlah);
        $this->assertSame(
            [60000, 70000],
            TransaksiKeuangan::where('rt_id', $this->rt->id)->orderBy('created_at')->orderBy('jumlah')->pluck('jumlah')->all()
        );
    }

    public function test_pembayaran_banyak_bulan_menjumlah_nominal_per_bulan(): void
    {
        $master = $this->master(60000);
        $master->ubahNominal(70000, 7);
        $kk = $this->kk('3217010101016631', 'Warga Tiga');

        // Bayar Juli dan Agustus sekaligus: 2 x 70.000, satu transaksi.
        IuranService::tandaiLunasBatch($master->fresh(), $kk->id, [7, 8], [
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara',
        ], $this->opRt->id);

        $this->assertSame(140000, (int) TransaksiKeuangan::where('rt_id', $this->rt->id)->value('jumlah'));
    }

    public function test_ubah_nama_tanpa_ubah_nominal_tidak_perlu_konfirmasi(): void
    {
        $master = $this->master(60000);

        $this->actingAs($this->opRt)->post(route('opkeuangan.iuran-bulanan.master'), [
            'tahun' => 2026, 'nama' => 'Iuran Keamanan', 'nominal' => '60.000', 'aktif' => 1,
        ])->assertSessionHas('success');

        $this->assertSame('Iuran Keamanan', $master->fresh()->nama);
        $this->assertSame(1, $master->fresh()->riwayat()->count());
    }

    public function test_tahun_lampau_tidak_dapat_diubah(): void
    {
        $lama = MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => 2025, 'nama' => 'Iuran Wajib', 'nominal' => 50000, 'aktif' => true,
        ]);

        $this->assertGreaterThan(12, $lama->bulanMulaiTerawal());
        $this->actingAs($this->opRt)->post(route('opkeuangan.iuran-bulanan.master'), [
            'tahun' => 2025, 'nama' => 'Iuran Wajib', 'nominal' => '80.000', 'aktif' => 1,
            'berlaku_mulai' => 12, 'konfirmasi_cutoff' => 1,
        ])->assertSessionHas('error');
        $this->assertSame(50000, $lama->fresh()->nominal);
    }

    // ───────────────────────── Setoran ke RW ─────────────────────────

    public function test_kewajiban_setoran_mengikuti_nominal_per_bulan(): void
    {
        $master = $this->master(60000);
        $kk1 = $this->kk('3217010101016641', 'Warga Empat');
        $kk2 = $this->kk('3217010101016642', 'Warga Lima');
        $this->bayar($master, $kk1, [5]);
        $this->bayar($master, $kk2, [5]);

        // Nominal setoran awal 20.000/KK sejak Januari, naik jadi 30.000 mulai Juni.
        SetoranRtService::simpanPengaturan(2026, null, 20000, 1);
        SetoranRtService::simpanPengaturan(2026, null, 30000, 6);

        // Juni: dua KK lunas juga.
        Carbon::setTestNow('2026-06-10 09:00:00');
        $masterJuni = MasterIuran::find($master->id);
        $this->bayar($masterJuni, $kk1, [6]);
        $this->bayar($masterJuni, $kk2, [6]);

        $rekap = SetoranRtService::rekapRt($this->rt->id, 2026, 6)['per_bulan'];
        $this->assertSame(40000, $rekap[5]['kewajiban']); // 2 x 20.000, tidak berubah oleh kenaikan Juni
        $this->assertSame(60000, $rekap[6]['kewajiban']); // 2 x 30.000
        $this->assertSame(120000, $rekap[5]['terkumpul']);
    }

    public function test_pengaturan_setoran_awal_tanpa_konfirmasi_lalu_perubahan_wajib_konfirmasi(): void
    {
        // Pengaturan awal: belum ada nominal sama sekali, cukup pilih bulan mulai.
        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => 2026, 'default' => '20.000', 'berlaku_mulai' => 1,
        ])->assertSessionHas('success');
        $this->assertSame(20000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 3));

        // Perubahan berikutnya wajib konfirmasi dan tidak boleh surut.
        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => 2026, 'default' => '30.000', 'berlaku_mulai' => 6,
        ])->assertSessionHas('error');
        $this->assertSame(30000 === SetoranRtService::nominalPerKk(2026, $this->rt->id, 6), false);

        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => 2026, 'default' => '30.000', 'berlaku_mulai' => 3, 'konfirmasi_cutoff' => 1,
        ])->assertSessionHas('error'); // sebelum bulan berjalan (Mei) = surut

        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => 2026, 'default' => '30.000', 'berlaku_mulai' => 6, 'konfirmasi_cutoff' => 1,
        ])->assertSessionHas('success');

        $this->assertSame(20000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 5));
        $this->assertSame(30000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 6));
    }

    public function test_bulan_yang_sudah_ada_setoran_tidak_dapat_dipilih(): void
    {
        SetoranRtService::simpanPengaturan(2026, null, 20000, 1);
        SetoranRt::create([
            'rt_id' => $this->rt->id, 'periode_bulan' => 5, 'periode_tahun' => 2026, 'nominal' => 10000,
            'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai', 'status' => 'dikonfirmasi',
            'dibuat_oleh' => $this->opRt->id,
        ]);

        $this->assertSame(6, SetoranRtService::bulanMulaiTerawal(2026));
        $this->actingAs($this->opRw)->post(route('opkeuangan.setoran.pengaturan'), [
            'tahun' => 2026, 'default' => '25.000', 'berlaku_mulai' => 5, 'konfirmasi_cutoff' => 1,
        ])->assertSessionHas('error');
        $this->assertSame(20000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 5));
    }

    public function test_override_rt_bisa_kembali_ikut_default_mulai_bulan_tertentu(): void
    {
        SetoranRtService::simpanPengaturan(2026, null, 20000, 1);
        SetoranRtService::simpanPengaturan(2026, $this->rt->id, 15000, 1);
        SetoranRtService::simpanPengaturan(2026, $this->rt->id, null, 7); // ikut default mulai Juli

        $this->assertSame(15000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 6));
        $this->assertSame(20000, SetoranRtService::nominalPerKk(2026, $this->rt->id, 7));
    }

    public function test_dialog_konfirmasi_tampil_di_halaman_pengaturan(): void
    {
        $this->master(60000);

        $this->actingAs($this->opRt)->get(route('opkeuangan.iuran-bulanan.index'))
            ->assertOk()
            ->assertSee('Konfirmasi perubahan nominal')
            ->assertSee('Riwayat nominal 2026')
            ->assertSee('Saya mengerti dan mengonfirmasi');
    }
}
