<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\GaleriDokumentasi;
use App\Models\MasterRt;
use App\Models\User;
use App\Services\HalamanUtama;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pengaturan Halaman Utama (Admin Aplikasi): banner (teks + gambar bawaan/galeri/unggah),
 * footer (copyright, kontak, partner), serta perubahan tampilan publik halaman utama.
 */
class HalamanUtamaTest extends TestCase
{
    use RefreshDatabase;

    private array $berkasPublik = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        foreach ($this->berkasPublik as $p) {
            @unlink(public_path($p));
        }
        parent::tearDown();
    }

    private function admin(): User
    {
        return User::firstOrCreate(['username' => 'adminapp'], ['nik' => null, 'role' => 'Admin Aplikasi',
            'status_akun' => 'Active', 'password' => 'password123']);
    }

    private function gambar(string $nama = 'b.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($nama, 1200, 800);
    }

    /** Foto galeri nyata di public/assets/images/galeri milik agenda RW (atau RT) yang terbit. */
    private function fotoGaleri(?string $rtId = null, string $status = 'Publish'): string
    {
        $op = User::firstOrCreate(['username' => 'opkonten'], ['nik' => null, 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);
        $agenda = Agenda::forceCreate([
            'judul_agenda' => 'Kerja Bakti ' . uniqid(), 'slug' => 'kerja-bakti-' . uniqid(), 'kategori' => 'Sosial',
            'tanggal_mulai' => now()->subDays(3), 'tanggal_selesai' => now()->subDays(2), 'lokasi' => 'Balai',
            'detail_pengumuman' => 'x', 'status' => $status, 'operator_id' => $op->id, 'rt_id' => $rtId,
        ]);
        $path = 'assets/images/galeri/uji_' . uniqid() . '.jpg';
        @mkdir(public_path('assets/images/galeri'), 0755, true);
        imagejpeg(imagecreatetruecolor(40, 30), public_path($path));
        $this->berkasPublik[] = $path;
        GaleriDokumentasi::create(['agenda_id' => $agenda->id, 'foto' => [$path], 'operator_id' => $op->id]);

        return $path;
    }

    private function dataForm(array $ubah = []): array
    {
        return array_replace_recursive([
            'banner' => ['badge' => 'Halo Uji', 'judul' => 'Judul Banner Uji', 'deskripsi' => 'Deskripsi uji', 'tombol_teks' => 'Mulai', 'tombol_url' => '/layanan-mandiri'],
            'gambar' => [
                'utama'    => ['sumber' => 'bawaan'],
                'samping1' => ['sumber' => 'bawaan', 'label' => 'Label Satu', 'sublabel' => 'Sub Satu'],
                'samping2' => ['sumber' => 'bawaan', 'label' => '', 'sublabel' => ''],
            ],
            'footer' => ['nama' => 'Nama Footer Uji', 'deskripsi' => 'Desk', 'sekretariat' => "Balai\nDesa", 'email' => 'rw@contoh.id',
                'telepon' => '0812 3456', 'peta_url' => '', 'copyright' => '© {tahun} Uji Copyright', 'judul_partner' => 'Didukung oleh'],
        ], $ubah);
    }

    public function test_hanya_admin_aplikasi_yang_bisa_membuka_dan_menyimpan(): void
    {
        $this->actingAs($this->admin())->get(route('admin.halaman-utama'))->assertOk()
            ->assertSee('Pengaturan Halaman Utama')->assertSee('data-testid="form-halaman-utama"', false);

        $rw = User::create(['nik' => null, 'username' => 'adminrw', 'role' => 'Admin RW', 'status_akun' => 'Active', 'password' => 'password123']);
        $this->actingAs($rw)->post(route('admin.halaman-utama.update'), $this->dataForm())->assertRedirect();
        $this->assertSame('Digitalisasi Gotong Royong RW 21 Tanimulya', HalamanUtama::data()['banner']['judul']);
    }

    public function test_simpan_teks_banner_dan_footer_tampil_di_halaman_utama(): void
    {
        $this->actingAs($this->admin())->post(route('admin.halaman-utama.update'), $this->dataForm([
            'partner' => [5 => ['nama' => 'Desa Tanimulya', 'url' => 'https://desa.contoh.id', 'logo' => $this->gambar('logo.png')]],
        ]))->assertSessionHasNoErrors()->assertRedirect(route('admin.halaman-utama'));

        $data = HalamanUtama::data();
        $this->assertSame('Judul Banner Uji', $data['banner']['judul']);
        $this->assertCount(1, $data['footer']['partner']);
        Storage::disk('public')->assertExists($data['footer']['partner'][0]['logo']);

        auth()->logout();
        $this->get('/')->assertOk()
            ->assertSee('Judul Banner Uji')->assertSee('Halo Uji')->assertSee('Label Satu')
            ->assertSee('Nama Footer Uji')
            ->assertSee('© ' . date('Y') . ' Uji Copyright')
            ->assertSee('data-testid="footer-partner"', false)
            ->assertSee('Desa Tanimulya')
            ->assertSee('mailto:rw@contoh.id', false);
    }

    public function test_gambar_banner_dari_galeri_dokumentasi(): void
    {
        $path = $this->fotoGaleri();

        $this->actingAs($this->admin())->get(route('admin.halaman-utama'))->assertOk()->assertSee($path, false);

        $this->actingAs($this->admin())->post(route('admin.halaman-utama.update'), $this->dataForm([
            'gambar' => ['utama' => ['sumber' => 'galeri', 'galeri' => $path]],
        ]))->assertSessionHasNoErrors();

        $this->assertSame(['galeri', $path], [HalamanUtama::data()['banner']['gambar']['utama']['sumber'], HalamanUtama::data()['banner']['gambar']['utama']['path']]);
        auth()->logout();
        $this->get('/')->assertOk()->assertSee(asset($path), false);
    }

    public function test_foto_galeri_tidak_sah_atau_milik_rt_ditolak(): void
    {
        $fotoRt = $this->fotoGaleri(MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01'])->id);
        $fotoDraft = $this->fotoGaleri(null, 'Draft');

        foreach ([$fotoRt, $fotoDraft, 'assets/images/galeri/../../../.env', 'storage/settings/logo.png', ''] as $p) {
            $this->actingAs($this->admin())->post(route('admin.halaman-utama.update'), $this->dataForm([
                'gambar' => ['utama' => ['sumber' => 'galeri', 'galeri' => $p]],
            ]))->assertSessionHasErrors('gambar.utama.galeri');
        }
        $this->assertSame('bawaan', HalamanUtama::data()['banner']['gambar']['utama']['sumber']);
    }

    public function test_unggah_gambar_baru_lalu_ganti_menghapus_berkas_lama(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.halaman-utama.update'), $this->dataForm([
            'gambar' => ['samping1' => ['sumber' => 'unggah', 'file' => $this->gambar()]],
        ]))->assertSessionHasNoErrors();
        $lama = HalamanUtama::data()['banner']['gambar']['samping1']['path'];
        $this->assertStringStartsWith('beranda/banner/', $lama);
        $this->assertStringEndsWith('.webp', $lama);
        Storage::disk('public')->assertExists($lama);

        // Simpan lagi tanpa berkas: tetap memakai unggahan sebelumnya.
        $this->actingAs($admin)->post(route('admin.halaman-utama.update'), $this->dataForm([
            'gambar' => ['samping1' => ['sumber' => 'unggah']],
        ]))->assertSessionHasNoErrors();
        $this->assertSame($lama, HalamanUtama::data()['banner']['gambar']['samping1']['path']);

        // Kembali ke bawaan: berkas unggahan dibuang.
        $this->actingAs($admin)->post(route('admin.halaman-utama.update'), $this->dataForm())->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($lama);
    }

    public function test_sumber_unggah_tanpa_berkas_ditolak(): void
    {
        $this->actingAs($this->admin())->post(route('admin.halaman-utama.update'), $this->dataForm([
            'gambar' => ['utama' => ['sumber' => 'unggah']],
        ]))->assertSessionHasErrors('gambar.utama.file');
    }

    public function test_partner_dihapus_logonya_ikut_dibuang_dan_logo_lama_tidak_bisa_dipalsukan(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.halaman-utama.update'), $this->dataForm([
            'partner' => [['nama' => 'Partner A', 'logo' => $this->gambar('a.png')]],
        ]));
        $logoA = HalamanUtama::data()['footer']['partner'][0]['logo'];
        Storage::disk('public')->put('settings/rahasia.png', 'x');

        $this->actingAs($admin)->post(route('admin.halaman-utama.update'), $this->dataForm([
            'partner' => [['nama' => 'Partner B', 'logo_lama' => 'settings/rahasia.png']],
        ]))->assertSessionHasNoErrors();

        $partner = HalamanUtama::data()['footer']['partner'];
        $this->assertSame('Partner B', $partner[0]['nama']);
        $this->assertNull($partner[0]['logo']);
        Storage::disk('public')->assertMissing($logoA);
        Storage::disk('public')->assertExists('settings/rahasia.png'); // di luar folder beranda tidak disentuh
    }

    public function test_validasi_tautan_tombol_dan_partner(): void
    {
        $this->actingAs($this->admin())->post(route('admin.halaman-utama.update'), $this->dataForm([
            'banner' => ['tombol_url' => 'javascript:alert(1)'],
            'partner' => [['nama' => 'X', 'url' => 'bukan url']],
        ]))->assertSessionHasErrors(['banner.tombol_url', 'partner.0.url']);
    }

    public function test_halaman_utama_publik_tanpa_lonceng_statistik_dan_menu_informasi(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('btn-notif', $html);
        $this->assertStringNotContainsString('bi-bell', $html);
        $this->assertStringNotContainsString('stats-bar', $html);
        $this->assertStringNotContainsString('Jiwa Aktif', $html);
        $this->assertStringNotContainsString('Layanan Digital', $html);
        $this->assertStringNotContainsString('href="/informasi"', $html);
        $this->assertStringNotContainsString('data-testid="footer-partner"', $html); // partner kosong: kolom tidak tampil

        // Urutan menu: Home, Berita, Agenda, Layanan Mandiri, Pojok UMKM.
        preg_match('/<ul class="navbar-nav[^"]*">(.*?)<\/ul>/s', $html, $m);
        preg_match_all('/class="nav-link[^"]*"[^>]*>([^<]+)</', $m[1], $menu);
        $this->assertSame(['Home', 'Berita', 'Agenda', 'Layanan Mandiri', 'Pojok UMKM'], array_map('trim', $menu[1]));
        $this->assertStringContainsString('href="' . route('agenda') . '"', $html); // menu Agenda ke halaman khusus
    }

    public function test_alamat_informasi_dialihkan_ke_beranda(): void
    {
        $this->get('/informasi')->assertRedirect('/');
    }
}
