<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\Penduduk;
use App\Models\SetoranRt;
use App\Models\Tagihan;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use App\Services\SetoranRtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dashboard Operator Keuangan RW: hanya kas RW (tanpa kas RT/DKM), setoran RT, Iuran Lainnya RW,
 * dan hal yang perlu tindakan.
 */
class DashboardKeuanganRwTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;
    private MasterRt $rt2;
    private User $opRw;
    private User $opRt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt = MasterRt::create(['kode_rt' => '961', 'nama_rt' => 'RT 961']);
        $this->rt2 = MasterRt::create(['kode_rt' => '962', 'nama_rt' => 'RT 962']);
        $this->opRw = User::create([
            'nik' => '3217010101012201', 'username' => 'oprw961', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $this->opRt = User::create([
            'nik' => '3217010101012202', 'username' => 'oprt961', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $this->rt->id, 'password' => 'password123',
        ]);
    }

    private function trx(string $unit, ?string $rtId, string $tipe, int $jumlah, string $kategori = 'Umum', string $status = 'Verified'): TransaksiKeuangan
    {
        static $n = 0;
        $n++;

        return TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-RW-' . $n, 'tipe' => $tipe, 'kategori' => $kategori, 'judul' => 'Uji ' . $n,
            'jumlah' => $jumlah, 'tanggal' => now()->toDateString(), 'status' => $status,
            'unit_sumber' => $unit, 'rt_id' => $rtId,
        ]);
    }

    private function kk(MasterRt $rt, string $nik, string $nama): Keluarga
    {
        $k = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji', 'rt_id' => $rt->id, 'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Belum Kawin',
        ]);

        return $k;
    }

    public function test_saldo_hanya_kas_rw_tanpa_kas_rt_dan_dkm(): void
    {
        $this->trx('RW', null, 'pemasukan', 900000, 'Setoran Iuran RT');
        $this->trx('RW', null, 'pengeluaran', 250000, 'Operasional');
        $this->trx('RW', null, 'pengeluaran', 777000, 'Operasional', 'Rejected'); // tidak dihitung
        $this->trx('RT', $this->rt->id, 'pemasukan', 5000000);                    // kas RT tidak tampil
        $this->trx('DKM', null, 'pemasukan', 3000000);                              // dana DKM tidak tampil

        $res = $this->actingAs($this->opRw)->get(route('opkeuangan.dashboard'));
        $res->assertOk()
            ->assertSee('Rp 650.000')
            ->assertDontSee('5.000.000')
            ->assertDontSee('3.000.000')
            ->assertDontSee('Dana Kematian')
            ->assertDontSee('Sheet Saldo')
            ->assertSee('Setoran Iuran RT')   // pemasukan per kategori
            ->assertSee('Operasional');       // pengeluaran per kategori
    }

    public function test_setoran_rt_dan_perlu_tindakan(): void
    {
        $master = MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => (int) now()->format('Y'), 'nama' => 'Iuran Wajib', 'nominal' => 60000, 'aktif' => true,
        ]);
        $kk = $this->kk($this->rt, '3217010101012211', 'Warga Satu');
        IuranPembayaran::create([
            'master_iuran_id' => $master->id, 'keluarga_id' => $kk->id, 'bulan' => (int) now()->format('n'),
            'status' => 'lunas', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara',
        ]);
        SetoranRtService::simpanPengaturan((int) now()->format('Y'), null, 20000, 1);

        // RT 961 mengajukan setoran sebagian yang menunggu konfirmasi.
        SetoranRt::create([
            'rt_id' => $this->rt->id, 'periode_bulan' => (int) now()->format('n'), 'periode_tahun' => (int) now()->format('Y'),
            'nominal' => 10000, 'tanggal_setor' => now()->toDateString(), 'metode' => 'tunai', 'status' => 'menunggu',
            'dibuat_oleh' => $this->opRt->id,
        ]);

        $this->actingAs($this->opRw)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Setoran RT menunggu konfirmasi')
            ->assertSee('RT yang belum menyetor penuh')
            ->assertSee('RT 961')
            ->assertSee('RT 962')
            ->assertSee('Rp 20.000'); // kewajiban RT 961 bulan ini
    }

    public function test_iuran_lainnya_rw_lewat_rt_dan_langsung_tampil_dengan_progres(): void
    {
        $kk1 = $this->kk($this->rt, '3217010101012221', 'Warga Dua');
        $kk2 = $this->kk($this->rt2, '3217010101012222', 'Warga Tiga');

        $viaRt = Tagihan::create([
            'id' => (string) Str::uuid(), 'judul' => 'Iuran Lewat RT', 'jenis' => 'insidental', 'nominal' => 50000,
            'periode_bulan' => 1, 'periode_tahun' => 2026, 'unit' => 'RW', 'via_rt' => true, 'status' => 'Active',
        ]);
        $langsung = Tagihan::create([
            'id' => (string) Str::uuid(), 'judul' => 'Iuran Langsung', 'jenis' => 'insidental', 'nominal' => 30000,
            'periode_bulan' => 1, 'periode_tahun' => 2026, 'unit' => 'RW', 'via_rt' => false, 'status' => 'Active',
        ]);
        foreach ([$viaRt, $langsung] as $t) {
            foreach ([$kk1, $kk2] as $kk) {
                PembayaranTagihan::create([
                    'id' => (string) Str::uuid(), 'tagihan_id' => $t->id, 'keluarga_id' => $kk->id, 'status' => 'Unpaid',
                ]);
            }
        }
        PembayaranTagihan::where('tagihan_id', $viaRt->id)->where('keluarga_id', $kk1->id)->update(['status' => 'Paid', 'nominal' => 50000]);
        PembayaranTagihan::where('tagihan_id', $langsung->id)->where('keluarga_id', $kk2->id)->update(['status' => 'Pending', 'nominal' => 30000]);

        $this->actingAs($this->opRw)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Iuran Lewat RT')
            ->assertSee('Lewat RT')
            ->assertSee('Iuran Langsung')
            ->assertSee('Langsung RW')
            ->assertSee('1/2 KK')
            ->assertSee('Belum disetor Rp 50.000')
            ->assertSee('Pembayaran Iuran Lainnya (langsung ke RW) menunggu verifikasi');
    }

    public function test_operator_dkm_tetap_memakai_dashboard_lama(): void
    {
        $dkm = User::create([
            'nik' => '3217010101012203', 'username' => 'dkm961', 'role' => 'DKM',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        $this->actingAs($dkm)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertDontSee('Dashboard Keuangan RW 21');
    }
}