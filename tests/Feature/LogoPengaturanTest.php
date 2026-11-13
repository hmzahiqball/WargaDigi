<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Services\GambarPengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Semua logo dan gambar (logo instance, logo kop, gambar login, ikon aplikasi) diatur dari Pengaturan Sistem.
 * Memakai disk public yang sebenarnya karena URL gambar membutuhkan tautan public/storage; berkas uji dibersihkan.
 */
class LogoPengaturanTest extends TestCase
{
    use RefreshDatabase;

    private array $berkasAwal = [];
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        if (! is_dir(public_path('storage'))) {
            $this->markTestSkipped('Tautan public/storage belum dibuat (php artisan storage:link).');
        }
        $this->berkasAwal = Storage::disk('public')->allFiles('settings');
        $this->admin = User::create([
            'nik' => '3217010101013101', 'username' => 'adminlogo', 'role' => 'Admin Aplikasi',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    protected function tearDown(): void
    {
        if (isset($this->berkasAwal)) {
            foreach (array_diff(Storage::disk('public')->allFiles('settings'), $this->berkasAwal) as $berkas) {
                Storage::disk('public')->delete($berkas);
            }
        }
        parent::tearDown();
    }

    private function simpan(array $berkas)
    {
        return $this->actingAs($this->admin)->post(route('admin.pengaturan-sistem.update'), ['instance_name' => 'RW Uji'] + $berkas);
    }

    public function test_halaman_pengaturan_memuat_input_untuk_semua_logo_dan_gambar(): void
    {
        $this->actingAs($this->admin)->get(route('admin.pengaturan-sistem'))->assertOk()
            ->assertSee('data-testid="kartu-logo-gambar"', false)
            ->assertSee('name="logo"', false)->assertSee('name="logo_kop"', false)
            ->assertSee('name="gambar_login"', false)->assertSee('name="ikon_aplikasi"', false)
            ->assertSee('Logo Kop Surat dan Resi')->assertSee('Gambar Halaman Login')->assertSee('Ikon Aplikasi');
    }

    public function test_bawaan_dipakai_selama_belum_ada_unggahan(): void
    {
        foreach (SystemSetting::GAMBAR as $kunci => $cfg) {
            $this->assertSame($cfg['bawaan'], SystemSetting::gambarPath($kunci), $kunci);
            $this->assertFalse(SystemSetting::gambarKustom($kunci), $kunci);
        }
        $this->get('/login')->assertOk()->assertSee('images/auth-hero.jpg', false);
    }

    public function test_logo_instance_dipakai_di_sidebar_navbar_dan_favicon(): void
    {
        $this->simpan(['logo' => UploadedFile::fake()->image('logo.png', 300, 300)])->assertSessionHasNoErrors();

        $path = SystemSetting::get('logo');
        $this->assertStringStartsWith('settings/', $path);
        $this->assertTrue(SystemSetting::gambarKustom('logo'));
        $url = 'storage/' . $path;

        $this->actingAs($this->admin)->get(route('admin.pengaturan-sistem'))->assertOk()->assertSee($url, false)->assertSee('Kustom');
        $this->get('/')->assertOk()->assertSee($url, false);
    }

    public function test_gambar_halaman_login_mengikuti_pengaturan_dan_dapat_dikembalikan_ke_bawaan(): void
    {
        $this->simpan(['gambar_login' => UploadedFile::fake()->image('hero.jpg', 1200, 800)])->assertSessionHasNoErrors();
        $path = SystemSetting::get('gambar_login');

        $this->get('/login')->assertOk()->assertSee('storage/' . $path, false)->assertDontSee('images/auth-hero.jpg', false);
        $this->assertTrue(Storage::disk('public')->exists($path));

        $this->simpan(['hapus_gambar_login' => '1'])->assertSessionHasNoErrors();

        $this->assertFalse(Storage::disk('public')->exists($path));
        $this->get('/login')->assertOk()->assertSee('images/auth-hero.jpg', false);
    }

    public function test_mengganti_gambar_menghapus_berkas_lama(): void
    {
        $this->simpan(['gambar_login' => UploadedFile::fake()->image('a.jpg', 1000, 700)]);
        $lama = SystemSetting::get('gambar_login');

        $this->simpan(['gambar_login' => UploadedFile::fake()->image('b.jpg', 1000, 700)]);
        $baru = SystemSetting::get('gambar_login');

        $this->assertNotSame($lama, $baru);
        $this->assertFalse(Storage::disk('public')->exists($lama));
        $this->assertTrue(Storage::disk('public')->exists($baru));
    }

    public function test_logo_kop_mengikuti_pengaturan_untuk_pdf(): void
    {
        $this->assertSame('images/logo_rw21.png', SystemSetting::gambarPath('logo_kop'));

        $this->simpan(['logo_kop' => UploadedFile::fake()->image('kop.png', 400, 400)])->assertSessionHasNoErrors();

        $path = SystemSetting::gambarPath('logo_kop');
        $this->assertStringStartsWith('storage/settings/', $path);
        $this->assertFileExists(public_path($path));   // dibaca pembuat PDF lewat public_path
    }

    public function test_ikon_aplikasi_membuat_tiga_ukuran_persegi_untuk_pwa(): void
    {
        $this->simpan(['ikon_aplikasi' => UploadedFile::fake()->image('ikon.png', 800, 600)])->assertSessionHasNoErrors();

        foreach (['192' => 192, '512' => 512, 'maskable' => 512] as $varian => $sisi) {
            $varian = (string) $varian;
            $this->assertTrue(Storage::disk('public')->exists(GambarPengaturan::pathIkon($varian)), $varian);
            $res = $this->get(route('pwa.ikon', ['varian' => $varian]));
            $res->assertOk()->assertHeader('Content-Type', 'image/png');
            [$w, $h] = getimagesizefromstring($res->baseResponse->getFile()->getContent());
            $this->assertSame([$sisi, $sisi], [$w, $h], "ukuran ikon {$varian}");
        }
    }

    public function test_manifest_dinamis_memuat_ikon_berversi_dan_bawaan_dipakai_bila_belum_diunggah(): void
    {
        $this->get('/manifest.webmanifest')->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertJsonPath('short_name', 'WargaDigi')
            ->assertJsonCount(3, 'icons')
            ->assertJsonPath('icons.0.src', '/ikon-aplikasi/192.png?v=0');

        // Tanpa unggahan, ikon bawaan di public/icons yang disajikan.
        $this->get(route('pwa.ikon', ['varian' => '192']))->assertOk();

        $this->simpan(['ikon_aplikasi' => UploadedFile::fake()->image('ikon.png', 700, 700)]);
        $json = $this->get('/manifest.webmanifest')->assertOk()->json();
        $this->assertMatchesRegularExpression('#^/ikon-aplikasi/192\.png\?v=\d+$#', $json['icons'][0]['src']);
        $this->assertNotSame('/ikon-aplikasi/192.png?v=0', $json['icons'][0]['src']);
    }

    public function test_hapus_ikon_aplikasi_menghapus_turunan_dan_kembali_ke_bawaan(): void
    {
        $this->simpan(['ikon_aplikasi' => UploadedFile::fake()->image('ikon.png', 600, 600)]);
        $this->assertTrue(Storage::disk('public')->exists(GambarPengaturan::pathIkon('192')));

        $this->simpan(['hapus_ikon_aplikasi' => '1']);

        foreach (['192', '512', 'maskable'] as $varian) {
            $this->assertFalse(Storage::disk('public')->exists(GambarPengaturan::pathIkon($varian)), (string) $varian);
        }
        $this->get('/manifest.webmanifest')->assertJsonPath('icons.0.src', '/ikon-aplikasi/192.png?v=0');
        $this->get(route('pwa.ikon', ['varian' => '512']))->assertOk();
    }

    public function test_varian_ikon_tidak_dikenal_ditolak(): void
    {
        $this->get('/ikon-aplikasi/999.png')->assertNotFound();
        $this->get('/ikon-aplikasi/..%2F.env.png')->assertNotFound();
    }

    public function test_validasi_ukuran_dan_format(): void
    {
        $this->simpan(['ikon_aplikasi' => UploadedFile::fake()->image('kecil.png', 300, 300)])->assertSessionHasErrors('ikon_aplikasi');
        $this->simpan(['gambar_login' => UploadedFile::fake()->image('kecil.jpg', 400, 300)])->assertSessionHasErrors('gambar_login');
        $this->simpan(['logo_kop' => UploadedFile::fake()->create('kop.svg', 10, 'image/svg+xml')])->assertSessionHasErrors('logo_kop');
        $this->simpan(['logo' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf')])->assertSessionHasErrors('logo');

        foreach (['logo', 'logo_kop', 'gambar_login', 'ikon_aplikasi'] as $kunci) {
            $this->assertFalse(SystemSetting::gambarKustom($kunci), $kunci);
        }
    }

    public function test_hanya_admin_aplikasi_yang_dapat_mengubah_logo(): void
    {
        $warga = User::create([
            'nik' => '3217010101013102', 'username' => 'wargalogo', 'role' => 'Warga',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        $this->actingAs($warga)->post(route('admin.pengaturan-sistem.update'), [
            'instance_name' => 'X', 'session_timeout' => 30, 'logo' => UploadedFile::fake()->image('x.png', 300, 300),
        ])->assertRedirect();

        $this->assertFalse(SystemSetting::gambarKustom('logo'));
    }

    // ------------- Regresi: simpan dari form yang sebenarnya -------------

    public function test_simpan_dari_form_tanpa_bagian_lanjutan_berhasil_tanpa_session_timeout(): void
    {
        // Form hanya memuat Informasi Instance, Logo dan Gambar, dan Persetujuan Konten (bagian lain disembunyikan).
        $this->actingAs($this->admin)->post(route('admin.pengaturan-sistem.update'), [
            'instance_name' => 'RW 21 Tanimulya', 'domain' => 'wargadigi21.com', 'description' => 'Platform warga',
        ])->assertRedirect(route('admin.pengaturan-sistem'))->assertSessionHasNoErrors();

        $this->assertSame('RW 21 Tanimulya', SystemSetting::get('instance_name'));
        $this->assertSame('wargadigi21.com', SystemSetting::get('domain'));
    }

    public function test_menyimpan_tidak_menimpa_mode_pemeliharaan_dan_keamanan_yang_tersembunyi(): void
    {
        SystemSetting::setMany(['maintenance_mode' => true, 'two_factor' => true, 'notif_push' => true, 'session_timeout' => 60]);

        $this->simpan(['gambar_login' => UploadedFile::fake()->image('hero.jpg', 1000, 700)])->assertSessionHasNoErrors();

        $this->assertTrue((bool) SystemSetting::get('maintenance_mode'));
        $this->assertTrue((bool) SystemSetting::get('two_factor'));
        $this->assertTrue((bool) SystemSetting::get('notif_push'));
        $this->assertSame(60, (int) SystemSetting::get('session_timeout'));
    }

    public function test_bagian_lanjutan_diproses_hanya_bila_dikirim_dan_session_timeout_wajib_saat_itu(): void
    {
        $this->simpan(['bagian_lanjutan' => '1'])->assertSessionHasErrors('session_timeout');

        $this->simpan(['bagian_lanjutan' => '1', 'session_timeout' => 120, 'maintenance_mode' => '1'])->assertSessionHasNoErrors();
        $this->assertSame(120, (int) SystemSetting::get('session_timeout'));
        $this->assertTrue((bool) SystemSetting::get('maintenance_mode'));
        $this->assertFalse((bool) SystemSetting::get('two_factor'));   // tidak dicentang, jadi mati

        $this->simpan(['bagian_lanjutan' => '1', 'session_timeout' => 45])->assertSessionHasErrors('session_timeout');
    }

    public function test_halaman_pengaturan_tidak_memuat_penanda_bagian_lanjutan_selama_bagian_itu_disembunyikan(): void
    {
        $this->actingAs($this->admin)->get(route('admin.pengaturan-sistem'))->assertOk()
            ->assertDontSee('name="bagian_lanjutan"', false)
            ->assertDontSee('name="session_timeout"', false);
    }
    public function test_simpan_tanpa_unggahan_tidak_mengubah_gambar_yang_sudah_ada(): void
    {
        $this->simpan(['gambar_login' => UploadedFile::fake()->image('hero.jpg', 1000, 700)]);
        $path = SystemSetting::get('gambar_login');

        $this->simpan([])->assertSessionHasNoErrors();

        $this->assertSame($path, SystemSetting::get('gambar_login'));
        $this->assertTrue(Storage::disk('public')->exists($path));
    }
}
