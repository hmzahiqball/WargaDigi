<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\KategoriKonten;
use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;
use App\Models\UmkmProduk;
use App\Models\UmkmUsaha;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Filter kategori di semua halaman diambil dari data master (kategori konten, kategori UMKM,
 * kategori produk per usaha), bukan daftar hardcode atau teks dari data konten.
 */
class FilterKategoriMasterTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $nik): User
    {
        return User::create(['nik' => $nik, 'username' => 'u' . $nik, 'role' => $role,
            'status_akun' => 'Active', 'password' => 'password123']);
    }

    private function opsiSelect(string $html, string $name = 'kategori'): array
    {
        preg_match('/<select[^>]*name="' . $name . '"[^>]*>(.*?)<\/select>/s', $html, $m);
        $this->assertNotEmpty($m, "Select {$name} tidak ditemukan.");
        preg_match_all('/<option value="([^"]*)"/', $m[1], $o);

        return array_values(array_filter($o[1], fn ($v) => $v !== ''));
    }

    public function test_galeri_dokumentasi_memakai_master_kategori_agenda(): void
    {
        KategoriKonten::query()->delete(); // mulai dari master kosong (migrasi mengisi kategori bawaan)
        KategoriKonten::create(['nama' => 'Ronda Malam', 'warna' => 'hijau', 'urutan' => 1,
            'berlaku_berita' => false, 'berlaku_agenda' => true, 'berlaku_pengumuman' => false]);
        KategoriKonten::create(['nama' => 'Khusus Berita', 'warna' => 'biru', 'urutan' => 2,
            'berlaku_berita' => true, 'berlaku_agenda' => false, 'berlaku_pengumuman' => false]);

        $html = $this->actingAs($this->user('Op Konten RW', '3217010101012601'))
            ->get(route('opkonten.galeri.index'))->assertOk()->getContent();

        $opsi = $this->opsiSelect($html);
        $this->assertContains('Ronda Malam', $opsi);
        $this->assertNotContains('Khusus Berita', $opsi);   // tidak berlaku untuk agenda
        $this->assertSame(['Ronda Malam'], $opsi);          // hanya isi master, tanpa daftar hardcode
    }

    public function test_halaman_publik_berita_memakai_urutan_master_dan_kategori_aktif(): void
    {
        KategoriKonten::query()->delete();
        $op = $this->user('Op Konten RW', '3217010101012601');
        $kedua = KategoriKonten::create(['nama' => 'Zakat', 'warna' => 'hijau', 'urutan' => 2]);
        KategoriKonten::create(['nama' => 'Arisan', 'warna' => 'biru', 'urutan' => 1]);
        $nonaktifTerpakai = KategoriKonten::create(['nama' => 'Lama Terpakai', 'warna' => 'abu', 'urutan' => 3, 'aktif' => false]);
        KategoriKonten::create(['nama' => 'Lama Kosong', 'warna' => 'abu', 'urutan' => 4, 'aktif' => false]);

        foreach ([$kedua, $nonaktifTerpakai] as $i => $kat) {
            Berita::create(['judul_berita' => 'Berita ' . $i, 'slug' => 'berita-' . $i, 'kategori' => $kat->nama,
                'kategori_id' => $kat->id, 'isi_berita' => 'Isi', 'status' => 'Publish', 'operator_id' => $op->id,
                'tanggal_publish' => now()]);
        }

        $kategori = $this->get(route('berita'))->assertOk()->viewData('categories');

        // Urutan master; kategori aktif tampil walau belum dipakai; nonaktif hanya bila masih dipakai.
        $this->assertSame(['Semua', 'Arisan', 'Zakat', 'Lama Terpakai'], $kategori);
    }

    private function usaha(string $status, KategoriUmkm $kategori, string $nik): UmkmUsaha
    {
        $pemilik = $this->user('Warga', $nik);

        return UmkmUsaha::forceCreate(['nik' => $pemilik->nik, 'kategori_umkm_id' => $kategori->id,
            'nama_usaha' => 'Usaha ' . $status, 'alamat_usaha' => 'Jl. Uji', 'no_wa' => '081200000000',
            'status_verifikasi' => $status, 'is_active' => $status === 'Approved']);
    }

    private function produk(UmkmUsaha $usaha, string $nama, string $kategoriProduk): UmkmProduk
    {
        $kp = KategoriProduk::forceCreate(['umkm_usaha_id' => $usaha->id, 'nama_kategori' => $kategoriProduk]);

        return UmkmProduk::forceCreate(['umkm_usaha_id' => $usaha->id, 'kategori_produk_id' => $kp->id,
            'nama_produk' => $nama, 'harga' => 1000, 'status_produk' => 'Aktif']);
    }

    public function test_galeri_umkm_memakai_master_kategori_umkm_tanpa_daftar_hardcode(): void
    {
        $kuliner = KategoriUmkm::forceCreate(['nama_kategori' => 'Kuliner Uji']);
        KategoriUmkm::forceCreate(['nama_kategori' => 'Jasa Uji']);

        $opsi = $this->opsiSelect($this->get(route('umkm.galeri'))->assertOk()->getContent());

        $this->assertSame(['Jasa Uji', 'Kuliner Uji'], $opsi);
        $this->assertNotContains('Fashion', $opsi);
        $this->assertNotContains('Koperasi', $opsi);
    }

    public function test_filter_kategori_produk_tidak_membocorkan_produk_usaha_yang_belum_disetujui(): void
    {
        $kuliner = KategoriUmkm::forceCreate(['nama_kategori' => 'Kuliner Uji']);
        $disetujui = $this->usaha('Approved', $kuliner, '3217010101013001');
        $pending = $this->usaha('Pending', $kuliner, '3217010101013002');
        $this->produk($disetujui, 'Produk Tampil', 'Makanan');
        $this->produk($pending, 'Produk Rahasia Pending', 'Makanan');

        $this->get(route('umkm.galeri', ['kategori' => 'Kuliner Uji']))
            ->assertOk()
            ->assertSee('Produk Tampil')
            ->assertDontSee('Produk Rahasia Pending');
    }

    public function test_halaman_satu_usaha_memakai_master_kategori_produk_usaha_itu_saja(): void
    {
        $kuliner = KategoriUmkm::forceCreate(['nama_kategori' => 'Kuliner Uji']);
        $a = $this->usaha('Approved', $kuliner, '3217010101013001');
        $b = $this->usaha('Approved', $kuliner, '3217010101013002');
        $this->produk($a, 'Produk A', 'Minuman A');
        $this->produk($b, 'Produk B', 'Kategori Milik B');

        $opsi = $this->opsiSelect($this->get(route('umkm.usaha.show', $a->id))->assertOk()->getContent());

        $this->assertContains('Minuman A', $opsi);
        $this->assertNotContains('Kategori Milik B', $opsi);
    }
}
