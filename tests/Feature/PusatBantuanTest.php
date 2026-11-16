<?php

namespace Tests\Feature;

use App\Models\MasterRt;
use App\Models\PanduanTopik;
use App\Models\User;
use App\Services\Panduan\PanduanRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Pusat Bantuan: hanya pengguna masuk, isi dan pencarian sesuai peran aktif. */
class PusatBantuanTest extends TestCase
{
    use RefreshDatabase;

    private string $sumber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sumber = storage_path('framework/testing/panduan-' . uniqid() . '.json');
        config(['panduan.sumber' => $this->sumber]);
        $this->tulisSumber($this->topikContoh(), 'versi-1');
    }

    protected function tearDown(): void
    {
        File::delete($this->sumber);
        parent::tearDown();
    }

    private function topikContoh(): array
    {
        $t = fn ($peran, $buku, $bab, $nomor, $judul, $slug, $isi, $jenis = 'topik', $kata = '') => [
            'peran' => $peran, 'buku' => $buku, 'bab_nomor' => null, 'bab_judul' => $bab, 'nomor' => $nomor,
            'judul' => $judul, 'slug' => $slug, 'jenis' => $jenis, 'ringkasan' => strip_tags($isi),
            'isi_html' => '<p>' . $isi . '</p>', 'isi_teks' => strip_tags($isi), 'kata_kunci' => $bab . ' | ' . $kata, 'urutan' => 0,
        ];

        return [
            $t('Warga', '01', 'Permohonan Surat', '4.2', 'Mengajukan Surat', 'warga-mengajukan-surat', 'Buka menu Permohonan Surat lalu pilih jenis surat.', 'topik', 'Buka menu Permohonan Surat'),
            $t('Warga', '01', 'Iuran Warga', '5.1', 'Iuran Bulanan', 'warga-iuran-bulanan', 'Unggah bukti transfer untuk bulan yang dibayar.'),
            $t('Warga', '01', 'Pertanyaan yang sering muncul', null, 'Surat saya ditolak. Apa yang harus dilakukan?', 'warga-faq-surat-ditolak', 'Baca alasannya lalu ajukan ulang.', 'faq'),
            $t('Ketua RT', '02', 'Persetujuan Surat Warga', '4.1', 'Memeriksa Pengajuan Surat', 'ketua-rt-memeriksa-pengajuan-surat', 'Setujui surat dengan tanda tangan digital.', 'topik', 'Bubuhkan tanda tangan'),
            $t('Semua', '00', 'Login, Pilih Peran, dan Keluar', '3.1', 'Cara login ke aplikasi', 'umum-cara-login-ke-aplikasi', 'Isi username atau nomor WhatsApp lalu password.'),
        ];
    }

    private function tulisSumber(array $topik, string $versi): void
    {
        foreach ($topik as $i => &$t) {
            $t['urutan'] = $i + 1;
        }
        File::ensureDirectoryExists(dirname($this->sumber));
        File::put($this->sumber, json_encode(['versi' => $versi, 'topik' => $topik], JSON_UNESCAPED_UNICODE));
        touch($this->sumber, time() + random_int(1, 1000));
        clearstatcache();
    }

    private function pengguna(array $peran, string $username): User
    {
        foreach ($peran as $p) {
            Role::findOrCreate($p, 'web');
        }
        $rt = MasterRt::firstOrCreate(['kode_rt' => '03'], ['nama_rt' => '03']);
        $user = User::create(['username' => $username, 'role' => $peran[0], 'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123']);
        $user->syncRoles($peran);

        return $user->fresh();
    }

    public function test_tamu_membaca_panduan_umum_dan_warga_di_tampilan_publik(): void
    {
        $this->get(route('bantuan.index'))->assertOk()
            ->assertSee('data-testid="bantuan-publik"', false)
            ->assertSee('data-testid="footer-pusat-bantuan"', false)
            ->assertSee('Mengajukan Surat')
            ->assertSee('Cara login ke aplikasi')
            ->assertSee('Baca alasannya lalu ajukan ulang.')
            ->assertDontSee('Memeriksa Pengajuan Surat')
            ->assertDontSee('data-testid="topik-terkunci"', false);

        $this->get(route('bantuan.show', 'warga-mengajukan-surat'))->assertOk()
            ->assertSee('Buka menu Permohonan Surat lalu pilih jenis surat.');
        $this->get(route('bantuan.show', 'umum-cara-login-ke-aplikasi'))->assertOk();
        $this->get(route('bantuan.show', 'tidak-ada'))->assertNotFound();
    }

    public function test_tamu_melihat_daftar_peran_lain_tetapi_isinya_perlu_masuk(): void
    {
        $this->get(route('bantuan.index', ['peran' => 'ketua-rt']))->assertOk()
            ->assertSee('Memeriksa Pengajuan Surat')
            ->assertSee('data-testid="topik-terkunci"', false)
            ->assertSee('data-testid="catatan-perlu-masuk"', false)
            ->assertDontSee('Setujui surat dengan tanda tangan digital.')
            ->assertDontSee('Iuran Bulanan');

        // Pencarian tamu mengikuti peran yang dipilih; topik terkunci tidak membocorkan ringkasan.
        $this->getJson(route('bantuan.cari', ['q' => 'tanda tangan', 'peran' => 'ketua-rt']))->assertOk()
            ->assertJsonPath('data.0.judul', 'Memeriksa Pengajuan Surat')
            ->assertJsonPath('data.0.terkunci', true)
            ->assertJsonPath('data.0.ringkasan', 'Masuk untuk membaca panduan ini.');

        // Membuka isi topik peran lain: diarahkan ke login, lalu kembali ke topik setelah masuk.
        $url = route('bantuan.show', 'ketua-rt-memeriksa-pengajuan-surat');
        $this->get($url)->assertRedirect(route('login'))->assertSessionHas('status');
        $this->assertSame($url, session('url.intended'));

        // Peran tidak dikenal kembali ke bawaan (Warga).
        $this->get(route('bantuan.index', ['peran' => 'asing']))->assertOk()->assertSee('Iuran Bulanan');
    }

    public function test_warga_hanya_melihat_panduan_warga_dan_umum(): void
    {
        $warga = $this->pengguna(['Warga'], 'wargabantuan');

        $this->actingAs($warga)->get(route('bantuan.index'))
            ->assertOk()
            ->assertSee('Mengajukan Surat')
            ->assertSee('Cara login ke aplikasi')
            ->assertSee('Surat saya ditolak.', false)
            ->assertDontSee('Memeriksa Pengajuan Surat');

        $this->assertSame(5, PanduanTopik::count(), 'JSON dimuat otomatis ke database saat halaman dibuka.');
    }

    public function test_pencarian_relevan_dan_dibatasi_peran(): void
    {
        $warga = $this->pengguna(['Warga'], 'wargacari');

        $hasil = $this->actingAs($warga)->get(route('bantuan.index', ['q' => 'bagaimana cara mengajukan surat']))
            ->assertOk()->viewData('hasil');
        $this->assertSame('warga-mengajukan-surat', $hasil->first()->slug);
        $this->assertNotContains('ketua-rt-memeriksa-pengajuan-surat', $hasil->pluck('slug'));

        // Topik "tanda tangan" milik Ketua RT tidak boleh muncul bagi Warga.
        $this->actingAs($warga)->get(route('bantuan.index', ['q' => 'tanda tangan']))
            ->assertOk()->assertSee('belum ditemukan')->assertDontSee('Memeriksa Pengajuan Surat');

        // Sinonim: "ttd" ditemukan sebagai "tanda tangan" untuk Ketua RT.
        $ketua = $this->pengguna(['Ketua RT'], 'ketuacari');
        $slug = $this->actingAs($ketua)->get(route('bantuan.index', ['q' => 'ttd']))->viewData('hasil')->pluck('slug');
        $this->assertContains('ketua-rt-memeriksa-pengajuan-surat', $slug);
    }

    public function test_saran_pencarian_berupa_json(): void
    {
        $warga = $this->pengguna(['Warga'], 'wargasaran');

        $this->actingAs($warga)->getJson(route('bantuan.cari', ['q' => 'surat ditolak']))
            ->assertOk()
            ->assertJsonPath('data.0.judul', 'Surat saya ditolak. Apa yang harus dilakukan?')
            ->assertJsonPath('data.0.url', route('bantuan.show', 'warga-faq-surat-ditolak'));

        $this->actingAs($warga)->getJson(route('bantuan.cari', ['q' => 's']))->assertOk()->assertExactJson(['data' => []]);
    }

    public function test_topik_peran_lain_tidak_bisa_dibuka(): void
    {
        $warga = $this->pengguna(['Warga'], 'wargatopik');

        $this->actingAs($warga)->get(route('bantuan.show', 'warga-mengajukan-surat'))
            ->assertOk()->assertSee('Buka menu Permohonan Surat lalu pilih jenis surat.');
        $this->actingAs($warga)->get(route('bantuan.show', 'umum-cara-login-ke-aplikasi'))->assertOk();
        // Topik peran lain tidak ditampilkan; pengguna diarahkan ke daftar dengan penjelasan.
        $this->actingAs($warga)->get(route('bantuan.show', 'ketua-rt-memeriksa-pengajuan-surat'))
            ->assertRedirect(route('bantuan.index'))->assertSessionHas('bantuan_info');
        $this->actingAs($warga)->get(route('bantuan.index'))->assertOk()->assertDontSee('Setujui surat dengan tanda tangan digital.');
        $this->actingAs($warga)->get(route('bantuan.show', 'tidak-ada'))->assertNotFound();
    }

    public function test_akun_dua_peran_mengikuti_peran_aktif(): void
    {
        $user = $this->pengguna(['Ketua RT', 'Warga'], 'duaperanbantuan');

        $this->actingAs($user)->withSession([User::ACTIVE_ROLE_SESSION_KEY => 'Ketua RT'])
            ->get(route('bantuan.index'))->assertOk()
            ->assertSee('Memeriksa Pengajuan Surat')->assertDontSee('Iuran Bulanan');

        $this->actingAs($user)->withSession([User::ACTIVE_ROLE_SESSION_KEY => 'Warga'])
            ->get(route('bantuan.index'))->assertOk()
            ->assertSee('Iuran Bulanan')->assertDontSee('Memeriksa Pengajuan Surat');
    }

    public function test_versi_baru_dimuat_ulang_otomatis(): void
    {
        $repo = app(PanduanRepository::class);
        $this->assertTrue($repo->sinkron());
        $this->assertFalse($repo->sinkron(), 'Tanpa perubahan JSON, tabel tidak dimuat ulang.');

        $topik = $this->topikContoh();
        $topik[1]['judul'] = 'Membayar Iuran Bulanan';
        $this->tulisSumber(array_slice($topik, 0, 4), 'versi-2');

        $this->assertTrue($repo->sinkron());
        $this->assertSame(4, PanduanTopik::count());
        $this->assertSame(['versi-2'], PanduanTopik::distinct()->pluck('versi')->all());
        $this->assertTrue(PanduanTopik::where('judul', 'Membayar Iuran Bulanan')->exists());
    }

    public function test_menu_pusat_bantuan_tersedia_untuk_semua_peran(): void
    {
        $semua = array_values(array_diff(config('panduan.buku'), ['Semua']));
        $item = collect(config('sidebarMenu'))->firstWhere('title', 'Pusat Bantuan');

        $this->assertNotNull($item);
        $this->assertSame('bantuan.index', $item['route']);
        foreach ($semua as $peran) {
            $this->assertContains($peran, $item['roles'], "Peran {$peran} belum punya menu Pusat Bantuan.");
        }

        $ketua = $this->pengguna(['Ketua RT'], 'ketuamenu');
        $this->actingAs($ketua)->get(route('bantuan.index'))->assertOk()
            ->assertSee('href="' . route('bantuan.index') . '"', false);
    }

    public function test_faq_dipaginasi(): void
    {
        $topik = $this->topikContoh();
        foreach (range(1, 10) as $i) {
            $faq = $topik[2];
            $faq['judul'] = sprintf('Pertanyaan nomor %02d?', $i);
            $faq['slug'] = 'warga-faq-nomor-' . $i;
            $topik[] = $faq;
        }
        $this->tulisSumber($topik, 'versi-faq');
        $warga = $this->pengguna(['Warga'], 'wargafaq');

        // 11 FAQ Warga: halaman 1 berisi 8, halaman 2 berisi 3.
        $hal1 = $this->actingAs($warga)->get(route('bantuan.index'))->assertOk()
            ->assertSee('data-testid="paginasi-faq-bantuan"', false)
            ->assertSee('Menampilkan 1-8 dari 11 pertanyaan')
            ->assertSee('faq=2#faq', false);
        $this->assertCount(8, $hal1->viewData('faq'));

        $hal2 = $this->actingAs($warga)->get(route('bantuan.index', ['faq' => 2]))->assertOk()
            ->assertSee('Menampilkan 9-11 dari 11 pertanyaan')
            ->assertSee('Pertanyaan nomor 10?')
            ->assertDontSee('Surat saya ditolak.', false)
            ->assertSee('Mengajukan Surat');
        $this->assertCount(3, $hal2->viewData('faq'));
    }

    public function test_bagian_kosong_dari_manual_book_dibuang(): void
    {
        $html = app(\App\Services\Panduan\PenyesuaianTeksPanduan::class)->html(
            '<section class="pb-bagian pb-bagian-langkah"><h4>Langkah-langkah</h4></section>'
            . '<section class="pb-bagian pb-bagian-langkah"><h4>Langkah-langkah</h4><ol class="pb-langkah"><li>Buka menu.</li></ol></section>'
        );

        $this->assertSame(1, substr_count($html, 'Langkah-langkah'));
        $this->assertStringContainsString('Buka menu.', $html);
    }

    public function test_nama_peran_lama_dinormalisasi(): void
    {
        $repo = app(PanduanRepository::class);

        $this->assertSame('Op Konten RT', $repo->normalisasiPeran('Op. Konten RT'));
        $this->assertSame('Admin Aplikasi', $repo->normalisasiPeran('admin aplikasi'));
        $this->assertNull($repo->normalisasiPeran('Peran Asing'));
    }
}
