<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Konten dibedakan per tingkat: RW (rt_id kosong) dan RT (rt_id terisi), seperti pemisahan unit pada keuangan. */
class KontenPerRtTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $opRt1;
    private User $opRt2;
    private User $opRw;
    private User $ketuaRt1;
    private User $ketuaRw;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt1 = MasterRt::create(['kode_rt' => '981', 'nama_rt' => 'RT 981']);
        $this->rt2 = MasterRt::create(['kode_rt' => '982', 'nama_rt' => 'RT 982']);
        $this->opRt1 = $this->user('Op Konten RT', '3217010101012501', $this->rt1->id);
        $this->opRt2 = $this->user('Op Konten RT', '3217010101012502', $this->rt2->id);
        $this->opRw = $this->user('Op Konten RW', '3217010101012503');
        $this->ketuaRt1 = $this->user('Ketua RT', '3217010101012504', $this->rt1->id);
        $this->ketuaRw = $this->user('Ketua RW', '3217010101012505');
    }

    private function user(string $role, string $nik, ?string $rtId = null): User
    {
        return User::create([
            'nik' => $nik, 'username' => 'u' . $nik, 'role' => $role, 'status_akun' => 'Active',
            'rt_id' => $rtId, 'password' => 'password123',
        ]);
    }

    private function pengumuman(string $judul, User $operator, ?string $rtId, string $status = 'Draft'): Pengumuman
    {
        return Pengumuman::create([
            'judul_pengumuman' => $judul, 'isi_pengumuman' => 'Isi ' . $judul, 'is_priority' => false,
            'status' => $status, 'operator_id' => $operator->id, 'rt_id' => $rtId,
        ]);
    }

    private function berita(string $judul, User $operator, ?string $rtId, string $status = 'Review'): Berita
    {
        return Berita::create([
            'judul_berita' => $judul, 'slug' => str($judul)->slug() . '-' . uniqid(), 'kategori' => 'Umum',
            'isi_berita' => 'Isi ' . $judul, 'status' => $status, 'operator_id' => $operator->id, 'rt_id' => $rtId,
        ]);
    }

    private function agenda(string $judul, User $operator, ?string $rtId, string $status = 'Publish'): Agenda
    {
        return Agenda::create([
            'judul_agenda' => $judul, 'kategori' => 'Sosial', 'tanggal_mulai' => now()->addDay(), 'tanggal_selesai' => now()->addDay()->addHour(),
            'lokasi' => 'Balai', 'detail_pengumuman' => 'Detail ' . $judul, 'status' => $status,
            'operator_id' => $operator->id, 'rt_id' => $rtId, 'is_rsvp_enabled' => false,
        ]);
    }

    private function warga(MasterRt $rt, string $nik): User
    {
        $kk = Keluarga::create(['no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji', 'rt_id' => $rt->id, 'no_wa' => '081234567890']);
        Penduduk::create([
            'keluarga_id' => $kk->id, 'nik' => $nik, 'nama_lengkap' => 'Warga ' . $nik, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Belum Kawin',
        ]);

        return $this->user('Warga', $nik, $rt->id);
    }

    // ───────────── Operator Konten ─────────────

    public function test_konten_yang_dibuat_operator_rt_terikat_ke_rt_di_akunnya(): void
    {
        $this->actingAs($this->opRt1)->post(route('opkonten.pengumuman.store'), [
            'judul_pengumuman' => 'Kerja bakti RT 981', 'isi_pengumuman' => '<p>Minggu pagi</p>',
        ])->assertRedirect();
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.store'), [
            'judul_pengumuman' => 'Rapat RW', 'isi_pengumuman' => '<p>Sabtu malam</p>',
        ])->assertRedirect();

        $this->assertSame($this->rt1->id, Pengumuman::where('judul_pengumuman', 'Kerja bakti RT 981')->value('rt_id'));
        $this->assertNull(Pengumuman::where('judul_pengumuman', 'Rapat RW')->value('rt_id'));
    }

    public function test_operator_hanya_melihat_pengumuman_dalam_cakupannya(): void
    {
        $this->pengumuman('Info RT satu', $this->opRt1, $this->rt1->id);
        $this->pengumuman('Info RT dua', $this->opRt2, $this->rt2->id);
        $this->pengumuman('Info tingkat RW', $this->opRw, null);

        $this->actingAs($this->opRt1)->get(route('opkonten.pengumuman.index'))->assertOk()
            ->assertSee('Info RT satu')->assertDontSee('Info RT dua')->assertDontSee('Info tingkat RW');

        $this->actingAs($this->opRw)->get(route('opkonten.pengumuman.index'))->assertOk()
            ->assertSee('Info tingkat RW')->assertDontSee('Info RT satu')->assertDontSee('Info RT dua');
    }

    public function test_operator_rt_tidak_dapat_mengubah_pengumuman_rt_lain(): void
    {
        $milikRt2 = $this->pengumuman('Milik RT dua', $this->opRt2, $this->rt2->id);

        $this->actingAs($this->opRt1)->put(route('opkonten.pengumuman.update', $milikRt2->id), [
            'judul_pengumuman' => 'Diubah paksa', 'isi_pengumuman' => '<p>x</p>',
        ])->assertNotFound();
        $this->assertSame('Milik RT dua', $milikRt2->fresh()->judul_pengumuman);
    }

    public function test_peran_rt_tanpa_rt_di_akun_ditolak_bukan_dialihkan_ke_rt_lain(): void
    {
        $tanpaRt = $this->user('Op Konten RT', '3217010101012599');

        $this->actingAs($tanpaRt)->post(route('opkonten.pengumuman.store'), [
            'judul_pengumuman' => 'Tanpa RT', 'isi_pengumuman' => '<p>x</p>',
        ])->assertForbidden();
        $this->assertSame(0, Pengumuman::where('judul_pengumuman', 'Tanpa RT')->count());
    }

    public function test_dashboard_operator_menampilkan_asal_konten(): void
    {
        $this->actingAs($this->opRt1)->get(route('opkonten.dashboard'))->assertOk()->assertSee('RT 981');
        $this->actingAs($this->opRw)->get(route('opkonten.dashboard'))->assertOk()
            ->assertSee('data-testid="asal-konten"', false)->assertDontSee('RT 981');
    }

    public function test_galeri_operator_rt_tidak_dapat_membuka_agenda_rt_lain(): void
    {
        $agendaRt2 = $this->agenda('Agenda RT dua', $this->opRt2, $this->rt2->id);

        $this->actingAs($this->opRt1)->get(route('opkonten.galeri.show', $agendaRt2->id))->assertNotFound();
        $this->actingAs($this->opRt2)->get(route('opkonten.galeri.show', $agendaRt2->id))->assertOk();
    }

    // ───────────── Persetujuan ─────────────

    public function test_ketua_rt_hanya_menyetujui_konten_rt_nya(): void
    {
        $milikRt1 = $this->berita('Berita RT satu', $this->opRt1, $this->rt1->id);
        $milikRt2 = $this->berita('Berita RT dua', $this->opRt2, $this->rt2->id);
        $milikRw = $this->berita('Berita RW', $this->opRw, null);

        $this->actingAs($this->ketuaRt1)->get(route('rt.pusat-informasi.index'))->assertOk()
            ->assertSee('Berita RT satu')->assertDontSee('Berita RT dua')->assertDontSee('Berita RW');

        $this->actingAs($this->ketuaRt1)->put(route('rt.pusat-informasi.berita.approve', $milikRt2->id))->assertNotFound();
        $this->actingAs($this->ketuaRt1)->put(route('rt.pusat-informasi.berita.approve', $milikRw->id))->assertNotFound();
        $this->actingAs($this->ketuaRt1)->put(route('rt.pusat-informasi.berita.approve', $milikRt1->id))->assertOk();

        $this->assertSame('Publish', $milikRt1->fresh()->status);
        $this->assertSame('Review', $milikRt2->fresh()->status);
        $this->assertSame('Review', $milikRw->fresh()->status);
    }

    public function test_ketua_rw_hanya_menyetujui_konten_tingkat_rw(): void
    {
        $milikRt1 = $this->berita('Berita RT satu', $this->opRt1, $this->rt1->id);
        $milikRw = $this->berita('Berita RW', $this->opRw, null);

        $this->actingAs($this->ketuaRw)->put(route('rw.pusat-informasi.berita.approve', $milikRt1->id))->assertNotFound();
        $this->actingAs($this->ketuaRw)->put(route('rw.pusat-informasi.berita.approve', $milikRw->id))->assertOk();

        $this->assertSame('Review', $milikRt1->fresh()->status);
        $this->assertSame('Publish', $milikRw->fresh()->status);
    }

    // ───────────── Tampilan warga ─────────────

    public function test_warga_melihat_konten_rw_dan_rt_nya_saja(): void
    {
        $this->berita('Berita tingkat RW', $this->opRw, null, 'Publish');
        $this->berita('Berita RT satu', $this->opRt1, $this->rt1->id, 'Publish');
        $this->berita('Berita RT dua', $this->opRt2, $this->rt2->id, 'Publish');
        $warga1 = $this->warga($this->rt1, '3217010101012511');

        $this->actingAs($warga1)->get(route('warga.berita.index'))->assertOk()
            ->assertSee('Berita tingkat RW')->assertSee('Berita RT satu')->assertDontSee('Berita RT dua');
    }

    public function test_agenda_rt_lain_tidak_dapat_dibuka_warga(): void
    {
        $agendaRt2 = $this->agenda('Agenda RT dua', $this->opRt2, $this->rt2->id);
        $agendaRt1 = $this->agenda('Agenda RT satu', $this->opRt1, $this->rt1->id);
        $warga1 = $this->warga($this->rt1, '3217010101012512');

        $this->actingAs($warga1)->get(route('warga.agenda.show', $agendaRt2->id))->assertNotFound();
        $this->actingAs($warga1)->get(route('warga.agenda.show', $agendaRt1->id))->assertOk();
    }

    public function test_beranda_publik_hanya_menampilkan_konten_rw(): void
    {
        $this->berita('Berita tingkat RW', $this->opRw, null, 'Publish');
        $this->berita('Berita khusus RT satu', $this->opRt1, $this->rt1->id, 'Publish');

        $this->get('/')->assertOk()->assertSee('Berita tingkat RW')->assertDontSee('Berita khusus RT satu');
    }

    public function test_notifikasi_konten_rt_hanya_untuk_warga_rt_tersebut(): void
    {
        $warga1 = $this->warga($this->rt1, '3217010101012521');
        $warga2 = $this->warga($this->rt2, '3217010101012522');

        $ids = NotificationService::wargaDiRt($this->rt1->id)->pluck('id')->all();

        $this->assertContains($warga1->id, $ids);
        $this->assertNotContains($warga2->id, $ids);
    }
}
