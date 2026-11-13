<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\KategoriUmkm;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\UmkmUsaha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Alur UMKM:
 * - warga (termasuk Ketua RT yang punya peran Warga) mendaftarkan usaha, boleh lebih dari satu;
 * - kategori hanya dari master;
 * - verifikasi, nonaktif/aktif kembali hanya oleh Admin RW (dikunci di server);
 * - usaha ditolak dapat diperbaiki lalu diajukan ulang.
 */
class UmkmAlurTest extends TestCase
{
    use RefreshDatabase;

    private KategoriUmkm $kuliner;
    private KategoriUmkm $koperasi;
    private MasterRt $rt;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['Warga', 'Ketua RT', 'Admin RW', 'Ketua RW'] as $r) {
            Role::findOrCreate($r, 'web');
        }
        $this->kuliner = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Kuliner Uji']);
        $this->koperasi = KategoriUmkm::firstOrCreate(['nama_kategori' => 'Koperasi']);
        $this->rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
    }

    private function akun(string $role, string $nik, array $peran = []): User
    {
        $u = User::create(['nik' => $nik, 'username' => 'u' . $nik, 'role' => $role, 'status_akun' => 'Active',
            'rt_id' => $this->rt->id, 'password' => 'password123']);
        $u->syncRoles($peran ?: [$role]);

        return $u;
    }

    private function warga(string $nik = '3217010101015001', array $peran = ['Warga']): User
    {
        $kk = Keluarga::create(['no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat', 'rt_id' => $this->rt->id, 'no_wa' => '081200000000']);
        Penduduk::create(['keluarga_id' => $kk->id, 'nik' => $nik, 'nama_lengkap' => 'Pemilik ' . $nik, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin']);

        return $this->akun($peran[0], $nik, $peran);
    }

    private function dataUsaha(array $ubah = []): array
    {
        return array_merge([
            'nama_usaha' => 'Warung Uji', 'kategori_umkm_id' => $this->kuliner->id,
            'alamat_usaha' => 'Jl. Uji 1', 'no_wa' => '81200000000', 'deskripsi' => 'Usaha uji',
        ], $ubah);
    }

    private function usaha(User $pemilik, string $status = 'Pending', bool $aktif = false): UmkmUsaha
    {
        return UmkmUsaha::forceCreate(['nik' => $pemilik->nik, 'kategori_umkm_id' => $this->kuliner->id,
            'nama_usaha' => 'Usaha ' . $status, 'alamat_usaha' => 'Jl. Uji', 'no_wa' => '081200000000',
            'status_verifikasi' => $status, 'is_active' => $aktif,
            'catatan_verifikasi' => $status === 'Rejected' ? 'Foto kurang jelas' : null]);
    }

    // ───────────── Pendaftaran & kategori ─────────────

    public function test_warga_mendaftar_usaha_berstatus_pending_dan_boleh_lebih_dari_satu(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha())->assertSessionHasNoErrors();
        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['nama_usaha' => 'Usaha Kedua']))->assertSessionHasNoErrors();

        $this->assertSame(2, UmkmUsaha::where('nik', $warga->nik)->where('status_verifikasi', 'Pending')->where('is_active', false)->count());
    }

    public function test_kategori_harus_dari_master_dan_tidak_membuat_kategori_baru(): void
    {
        $warga = $this->warga();
        $jumlahAwal = KategoriUmkm::count();

        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['kategori_umkm_id' => 'Kategori Karangan']))
            ->assertSessionHasErrors('kategori_umkm_id');
        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['kategori_umkm_id' => null, 'kategori' => 'Kategori Lain']))
            ->assertSessionHasErrors('kategori_umkm_id');

        $usaha = $this->usaha($warga, 'Approved', true);
        $this->actingAs($warga)->put(route('warga.umkm.update-usaha', $usaha->id), $this->dataUsaha(['kategori_umkm_id' => 'Kategori Edit']))
            ->assertSessionHasErrors('kategori_umkm_id');

        $this->assertSame($jumlahAwal, KategoriUmkm::count());
        $this->assertSame(0, UmkmUsaha::where('nama_usaha', 'Warung Uji')->count());
    }

    public function test_kategori_koperasi_ditolak_untuk_usaha_baru(): void
    {
        $this->actingAs($this->warga())->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['kategori_umkm_id' => $this->koperasi->id]))
            ->assertSessionHasErrors('kategori_umkm_id');
    }

    public function test_ketua_rt_yang_punya_peran_warga_bisa_mendaftarkan_usaha(): void
    {
        $ketuaRt = $this->warga('3217010101015009', ['Ketua RT', 'Warga']);

        // Dengan peran aktif Ketua RT, menu warga tidak bisa dibuka.
        $this->actingAs($ketuaRt)->withSession(['active_role' => 'Ketua RT'])
            ->post(route('warga.umkm.store-usaha'), $this->dataUsaha())->assertRedirect();
        $this->assertSame(0, UmkmUsaha::count());

        // Setelah beralih ke peran Warga, pendaftaran berhasil.
        $this->actingAs($ketuaRt)->withSession(['active_role' => 'Warga'])
            ->post(route('warga.umkm.store-usaha'), $this->dataUsaha())->assertSessionHasNoErrors();
        $this->assertSame(1, UmkmUsaha::where('nik', $ketuaRt->nik)->count());
    }

    // ───────────── Verifikasi hanya Admin RW ─────────────

    public function test_hanya_admin_rw_yang_bisa_menyetujui_dan_menolak(): void
    {
        $usaha = $this->usaha($this->warga());
        $ketuaRw = $this->akun('Ketua RW', '3217010101010003');

        $this->actingAs($ketuaRw)->post(route('rw.umkm.approve', $usaha->id))->assertForbidden();
        $this->actingAs($ketuaRw)->post(route('rw.umkm.reject', $usaha->id))->assertForbidden();
        $this->assertSame('Pending', $usaha->fresh()->status_verifikasi);

        $this->actingAs($this->akun('Admin RW', '3217010101010002'))->post(route('rw.umkm.approve', $usaha->id))->assertSessionHas('success');
        $this->assertSame('Approved', $usaha->fresh()->status_verifikasi);
        $this->assertTrue((bool) $usaha->fresh()->is_active);
    }

    public function test_setujui_dan_tolak_dua_kali_tidak_error_dan_notifikasi_sekali(): void
    {
        $pemilik = $this->warga();
        $admin = $this->akun('Admin RW', '3217010101010002');
        $a = $this->usaha($pemilik);
        $b = $this->usaha($this->warga('3217010101015002'));

        $this->actingAs($admin)->post(route('rw.umkm.approve', $a->id))->assertSessionHas('success');
        $this->actingAs($admin)->post(route('rw.umkm.approve', $a->id))
            ->assertSessionHas('success')->assertSessionMissing('error');
        $this->assertSame(1, $pemilik->notifications()->count());

        $this->actingAs($admin)->post(route('rw.umkm.reject', $b->id), ['catatan_verifikasi' => 'Kurang lengkap'])->assertSessionHas('success');
        $this->actingAs($admin)->post(route('rw.umkm.reject', $b->id), ['catatan_verifikasi' => 'Kurang lengkap'])
            ->assertSessionHas('success')->assertSessionMissing('error');
        $this->assertSame('Rejected', $b->fresh()->status_verifikasi);

        // Usaha ditolak tetap tidak bisa langsung disetujui (harus diajukan ulang dulu).
        $this->actingAs($admin)->post(route('rw.umkm.approve', $b->id))->assertSessionHas('error');
    }

    // ───────────── Ditolak → ajukan ulang ─────────────

    public function test_usaha_ditolak_tampil_di_kelola_dan_bisa_diajukan_ulang(): void
    {
        $warga = $this->warga();
        $usaha = $this->usaha($warga, 'Rejected');

        $this->actingAs($warga)->get(route('warga.umkm.kelola', ['usaha_id' => $usaha->id]))
            ->assertOk()
            ->assertSee('data-testid="banner-umkm-ditolak"', false)
            ->assertSee('Foto kurang jelas')
            ->assertSee(route('warga.umkm.ajukan-ulang', $usaha->id), false);

        $this->actingAs($warga)->put(route('warga.umkm.ajukan-ulang', $usaha->id), $this->dataUsaha(['nama_usaha' => 'Usaha Diperbaiki']))
            ->assertSessionHasNoErrors()->assertSessionHas('success');

        $baru = $usaha->fresh();
        $this->assertSame('Pending', $baru->status_verifikasi);
        $this->assertSame('Usaha Diperbaiki', $baru->nama_usaha);
        $this->assertNull($baru->catatan_verifikasi);
        $this->assertFalse((bool) $baru->is_active);
    }

    public function test_ajukan_ulang_hanya_pemilik_dan_hanya_usaha_ditolak(): void
    {
        $pemilik = $this->warga();
        $ditolak = $this->usaha($pemilik, 'Rejected');
        $disetujui = $this->usaha($pemilik, 'Approved', true);
        $lain = $this->warga('3217010101015002');

        $this->actingAs($lain)->put(route('warga.umkm.ajukan-ulang', $ditolak->id), $this->dataUsaha())->assertForbidden();
        $this->actingAs($pemilik)->put(route('warga.umkm.ajukan-ulang', $disetujui->id), $this->dataUsaha())->assertSessionHas('error');

        $this->assertSame('Rejected', $ditolak->fresh()->status_verifikasi);
        $this->assertSame('Approved', $disetujui->fresh()->status_verifikasi);
    }

    // ───────────── Nonaktifkan / aktifkan ─────────────

    public function test_admin_rw_menonaktifkan_dan_mengaktifkan_kembali_usaha(): void
    {
        $pemilik = $this->warga();
        $usaha = $this->usaha($pemilik, 'Approved', true);
        $admin = $this->akun('Admin RW', '3217010101010002');

        // Alasan wajib.
        $this->actingAs($admin)->post(route('rw.umkm.nonaktifkan', $usaha->id), [])->assertSessionHasErrors('catatan_verifikasi');

        $this->actingAs($admin)->post(route('rw.umkm.nonaktifkan', $usaha->id), ['catatan_verifikasi' => 'Nomor WA tidak aktif'])
            ->assertSessionHas('success');
        $this->assertFalse((bool) $usaha->fresh()->is_active);
        $this->assertSame('Nomor WA tidak aktif', $usaha->fresh()->catatan_verifikasi);

        // Tidak tampil untuk publik, pemilik melihat banner alasan.
        auth()->logout();
        $this->get(route('umkm.galeri'))->assertOk()->assertDontSee('Usaha Approved');
        $this->actingAs($pemilik)->get(route('warga.umkm.kelola', ['usaha_id' => $usaha->id]))
            ->assertOk()->assertSee('data-testid="banner-umkm-nonaktif"', false)->assertSee('Nomor WA tidak aktif');

        $this->actingAs($admin)->post(route('rw.umkm.aktifkan', $usaha->id))->assertSessionHas('success');
        $this->assertTrue((bool) $usaha->fresh()->is_active);
        $this->assertNull($usaha->fresh()->catatan_verifikasi);
        $this->assertSame(2, $pemilik->notifications()->count());
    }

    public function test_ketua_rw_tidak_bisa_menonaktifkan_dan_tidak_melihat_tombolnya(): void
    {
        $usaha = $this->usaha($this->warga(), 'Approved', true);
        $ketuaRw = $this->akun('Ketua RW', '3217010101010003');

        $this->actingAs($ketuaRw)->post(route('rw.umkm.nonaktifkan', $usaha->id), ['catatan_verifikasi' => 'x'])->assertForbidden();
        $this->actingAs($ketuaRw)->post(route('rw.umkm.aktifkan', $usaha->id))->assertForbidden();
        $this->assertTrue((bool) $usaha->fresh()->is_active);

        $this->actingAs($ketuaRw)->get(route('rw.umkm.usaha.detail', $usaha->id))
            ->assertOk()->assertDontSee('data-testid="btn-nonaktifkan-umkm"', false);

        $this->actingAs($this->akun('Admin RW', '3217010101010002'))->get(route('rw.umkm.usaha.detail', $usaha->id))
            ->assertOk()->assertSee('data-testid="btn-nonaktifkan-umkm"', false);
    }

    public function test_pojok_umkm_rw_hanya_menampilkan_usaha_disetujui_dengan_aksi_khusus_admin_rw(): void
    {
        $aktif = $this->usaha($this->warga(), 'Approved', true);
        $nonaktif = $this->usaha($this->warga('3217010101015002'), 'Approved', false);
        $pending = $this->usaha($this->warga('3217010101015003'), 'Pending');
        $pending->update(['nama_usaha' => 'Usaha Masih Menunggu']);

        $this->actingAs($this->akun('Admin RW', '3217010101010002'))
            ->get(route('rw.umkm.index'))
            ->assertOk()
            ->assertSee('Pojok UMKM')
            ->assertSee('data-testid="daftar-umkm-terdaftar"', false)
            ->assertDontSee('Usaha Masih Menunggu')
            ->assertSee(route('rw.umkm.nonaktifkan', $aktif->id), false)
            ->assertSee(route('rw.umkm.aktifkan', $nonaktif->id), false);

        $this->actingAs($this->akun('Ketua RW', '3217010101010003'))
            ->get(route('rw.umkm.index'))
            ->assertOk()
            ->assertDontSee(route('rw.umkm.nonaktifkan', $aktif->id), false)
            ->assertDontSee(route('rw.umkm.aktifkan', $nonaktif->id), false);
    }

    public function test_halaman_persetujuan_umkm_punya_tab_pengajuan_dan_ditolak(): void
    {
        $this->usaha($this->warga(), 'Pending')->update(['nama_usaha' => 'Usaha Antre']);
        $this->usaha($this->warga('3217010101015002'), 'Rejected')->update(['nama_usaha' => 'Usaha Ditolak']);
        $this->usaha($this->warga('3217010101015003'), 'Approved', true)->update(['nama_usaha' => 'Usaha Sudah Disetujui']);
        $admin = $this->akun('Admin RW', '3217010101010002');

        $this->actingAs($admin)->get(route('rw.persetujuan-umkm'))
            ->assertOk()
            ->assertSee('Persetujuan UMKM')->assertSee('Pengajuan Ditolak')
            ->assertSee('Usaha Antre')
            ->assertDontSee('Usaha Ditolak')
            ->assertDontSee('Usaha Sudah Disetujui')
            ->assertSee(route('rw.umkm.approve', UmkmUsaha::where('nama_usaha', 'Usaha Antre')->value('id')), false);

        $this->actingAs($admin)->get(route('rw.persetujuan-umkm', ['tab' => 'ditolak']))
            ->assertOk()
            ->assertSee('Usaha Ditolak')
            ->assertDontSee('Usaha Antre');

        // Ketua RW: mode pantau riwayat, tanpa tombol setujui.
        $this->actingAs($this->akun('Ketua RW', '3217010101010003'))->get(route('rw.persetujuan-umkm'))
            ->assertOk()
            ->assertSee('Mode Monitoring Ketua RW')
            ->assertSee('Usaha Sudah Disetujui')
            ->assertDontSee(route('rw.umkm.approve', UmkmUsaha::where('nama_usaha', 'Usaha Antre')->value('id')), false);
    }

    public function test_tautan_lama_tab_persetujuan_dialihkan_ke_menu_persetujuan(): void
    {
        $admin = $this->akun('Admin RW', '3217010101010002');

        $this->actingAs($admin)->get(route('rw.umkm.index', ['tab' => 'persetujuan']))
            ->assertRedirect(route('rw.persetujuan-umkm', ['tab' => 'persetujuan']));
        $this->actingAs($admin)->get(route('rw.umkm.index', ['tab' => 'ditolak']))
            ->assertRedirect(route('rw.persetujuan-umkm', ['tab' => 'ditolak']));
    }

    public function test_notifikasi_pendaftaran_baru_mengarah_ke_persetujuan_umkm(): void
    {
        $admin = $this->akun('Admin RW', '3217010101010002');
        $this->actingAs($this->warga())->post(route('warga.umkm.store-usaha'), $this->dataUsaha())->assertSessionHasNoErrors();

        $this->assertSame(route('rw.persetujuan-umkm'), $admin->notifications()->first()?->data['url'] ?? null);
    }

    public function test_tombol_daftarkan_usaha_membuka_galeri_dengan_modal_pendaftaran(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->get(route('warga.umkm.daftar'))
            ->assertRedirect(route('umkm.galeri', ['daftar' => 1]));

        $this->actingAs($warga)->get(route('umkm.galeri', ['daftar' => 1]))
            ->assertOk()
            ->assertSee('id="modalDaftarUsaha"', false)
            ->assertSee("params.get('daftar') === '1'", false)
            ->assertSee(route('warga.umkm.store-usaha'), false);
    }

    public function test_ketua_rt_berperan_warga_melihat_tombol_daftar_di_galeri(): void
    {
        $ketuaRt = $this->warga('3217010101015009', ['Ketua RT', 'Warga']);

        $this->actingAs($ketuaRt)->withSession(['active_role' => 'Warga'])
            ->get(route('umkm.galeri'))
            ->assertOk()
            ->assertSee('data-bs-target="#modalDaftarUsaha"', false);
    }

    public function test_form_menampilkan_label_kontak_bisnis_dan_nomor_profil_dari_database(): void
    {
        $warga = $this->warga(); // nomor WA keluarga: 081200000000
        Penduduk::where('nik', $warga->nik)->update(['no_wa' => '085711112222']);

        $this->actingAs($warga)->get(route('umkm.galeri'))
            ->assertOk()
            ->assertSee('Nomor Kontak Bisnis (Nomor WA)')
            ->assertDontSee('No. WhatsApp Business')
            ->assertSee('Gunakan nomor kontak di profil')
            ->assertSee('data-nomor="85711112222"', false); // nomor penduduk lebih diutamakan dari nomor keluarga
    }

    public function test_ceklis_kontak_profil_memakai_nomor_dari_database_bukan_dari_klien(): void
    {
        $warga = $this->warga();

        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha([
            'no_wa' => '89999999999', 'pakai_kontak_profil' => '1',
        ]))->assertSessionHasNoErrors();

        $this->assertSame('6281200000000', UmkmUsaha::where('nik', $warga->nik)->value('no_wa'));
    }

    public function test_ceklis_kontak_profil_ditolak_bila_profil_belum_punya_nomor(): void
    {
        $warga = $this->warga();
        Keluarga::where('nik_kepala_keluarga', $warga->nik)->update(['no_wa' => null]);

        $this->actingAs($warga)->get(route('umkm.galeri'))
            ->assertOk()->assertSee('nomor WA belum diisi di profil');

        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['pakai_kontak_profil' => '1']))
            ->assertSessionHasErrors('no_wa');
        $this->assertSame(0, UmkmUsaha::count());
    }

    public function test_ceklis_alamat_profil_memakai_alamat_dari_database_bukan_dari_klien(): void
    {
        $warga = $this->warga(); // alamat KK: "Alamat", RT 01
        Penduduk::where('nik', $warga->nik)->update(['alamat_domisili' => 'Jl. Domisili No. 7']);

        $this->actingAs($warga)->get(route('umkm.galeri'))
            ->assertOk()
            ->assertSee('Gunakan alamat di profil')
            ->assertSee('data-nilai="Jl. Domisili No. 7, RT 01"', false); // domisili diutamakan, RT dilengkapi

        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha([
            'alamat_usaha' => 'Alamat palsu', 'pakai_alamat_profil' => '1',
        ]))->assertSessionHasNoErrors();

        $this->assertSame('Jl. Domisili No. 7, RT 01', UmkmUsaha::where('nik', $warga->nik)->value('alamat_usaha'));
    }

    public function test_ceklis_alamat_profil_ditolak_bila_profil_belum_punya_alamat(): void
    {
        $warga = $this->warga();
        Keluarga::where('nik_kepala_keluarga', $warga->nik)->update(['alamat' => '']);

        $this->actingAs($warga)->get(route('umkm.galeri'))->assertOk()->assertSee('alamat belum diisi di profil');
        $this->actingAs($warga)->post(route('warga.umkm.store-usaha'), $this->dataUsaha(['pakai_alamat_profil' => '1']))
            ->assertSessionHasErrors('alamat_usaha');
        $this->assertSame(0, UmkmUsaha::count());
    }

    public function test_edit_usaha_menandai_ceklis_bila_nomor_sama_dengan_profil(): void
    {
        $warga = $this->warga();
        $usaha = $this->usaha($warga, 'Approved', true); // no_wa = nomor keluarga

        $html = $this->actingAs($warga)->get(route('warga.umkm.kelola', ['usaha_id' => $usaha->id]))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/id="noWaEditUsaha_profil"[^>]*checked/s', $html);
        $this->assertMatchesRegularExpression('/id="noWaEditUsaha"[^>]*readonly/s', $html);
    }

    public function test_produk_tidak_bisa_ditambah_pada_usaha_ditolak(): void
    {
        $pemilik = $this->warga();
        $usaha = $this->usaha($pemilik, 'Rejected');

        $this->actingAs($pemilik)->post(route('warga.umkm.produk.store'), [
            'umkm_usaha_id' => $usaha->id, 'nama_produk' => 'Produk Uji', 'harga' => '1000', 'nama_kategori_baru' => 'Umum',
        ])->assertSessionHas('error');
        $this->assertSame(0, $usaha->produk()->count());
    }
}
