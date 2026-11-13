<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\MasterRt;
use App\Models\User;
use App\Support\KontenScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Semua konten (berita, agenda, pengumuman; dari RT maupun RW) hanya disetujui Admin RW. Ketua RT dan Ketua RW tidak menyetujui konten. */
class PersetujuanBeritaAdminRwTest extends TestCase
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
        $this->rt1 = MasterRt::create(['kode_rt' => '993', 'nama_rt' => 'RT 993']);
        $this->rt2 = MasterRt::create(['kode_rt' => '994', 'nama_rt' => 'RT 994']);
        $this->adminRw = $this->user('Admin RW', '3217010101012701');
        $this->ketuaRw = $this->user('Ketua RW', '3217010101012702');
        $this->ketuaRt1 = $this->user('Ketua RT', '3217010101012703', $this->rt1->id);
        $this->opRw = $this->user('Op Konten RW', '3217010101012704');
        $this->opRt1 = $this->user('Op Konten RT', '3217010101012705', $this->rt1->id);
        $this->opRt2 = $this->user('Op Konten RT', '3217010101012706', $this->rt2->id);
    }

    private function user(string $role, string $nik, ?string $rtId = null): User
    {
        return User::create([
            'nik' => $nik, 'username' => 'u' . $nik, 'role' => $role, 'status_akun' => 'Active',
            'rt_id' => $rtId, 'password' => 'password123',
        ]);
    }

    private function berita(string $judul, User $operator, ?string $rtId, string $status = 'Review'): Berita
    {
        return Berita::create([
            'judul_berita' => $judul, 'kategori' => 'Umum', 'isi_berita' => 'Isi ' . $judul, 'status' => $status,
            'operator_id' => $operator->id, 'rt_id' => $rtId,
        ]);
    }

    public function test_admin_rw_melihat_dan_menyetujui_berita_rt_maupun_rw(): void
    {
        $beritaRw = $this->berita('Berita tingkat RW', $this->opRw, null);
        $beritaRt1 = $this->berita('Berita dari RT satu', $this->opRt1, $this->rt1->id);
        $beritaRt2 = $this->berita('Berita dari RT dua', $this->opRt2, $this->rt2->id);

        $this->actingAs($this->adminRw)->get(route('rw.persetujuan-konten.index'))->assertOk()
            ->assertSee('id="tabBerita"', false)
            ->assertSee('Berita tingkat RW')->assertSee('Berita dari RT satu')->assertSee('Berita dari RT dua')
            ->assertSee('RT 993')->assertSee('menyetujui semua berita');

        foreach ([$beritaRw, $beritaRt1, $beritaRt2] as $b) {
            $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.berita.approve', $b->id))->assertOk();
            $this->assertSame('Publish', $b->fresh()->status);
            $this->assertSame($this->adminRw->id, $b->fresh()->approval_id);
        }
    }

    public function test_admin_rw_menolak_berita_rt_dengan_catatan_dan_operator_menerima_revisi(): void
    {
        $berita = $this->berita('Berita dari RT satu', $this->opRt1, $this->rt1->id);

        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.berita.reject', $berita->id), [])->assertSessionHasErrors('catatan_revisi');
        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.berita.reject', $berita->id), ['catatan_revisi' => 'Ganti foto'])->assertOk();

        $this->assertSame('Revisi', $berita->fresh()->status);
        $this->assertSame('Ganti foto', $berita->fresh()->catatan_revisi);
    }

    public function test_ketua_rw_tidak_dapat_menyetujui_atau_menolak_berita(): void
    {
        $berita = $this->berita('Berita tingkat RW', $this->opRw, null);

        $this->actingAs($this->ketuaRw)->put(route('rw.persetujuan-konten.berita.approve', $berita->id))->assertForbidden();
        $this->actingAs($this->ketuaRw)->put(route('rw.persetujuan-konten.berita.reject', $berita->id), ['catatan_revisi' => 'x'])->assertForbidden();
        $this->assertSame('Review', $berita->fresh()->status);

        $this->actingAs($this->ketuaRw)->get(route('rw.persetujuan-konten.index'))->assertForbidden();
    }

    public function test_ketua_rt_tidak_memiliki_akses_persetujuan_konten_termasuk_berita_rt_nya(): void
    {
        $berita = $this->berita('Berita dari RT satu', $this->opRt1, $this->rt1->id);

        $this->actingAs($this->ketuaRt1)->get('/rt/persetujuan-konten')->assertNotFound();
        $this->actingAs($this->ketuaRt1)->put('/rt/persetujuan-konten/berita/' . $berita->id . '/approve')->assertNotFound();
        $this->assertSame('Review', $berita->fresh()->status);

        // Pusat Informasi hanya memuat konten terbit, jadi berita yang menunggu tidak tampil.
        $this->actingAs($this->ketuaRt1)->get(route('rt.pusat-informasi.index'))->assertOk()
            ->assertDontSee('Berita dari RT satu');
    }

    public function test_admin_rw_menyetujui_agenda_dan_pengumuman_rt(): void
    {
        $agendaRt = \App\Models\Agenda::create([
            'judul_agenda' => 'Agenda RT satu', 'kategori' => 'Umum', 'tanggal_mulai' => now()->addDay(), 'tanggal_selesai' => now()->addDay()->addHour(),
            'lokasi' => 'Balai', 'detail_pengumuman' => 'Detail', 'status' => 'Review', 'operator_id' => $this->opRt1->id,
            'rt_id' => $this->rt1->id, 'is_rsvp_enabled' => false,
        ]);
        $infoRt = \App\Models\Pengumuman::create([
            'judul_pengumuman' => 'Info RT satu', 'isi_pengumuman' => 'Isi', 'is_priority' => false, 'status' => 'Review',
            'operator_id' => $this->opRt1->id, 'rt_id' => $this->rt1->id,
        ]);

        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.agenda.approve', $agendaRt->id))->assertOk();
        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.pengumuman.reject', $infoRt->id), ['catatan_revisi' => 'Perjelas'])->assertOk();

        $this->assertSame('Publish', $agendaRt->fresh()->status);
        $this->assertSame('Revisi', $infoRt->fresh()->status);
    }

    public function test_hanya_admin_rw_yang_menjadi_penyetuju_dan_dashboard_menghitung_antrean_untuknya(): void
    {
        $this->berita('Berita RT satu', $this->opRt1, $this->rt1->id);
        $this->berita('Berita RW', $this->opRw, null);

        foreach (['berita', 'agenda', 'pengumuman'] as $jenis) {
            $this->assertTrue(KontenScope::bolehMenyetujui($jenis, $this->adminRw), $jenis);
            $this->assertFalse(KontenScope::bolehMenyetujui($jenis, $this->ketuaRw), $jenis);
            $this->assertFalse(KontenScope::bolehMenyetujui($jenis, $this->ketuaRt1), $jenis);
        }

        $this->actingAs($this->adminRw)->get(route('rw.dashboard'))->assertOk()->assertSee(route('rw.persetujuan-konten.index'), false);
        $this->actingAs($this->ketuaRw)->get(route('rw.dashboard'))->assertOk()
            ->assertDontSee(route('rw.persetujuan-konten.index'), false)
            ->assertSee(route('rw.pusat-informasi.index'), false);
    }

    public function test_operator_selalu_melihat_admin_rw_sebagai_penyetuju(): void
    {
        foreach (['berita', 'agenda', 'pengumuman'] as $jenis) {
            $this->assertSame('Admin RW', KontenScope::penyetuju($jenis, $this->opRt1), $jenis);
            $this->assertSame('Admin RW', KontenScope::penyetuju($jenis, $this->opRw), $jenis);
        }

        foreach (['opkonten.berita.index', 'opkonten.agenda.index', 'opkonten.pengumuman.index'] as $rute) {
            $this->actingAs($this->opRt1)->get(route($rute))->assertOk()
                ->assertSee('Admin RW')->assertDontSee('Ketua RT?')->assertDontSee('ke Ketua RW?');
        }
    }
}