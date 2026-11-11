<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Tab Statistik laporan keuangan untuk pimpinan: perhitungan dan batas akses data. */
class StatistikLaporanKeuanganTest extends TestCase
{
    use RefreshDatabase;

    private int $tahun;
    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $ketuaRw;
    private User $ketuaRt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tahun = (int) now()->format('Y') - 1; // tahun lampau: semua bulan sudah lewat
        $this->rt1 = MasterRt::create(['kode_rt' => '971', 'nama_rt' => 'RT 971']);
        $this->rt2 = MasterRt::create(['kode_rt' => '972', 'nama_rt' => 'RT 972']);
        $this->ketuaRw = User::create([
            'nik' => '3217010101012401', 'username' => 'ketuarw971', 'role' => 'Ketua RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $this->ketuaRt = User::create([
            'nik' => '3217010101012402', 'username' => 'ketuart971', 'role' => 'Ketua RT',
            'status_akun' => 'Active', 'rt_id' => $this->rt1->id, 'password' => 'password123',
        ]);
    }

    private function laporan(string $unit, int $bulan, int $awal, int $masuk, int $keluar, bool $terbit = true, ?string $rtId = null, string $status = 'Approved'): LaporanKeuangan
    {
        return LaporanKeuangan::create([
            'unit' => $unit, 'rt_id' => $rtId, 'periode_bulan' => $bulan, 'periode_tahun' => $this->tahun,
            'judul' => "Laporan {$unit} {$bulan}", 'saldo_awal' => $awal, 'total_pemasukan' => $masuk,
            'total_pengeluaran' => $keluar, 'saldo_akhir' => $awal + $masuk - $keluar,
            'status' => $status, 'is_published' => $terbit,
        ]);
    }

    private function trx(string $unit, string $tipe, int $jumlah, string $kategori, int $bulan, ?string $rtId = null, string $status = 'Verified', ?string $bukti = null): void
    {
        static $n = 0;
        $n++;
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-ST-' . $n, 'tipe' => $tipe, 'kategori' => $kategori, 'judul' => 'Uji ' . $n,
            'jumlah' => $jumlah, 'tanggal' => sprintf('%d-%02d-10', $this->tahun, $bulan), 'status' => $status,
            'unit_sumber' => $unit, 'rt_id' => $rtId, 'bukti_file' => $bukti,
        ]);
    }

    private function halaman(User $user, array $query)
    {
        return $this->actingAs($user)->get(route('laporan-keuangan.publik', array_merge(['tab' => 'statistik', 'tahun' => $this->tahun], $query)));
    }

    private function kk(MasterRt $rt, string $nik): Keluarga
    {
        $k = Keluarga::create(['no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji', 'rt_id' => $rt->id, 'no_wa' => '081234567890']);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => 'Warga ' . $nik, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Belum Kawin',
        ]);

        return $k;
    }

    public function test_ringkasan_cadangan_rasio_dan_perubahan_dihitung_dari_laporan_disetujui(): void
    {
        $this->laporan('RW', 1, 1000000, 500000, 200000);
        $this->laporan('RW', 2, 1300000, 300000, 600000);
        $this->laporan('RW', 3, 1000000, 200000, 400000);
        $this->laporan('RW', 4, 800000, 9999999, 0, true, null, 'Draft'); // bukan Approved: tidak dihitung
        $this->trx('RW', 'pengeluaran', 600000, 'Kebersihan', 2);
        $this->trx('RW', 'pengeluaran', 400000, 'Operasional', 3);

        $this->halaman($this->ketuaRw, ['unit' => 'RW'])
            ->assertOk()
            ->assertSee('data-testid="penilaian-statistik"', false)
            ->assertSee('Perlu diperhatikan')           // cadangan 2,0 bulan (<3) dan rasio 120%
            ->assertSee('2,0 bulan')
            ->assertSee('120%')
            ->assertSee('Pengeluaran turun 33% dibanding Februari')
            ->assertSee('Rp 800.000')                    // saldo terakhir
            ->assertDontSee('9.999.999')
            ->assertSee('Kebersihan')
            ->assertSee('data-testid="perubahan-kategori"', false);
    }

    public function test_tab_statistik_tidak_tersedia_bagi_operator_dan_warga(): void
    {
        $this->laporan('RW', 1, 0, 100000, 0);
        $op = User::create([
            'nik' => '3217010101012403', 'username' => 'oprw971', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        $this->halaman($op, ['unit' => 'RW'])->assertOk()
            ->assertDontSee('data-testid="penilaian-statistik"', false)
            ->assertDontSee('data-testid="tab-statistik"', false);
    }

    public function test_ketua_rt_hanya_melihat_laporan_terbit_untuk_unit_lain(): void
    {
        $this->laporan('RW', 1, 0, 100000, 0, true);   // terbit
        $this->laporan('RW', 2, 100000, 777000, 0, false); // disetujui tetapi belum terbit

        $this->halaman($this->ketuaRt, ['unit' => 'RW'])->assertOk()
            ->assertSee('Rp 100.000')
            ->assertDontSee('777.000')
            ->assertSee('dari laporan yang sudah dipublikasikan');

        // Pimpinan RW melihat laporan yang sudah disetujui walau belum terbit.
        $this->halaman($this->ketuaRw, ['unit' => 'RW'])->assertOk()->assertSee('Rp 877.000');
    }

    public function test_ketua_rt_tidak_melihat_rt_lain_dan_ketua_rw_melihat_perbandingan_per_rt(): void
    {
        $this->laporan('RT', 1, 0, 300000, 0, true, $this->rt1->id);
        $this->laporan('RT', 1, 0, 555000, 0, true, $this->rt2->id);

        $this->halaman($this->ketuaRt, ['unit' => 'RT'])->assertOk()
            ->assertSee('Rp 300.000')
            ->assertDontSee('555.000')
            ->assertDontSee('data-testid="tabel-per-rt"', false);

        $this->halaman($this->ketuaRw, ['unit' => 'RT'])->assertOk()
            ->assertSee('Rp 855.000')                    // gabungan semua RT
            ->assertSee('data-testid="tabel-per-rt"', false)
            ->assertSee('RT 971')->assertSee('RT 972')
            ->assertSee('bukan peringkat');

        $this->halaman($this->ketuaRw, ['unit' => 'RT', 'rt' => $this->rt2->id])->assertOk()
            ->assertSee('Rp 555.000')
            ->assertDontSee('data-testid="tabel-per-rt"', false);
    }

    public function test_kepatuhan_iuran_dan_tunggakan_dihitung_per_kk(): void
    {
        $this->laporan('RT', 1, 0, 100000, 0, true, $this->rt1->id);
        $master = MasterIuran::create(['rt_id' => $this->rt1->id, 'tahun' => $this->tahun, 'nama' => 'Iuran Wajib', 'nominal' => 50000, 'aktif' => true]);
        $a = $this->kk($this->rt1, '3217010101012411');
        $b = $this->kk($this->rt1, '3217010101012412');
        foreach (range(1, 12) as $bln) {
            IuranPembayaran::create(['master_iuran_id' => $master->id, 'keluarga_id' => $a->id, 'bulan' => $bln, 'status' => 'lunas', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara']);
        }
        foreach (range(1, 10) as $bln) {
            IuranPembayaran::create(['master_iuran_id' => $master->id, 'keluarga_id' => $b->id, 'bulan' => $bln, 'status' => 'lunas', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara']);
        }

        // (12 + 10) / (2 KK x 12 bulan) = 92%; KK kedua menunggak 2 bulan.
        $this->halaman($this->ketuaRt, ['unit' => 'RT'])->assertOk()
            ->assertSee('data-testid="kesehatan-iuran"', false)
            ->assertSee('92%')
            ->assertSee('1 KK menunggak iuran 2 bulan atau lebih');
    }

    public function test_tata_kelola_menghitung_bukti_dan_pengeluaran_belum_diputuskan(): void
    {
        $this->laporan('RW', 1, 0, 100000, 0);
        $this->laporan('RW', 1, 0, 1, 1, true, null, 'Rejected');
        $this->trx('RW', 'pengeluaran', 700000, 'Operasional', 1, null, 'Verified', 'bukti/a.jpg');
        $this->trx('RW', 'pengeluaran', 900000, 'Operasional', 1, null, 'Verified', null);  // besar tanpa bukti
        $this->trx('RW', 'pengeluaran', 150000, 'Operasional', 1, null, 'Pending', null);   // belum diputuskan

        $this->halaman($this->ketuaRw, ['unit' => 'RW'])->assertOk()
            ->assertSee('data-testid="tata-kelola"', false)
            ->assertSee('33%')                                   // 1 dari 3 pengeluaran berbukti
            ->assertSee('1 pengeluaran (Rp 150.000) belum diputuskan')
            ->assertSee('1 pengeluaran sebesar Rp 500.000 atau lebih tidak memiliki bukti')
            ->assertSee('1 laporan ditolak');

        // Ketua RT melihat RW hanya dari laporan terbit: data operasional RW tidak dibuka.
        $this->halaman($this->ketuaRt, ['unit' => 'RW'])->assertOk()
            ->assertDontSee('data-testid="tata-kelola"', false)
            ->assertDontSee('belum diputuskan');
    }

    public function test_tanpa_laporan_menampilkan_keadaan_kosong(): void
    {
        $this->halaman($this->ketuaRw, ['unit' => 'DKM'])->assertOk()
            ->assertSee('Belum ada laporan yang disetujui')
            ->assertSee('Belum ada data untuk dianalisis');
    }
}
