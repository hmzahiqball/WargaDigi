<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\MasterRt;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Persetujuan konten ada di menu Persetujuan (antrean Review/Revisi); Pusat Informasi hanya memuat konten
 * yang sudah terbit dan tidak lagi menyediakan aksi setuju/tolak.
 */
class PusatInformasiTerbitTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $adminRw;
    private User $ketuaRw;
    private User $ketuaRt1;
    private User $opRw;
    private User $opRt1;
    private User $opRt2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt1 = MasterRt::create(['kode_rt' => '997', 'nama_rt' => 'RT 997']);
        $this->rt2 = MasterRt::create(['kode_rt' => '998', 'nama_rt' => 'RT 998']);
        $this->adminRw = $this->user('Admin RW', '3217010101012901');
        $this->ketuaRw = $this->user('Ketua RW', '3217010101012902');
        $this->ketuaRt1 = $this->user('Ketua RT', '3217010101012903', $this->rt1->id);
        $this->opRw = $this->user('Op Konten RW', '3217010101012904');
        $this->opRt1 = $this->user('Op Konten RT', '3217010101012905', $this->rt1->id);
        $this->opRt2 = $this->user('Op Konten RT', '3217010101012906', $this->rt2->id);
    }

    private function user(string $role, string $nik, ?string $rtId = null): User
    {
        return User::create([
            'nik' => $nik, 'username' => 'u' . $nik, 'role' => $role, 'status_akun' => 'Active',
            'rt_id' => $rtId, 'password' => 'password123',
        ]);
    }

    private function berita(string $judul, User $op, ?string $rtId, string $status): Berita
    {
        return Berita::create([
            'judul_berita' => $judul, 'kategori' => 'Umum', 'isi_berita' => 'Isi', 'status' => $status,
            'operator_id' => $op->id, 'rt_id' => $rtId, 'tanggal_publish' => $status === 'Publish' ? now() : null,
        ]);
    }

    private function pengumuman(string $judul, User $op, ?string $rtId, string $status): Pengumuman
    {
        return Pengumuman::create([
            'judul_pengumuman' => $judul, 'isi_pengumuman' => 'Isi', 'is_priority' => false, 'status' => $status,
            'operator_id' => $op->id, 'rt_id' => $rtId,
        ]);
    }

    private function agenda(string $judul, User $op, ?string $rtId, string $status): Agenda
    {
        return Agenda::create([
            'judul_agenda' => $judul, 'kategori' => 'Umum', 'tanggal_mulai' => now()->addDay(), 'tanggal_selesai' => now()->addDay()->addHour(),
            'lokasi' => 'Balai', 'detail_pengumuman' => 'Detail', 'status' => $status, 'operator_id' => $op->id,
            'rt_id' => $rtId, 'is_rsvp_enabled' => false,
        ]);
    }

    public function test_pusat_informasi_hanya_memuat_konten_terbit_tanpa_aksi_persetujuan(): void
    {
        $this->berita('Berita terbit', $this->opRw, null, 'Publish');
        $this->berita('Berita menunggu', $this->opRw, null, 'Review');
        $this->berita('Berita diarsipkan', $this->opRw, null, 'Archive');
        $this->agenda('Agenda terbit', $this->opRw, null, 'Publish');
        $this->agenda('Agenda draf', $this->opRw, null, 'Draft');
        $this->pengumuman('Pengumuman terbit', $this->opRw, null, 'Publish');
        $this->pengumuman('Pengumuman revisi', $this->opRw, null, 'Revisi');

        $this->actingAs($this->adminRw)->get(route('rw.pusat-informasi.index'))->assertOk()
            ->assertSee('Berita terbit')->assertSee('Agenda terbit')->assertSee('Pengumuman terbit')
            ->assertDontSee('Berita menunggu')->assertDontSee('Berita diarsipkan')
            ->assertDontSee('Agenda draf')->assertDontSee('Pengumuman revisi')
            ->assertSee('yang sudah terbit')
            ->assertDontSee('Meninjau pengajuan');
    }

    public function test_aksi_setuju_dan_tolak_tidak_lagi_ada_di_pusat_informasi(): void
    {
        $berita = $this->berita('Berita menunggu', $this->opRt1, $this->rt1->id, 'Review');

        // Alamat lama tidak ada lagi; persetujuan hanya lewat menu Persetujuan.
        $this->actingAs($this->adminRw)->put('/rw/pusat-informasi/berita/' . $berita->id . '/approve')->assertNotFound();
        $this->actingAs($this->adminRw)->put('/rw/pusat-informasi/berita/' . $berita->id . '/reject', ['catatan_revisi' => 'x'])->assertNotFound();
        $this->assertSame('Review', $berita->fresh()->status);

        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.berita.approve', $berita->id))->assertOk();
        $this->assertSame('Publish', $berita->fresh()->status);
    }

    public function test_pimpinan_rw_memantau_konten_terbit_rw_dan_semua_rt(): void
    {
        $this->pengumuman('Info RW terbit', $this->opRw, null, 'Publish');
        $this->pengumuman('Info RT satu terbit', $this->opRt1, $this->rt1->id, 'Publish');
        $this->pengumuman('Info RT dua terbit', $this->opRt2, $this->rt2->id, 'Publish');

        foreach ([$this->adminRw, $this->ketuaRw] as $pimpinan) {
            $this->actingAs($pimpinan)->get(route('rw.pusat-informasi.index'))->assertOk()
                ->assertSee('Info RW terbit')->assertSee('Info RT satu terbit')->assertSee('Info RT dua terbit');
        }
    }

    public function test_ketua_rt_memantau_konten_rw_dan_rt_nya_saja(): void
    {
        $this->pengumuman('Info RW terbit', $this->opRw, null, 'Publish');
        $this->pengumuman('Info RT satu terbit', $this->opRt1, $this->rt1->id, 'Publish');
        $this->pengumuman('Info RT dua terbit', $this->opRt2, $this->rt2->id, 'Publish');
        $this->pengumuman('Info RT satu menunggu', $this->opRt1, $this->rt1->id, 'Review');

        $this->actingAs($this->ketuaRt1)->get(route('rt.pusat-informasi.index'))->assertOk()
            ->assertSee('Info RW terbit')->assertSee('Info RT satu terbit')
            ->assertDontSee('Info RT dua terbit')->assertDontSee('Info RT satu menunggu')
            ->assertSee('tampil bagi warga');
    }

    public function test_antrean_persetujuan_memuat_review_dan_revisi_bukan_konten_terbit(): void
    {
        $this->agenda('Agenda menunggu', $this->opRw, null, 'Review');
        $this->agenda('Agenda dikembalikan', $this->opRw, null, 'Revisi');
        $this->agenda('Agenda sudah terbit', $this->opRw, null, 'Publish');
        $this->agenda('Agenda masih draf', $this->opRw, null, 'Draft');

        $this->actingAs($this->adminRw)->get(route('rw.persetujuan-konten.index'))->assertOk()
            ->assertSee('Persetujuan Konten')
            ->assertSee('Agenda menunggu')->assertSee('Agenda dikembalikan')
            ->assertDontSee('Agenda sudah terbit')->assertDontSee('Agenda masih draf');
    }

    public function test_daftar_pusat_informasi_dan_persetujuan_konten_memuat_paginasi_per_tab(): void
    {
        foreach (range(1, 12) as $n) {
            $this->berita('Berita terbit ' . $n, $this->opRw, null, 'Publish');
            $this->berita('Berita menunggu ' . $n, $this->opRw, null, 'Review');
        }

        foreach ([route('rw.pusat-informasi.index'), route('rw.persetujuan-konten.index')] as $url) {
            $html = $this->actingAs($this->adminRw)->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('data-testid="paginasi-konten"', $html, $url);
            $this->assertStringContainsString('id="paginasiNav"', $html, $url);
            $this->assertStringContainsString('const PER_HALAMAN = 10;', $html, $url);
            // Ganti tab, cari, dan filter status kembali ke halaman pertama.
            $this->assertSame(4, substr_count($html, 'halamanSaatIni = 1;'), $url);
        }

        // Seluruh data tab tetap dikirim, sehingga pencarian dan filter berlaku di semua halaman, bukan hanya yang tampil.
        $html = $this->actingAs($this->adminRw)->get(route('rw.pusat-informasi.index'))->getContent();
        $this->assertStringContainsString('Berita terbit 1', $html);
        $this->assertStringContainsString('Berita terbit 12', $html);
    }
    public function test_menu_persetujuan_konten_hanya_untuk_admin_rw_sedangkan_pusat_informasi_untuk_pemantau(): void
    {
        $this->actingAs($this->adminRw)->get(route('rw.dashboard'))->assertOk()
            ->assertSee(route('rw.persetujuan-konten.index'), false)
            ->assertSee(route('rw.pusat-informasi.index'), false);

        // Ketua RW dan Ketua RT tidak menyetujui konten: menu persetujuan tidak ada, Pusat Informasi tetap ada.
        $this->actingAs($this->ketuaRw)->get(route('rw.dashboard'))->assertOk()
            ->assertDontSee(route('rw.persetujuan-konten.index'), false)
            ->assertSee(route('rw.pusat-informasi.index'), false);

        $this->actingAs($this->ketuaRt1)->get(route('rt.dashboard'))->assertOk()
            ->assertDontSee('persetujuan-konten', false)
            ->assertSee(route('rt.pusat-informasi.index'), false)
            ->assertSee(route('rt.persetujuan-keuangan.index'), false)
            ->assertSee(route('rt.mutasi.index'), false);
    }
}