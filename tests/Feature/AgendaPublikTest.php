<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\MasterRt;
use App\Models\User;
use App\Support\HtmlAman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Halaman Agenda publik (/agenda): kalender kegiatan, agenda mendatang, arsip, dan detail. */
class AgendaPublikTest extends TestCase
{
    use RefreshDatabase;

    private function agenda(string $judul, $mulai, $selesai, string $status = 'Publish', ?string $rtId = null, string $isi = '<p>Detail kegiatan</p>'): Agenda
    {
        $op = User::firstOrCreate(['username' => 'opkonten'], ['nik' => null, 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);

        return Agenda::forceCreate([
            'judul_agenda' => $judul, 'slug' => \Illuminate\Support\Str::slug($judul), 'kategori' => 'Sosial',
            'tanggal_mulai' => $mulai, 'tanggal_selesai' => $selesai, 'lokasi' => 'Balai RW',
            'detail_pengumuman' => $isi, 'status' => $status, 'operator_id' => $op->id, 'rt_id' => $rtId,
        ]);
    }

    public function test_halaman_agenda_memisahkan_mendatang_dan_arsip(): void
    {
        $this->agenda('Rapat Pekan Depan', now()->addWeek(), now()->addWeek()->addHours(2));
        $this->agenda('Kerja Bakti Berlangsung', now()->subHour(), now()->addHours(3));
        $this->agenda('Pengajian Bulan Lalu', now()->subMonth(), now()->subMonth()->addHours(2));
        $this->agenda('Agenda Draft', now()->addDays(2), now()->addDays(2)->addHour(), 'Draft');
        $this->agenda('Agenda Khusus RT', now()->addDays(3), now()->addDays(3)->addHour(), 'Publish',
            MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01'])->id);

        $res = $this->get(route('agenda'))->assertOk()
            ->assertSee('Kalender Kegiatan')->assertSee('Agenda Mendatang')
            ->assertSee('Sedang berlangsung')
            ->assertSee('Kegiatan yang Telah Terlaksana')
            ->assertDontSee('Agenda Draft')->assertDontSee('Agenda Khusus RT');

        $this->assertSame(['Kerja Bakti Berlangsung', 'Rapat Pekan Depan'], $res->viewData('mendatang')->pluck('judul')->all());
        $this->assertSame(['Pengajian Bulan Lalu'], collect($res->viewData('selesai')->items())->pluck('judul')->all());
        $this->assertCount(3, $res->viewData('tandaKalender'));
    }

    public function test_detail_agenda_publik_dan_tautan_lama_berbasis_id(): void
    {
        $a = $this->agenda('Tabligh Akbar RW', now()->addDays(4), now()->addDays(4)->addHours(3));

        $this->get(route('agenda.show', $a->slug))->assertOk()
            ->assertSee('Tabligh Akbar RW')->assertSee('Balai RW')->assertSee('Detail kegiatan', false)
            ->assertSee(route('agenda'), false);
        $this->get(route('agenda.show', $a->id))->assertRedirect(route('agenda.show', $a->slug));

        $draft = $this->agenda('Rahasia Draft', now()->addDay(), now()->addDay(), 'Draft');
        $this->get(route('agenda.show', $draft->slug))->assertNotFound();
    }

    public function test_detail_agenda_membersihkan_html_berbahaya(): void
    {
        $a = $this->agenda('Agenda Berisi Script', now()->addDay(), now()->addDay()->addHour(), 'Publish', null,
            '<p onclick="alert(1)">Aman</p><script>alert(2)</script><a href="javascript:alert(3)">x</a><img src="x" onerror="alert(4)">');

        $html = $this->get(route('agenda.show', $a->slug))->assertOk()->getContent();
        $isi = \Illuminate\Support\Str::between($html, 'agenda-detail-isi">', '</div>');
        $this->assertStringContainsString('Aman', $isi);
        $this->assertStringNotContainsString('<script', $isi);
        $this->assertStringNotContainsString('onclick', $isi);
        $this->assertStringNotContainsString('onerror', $isi);
        $this->assertStringNotContainsString('javascript:', $isi);
    }

    public function test_pembersih_html_mempertahankan_format_dan_tautan_aman(): void
    {
        $hasil = HtmlAman::bersihkan('<h3>Judul</h3><p><strong>Tebal</strong> <a href="https://contoh.id">tautan</a></p><iframe src="https://x"></iframe>');

        $this->assertStringContainsString('<h3>Judul</h3>', $hasil);
        $this->assertStringContainsString('<strong>Tebal</strong>', $hasil);
        $this->assertStringContainsString('href="https://contoh.id"', $hasil);
        $this->assertStringContainsString('rel="noopener nofollow"', $hasil);
        $this->assertStringNotContainsString('iframe', $hasil);
    }

    public function test_menu_dan_tautan_agenda_mengarah_ke_halaman_agenda(): void
    {
        $this->agenda('Rapat Warga', now()->addDays(2), now()->addDays(2)->addHour());

        $this->get('/')->assertOk()
            ->assertSee('Kalender Kegiatan')->assertDontSee('Kalender Interaktif')
            ->assertSee('href="' . route('agenda') . '"', false)
            ->assertSee(route('agenda.show', 'rapat-warga'), false)
            ->assertDontSee('#agenda', false);
    }
}
