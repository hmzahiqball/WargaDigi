<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Keluarga;
use App\Models\KategoriKonten;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\Pengumuman;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Admin Aplikasi mengatur per jenis konten apakah perlu persetujuan. Aktif: diajukan masuk antrean persetujuan.
 * Nonaktif: langsung terbit. Juga: pengumuman dapat langsung diajukan, dan pesan sukses tidak ganda.
 */
class PengaturanPersetujuanKontenTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $admin;
    private User $adminRw;
    private User $ketuaRt1;
    private User $opRw;
    private User $opRt1;
    private KategoriKonten $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->rt1 = MasterRt::create(['kode_rt' => '971', 'nama_rt' => 'RT 971']);
        $this->rt2 = MasterRt::create(['kode_rt' => '972', 'nama_rt' => 'RT 972']);
        $this->admin = $this->user('Admin Aplikasi', '3217010101013001');
        $this->adminRw = $this->user('Admin RW', '3217010101013002');
        $this->ketuaRt1 = $this->user('Ketua RT', '3217010101013003', $this->rt1->id);
        $this->opRw = $this->user('Op Konten RW', '3217010101013004');
        $this->opRt1 = $this->user('Op Konten RT', '3217010101013005', $this->rt1->id);
        $this->kategori = KategoriKonten::create(['nama' => 'Uji Pengaturan', 'warna' => 'hijau']);
        foreach (['berita', 'agenda', 'pengumuman'] as $jenis) {
            SystemSetting::set('konten_approval_' . $jenis, true);
        }
    }

    protected function tearDown(): void
    {
        foreach (Agenda::whereNotNull('banner_flyer')->pluck('banner_flyer') as $path) {
            $file = public_path($path);
            if (str_starts_with((string) $path, 'assets/images/agenda/') && is_file($file)) {
                @unlink($file);
            }
        }
        parent::tearDown();
    }

    private function user(string $role, string $nik, ?string $rtId = null): User
    {
        return User::create([
            'nik' => $nik, 'username' => 'u' . $nik, 'role' => $role, 'status_akun' => 'Active',
            'rt_id' => $rtId, 'password' => 'password123',
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

    private function postBerita(User $op, string $aksi, string $judul = 'Berita Uji'): void
    {
        $this->actingAs($op)->post(route('opkonten.berita.store'), [
            'judul_berita' => $judul, 'kategori_id' => $this->kategori->id, 'isi_berita' => '<p>x</p>',
            'foto_utama' => UploadedFile::fake()->image('f.jpg'), 'action' => $aksi,
        ])->assertRedirect();
    }

    private function postAgenda(User $op, string $aksi, string $judul = 'Agenda Uji'): void
    {
        $this->actingAs($op)->post(route('opkonten.agenda.store'), [
            'judul_agenda' => $judul, 'kategori_id' => $this->kategori->id, 'tanggal' => now()->addDays(2)->toDateString(),
            'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Balai',
            'detail_pengumuman' => '<p>x</p>', 'banner_flyer' => UploadedFile::fake()->image('b.jpg'), 'action' => $aksi,
        ])->assertRedirect();
    }

    private function postPengumuman(User $op, ?string $aksi, string $judul = 'Pengumuman Uji'): void
    {
        $this->actingAs($op)->post(route('opkonten.pengumuman.store'), array_filter([
            'judul_pengumuman' => $judul, 'kategori_id' => $this->kategori->id, 'isi_pengumuman' => '<p>x</p>', 'action' => $aksi,
        ]))->assertRedirect();
    }

    // ───────────── Pengaturan di Admin Aplikasi ─────────────

    public function test_admin_aplikasi_melihat_dan_menyimpan_pengaturan_persetujuan_per_jenis(): void
    {
        $this->actingAs($this->admin)->get(route('admin.pengaturan-sistem'))->assertOk()
            ->assertSee('data-testid="kartu-persetujuan-konten"', false)
            ->assertSee('Perlu persetujuan: Berita')->assertSee('Perlu persetujuan: Agenda')->assertSee('Perlu persetujuan: Pengumuman');

        // Hanya agenda yang tetap aktif; berita dan pengumuman dimatikan.
        $this->actingAs($this->admin)->post(route('admin.pengaturan-sistem.update'), [
            'instance_name' => 'RW Uji', 'session_timeout' => 30,
            'konten_approval_form' => '1', 'konten_approval_agenda' => '1',
        ])->assertRedirect();

        $this->assertFalse((bool) SystemSetting::get('konten_approval_berita'));
        $this->assertTrue((bool) SystemSetting::get('konten_approval_agenda'));
        $this->assertFalse((bool) SystemSetting::get('konten_approval_pengumuman'));
    }

    public function test_simpan_pengaturan_tanpa_bagian_persetujuan_tidak_mengubahnya(): void
    {
        $this->actingAs($this->admin)->post(route('admin.pengaturan-sistem.update'), [
            'instance_name' => 'RW Uji', 'session_timeout' => 30,
        ])->assertRedirect();

        foreach (['berita', 'agenda', 'pengumuman'] as $jenis) {
            $this->assertTrue((bool) SystemSetting::get('konten_approval_' . $jenis), $jenis);
        }
    }

    public function test_default_perlu_persetujuan_untuk_semua_jenis(): void
    {
        SystemSetting::query()->where('key', 'like', 'konten_approval_%')->delete();
        SystemSetting::flushCache();

        foreach (['berita', 'agenda', 'pengumuman'] as $jenis) {
            $this->assertTrue((bool) SystemSetting::get('konten_approval_' . $jenis), $jenis);
        }
    }

    // ───────────── Persetujuan aktif ─────────────

    public function test_bila_aktif_pengajuan_masuk_antrean_review(): void
    {
        $this->postBerita($this->opRt1, 'review');
        $this->postAgenda($this->opRt1, 'review');
        $this->postPengumuman($this->opRt1, 'review');

        $this->assertSame('Review', Berita::first()->status);
        $this->assertSame('Review', Agenda::first()->status);
        $this->assertSame('Review', Pengumuman::first()->status);
        $this->assertNull(Berita::first()->tanggal_publish);
    }

    // ───────────── Persetujuan nonaktif ─────────────

    public function test_bila_nonaktif_berita_langsung_terbit_tanpa_notifikasi_penyetuju(): void
    {
        Notification::fake();
        SystemSetting::set('konten_approval_berita', false);

        $this->postBerita($this->opRt1, 'review');

        $berita = Berita::first();
        $this->assertSame('Publish', $berita->status);
        $this->assertNotNull($berita->tanggal_publish);
        $this->assertNull($berita->approval_id);
        Notification::assertNothingSent();   // tidak ada pengajuan, dan berita memang tanpa notifikasi warga
    }

    public function test_bila_nonaktif_agenda_rt_langsung_terbit_dan_hanya_memberi_tahu_warga_rt_itu(): void
    {
        Notification::fake();
        SystemSetting::set('konten_approval_agenda', false);
        $wargaRt1 = $this->warga($this->rt1, '3217010101013011');
        $wargaRt2 = $this->warga($this->rt2, '3217010101013012');

        $this->postAgenda($this->opRt1, 'review');

        $this->assertSame('Publish', Agenda::first()->status);
        $this->assertSame(1, Notification::sent($wargaRt1, GeneralNotification::class)->count());
        $this->assertSame(0, Notification::sent($wargaRt2, GeneralNotification::class)->count());
        $this->assertSame(0, Notification::sent($this->ketuaRt1, GeneralNotification::class)->count());
    }

    public function test_bila_nonaktif_pengumuman_langsung_terbit_dari_tombol_ajukan_dan_dari_draf(): void
    {
        SystemSetting::set('konten_approval_pengumuman', false);

        $this->postPengumuman($this->opRw, 'review', 'Langsung Terbit');
        $this->assertSame('Publish', Pengumuman::where('judul_pengumuman', 'Langsung Terbit')->first()->status);

        $this->postPengumuman($this->opRw, 'draft', 'Masih Draf');
        $draf = Pengumuman::where('judul_pengumuman', 'Masih Draf')->first();
        $this->assertSame('Draft', $draf->status);

        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.submit', $draf->id))->assertRedirect();
        $this->assertSame('Publish', $draf->fresh()->status);
        $this->assertNotNull($draf->fresh()->tanggal_publish);
    }

    public function test_pengaturan_per_jenis_berdiri_sendiri(): void
    {
        SystemSetting::set('konten_approval_berita', false);

        $this->postBerita($this->opRw, 'review');
        $this->postAgenda($this->opRw, 'review');

        $this->assertSame('Publish', Berita::first()->status);
        $this->assertSame('Review', Agenda::first()->status);
    }

    public function test_revisi_yang_disimpan_saat_persetujuan_nonaktif_langsung_terbit(): void
    {
        SystemSetting::set('konten_approval_pengumuman', false);
        $revisi = Pengumuman::create([
            'judul_pengumuman' => 'Revisi Lama', 'isi_pengumuman' => 'x', 'is_priority' => false, 'status' => 'Revisi',
            'operator_id' => $this->opRw->id, 'kategori' => $this->kategori->nama, 'kategori_id' => $this->kategori->id,
        ]);

        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.update', $revisi->id), [
            'judul_pengumuman' => 'Revisi Lama', 'kategori_id' => $this->kategori->id, 'isi_pengumuman' => '<p>diperbaiki</p>',
        ])->assertRedirect();

        $this->assertSame('Publish', $revisi->fresh()->status);
    }

    public function test_konten_yang_sudah_review_saat_pengaturan_dimatikan_tetap_diproses_penyetuju(): void
    {
        $this->postBerita($this->opRt1, 'review');
        $berita = Berita::first();
        SystemSetting::set('konten_approval_berita', false);

        $this->assertSame('Review', $berita->fresh()->status);
        $this->actingAs($this->adminRw)->put(route('rw.persetujuan-konten.berita.approve', $berita->id))->assertOk();
        $this->assertSame('Publish', $berita->fresh()->status);
    }

    // ───────────── Pengumuman: ajukan langsung ─────────────

    public function test_form_pengumuman_punya_tombol_ajukan_dan_simpan_draf(): void
    {
        $this->actingAs($this->opRw)->get(route('opkonten.pengumuman.index'))->assertOk()
            ->assertSee('id="btnAjukanPengumuman"', false)
            ->assertSee('Ajukan Pengumuman')
            ->assertSee('Simpan ke Draf');

        SystemSetting::set('konten_approval_pengumuman', false);
        $this->actingAs($this->opRw)->get(route('opkonten.pengumuman.index'))->assertOk()
            ->assertSee('Terbitkan Pengumuman')->assertDontSee('Ajukan Pengumuman');
    }

    public function test_simpan_draf_pengumuman_tidak_mengajukan_dan_edit_draf_bisa_langsung_diajukan(): void
    {
        $this->postPengumuman($this->opRw, 'draft');
        $draf = Pengumuman::first();
        $this->assertSame('Draft', $draf->status);

        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.update', $draf->id), [
            'judul_pengumuman' => 'Pengumuman Uji', 'kategori_id' => $this->kategori->id, 'isi_pengumuman' => '<p>x</p>', 'action' => 'review',
        ])->assertRedirect();
        $this->assertSame('Review', $draf->fresh()->status);
    }

    public function test_teks_tombol_operator_mengikuti_pengaturan_untuk_berita_dan_agenda(): void
    {
        $this->actingAs($this->opRt1)->get(route('opkonten.berita.index'))->assertOk()->assertSee('Ajukan Berita');
        $this->actingAs($this->opRt1)->get(route('opkonten.agenda.index'))->assertOk()->assertSee('Ajukan Agenda');

        SystemSetting::set('konten_approval_berita', false);
        SystemSetting::set('konten_approval_agenda', false);

        $this->actingAs($this->opRt1)->get(route('opkonten.berita.index'))->assertOk()
            ->assertSee('Terbitkan Berita')->assertDontSee('Ajukan Berita');
        $this->actingAs($this->opRt1)->get(route('opkonten.agenda.index'))->assertOk()
            ->assertSee('Terbitkan Agenda')->assertDontSee('Ajukan Agenda');
    }

    // ───────────── Pesan sukses tidak ganda ─────────────

    public function test_pesan_sukses_hanya_ditampilkan_sekali_di_halaman_operator(): void
    {
        $pesan = 'Pesan uji unik 12345';

        foreach (['opkonten.berita.index', 'opkonten.agenda.index', 'opkonten.pengumuman.index'] as $rute) {
            $html = $this->withSession(['success' => $pesan])->actingAs($this->opRw)->get(route($rute))->assertOk()->getContent();
            $this->assertSame(1, substr_count($html, $pesan), "Pesan tampil lebih dari sekali di {$rute}");
        }

        // Galat: dialog global menulis pesan di dua cabang if/else (hanya satu yang berjalan); halaman tidak boleh menambah panggilan sendiri.
        foreach (['opkonten.berita.index', 'opkonten.agenda.index', 'opkonten.pengumuman.index', 'opkonten.galeri.index'] as $rute) {
            $html = $this->withSession(['error' => 'Galat uji unik 67890'])->actingAs($this->opRw)->get(route($rute))->assertOk()->getContent();
            $this->assertStringNotContainsString('showNotification("Galat uji unik 67890"', $html, $rute);
        }
    }
}
