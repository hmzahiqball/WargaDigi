<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\Penduduk;
use App\Models\RekeningBendahara;
use App\Models\SetoranRt;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use App\Services\SetoranRtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Iuran Lainnya buatan RW yang dikumpulkan lewat RT:
 * warga bayar ke RT, RT memverifikasi, lalu menyetor 100% yang terkumpul ke RW.
 */
class IuranLainnyaViaRtTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $opRt1;
    private User $opRt2;
    private User $opRw;
    private Keluarga $kk1;
    private Keluarga $kk2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt1 = MasterRt::create(['kode_rt' => '801', 'nama_rt' => 'RT 801']);
        $this->rt2 = MasterRt::create(['kode_rt' => '802', 'nama_rt' => 'RT 802']);
        $this->opRt1 = $this->user('3217010101015501', 'oprt801', 'Op Keuangan RT', $this->rt1);
        $this->opRt2 = $this->user('3217010101015502', 'oprt802', 'Op Keuangan RT', $this->rt2);
        $this->opRw = $this->user('3217010101015503', 'oprw801', 'Op Keuangan RW', null);
        $this->kk1 = $this->kk($this->rt1, '3217010101015511', 'Warga Satu');
        $this->kk2 = $this->kk($this->rt2, '3217010101015512', 'Warga Dua');
    }

    private function user(string $nik, string $username, string $role, ?MasterRt $rt): User
    {
        return User::create([
            'nik' => $nik, 'username' => $username, 'role' => $role,
            'status_akun' => 'Active', 'rt_id' => $rt?->id, 'password' => 'password123',
        ]);
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

    private function buatTagihanRw(bool $viaRt, string $judul = 'Iuran Kegiatan 17an'): Tagihan
    {
        $data = ['judul' => $judul, 'nominal' => '50.000'];
        if ($viaRt) {
            $data['via_rt'] = 1;
        }
        $this->actingAs($this->opRw)->post(route('opkeuangan.tagihan.store'), $data)->assertSessionHas('success');

        return Tagihan::where('judul', $judul)->firstOrFail();
    }

    private function pending(Tagihan $t, Keluarga $kk, int $nominal = 50000): PembayaranTagihan
    {
        $p = PembayaranTagihan::where('tagihan_id', $t->id)->where('keluarga_id', $kk->id)->firstOrFail();
        $p->update(['status' => 'Pending', 'nominal' => $nominal, 'metode' => 'Cash', 'tanggal_bayar' => now()->toDateString()]);

        return $p->fresh();
    }

    public function test_rw_memilih_cara_pengumpulan_dan_rt_tidak_bisa_membuat_via_rt(): void
    {
        $viaRt = $this->buatTagihanRw(true, 'Iuran A');
        $langsung = $this->buatTagihanRw(false, 'Iuran B');

        $this->assertTrue($viaRt->via_rt);
        $this->assertFalse($langsung->via_rt);
        $this->assertSame(2, PembayaranTagihan::where('tagihan_id', $viaRt->id)->count()); // disebar ke semua KK

        // Opsi via_rt dari akun RT diabaikan (hanya iuran buatan RW).
        $this->actingAs($this->opRt1)->post(route('opkeuangan.tagihan.store'), ['judul' => 'Iuran RT', 'nominal' => '10.000', 'via_rt' => 1]);
        $this->assertFalse(Tagihan::where('judul', 'Iuran RT')->firstOrFail()->via_rt);
    }

    public function test_rt_hanya_melihat_iuran_rw_yang_via_rt(): void
    {
        $this->buatTagihanRw(true, 'Iuran Lewat RT');
        $this->buatTagihanRw(false, 'Iuran Langsung RW');

        $this->actingAs($this->opRt1)->get(route('opkeuangan.iuran.index'))
            ->assertOk()
            ->assertSee('Iuran Lewat RT')
            ->assertDontSee('Iuran Langsung RW');

        $langsung = Tagihan::where('judul', 'Iuran Langsung RW')->first();
        $this->actingAs($this->opRt1)->get(route('opkeuangan.iuran.detail', $langsung->id))->assertForbidden();
    }

    public function test_rt_memverifikasi_hanya_kk_sendiri_dan_masuk_kas_rt(): void
    {
        $t = $this->buatTagihanRw(true);
        $p1 = $this->pending($t, $this->kk1);
        $p2 = $this->pending($t, $this->kk2);

        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $p1->id))->assertSessionHas('success');
        $this->assertSame('Paid', $p1->fresh()->status);

        $trx = TransaksiKeuangan::find($p1->fresh()->transaksi_id);
        $this->assertSame('RT', $trx->unit_sumber);
        $this->assertSame($this->rt1->id, $trx->rt_id);
        $this->assertSame(50000, $trx->jumlah);

        // RT lain tidak boleh memverifikasi KK RT ini, dan sebaliknya.
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $p2->id))->assertForbidden();
        $this->assertSame('Pending', $p2->fresh()->status);

        // RW tidak memverifikasi per warga untuk iuran yang dikumpulkan lewat RT.
        $this->actingAs($this->opRw)->put(route('opkeuangan.pembayaran.approve', $p2->id))->assertForbidden();
        $this->assertSame('Pending', $p2->fresh()->status);
    }

    public function test_iuran_langsung_rw_tetap_diverifikasi_rw_dan_masuk_kas_rw(): void
    {
        $t = $this->buatTagihanRw(false, 'Donasi Langsung');
        $p = $this->pending($t, $this->kk1);

        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $p->id))->assertForbidden();
        $this->actingAs($this->opRw)->put(route('opkeuangan.pembayaran.approve', $p->id))->assertSessionHas('success');

        $trx = TransaksiKeuangan::find($p->fresh()->transaksi_id);
        $this->assertSame('RW', $trx->unit_sumber);
        $this->assertNull($trx->rt_id);
    }

    public function test_setoran_iuran_lainnya_seratus_persen_dan_konfirmasi_membuat_dua_transaksi(): void
    {
        $t = $this->buatTagihanRw(true);
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($t, $this->kk1, 60000)->id));

        $angka = SetoranRtService::angkaTagihan($t, $this->rt1->id);
        $this->assertSame(60000, $angka['terkumpul']); // nominal aktual (bukan nominal minimal)
        $this->assertSame(60000, $angka['bisa_disetor']);

        // Melebihi yang terkumpul ditolak.
        $this->actingAs($this->opRt1)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'lainnya', 'items' => [$t->id => '70.000'], 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('error');
        $this->assertSame(0, SetoranRt::count());

        // Setoran sebagian diperbolehkan.
        $this->actingAs($this->opRt1)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'lainnya', 'items' => [$t->id => '40.000'], 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('success');
        $setoran = SetoranRt::firstOrFail();
        $this->assertSame($t->id, $setoran->tagihan_id);
        $this->assertStringContainsString('Iuran Kegiatan 17an', $setoran->periode_label);
        $this->assertSame(20000, SetoranRtService::angkaTagihan($t, $this->rt1->id)['bisa_disetor']);

        // RW mengonfirmasi: pengeluaran RT + pemasukan RW, nominal sama.
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $setoran->id))->assertSessionHas('success');
        $setoran->refresh();
        $keluar = TransaksiKeuangan::find($setoran->transaksi_rt_id);
        $masuk = TransaksiKeuangan::find($setoran->transaksi_rw_id);
        $this->assertSame('pengeluaran', $keluar->tipe);
        $this->assertSame('RT', $keluar->unit_sumber);
        $this->assertSame('pemasukan', $masuk->tipe);
        $this->assertSame('RW', $masuk->unit_sumber);
        $this->assertSame(40000, $keluar->jumlah);
        $this->assertSame($keluar->jumlah, $masuk->jumlah);

        $akhir = SetoranRtService::angkaTagihan($t, $this->rt1->id);
        $this->assertSame(40000, $akhir['disetor']);
        $this->assertSame(20000, $akhir['sisa']);
    }

    public function test_setoran_hanya_untuk_iuran_via_rt_dan_tidak_mengacaukan_setoran_bulanan(): void
    {
        $langsung = $this->buatTagihanRw(false, 'Iuran Langsung');
        $this->actingAs($this->opRt1)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'lainnya', 'items' => [$langsung->id => '10.000'], 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('error');

        $viaRt = $this->buatTagihanRw(true, 'Iuran Via RT');
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($viaRt, $this->kk1)->id));
        $this->actingAs($this->opRt1)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'lainnya', 'items' => [$viaRt->id => '50.000'], 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('success');

        // Setoran Iuran Lainnya tidak dihitung sebagai setoran iuran bulanan.
        $rekap = SetoranRtService::rekapRt($this->rt1->id, (int) now()->format('Y'), (int) now()->format('n'));
        $this->assertSame(0, $rekap['total']['menunggu']);
        $this->assertSame(0, $rekap['total']['disetor']);
    }

    public function test_rw_melihat_rekap_per_rt_tanpa_daftar_warga(): void
    {
        $t = $this->buatTagihanRw(true);
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($t, $this->kk1)->id));

        $this->actingAs($this->opRw)->get(route('opkeuangan.iuran.detail', $t->id))
            ->assertOk()
            ->assertSee('Rekap per RT')
            ->assertSee('RT 801')
            ->assertSee('RT 802')
            ->assertSee('Rp 50.000')
            ->assertDontSee('Warga Satu')
            ->assertDontSee('Warga Dua');

        // Halaman Iuran Bulanan RW memuat tab Iuran Lainnya.
        $this->actingAs($this->opRw)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('Iuran Lainnya')
            ->assertSee('Iuran Kegiatan 17an');
    }

    public function test_warga_membayar_ke_rekening_rt_untuk_iuran_via_rt(): void
    {
        $t = $this->buatTagihanRw(true);
        foreach ([['RT', $this->rt1->id, 'BANK-RT-KHUSUS'], ['RW', null, 'BANK-RW-KHUSUS']] as [$unit, $rtId, $bank]) {
            RekeningBendahara::create([
                'id' => (string) Str::uuid(), 'unit' => $unit, 'rt_id' => $rtId,
                'bank' => $bank, 'no_rek' => '1234567890', 'nama_rek' => 'Bendahara',
            ]);
        }

        Role::findOrCreate('Warga', 'web');
        $warga = $this->user('3217010101015511', 'warga801', 'Warga', $this->rt1);
        $warga->syncRoles(['Warga']);

        $this->actingAs($warga)->get(route('warga.tagihan.index'))
            ->assertOk()
            ->assertSee('Dari RW, dibayar lewat RT')
            ->assertSee('BANK-RT-KHUSUS')
            ->assertDontSee('BANK-RW-KHUSUS');
    }

    public function test_dashboard_rt_menampilkan_setoran_iuran_lainnya_belum_lunas(): void
    {
        $t = $this->buatTagihanRw(true);
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($t, $this->kk1)->id));

        $this->actingAs($this->opRt1)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Setoran Iuran Lainnya dari RW belum lunas')
            ->assertSee('Iuran Kegiatan 17an');

        // Halaman setoran RT menampilkan bagian Iuran Lainnya dari RW.
        $this->actingAs($this->opRt1)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('Iuran Lainnya dari RW')
            ->assertSee('Rp 50.000');
    }

    public function test_setor_dua_iuran_lainnya_sekaligus_satu_pengeluaran_rt_dan_pemasukan_rw_per_iuran(): void
    {
        $a = $this->buatTagihanRw(true, 'Iuran Satu');
        $b = $this->buatTagihanRw(true, 'Iuran Dua');
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($a, $this->kk1, 50000)->id));
        $this->actingAs($this->opRt1)->put(route('opkeuangan.pembayaran.approve', $this->pending($b, $this->kk1, 80000)->id));

        // Modal tombol "Setor Iuran Lainnya" terisi sebesar yang sudah diterima dan belum disetor.
        $this->actingAs($this->opRt1)->get(route('opkeuangan.setoran.index'))
            ->assertOk()
            ->assertSee('rincian-setor-lainnya', false)
            ->assertSee('Iuran Satu')
            ->assertSee('Iuran Dua')
            ->assertSee('value="80.000"', false);

        $this->actingAs($this->opRt1)->post(route('opkeuangan.setoran.store'), [
            'jenis' => 'lainnya', 'items' => [$a->id => '50.000', $b->id => '80.000'],
            'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai',
        ])->assertSessionHas('success');

        $baris = SetoranRt::all();
        $this->assertCount(2, $baris);
        $this->assertSame(1, $baris->pluck('batch_id')->unique()->count());

        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.konfirmasi', $baris->first()->id))->assertSessionHas('success');

        $keluarRt = TransaksiKeuangan::where('unit_sumber', 'RT')->where('tipe', 'pengeluaran')->get();
        $masukRw = TransaksiKeuangan::where('unit_sumber', 'RW')->where('tipe', 'pemasukan')->orderBy('jumlah')->get();
        $this->assertCount(1, $keluarRt);
        $this->assertSame(130000, $keluarRt->first()->jumlah);
        $this->assertSame([50000, 80000], $masukRw->pluck('jumlah')->all()); // pemasukan RW dipecah per iuran

        $this->assertSame(0, SetoranRtService::angkaTagihan($a, $this->rt1->id)['bisa_disetor']);
        $this->assertSame(0, SetoranRtService::angkaTagihan($b, $this->rt1->id)['bisa_disetor']);

        // Batalkan batch: semua transaksi dihapus.
        $this->actingAs($this->opRw)->put(route('opkeuangan.setoran.batalkan', $baris->first()->id), ['alasan' => 'Koreksi'])
            ->assertSessionHas('success');
        $this->assertSame(0, TransaksiKeuangan::where('kode_transaksi', 'like', 'SR%')->count());
    }}
