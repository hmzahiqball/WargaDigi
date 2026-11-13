<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\KategoriKonten;
use App\Models\MasterRt;
use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Penyetuju diberi tahu saat konten diajukan (sesuai cakupan), dan status pengumuman dijaga seperti berita/agenda. */
class NotifikasiPengajuanKontenTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $adminRw;
    private User $ketuaRw;
    private User $ketuaRt1;
    private User $ketuaRt2;
    private User $opRw;
    private User $opRt1;
    private User $opRt2;
    private KategoriKonten $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->rt1 = MasterRt::create(['kode_rt' => '995', 'nama_rt' => 'RT 995']);
        $this->rt2 = MasterRt::create(['kode_rt' => '996', 'nama_rt' => 'RT 996']);
        $this->adminRw = $this->user('Admin RW', '3217010101012801');
        $this->ketuaRw = $this->user('Ketua RW', '3217010101012802');
        $this->ketuaRt1 = $this->user('Ketua RT', '3217010101012803', $this->rt1->id);
        $this->ketuaRt2 = $this->user('Ketua RT', '3217010101012804', $this->rt2->id);
        $this->opRw = $this->user('Op Konten RW', '3217010101012805');
        $this->opRt1 = $this->user('Op Konten RT', '3217010101012806', $this->rt1->id);
        $this->opRt2 = $this->user('Op Konten RT', '3217010101012807', $this->rt2->id);
        $this->kategori = KategoriKonten::create(['nama' => 'Uji Notifikasi', 'warna' => 'hijau']);
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

    private function pengumuman(User $operator, ?string $rtId, string $status): Pengumuman
    {
        return Pengumuman::create([
            'judul_pengumuman' => 'Info ' . $status . ' ' . ($rtId ? 'RT' : 'RW'), 'isi_pengumuman' => 'Isi', 'is_priority' => false,
            'status' => $status, 'operator_id' => $operator->id, 'rt_id' => $rtId,
            'kategori' => $this->kategori->nama, 'kategori_id' => $this->kategori->id,
        ]);
    }

    private function sampai(User $user): bool
    {
        return Notification::sent($user, GeneralNotification::class)->isNotEmpty();
    }

    // ───────────── Notifikasi pengajuan ─────────────

    public function test_pengajuan_berita_hanya_memberi_tahu_admin_rw(): void
    {
        Notification::fake();

        $this->actingAs($this->opRt1)->post(route('opkonten.berita.store'), [
            'judul_berita' => 'Berita RT', 'kategori_id' => $this->kategori->id, 'isi_berita' => '<p>x</p>',
            'foto_utama' => UploadedFile::fake()->image('f.jpg'), 'action' => 'review',
        ])->assertRedirect();

        $this->assertTrue($this->sampai($this->adminRw));
        $this->assertFalse($this->sampai($this->ketuaRw));
        $this->assertFalse($this->sampai($this->ketuaRt1));
        $this->assertFalse($this->sampai($this->ketuaRt2));
    }

    public function test_draf_tidak_memberi_notifikasi_penyetuju(): void
    {
        Notification::fake();

        $this->actingAs($this->opRt1)->post(route('opkonten.berita.store'), [
            'judul_berita' => 'Draf RT', 'kategori_id' => $this->kategori->id, 'isi_berita' => '<p>x</p>',
            'foto_utama' => UploadedFile::fake()->image('f.jpg'), 'action' => 'draft',
        ])->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_pengajuan_agenda_rt_hanya_memberi_tahu_admin_rw(): void
    {
        Notification::fake();

        $this->actingAs($this->opRt1)->post(route('opkonten.agenda.store'), [
            'judul_agenda' => 'Agenda RT', 'kategori_id' => $this->kategori->id, 'tanggal' => now()->addDays(2)->toDateString(),
            'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Balai',
            'detail_pengumuman' => '<p>x</p>', 'banner_flyer' => UploadedFile::fake()->image('b.jpg'), 'action' => 'review',
        ])->assertRedirect();

        // Semua pengajuan konten (RT maupun RW) hanya diberitahukan ke Admin RW.
        $this->assertTrue($this->sampai($this->adminRw));
        $this->assertFalse($this->sampai($this->ketuaRt1));
        $this->assertFalse($this->sampai($this->ketuaRt2));
        $this->assertFalse($this->sampai($this->ketuaRw));
    }

    public function test_pengajuan_pengumuman_rw_hanya_memberi_tahu_admin_rw(): void
    {
        Notification::fake();
        $draf = $this->pengumuman($this->opRw, null, 'Draft');

        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.submit', $draf->id))->assertRedirect();

        $this->assertSame('Review', $draf->fresh()->status);
        $this->assertTrue($this->sampai($this->adminRw));
        $this->assertFalse($this->sampai($this->ketuaRw));
        $this->assertFalse($this->sampai($this->ketuaRt1));
    }

    public function test_pengumuman_revisi_yang_disimpan_kembali_ke_review_dan_memberi_tahu_penyetuju(): void
    {
        Notification::fake();
        $revisi = $this->pengumuman($this->opRt1, $this->rt1->id, 'Revisi');

        $this->actingAs($this->opRt1)->put(route('opkonten.pengumuman.update', $revisi->id), [
            'judul_pengumuman' => $revisi->judul_pengumuman, 'kategori_id' => $this->kategori->id, 'isi_pengumuman' => '<p>diperbaiki</p>',
        ])->assertRedirect();

        $this->assertSame('Review', $revisi->fresh()->status);
        $this->assertTrue($this->sampai($this->adminRw));
        $this->assertFalse($this->sampai($this->ketuaRt1));
        $this->assertFalse($this->sampai($this->ketuaRt2));
    }

    // ───────────── Penjagaan status pengumuman ─────────────

    public function test_pengumuman_terbit_tidak_dapat_diajukan_ulang_dibatalkan_atau_dihapus(): void
    {
        $terbit = $this->pengumuman($this->opRw, null, 'Publish');

        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.submit', $terbit->id))->assertForbidden();
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.cancel-submit', $terbit->id))->assertForbidden();
        $this->actingAs($this->opRw)->delete(route('opkonten.pengumuman.destroy', $terbit->id))->assertForbidden();

        $this->assertSame('Publish', $terbit->fresh()->status);
    }

    public function test_pengumuman_review_tidak_dapat_dihapus_dan_draf_atau_revisi_boleh(): void
    {
        $review = $this->pengumuman($this->opRw, null, 'Review');
        $draf = $this->pengumuman($this->opRw, null, 'Draft');
        $revisi = $this->pengumuman($this->opRw, null, 'Revisi');

        $this->actingAs($this->opRw)->delete(route('opkonten.pengumuman.destroy', $review->id))->assertForbidden();
        $this->actingAs($this->opRw)->delete(route('opkonten.pengumuman.destroy', $draf->id))->assertRedirect();
        $this->actingAs($this->opRw)->delete(route('opkonten.pengumuman.destroy', $revisi->id))->assertRedirect();

        $this->assertDatabaseHas('pengumuman', ['id' => $review->id]);
        $this->assertDatabaseMissing('pengumuman', ['id' => $draf->id]);
        $this->assertDatabaseMissing('pengumuman', ['id' => $revisi->id]);
    }

    public function test_hanya_review_yang_dapat_dibatalkan_dan_hanya_draf_atau_revisi_yang_dapat_diajukan(): void
    {
        $draf = $this->pengumuman($this->opRw, null, 'Draft');
        $review = $this->pengumuman($this->opRw, null, 'Review');

        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.cancel-submit', $draf->id))->assertForbidden();
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.submit', $review->id))->assertForbidden();
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.cancel-submit', $review->id))->assertRedirect();
        $this->assertSame('Draft', $review->fresh()->status);
    }

    public function test_arsip_pengumuman_mencabut_dari_warga_dan_dapat_dibuka_kembali(): void
    {
        $terbit = $this->pengumuman($this->opRw, null, 'Publish');

        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.archive', $terbit->id))->assertRedirect();
        $this->assertSame('Archive', $terbit->fresh()->status);

        // Hanya pengumuman terbit yang dapat diarsipkan; hanya arsip yang dapat dibuka.
        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.archive', $terbit->id))->assertForbidden();
        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.unarchive', $terbit->id))->assertRedirect();
        $this->assertSame('Publish', $terbit->fresh()->status);
        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.unarchive', $terbit->id))->assertForbidden();
    }

    public function test_operator_lain_tidak_dapat_mengarsipkan_pengumuman_orang_lain(): void
    {
        $milikRt1 = $this->pengumuman($this->opRt1, $this->rt1->id, 'Publish');

        $this->actingAs($this->opRt2)->put(route('opkonten.pengumuman.archive', $milikRt1->id))->assertNotFound();
        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.archive', $milikRt1->id))->assertNotFound();
        $this->assertSame('Publish', $milikRt1->fresh()->status);
    }

    public function test_halaman_operator_pengumuman_menampilkan_aksi_arsip(): void
    {
        $this->pengumuman($this->opRw, null, 'Archive');

        $this->actingAs($this->opRw)->get(route('opkonten.pengumuman.index', ['status' => 'Archive']))->assertOk()
            ->assertSee('Diarsipkan')
            ->assertSee('Buka Arsip');
    }
}