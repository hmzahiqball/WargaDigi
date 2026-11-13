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
use App\Models\User;
use App\Support\SlugKonten;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Slug unik, foto utama wajib, dan master kategori konten (dikelola Op Konten RW, dipakai semua RT). */
class KontenSlugKategoriTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt1;
    private MasterRt $rt2;
    private User $opRw;
    private User $opRt1;
    private User $opRt2;
    private KategoriKonten $semua;
    private array $berkasAgenda = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->rt1 = MasterRt::create(['kode_rt' => '991', 'nama_rt' => 'RT 991']);
        $this->rt2 = MasterRt::create(['kode_rt' => '992', 'nama_rt' => 'RT 992']);
        $this->opRw = $this->user('Op Konten RW', '3217010101012601');
        $this->opRt1 = $this->user('Op Konten RT', '3217010101012602', $this->rt1->id);
        $this->opRt2 = $this->user('Op Konten RT', '3217010101012603', $this->rt2->id);
        $this->semua = KategoriKonten::create(['nama' => 'Uji Semua Jenis', 'warna' => 'hijau']);
    }

    protected function tearDown(): void
    {
        // Agenda menyimpan banner di public/assets/images/agenda; bersihkan berkas uji.
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

    private function foto(string $nama = 'foto.jpg'): UploadedFile
    {
        return UploadedFile::fake()->image($nama, 640, 480)->size(100);
    }

    private function dataBerita(array $ubah = []): array
    {
        return array_merge([
            'judul_berita' => 'Kerja Bakti Akhir Pekan', 'kategori_id' => $this->semua->id,
            'isi_berita' => '<p>Isi berita</p>', 'foto_utama' => $this->foto(), 'action' => 'draft',
        ], $ubah);
    }

    private function dataAgenda(array $ubah = []): array
    {
        return array_merge([
            'judul_agenda' => 'Rapat Warga', 'kategori_id' => $this->semua->id, 'tanggal' => now()->addDays(3)->toDateString(),
            'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Balai RW',
            'detail_pengumuman' => '<p>Detail</p>', 'banner_flyer' => $this->foto('banner.jpg'), 'action' => 'draft',
        ], $ubah);
    }

    private function dataPengumuman(array $ubah = []): array
    {
        return array_merge([
            'judul_pengumuman' => 'Jadwal Ronda', 'kategori_id' => $this->semua->id, 'isi_pengumuman' => '<p>Isi</p>',
        ], $ubah);
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

    // ───────────── Slug ─────────────

    public function test_slug_dibuat_dari_judul_dan_tidak_pernah_kembar_antar_rt(): void
    {
        $this->actingAs($this->opRt1)->post(route('opkonten.berita.store'), $this->dataBerita())->assertRedirect();
        $this->actingAs($this->opRt2)->post(route('opkonten.berita.store'), $this->dataBerita(['foto_utama' => $this->foto('b.jpg')]))->assertRedirect();
        $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita(['foto_utama' => $this->foto('c.jpg')]))->assertRedirect();

        $slug = Berita::orderBy('created_at')->orderBy('id')->pluck('slug')->sort()->values()->all();
        $this->assertSame(['kerja-bakti-akhir-pekan', 'kerja-bakti-akhir-pekan-2', 'kerja-bakti-akhir-pekan-3'], $slug);
    }

    public function test_slug_agenda_dan_pengumuman_dibuat_otomatis_dan_unik(): void
    {
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman())->assertRedirect();
        $this->actingAs($this->opRt1)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman())->assertRedirect();
        $this->actingAs($this->opRw)->post(route('opkonten.agenda.store'), $this->dataAgenda())->assertRedirect();
        $this->actingAs($this->opRt2)->post(route('opkonten.agenda.store'), $this->dataAgenda(['banner_flyer' => $this->foto('b2.jpg')]))->assertRedirect();

        $this->assertEqualsCanonicalizing(['jadwal-ronda', 'jadwal-ronda-2'], Pengumuman::pluck('slug')->all());
        $this->assertEqualsCanonicalizing(['rapat-warga', 'rapat-warga-2'], Agenda::pluck('slug')->all());
    }

    public function test_slug_menghindari_kata_yang_bentrok_dengan_rute_dan_judul_tanpa_huruf(): void
    {
        $this->assertSame('rsvp-agenda', SlugKonten::buat('agenda', 'RSVP'));
        $this->assertSame('berita', SlugKonten::buat('berita', '!!! ???'));
        $this->assertLessThanOrEqual(100, strlen(SlugKonten::buat('berita', str_repeat('Judul sangat panjang ', 30))));
    }

    public function test_slug_draf_mengikuti_judul_baru_dan_tetap_unik(): void
    {
        $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita(['judul_berita' => 'Berita Pertama']))->assertRedirect();
        $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita(['judul_berita' => 'Berita Kedua', 'foto_utama' => $this->foto('k.jpg')]))->assertRedirect();
        $kedua = Berita::where('judul_berita', 'Berita Kedua')->first();

        // Diganti menjadi judul yang sudah dipakai berita lain: slug mendapat akhiran.
        $this->actingAs($this->opRw)->put(route('opkonten.berita.update', $kedua->id), [
            'judul_berita' => 'Berita Pertama', 'kategori_id' => $this->semua->id, 'isi_berita' => '<p>x</p>', 'action' => 'draft',
        ] + ['foto_utama' => $this->foto('l.jpg')])->assertRedirect();

        $this->assertSame('berita-pertama-2', $kedua->fresh()->slug);
    }

    // ───────────── Foto utama wajib ─────────────

    public function test_berita_tanpa_foto_ditolak_bahkan_untuk_draf(): void
    {
        foreach (['draft', 'review'] as $aksi) {
            $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita(['action' => $aksi, 'foto_utama' => null]))
                ->assertSessionHasErrors('foto_utama');
        }
        $this->assertSame(0, Berita::count());
    }

    public function test_edit_berita_tidak_perlu_unggah_ulang_bila_sudah_ada_foto(): void
    {
        $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita())->assertRedirect();
        $berita = Berita::first();
        $this->assertNotEmpty($berita->featured_image);

        $this->actingAs($this->opRw)->put(route('opkonten.berita.update', $berita->id), [
            'judul_berita' => 'Berita Diedit', 'kategori_id' => $this->semua->id, 'isi_berita' => '<p>x</p>', 'action' => 'draft',
        ])->assertSessionHasNoErrors();
        $this->assertSame('Berita Diedit', $berita->fresh()->judul_berita);
    }

    public function test_berita_lama_tanpa_foto_wajib_diberi_foto_saat_diedit(): void
    {
        $lama = Berita::create([
            'judul_berita' => 'Berita Lama', 'kategori' => 'Uji Semua Jenis', 'kategori_id' => $this->semua->id,
            'isi_berita' => 'Isi', 'status' => 'Draft', 'operator_id' => $this->opRw->id, 'featured_image' => null,
        ]);

        $this->actingAs($this->opRw)->put(route('opkonten.berita.update', $lama->id), [
            'judul_berita' => 'Berita Lama', 'kategori_id' => $this->semua->id, 'isi_berita' => '<p>x</p>', 'action' => 'draft',
        ])->assertSessionHasErrors('foto_utama');
    }

    public function test_agenda_tanpa_foto_ditolak_dan_foto_tidak_boleh_dihapus_tanpa_pengganti(): void
    {
        $this->actingAs($this->opRw)->post(route('opkonten.agenda.store'), $this->dataAgenda(['banner_flyer' => null]))
            ->assertSessionHasErrors('banner_flyer');
        $this->assertSame(0, Agenda::count());

        $this->actingAs($this->opRw)->post(route('opkonten.agenda.store'), $this->dataAgenda())->assertRedirect();
        $agenda = Agenda::first();

        $ubah = [
            'judul_agenda' => 'Rapat Warga', 'kategori_id' => $this->semua->id, 'tanggal' => now()->addDays(3)->toDateString(),
            'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Balai RW', 'detail_pengumuman' => '<p>Detail</p>', 'action' => 'draft',
        ];
        $this->actingAs($this->opRw)->put(route('opkonten.agenda.update', $agenda->id), $ubah + ['remove_banner' => '1'])
            ->assertSessionHasErrors('banner_flyer');
        $this->actingAs($this->opRw)->put(route('opkonten.agenda.update', $agenda->id), $ubah)->assertSessionHasNoErrors();
    }

    // ───────────── Kategori master ─────────────

    public function test_kategori_harus_aktif_dan_berlaku_untuk_jenis_konten(): void
    {
        $hanyaAgenda = KategoriKonten::create(['nama' => 'Uji Hanya Agenda', 'warna' => 'biru', 'berlaku_berita' => false, 'berlaku_pengumuman' => false]);
        $nonaktif = KategoriKonten::create(['nama' => 'Uji Nonaktif', 'warna' => 'ungu', 'aktif' => false]);

        $this->actingAs($this->opRw)->post(route('opkonten.berita.store'), $this->dataBerita(['kategori_id' => $hanyaAgenda->id]))->assertSessionHasErrors('kategori_id');
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman(['kategori_id' => $hanyaAgenda->id]))->assertSessionHasErrors('kategori_id');
        $this->actingAs($this->opRw)->post(route('opkonten.agenda.store'), $this->dataAgenda(['kategori_id' => $nonaktif->id]))->assertSessionHasErrors('kategori_id');
        $this->actingAs($this->opRw)->post(route('opkonten.agenda.store'), $this->dataAgenda(['kategori_id' => $hanyaAgenda->id]))->assertSessionHasNoErrors();

        $this->assertSame('Uji Hanya Agenda', Agenda::first()->kategori);
    }

    public function test_kategori_pengumuman_wajib(): void
    {
        $this->actingAs($this->opRt1)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman(['kategori_id' => null]))
            ->assertSessionHasErrors('kategori_id');
        $this->assertSame(0, Pengumuman::count());
    }

    public function test_operator_rt_hanya_memilih_dan_semua_rt_melihat_kategori_yang_sama(): void
    {
        $this->actingAs($this->opRt1)->get(route('opkonten.berita.index'))->assertOk()->assertSee('Uji Semua Jenis');
        $this->actingAs($this->opRt2)->get(route('opkonten.agenda.index'))->assertOk()->assertSee('Uji Semua Jenis');

        // Master hanya dikelola Op Konten RW: Op Konten RT dialihkan dari seluruh aksinya.
        $this->actingAs($this->opRt1)->get(route('opkonten.kategori.index'))->assertRedirect();
        $this->actingAs($this->opRt1)->post(route('opkonten.kategori.store'), ['nama' => 'Liar', 'warna' => 'hijau', 'berlaku' => ['berita']])->assertRedirect();
        $this->assertDatabaseMissing('kategori_konten', ['nama' => 'Liar']);
    }

    public function test_op_konten_rw_mengelola_master_kategori(): void
    {
        $this->actingAs($this->opRw)->get(route('opkonten.kategori.index'))->assertOk()->assertSee('Uji Semua Jenis');

        $this->actingAs($this->opRw)->post(route('opkonten.kategori.store'), [
            'nama' => 'Uji Baru', 'warna' => 'oranye', 'berlaku' => ['berita', 'pengumuman'], 'aktif' => '1',
        ])->assertRedirect();
        $baru = KategoriKonten::where('nama', 'Uji Baru')->first();
        $this->assertTrue($baru->berlaku_berita);
        $this->assertFalse($baru->berlaku_agenda);
        $this->assertTrue($baru->berlaku_pengumuman);

        // Nama unik dan minimal satu jenis.
        $this->actingAs($this->opRw)->post(route('opkonten.kategori.store'), ['nama' => 'uji baru', 'warna' => 'hijau', 'berlaku' => ['agenda']])
            ->assertSessionHasErrors('nama');
        $this->actingAs($this->opRw)->post(route('opkonten.kategori.store'), ['nama' => 'Uji Kosong', 'warna' => 'hijau'])
            ->assertSessionHasErrors('berlaku');
    }

    public function test_ganti_nama_kategori_ikut_mengubah_konten_dan_kategori_terpakai_tidak_bisa_dihapus(): void
    {
        $this->actingAs($this->opRt1)->post(route('opkonten.berita.store'), $this->dataBerita())->assertRedirect();
        $this->actingAs($this->opRt2)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman())->assertRedirect();

        $this->actingAs($this->opRw)->put(route('opkonten.kategori.update', $this->semua->id), [
            'nama' => 'Uji Nama Baru', 'warna' => 'hijau', 'berlaku' => ['berita', 'agenda', 'pengumuman'], 'aktif' => '1',
        ])->assertRedirect();
        $this->assertSame('Uji Nama Baru', Berita::first()->kategori);
        $this->assertSame('Uji Nama Baru', Pengumuman::first()->kategori);

        $this->actingAs($this->opRw)->delete(route('opkonten.kategori.destroy', $this->semua->id))->assertRedirect();
        $this->assertDatabaseHas('kategori_konten', ['id' => $this->semua->id]);

        $kosong = KategoriKonten::create(['nama' => 'Uji Kosong Dipakai', 'warna' => 'abu']);
        $this->actingAs($this->opRw)->delete(route('opkonten.kategori.destroy', $kosong->id))->assertRedirect();
        $this->assertDatabaseMissing('kategori_konten', ['id' => $kosong->id]);
    }

    public function test_draf_dengan_kategori_yang_kemudian_dinonaktifkan_tetap_bisa_disimpan(): void
    {
        $this->actingAs($this->opRw)->post(route('opkonten.pengumuman.store'), $this->dataPengumuman())->assertRedirect();
        $pengumuman = Pengumuman::first();
        $this->semua->update(['aktif' => false]);

        $this->actingAs($this->opRw)->put(route('opkonten.pengumuman.update', $pengumuman->id), [
            'judul_pengumuman' => 'Jadwal Ronda Baru', 'kategori_id' => $this->semua->id, 'isi_pengumuman' => '<p>x</p>',
        ])->assertSessionHasNoErrors();
        $this->assertSame('jadwal-ronda-baru', $pengumuman->fresh()->slug);
    }

    // ───────────── URL warga dan publik ─────────────

    public function test_berita_publik_memakai_slug_dan_id_lama_dialihkan(): void
    {
        $berita = Berita::create([
            'judul_berita' => 'Warga Gotong Royong', 'kategori' => 'Uji Semua Jenis', 'kategori_id' => $this->semua->id,
            'isi_berita' => 'Isi', 'status' => 'Publish', 'operator_id' => $this->opRw->id, 'featured_image' => 'x.jpg', 'tanggal_publish' => now(),
        ]);
        $this->assertSame('warga-gotong-royong', $berita->slug);

        $this->get('/berita/warga-gotong-royong')->assertOk()->assertSee('Warga Gotong Royong');
        $this->get('/berita/' . $berita->id)->assertRedirect('/berita/warga-gotong-royong')->assertStatus(301);
        $this->get('/berita')->assertOk()->assertSee('/berita/warga-gotong-royong', false)->assertDontSee('/berita/' . $berita->id, false);
    }

    public function test_agenda_warga_memakai_slug_dan_id_lama_dialihkan_serta_rt_lain_tidak_bisa_membuka(): void
    {
        $agendaRt1 = Agenda::create([
            'judul_agenda' => 'Senam Pagi RT', 'kategori' => 'Uji Semua Jenis', 'kategori_id' => $this->semua->id,
            'tanggal_mulai' => now()->addDay(), 'tanggal_selesai' => now()->addDay()->addHour(), 'lokasi' => 'Lapangan',
            'detail_pengumuman' => 'Detail', 'status' => 'Publish', 'operator_id' => $this->opRt1->id, 'rt_id' => $this->rt1->id,
            'banner_flyer' => 'assets/x.jpg', 'is_rsvp_enabled' => false,
        ]);
        $warga1 = $this->warga($this->rt1, '3217010101012611');
        $warga2 = $this->warga($this->rt2, '3217010101012612');

        $this->actingAs($warga1)->get('/warga/agenda/senam-pagi-rt')->assertOk();
        $this->actingAs($warga1)->get('/warga/agenda/' . $agendaRt1->id)->assertRedirect('/warga/agenda/senam-pagi-rt');
        $this->actingAs($warga2)->get('/warga/agenda/senam-pagi-rt')->assertNotFound();
        $this->actingAs($warga2)->get('/warga/agenda/' . $agendaRt1->id)->assertNotFound();
    }

    public function test_kalender_agenda_warga_memakai_slug_dan_warna_dari_master(): void
    {
        $oranye = KategoriKonten::create(['nama' => 'Uji Oranye', 'warna' => 'oranye']);
        Agenda::create([
            'judul_agenda' => 'Bazar Warga', 'kategori' => $oranye->nama, 'kategori_id' => $oranye->id,
            'tanggal_mulai' => now()->addDay(), 'tanggal_selesai' => now()->addDay()->addHour(), 'lokasi' => 'Balai',
            'detail_pengumuman' => 'Detail', 'status' => 'Publish', 'operator_id' => $this->opRw->id,
            'banner_flyer' => 'assets/x.jpg', 'is_rsvp_enabled' => false,
        ]);
        $warga = $this->warga($this->rt1, '3217010101012613');

        $this->actingAs($warga)->get(route('warga.agenda.index'))->assertOk()
            ->assertSee('bazar-warga', false)
            ->assertSee('#ffedd5', false); // warna latar oranye dari master
    }

    public function test_halaman_berita_warga_membawa_slug_dan_kategori_pengumuman(): void
    {
        Pengumuman::create([
            'judul_pengumuman' => 'Pembagian Sembako', 'kategori' => 'Uji Semua Jenis', 'kategori_id' => $this->semua->id,
            'isi_pengumuman' => 'Isi', 'status' => 'Publish', 'operator_id' => $this->opRw->id, 'is_priority' => false,
        ]);
        $warga = $this->warga($this->rt1, '3217010101012614');

        $this->actingAs($warga)->get(route('warga.berita.index'))->assertOk()
            ->assertSee('pembagian-sembako', false)
            ->assertSee('Uji Semua Jenis');
    }
}
