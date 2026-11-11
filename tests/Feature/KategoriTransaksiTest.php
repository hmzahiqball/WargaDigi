<?php

namespace Tests\Feature;

use App\Models\KategoriTransaksi;
use App\Models\MasterRt;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Master kategori transaksi: keterikatan tipe, scope unit, dan validasi
 * saat mencatat transaksi.
 */
class KategoriTransaksiTest extends TestCase
{
    use RefreshDatabase;

    private function bendaharaRt(MasterRt $rt): User
    {
        return User::create([
            'nik' => '3217010101019801',
            'username' => 'bendahara_rt_kat',
            'role' => 'Op Keuangan RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'nik' => '3217010101019802',
            'username' => 'admin_kat',
            'role' => 'Admin Aplikasi',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
    }

    public function test_transaksi_tersimpan_dengan_kategori_valid(): void
    {
        $rt = MasterRt::create(['kode_rt' => '201', 'nama_rt' => 'RT 201']);
        $bendahara = $this->bendaharaRt($rt);
        $kat = KategoriTransaksi::create(['nama' => 'Iuran Bulanan', 'tipe' => 'pemasukan', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan',
                'judul' => 'Iuran Oktober',
                'kategori_id' => $kat->id,
                'tanggal' => now()->toDateString(),
                'jumlah' => '150000',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('transaksi_keuangan', [
            'judul' => 'Iuran Oktober',
            'kategori' => 'Iuran Bulanan',
            'kategori_id' => $kat->id,
            'tipe' => 'pemasukan',
        ]);
    }

    public function test_tolak_kategori_tidak_cocok_tipe(): void
    {
        $rt = MasterRt::create(['kode_rt' => '202', 'nama_rt' => 'RT 202']);
        $bendahara = $this->bendaharaRt($rt);
        // Kategori hanya untuk pengeluaran.
        $kat = KategoriTransaksi::create(['nama' => 'Belanja ATK', 'tipe' => 'pengeluaran', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan', // tidak cocok
                'judul' => 'Salah pasang',
                'kategori_id' => $kat->id,
                'tanggal' => now()->toDateString(),
                'jumlah' => '50000',
            ])
            ->assertSessionHasErrors('kategori_id');

        $this->assertDatabaseMissing('transaksi_keuangan', ['judul' => 'Salah pasang']);
    }

    public function test_kategori_keduanya_boleh_untuk_pemasukan_dan_pengeluaran(): void
    {
        $rt = MasterRt::create(['kode_rt' => '203', 'nama_rt' => 'RT 203']);
        $bendahara = $this->bendaharaRt($rt);
        $kat = KategoriTransaksi::create(['nama' => 'Lain-lain', 'tipe' => 'keduanya', 'aktif' => true]);

        foreach (['pemasukan', 'pengeluaran'] as $tipe) {
            $this->actingAs($bendahara)
                ->post(route('opkeuangan.transaksi.store'), [
                    'tipe' => $tipe,
                    'judul' => 'Netral ' . $tipe,
                    'kategori_id' => $kat->id,
                    'tanggal' => now()->toDateString(),
                    'jumlah' => '10000',
                ])
                ->assertRedirect();
        }

        $this->assertDatabaseHas('transaksi_keuangan', ['judul' => 'Netral pemasukan']);
        $this->assertDatabaseHas('transaksi_keuangan', ['judul' => 'Netral pengeluaran']);
    }

    public function test_rt_tidak_bisa_pakai_kategori_khusus_rw(): void
    {
        $rt = MasterRt::create(['kode_rt' => '204', 'nama_rt' => 'RT 204']);
        $bendahara = $this->bendaharaRt($rt);
        $katRw = KategoriTransaksi::create(['nama' => 'Setoran Antar RW', 'tipe' => 'pemasukan', 'unit' => 'RW', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan',
                'judul' => 'Pakai kategori RW',
                'kategori_id' => $katRw->id,
                'tanggal' => now()->toDateString(),
                'jumlah' => '10000',
            ])
            ->assertSessionHasErrors('kategori_id');
    }

    public function test_operator_rt_menambah_kategori_khusus_unitnya(): void
    {
        $rt = MasterRt::create(['kode_rt' => '205', 'nama_rt' => 'RT 205']);
        $bendahara = $this->bendaharaRt($rt);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.kategori-transaksi.store'), [
                'nama' => 'Kas Khusus RT 205',
                'tipe' => 'pemasukan',
                'aktif' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kategori_transaksi', [
            'nama' => 'Kas Khusus RT 205',
            'unit' => 'RT',
            'rt_id' => $rt->id,
        ]);
    }

    public function test_admin_menambah_kategori_umum(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.kategori-transaksi.store'), [
                'nama' => 'Kategori Umum Baru',
                'tipe' => 'keduanya',
                'aktif' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('kategori_transaksi', [
            'nama' => 'Kategori Umum Baru',
            'unit' => null,
        ]);
    }

    public function test_tolak_tanggal_transaksi_di_masa_depan(): void
    {
        $rt = MasterRt::create(['kode_rt' => '207', 'nama_rt' => 'RT 207']);
        $bendahara = $this->bendaharaRt($rt);
        $kat = KategoriTransaksi::create(['nama' => 'Iuran', 'tipe' => 'pemasukan', 'aktif' => true]);

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan',
                'judul' => 'Tanggal masa depan',
                'kategori_id' => $kat->id,
                'tanggal' => now()->addDays(3)->toDateString(),
                'jumlah' => '50000',
            ])
            ->assertSessionHasErrors('tanggal');

        $this->assertDatabaseMissing('transaksi_keuangan', ['judul' => 'Tanggal masa depan']);
    }

    public function test_search_dan_filter_kategori(): void
    {
        $admin = $this->admin();
        KategoriTransaksi::create(['nama' => 'Iuran Spesial', 'tipe' => 'pemasukan', 'aktif' => true]);
        KategoriTransaksi::create(['nama' => 'Belanja Rutin', 'tipe' => 'pengeluaran', 'aktif' => false]);

        // Search nama.
        $this->actingAs($admin)
            ->get(route('admin.kategori-transaksi.index', ['search' => 'Spesial']))
            ->assertOk()->assertSee('Iuran Spesial')->assertDontSee('Belanja Rutin');

        // Filter tipe pengeluaran.
        $this->actingAs($admin)
            ->get(route('admin.kategori-transaksi.index', ['tipe' => 'pengeluaran']))
            ->assertOk()->assertSee('Belanja Rutin')->assertDontSee('Iuran Spesial');

        // Filter status nonaktif.
        $this->actingAs($admin)
            ->get(route('admin.kategori-transaksi.index', ['status' => 'nonaktif']))
            ->assertOk()->assertSee('Belanja Rutin')->assertDontSee('Iuran Spesial');
    }

    public function test_hapus_kategori_terpakai_hanya_dinonaktifkan(): void
    {
        $rt = MasterRt::create(['kode_rt' => '206', 'nama_rt' => 'RT 206']);
        $bendahara = $this->bendaharaRt($rt);
        $kat = KategoriTransaksi::create(['nama' => 'Dipakai RT 206', 'tipe' => 'pemasukan', 'unit' => 'RT', 'rt_id' => $rt->id, 'aktif' => true]);
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-KAT-1', 'tipe' => 'pemasukan', 'kategori' => $kat->nama,
            'kategori_id' => $kat->id, 'judul' => 'Pakai', 'jumlah' => 1000, 'tanggal' => now(),
            'status' => 'Verified', 'unit_sumber' => 'RT', 'rt_id' => $rt->id,
        ]);

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.kategori-transaksi.destroy', $kat->id))
            ->assertRedirect();

        $this->assertDatabaseHas('kategori_transaksi', ['id' => $kat->id, 'aktif' => false]);
    }

    // ───────────────────────── Berlaku di RW, RT, DKM ─────────────────────────

    private ?User $rwCache = null;

    private function opRw(): User
    {
        return $this->rwCache ??= User::create([
            'nik' => '3217010101019811', 'username' => 'oprw_kat', 'role' => 'Op Keuangan RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    private ?User $dkmCache = null;

    private function opDkm(): User
    {
        return $this->dkmCache ??= User::create([
            'nik' => '3217010101019812', 'username' => 'dkm_kat', 'role' => 'DKM',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    private function buatRw(string $nama, array $berlakuDi, string $tipe = 'pemasukan')
    {
        return $this->actingAs($this->opRw())->post(route('opkeuangan.kategori-transaksi.store'), [
            'nama' => $nama, 'tipe' => $tipe, 'berlaku_di' => $berlakuDi,
        ]);
    }

    private function kategoriRw(string $nama, bool $rw, bool $rt, bool $dkm, string $tipe = 'pemasukan'): KategoriTransaksi
    {
        return KategoriTransaksi::create([
            'nama' => $nama, 'tipe' => $tipe, 'unit' => 'RW', 'berlaku_rw' => $rw, 'berlaku_rt' => $rt, 'berlaku_dkm' => $dkm,
        ]);
    }

    public function test_rw_membuat_kategori_dengan_kombinasi_kotak_centang(): void
    {
        $this->buatRw('Hanya RW', ['rw'])->assertSessionHas('success');
        $this->buatRw('Hanya RT', ['rt'])->assertSessionHas('success');
        $this->buatRw('Hanya DKM', ['dkm'])->assertSessionHas('success');
        $this->buatRw('RW dan DKM', ['rw', 'dkm'])->assertSessionHas('success');
        $this->buatRw('Semua Unit', ['rw', 'rt', 'dkm'])->assertSessionHas('success');

        $cek = fn (string $nama) => KategoriTransaksi::where('nama', $nama)->firstOrFail();
        $this->assertSame('RW', $cek('Hanya DKM')->unit); // tetap milik RW; hanya penandanya yang menentukan
        $this->assertSame(['RW'], $cek('Hanya RW')->unit_berlaku);
        $this->assertSame(['RT'], $cek('Hanya RT')->unit_berlaku);
        $this->assertSame(['DKM'], $cek('Hanya DKM')->unit_berlaku);
        $this->assertSame(['RW', 'DKM'], $cek('RW dan DKM')->unit_berlaku);
        $this->assertSame(['RW', 'RT', 'DKM'], $cek('Semua Unit')->unit_berlaku);
        $this->assertSame('RW dan DKM', $cek('RW dan DKM')->berlaku_di);
        $this->assertSame('Semua (RW, RT, DKM)', $cek('Semua Unit')->berlaku_di);
    }

    public function test_wajib_memilih_minimal_satu_unit_dan_nilai_valid(): void
    {
        $this->buatRw('Kosong', [])->assertSessionHasErrors('berlaku_di');
        $this->actingAs($this->opRw())->post(route('opkeuangan.kategori-transaksi.store'), ['nama' => 'Tanpa Pilihan', 'tipe' => 'pemasukan'])
            ->assertSessionHasErrors('berlaku_di');
        $this->buatRw('Nilai Salah', ['rw', 'desa'])->assertSessionHasErrors('berlaku_di.1');

        $this->assertDatabaseMissing('kategori_transaksi', ['nama' => 'Kosong']);
        $this->assertDatabaseMissing('kategori_transaksi', ['nama' => 'Tanpa Pilihan']);
    }

    public function test_kategori_tersedia_sesuai_unit_penerapan(): void
    {
        $this->kategoriRw('Hanya RW', true, false, false);
        $this->kategoriRw('Hanya RT', false, true, false);
        $this->kategoriRw('Hanya DKM', false, false, true);
        $this->kategoriRw('RW dan DKM', true, false, true);
        $this->kategoriRw('Semua Unit', true, true, true);
        KategoriTransaksi::create(['nama' => 'Umum Semua', 'tipe' => 'pemasukan', 'unit' => null]);

        $nama = fn (string $unit) => KategoriTransaksi::untukUnit($unit)->pluck('nama')->all();

        $this->assertEqualsCanonicalizing(['Hanya RW', 'RW dan DKM', 'Semua Unit', 'Umum Semua'], $nama('RW'));
        $this->assertEqualsCanonicalizing(['Hanya RT', 'Semua Unit', 'Umum Semua'], $nama('RT'));
        // DKM hanya melihat kategori yang diterapkan untuk DKM (dan buatannya sendiri); kategori umum tidak ikut.
        $this->assertEqualsCanonicalizing(['Hanya DKM', 'RW dan DKM', 'Semua Unit'], $nama('DKM'));
        $this->assertNotContains('Umum Semua', $nama('DKM'));
    }

    public function test_form_iuran_lainnya_dkm_hanya_menampilkan_kategori_dkm(): void
    {
        KategoriTransaksi::create(['nama' => 'Iuran Umum Admin', 'tipe' => 'pemasukan', 'unit' => null]);
        $this->kategoriRw('Donasi Khusus DKM', false, false, true);
        $this->kategoriRw('Hanya Milik RW', true, false, false);
        $this->kategoriRw('Penyaluran DKM', false, false, true, 'pengeluaran');

        $res = $this->actingAs($this->opDkm())->get(route('opkeuangan.iuran.index'));
        $res->assertOk()
            ->assertSee('Donasi Khusus DKM')
            ->assertDontSee('Iuran Umum Admin')
            ->assertDontSee('Hanya Milik RW')
            ->assertDontSee('Penyaluran DKM'); // kategori pengeluaran tidak muncul pada form pemasukan

        // Halaman catat transaksi DKM juga dibatasi pada kategori DKM.
        $this->actingAs($this->opDkm())->get(route('opkeuangan.transaksi.index'))
            ->assertOk()
            ->assertSee('Donasi Khusus DKM')
            ->assertSee('Penyaluran DKM')
            ->assertDontSee('Iuran Umum Admin')
            ->assertDontSee('Hanya Milik RW');

        // Kategori umum ditolak saat DKM mencatat transaksi.
        $umum = KategoriTransaksi::where('nama', 'Iuran Umum Admin')->first();
        $this->actingAs($this->opDkm())->post(route('opkeuangan.transaksi.store'), [
            'tipe' => 'pemasukan', 'judul' => 'Pakai Umum', 'kategori_id' => $umum->id,
            'tanggal' => now()->toDateString(), 'jumlah' => '1000',
        ]);
        $this->assertDatabaseMissing('transaksi_keuangan', ['judul' => 'Pakai Umum']);
    }

    public function test_dkm_dan_rt_memakai_kategori_yang_diterapkan_untuknya(): void
    {
        $semua = $this->kategoriRw('Dana Bersama', true, true, true);
        $hanyaRw = $this->kategoriRw('Khusus RW', true, false, false);
        $rt = MasterRt::create(['kode_rt' => '231', 'nama_rt' => 'RT 231']);
        $bendahara = $this->bendaharaRt($rt);
        $dkm = $this->opDkm();

        foreach ([$dkm, $bendahara] as $pencatat) {
            $this->actingAs($pencatat)->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan', 'judul' => 'Dana oleh ' . $pencatat->role, 'kategori_id' => $semua->id,
                'tanggal' => now()->toDateString(), 'jumlah' => '50000',
            ])->assertRedirect();
            $this->assertDatabaseHas('transaksi_keuangan', ['judul' => 'Dana oleh ' . $pencatat->role, 'kategori_id' => $semua->id]);

            $this->actingAs($pencatat)->post(route('opkeuangan.transaksi.store'), [
                'tipe' => 'pemasukan', 'judul' => 'Salah ' . $pencatat->role, 'kategori_id' => $hanyaRw->id,
                'tanggal' => now()->toDateString(), 'jumlah' => '1000',
            ]);
            $this->assertDatabaseMissing('transaksi_keuangan', ['judul' => 'Salah ' . $pencatat->role]);
        }
    }

    public function test_operator_dkm_membuat_kategori_pengeluaran_saja(): void
    {
        $dkm = $this->opDkm();

        // Pengeluaran diperbolehkan.
        $this->actingAs($dkm)->post(route('opkeuangan.kategori-transaksi.store'), ['nama' => 'Biaya Pemakaman', 'tipe' => 'pengeluaran'])
            ->assertSessionHas('success');
        $kat = KategoriTransaksi::where('nama', 'Biaya Pemakaman')->firstOrFail();
        $this->assertSame('DKM', $kat->unit);
        $this->assertSame('pengeluaran', $kat->tipe);

        // Pemasukan dan keduanya ditolak.
        foreach (['pemasukan', 'keduanya'] as $tipe) {
            $this->actingAs($dkm)->post(route('opkeuangan.kategori-transaksi.store'), ['nama' => 'Tidak Boleh ' . $tipe, 'tipe' => $tipe])
                ->assertSessionHasErrors('tipe');
            $this->assertDatabaseMissing('kategori_transaksi', ['nama' => 'Tidak Boleh ' . $tipe]);
        }

        // Mengubah tipe menjadi pemasukan juga ditolak.
        $this->actingAs($dkm)->put(route('opkeuangan.kategori-transaksi.update', $kat->id), ['nama' => 'Biaya Pemakaman', 'tipe' => 'pemasukan'])
            ->assertSessionHasErrors('tipe');
        $this->assertSame('pengeluaran', $kat->fresh()->tipe);

        // Mengubah nama dan menghapus kategori sendiri diperbolehkan.
        $this->actingAs($dkm)->put(route('opkeuangan.kategori-transaksi.update', $kat->id), ['nama' => 'Biaya Pemakaman Baru', 'tipe' => 'pengeluaran'])
            ->assertSessionHas('success');
        $this->assertSame('Biaya Pemakaman Baru', $kat->fresh()->nama);

        // Menu Kategori Transaksi tampil kembali untuk DKM.
        $menu = collect(config('sidebarMenu'))->firstWhere('route', 'opkeuangan.kategori-transaksi.index');
        $this->assertContains('DKM', $menu['roles']);

        // Kategori buatan DKM dipakai DKM untuk mencatat pengeluaran.
        $this->actingAs($dkm)->post(route('opkeuangan.transaksi.store'), [
            'tipe' => 'pengeluaran', 'judul' => 'Kain kafan', 'kategori_id' => $kat->id,
            'tanggal' => now()->toDateString(), 'jumlah' => '75000',
        ])->assertRedirect();
        $this->assertDatabaseHas('transaksi_keuangan', ['judul' => 'Kain kafan', 'kategori_id' => $kat->id, 'unit_sumber' => 'DKM']);

        $this->actingAs($dkm)->delete(route('opkeuangan.kategori-transaksi.destroy', $kat->id))->assertSessionHas('success');
        $this->assertNotNull($kat->fresh()); // sudah dipakai transaksi: dinonaktifkan, bukan dihapus
        $this->assertFalse($kat->fresh()->aktif);
    }

    public function test_operator_dkm_tidak_dapat_mengelola_kategori_rw_atau_umum_dan_daftarnya_terbatas(): void
    {
        $dkm = $this->opDkm();
        $milikRw = $this->kategoriRw('Dana RW untuk DKM', false, false, true);
        $rwSaja = $this->kategoriRw('Hanya RW', true, false, false);
        $umum = KategoriTransaksi::create(['nama' => 'Kategori Umum Admin', 'tipe' => 'pemasukan', 'unit' => null]);
        KategoriTransaksi::create(['nama' => 'Buatan DKM Sendiri', 'tipe' => 'pengeluaran', 'unit' => 'DKM']);

        $this->actingAs($dkm)->get(route('opkeuangan.kategori-transaksi.index'))
            ->assertOk()
            ->assertSee('Buatan DKM Sendiri')
            ->assertSee('Dana RW untuk DKM')     // dari RW (referensi)
            ->assertSee('Dari RW')
            ->assertDontSee('Hanya RW')
            ->assertDontSee('Kategori Umum Admin')
            ->assertSee('tipe-dkm-tetap', false)
            ->assertDontSee('pilihan-berlaku-di', false);

        foreach ([$milikRw, $rwSaja, $umum] as $bukanMilikDkm) {
            $this->actingAs($dkm)->put(route('opkeuangan.kategori-transaksi.update', $bukanMilikDkm->id), ['nama' => 'Diubah', 'tipe' => 'pengeluaran'])->assertForbidden();
            $this->actingAs($dkm)->delete(route('opkeuangan.kategori-transaksi.destroy', $bukanMilikDkm->id))->assertForbidden();
        }
        $this->assertSame('Dana RW untuk DKM', $milikRw->fresh()->nama);
    }

    public function test_rw_melihat_kategori_buatan_dkm_tetapi_tidak_dapat_mengubahnya(): void
    {
        $rw = $this->opRw();
        $milikDkm = KategoriTransaksi::create(['nama' => 'Buatan DKM', 'tipe' => 'pengeluaran', 'unit' => 'DKM']);

        $this->actingAs($rw)->get(route('opkeuangan.kategori-transaksi.index'))
            ->assertOk()->assertSee('Buatan DKM')->assertSee('Dikelola DKM');

        $this->actingAs($rw)->put(route('opkeuangan.kategori-transaksi.update', $milikDkm->id), [
            'nama' => 'Diubah RW', 'tipe' => 'pengeluaran', 'berlaku_di' => ['rw'],
        ])->assertForbidden();
        $this->actingAs($rw)->delete(route('opkeuangan.kategori-transaksi.destroy', $milikDkm->id))->assertForbidden();
        $this->assertSame('Buatan DKM', $milikDkm->fresh()->nama);
    }
    public function test_form_rw_memuat_kotak_centang_dan_pilih_semua(): void
    {
        $this->kategoriRw('Dana RW DKM', true, false, true);

        $this->actingAs($this->opRw())->get(route('opkeuangan.kategori-transaksi.index'))
            ->assertOk()
            ->assertSee('pilihan-berlaku-di', false)
            ->assertSee('name="berlaku_di[]"', false)
            ->assertSee('data-pilih-semua', false)
            ->assertSee('Pilih semua')
            ->assertSee('RW dan DKM')
            ->assertDontSee('DKM saja');
    }

    public function test_rt_melihat_kategori_rw_untuk_rt_sebagai_referensi_tanpa_bisa_mengubah(): void
    {
        $rt = MasterRt::create(['kode_rt' => '232', 'nama_rt' => 'RT 232']);
        $bendahara = $this->bendaharaRt($rt);
        $dariRw = $this->kategoriRw('Dana Dari RW', false, true, false);
        $this->kategoriRw('Tidak Untuk RT', true, false, false);

        $this->actingAs($bendahara)->get(route('opkeuangan.kategori-transaksi.index'))
            ->assertOk()
            ->assertSee('Dana Dari RW')
            ->assertSee('Dari RW')
            ->assertDontSee('Tidak Untuk RT')
            ->assertDontSee('pilihan-berlaku-di', false);

        $this->actingAs($bendahara)->put(route('opkeuangan.kategori-transaksi.update', $dariRw->id), ['nama' => 'Dicuri', 'tipe' => 'pemasukan'])->assertForbidden();
        $this->actingAs($bendahara)->delete(route('opkeuangan.kategori-transaksi.destroy', $dariRw->id))->assertForbidden();
        $this->assertSame('Dana Dari RW', $dariRw->fresh()->nama);
    }

    public function test_rw_mengelola_kategori_rw_tetapi_tidak_kategori_rt(): void
    {
        $rw = $this->opRw();
        $kat = $this->kategoriRw('Infak', true, false, true);

        $this->actingAs($rw)->put(route('opkeuangan.kategori-transaksi.update', $kat->id), [
            'nama' => 'Infak Baru', 'tipe' => 'pemasukan', 'berlaku_di' => ['rw', 'dkm'],
        ])->assertSessionHas('success');
        $this->assertSame('Infak Baru', $kat->fresh()->nama);

        $rtKat = KategoriTransaksi::create(['nama' => 'Milik RT', 'tipe' => 'pemasukan', 'unit' => 'RT']);
        $this->actingAs($rw)->put(route('opkeuangan.kategori-transaksi.update', $rtKat->id), ['nama' => 'Dicuri', 'tipe' => 'pemasukan', 'berlaku_di' => ['rw']])->assertForbidden();
    }

    public function test_unit_boleh_ditambah_dan_hanya_dilepas_bila_belum_dipakai(): void
    {
        $rw = $this->opRw();
        $kat = $this->kategoriRw('Dana Bersama', true, false, false);
        $ubah = fn (array $berlaku) => $this->actingAs($rw)->put(route('opkeuangan.kategori-transaksi.update', $kat->id), [
            'nama' => 'Dana Bersama', 'tipe' => 'pemasukan', 'berlaku_di' => $berlaku,
        ]);

        // Menambah unit: selalu boleh.
        $ubah(['rw', 'rt', 'dkm'])->assertSessionHas('success');
        $this->assertSame(['RW', 'RT', 'DKM'], $kat->fresh()->unit_berlaku);

        // DKM sudah memakai kategori ini.
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-KAT-DKM', 'tipe' => 'pemasukan', 'kategori' => 'Dana Bersama', 'kategori_id' => $kat->id,
            'judul' => 'Infak', 'jumlah' => 10000, 'tanggal' => now()->toDateString(), 'status' => 'Verified', 'unit_sumber' => 'DKM',
        ]);

        // Melepas DKM ditolak, melepas RT (belum dipakai) diperbolehkan.
        $ubah(['rw', 'rt'])->assertSessionHas('error');
        $this->assertSame(['RW', 'RT', 'DKM'], $kat->fresh()->unit_berlaku);
        $ubah(['rw', 'dkm'])->assertSessionHas('success');
        $this->assertSame(['RW', 'DKM'], $kat->fresh()->unit_berlaku);

        // Tanpa memilih unit sama sekali ditolak.
        $ubah([])->assertSessionHasErrors('berlaku_di');
    }

    public function test_kategori_dkm_lama_dipindah_menjadi_kategori_rw_khusus_dkm(): void
    {
        // Simulasi data lama: unit DKM. Migrasi mengubahnya menjadi unit RW dengan penanda hanya DKM.
        $lama = KategoriTransaksi::create(['nama' => 'Lama DKM', 'tipe' => 'pemasukan', 'unit' => 'DKM']);
        \Illuminate\Support\Facades\DB::table('kategori_transaksi')->where('unit', 'DKM')->update([
            'unit' => 'RW', 'berlaku_rw' => false, 'berlaku_dkm' => true,
        ]);

        $lama->refresh();
        $this->assertSame('RW', $lama->unit);
        $this->assertSame(['DKM'], $lama->unit_berlaku);
        $this->assertContains('Lama DKM', KategoriTransaksi::untukUnit('DKM')->pluck('nama')->all());
        $this->assertNotContains('Lama DKM', KategoriTransaksi::untukUnit('RW')->pluck('nama')->all());
    }
}