<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Halaman Berita publik: berita utama, kartu, gambar, waktu baca, filter, dan menu Home. */
class BeritaPublikTest extends TestCase
{
    use RefreshDatabase;

    private function berita(string $judul, ?string $gambar, int $hariLalu, string $kategori = 'Sosial'): Berita
    {
        $op = User::firstOrCreate(['username' => 'opkonten'], ['nik' => null, 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);

        return Berita::create([
            'judul_berita' => $judul, 'slug' => \Illuminate\Support\Str::slug($judul), 'kategori' => $kategori,
            'isi_berita' => '<p>' . str_repeat('kata ', 450) . '</p>', 'featured_image' => $gambar,
            'status' => 'Publish', 'operator_id' => $op->id, 'tanggal_publish' => now()->subDays($hariLalu),
        ]);
    }

    public function test_url_gambar_berita_berbagai_format(): void
    {
        $this->assertNull(Berita::urlGambar(null));
        $this->assertNull(Berita::urlGambar(''));
        $this->assertSame('https://contoh.id/a.jpg', Berita::urlGambar('https://contoh.id/a.jpg'));
        // Regresi: path "/storage/..." sebelumnya menjadi "storage//storage/..." sehingga gambar rusak.
        $this->assertSame(asset('storage/berita/a.webp'), Berita::urlGambar('/storage/berita/a.webp'));
        $this->assertSame(asset('storage/berita/a.webp'), Berita::urlGambar('berita/a.webp'));
    }

    public function test_berita_terbaru_tampil_sebagai_berita_utama_dan_sisanya_kartu(): void
    {
        $this->berita('Berita Lama Sekali', null, 5, 'Kesehatan');
        $this->berita('Berita Paling Baru', '/storage/berita/x.webp', 0);

        $html = $this->get(route('berita'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/class="berita-utama[^"]*".*?Berita Paling Baru/s', $html);
        $this->assertStringContainsString('Berita Lainnya', $html);
        $this->assertStringContainsString('Berita Lama Sekali', $html);
        $this->assertStringContainsString(asset('storage/berita/x.webp'), $html);
        $this->assertStringNotContainsString('storage//storage', $html);
        $this->assertStringContainsString('3 menit baca', $html);              // 450 kata ≈ 3 menit
        $this->assertStringContainsString(asset('images/berita-'), $html);      // cadangan untuk berita tanpa foto
        $this->assertStringContainsString('id="cariBerita"', $html);
        $this->assertStringContainsString('data-kategori="Kesehatan"', $html);
    }

    private function agenda(string $judul, $mulai, $selesai, string $status = 'Publish', ?string $rtId = null): \App\Models\Agenda
    {
        $op = User::firstOrCreate(['username' => 'opkonten'], ['nik' => null, 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);

        return \App\Models\Agenda::forceCreate([
            'judul_agenda' => $judul, 'slug' => \Illuminate\Support\Str::slug($judul), 'kategori' => 'Sosial',
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'lokasi' => 'Balai RW',
            'detail_pengumuman' => 'x', 'status' => $status, 'operator_id' => $op->id, 'rt_id' => $rtId,
        ]);
    }

    public function test_beranda_menampilkan_agenda_terbaru_dari_database_dan_tanda_kalender_nyata(): void
    {
        $this->agenda('Agenda Sedang Berlangsung', now()->subDay(), now()->addDay());
        $this->agenda('Agenda Pekan Depan', now()->addWeek(), now()->addWeek()->addHours(2));
        $this->agenda('Agenda Sudah Lewat', now()->subWeek(), now()->subWeek()->addHours(2));
        $this->agenda('Agenda Masih Draft', now()->addDays(2), now()->addDays(2)->addHour(), 'Draft');
        $rt = \App\Models\MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $this->agenda('Agenda Khusus RT', now()->addDays(3), now()->addDays(3)->addHour(), 'Publish', $rt->id);

        $res = $this->get('/')->assertOk();
        $judul = collect($res->viewData('agenda'))->pluck('title')->all();

        $this->assertSame(['Agenda Sedang Berlangsung', 'Agenda Pekan Depan'], $judul);
        $res->assertSee(route('agenda.show', 'agenda-pekan-depan'), false); // detail agenda publik

        // Regresi: kalender sebelumnya memakai tanggal hardcode (17 Jul, 25 Jul, 1 Agu).
        $tanda = $res->viewData('tandaKalender');
        $this->assertContains(['day' => (int) now()->addWeek()->format('j'), 'month' => (int) now()->addWeek()->format('n') - 1, 'year' => (int) now()->addWeek()->format('Y')], $tanda);
        $this->assertCount(3, $tanda); // berlangsung, pekan depan, sudah lewat (draft dan RT tidak)
        $res->assertDontSee('{ day: 17, month: 7 }', false);
    }

    public function test_beranda_menampilkan_berita_terbit_terbaru_dari_database(): void
    {
        $this->berita('Berita Kemarin', null, 1);
        $this->berita('Berita Hari Ini', null, 0);
        $draft = $this->berita('Berita Draft', null, 0);
        $draft->update(['status' => 'Draft']);

        $judul = collect($this->get('/')->assertOk()->viewData('berita'))->pluck('title')->all();
        $this->assertSame(['Berita Hari Ini', 'Berita Kemarin'], $judul);
    }

    public function test_menu_home_ada_di_navbar_publik(): void
    {
        $this->get(route('berita'))->assertOk()
            ->assertSee('href="' . route('home') . '"', false)
            ->assertSee('>Home<', false);
    }
}
