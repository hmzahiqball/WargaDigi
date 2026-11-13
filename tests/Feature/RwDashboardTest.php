<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard Admin RW harus menampilkan data nyata dari database,
 * tanpa angka/aktivitas/dokumen hardcoded.
 */
class RwDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function adminRw(): User
    {
        return User::create([
            'nik' => '3217010101010002',
            'username' => 'Admin RW',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
    }

    private function buatPenduduk(MasterRt $rt, string $nik, string $nama): void
    {
        $keluarga = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081200000000',
        ]);
        Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);
    }

    public function test_dashboard_menampilkan_jumlah_penduduk_nyata_tanpa_angka_hardcode(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $response = $this->actingAs($this->adminRw())->get(route('rw.dashboard'));

        $response->assertOk();
        $response->assertSee('2'); // jumlah penduduk nyata
        $response->assertDontSee('12,450'); // tidak ada fallback fiktif
    }

    public function test_dashboard_kosong_tidak_menampilkan_angka_fiktif(): void
    {
        $response = $this->actingAs($this->adminRw())->get(route('rw.dashboard'));

        $response->assertOk();
        $response->assertDontSee('12,450');
        $response->assertDontSee('+142 bulan ini');
    }

    public function test_dashboard_dokumen_terbaru_dari_konten_nyata(): void
    {
        $operator = User::create([
            'nik' => '3217010101011050',
            'username' => 'Operator Konten',
            'role' => 'Op Konten RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        Berita::create([
            'judul_berita' => 'Kerja Bakti Mingguan',
            'slug' => 'kerja-bakti-mingguan',
            'kategori' => 'Kegiatan',
            'isi_berita' => '<p>Kegiatan kerja bakti rutin warga.</p>',
            'status' => 'Publish',
            'operator_id' => $operator->id,
        ]);

        $this->actingAs($this->adminRw())
            ->get(route('rw.dashboard'))
            ->assertOk()
            ->assertSee('Kerja Bakti Mingguan')
            ->assertDontSee('SOP_UMKM_2023.pdf'); // dokumen dummy lama hilang
    }

    private function operator(): User
    {
        return User::create([
            'nik' => '3217010101011050',
            'username' => 'Operator Konten',
            'role' => 'Op Konten RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
    }

    public function test_konten_terbaru_lintas_jenis_termasuk_rt_dan_tanpa_draft(): void
    {
        $op = $this->operator();
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => '03']);

        Berita::create(['judul_berita' => 'Berita RT Tiga', 'slug' => 'berita-rt-tiga', 'kategori' => 'Kegiatan',
            'isi_berita' => 'Isi', 'status' => 'Publish', 'operator_id' => $op->id, 'rt_id' => $rt->id]);
        Berita::create(['judul_berita' => 'Berita Draft Rahasia', 'slug' => 'berita-draft', 'kategori' => 'Kegiatan',
            'isi_berita' => 'Isi', 'status' => 'Draft', 'operator_id' => $op->id]);
        Berita::create(['judul_berita' => 'Berita Menunggu', 'slug' => 'berita-menunggu', 'kategori' => 'Kegiatan',
            'isi_berita' => 'Isi', 'status' => 'Review', 'operator_id' => $op->id]);

        $this->actingAs($this->adminRw())
            ->get(route('rw.dashboard'))
            ->assertOk()
            ->assertSee('Konten Terbaru')
            ->assertSee('Berita RT Tiga')
            ->assertSee('RT 03')
            ->assertSee('Berita Menunggu')            // Admin RW melihat antrean tinjauannya
            ->assertSee('MENUNGGU TINJAUAN')
            ->assertDontSee('Berita Draft Rahasia');
    }

    public function test_ketua_rw_hanya_melihat_konten_terbit(): void
    {
        $op = $this->operator();
        Berita::create(['judul_berita' => 'Berita Menunggu', 'slug' => 'berita-menunggu', 'kategori' => 'Kegiatan',
            'isi_berita' => 'Isi', 'status' => 'Review', 'operator_id' => $op->id]);

        $ketuaRw = User::create([
            'nik' => '3217010101010003', 'username' => 'Ketua RW', 'role' => 'Ketua RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        $this->actingAs($ketuaRw)
            ->get(route('rw.dashboard'))
            ->assertOk()
            ->assertDontSee('Berita Menunggu')
            ->assertDontSee(route('rw.persetujuan-konten.index'), false);
    }

    public function test_tanpa_link_mati_dan_teks_bahasa_indonesia(): void
    {
        $this->actingAs($this->adminRw())
            ->get(route('rw.dashboard'))
            ->assertOk()
            // Link mati lama di konten dashboard ("Lihat Semua" dan kartu "Customize"); href="#" pada sidebar/topbar adalah pemicu JS.
            ->assertDontSee('href="#" class="text-success', false)
            ->assertDontSee('href="#" class="text-decoration-none text-dark"', false)
            ->assertDontSee('In progress')
            ->assertDontSee('Customize')
            ->assertSee('Semua sudah ditinjau')
            ->assertSee('Tidak ada aktivitas yang menunggu tindakan.')
            ->assertSee('Tidak ada penambahan bulan ini')
            ->assertDontSee('bi-arrow-up-short', false)
            ->assertSee(route('rw.penduduk.index'), false)
            ->assertSee(route('rw.persetujuan-dokumen'), false);
    }

    public function test_panah_naik_muncul_bila_ada_penduduk_baru(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');

        $this->actingAs($this->adminRw())
            ->get(route('rw.dashboard'))
            ->assertOk()
            ->assertSee('+1 bulan ini')
            ->assertSee('bi-arrow-up-short', false);
    }

    public function test_aktivitas_diurutkan_berdasarkan_waktu_terbaru(): void
    {
        $rt = MasterRt::create(['kode_rt' => '02', 'nama_rt' => '02']);
        $this->buatPenduduk($rt, '3217010101011001', 'Pemohon Surat');
        $penduduk = Penduduk::where('nik', '3217010101011001')->first();
        $pemilik = User::create([
            'nik' => '3217010101011001', 'username' => 'pemilik', 'role' => 'Warga',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);

        // UMKM lama (3 hari lalu) dan surat baru (1 jam lalu): surat harus tampil lebih dulu.
        $kategori = \App\Models\KategoriUmkm::forceCreate(['nama_kategori' => 'Kuliner']);
        $umkm = \App\Models\UmkmUsaha::forceCreate([
            'nik' => $pemilik->nik, 'kategori_umkm_id' => $kategori->id, 'nama_usaha' => 'Usaha Lama',
            'alamat_usaha' => 'Jl. Uji', 'no_wa' => '081200000000', 'status_verifikasi' => 'Pending',
        ]);
        $umkm->forceFill(['created_at' => now()->subDays(3)])->saveQuietly();

        $surat = \App\Models\PengajuanSurat::forceCreate([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Domisili', 'status' => 'Disetujui RT',
        ]);
        $surat->forceFill(['updated_at' => now()->subHour()])->saveQuietly();

        $html = $this->actingAs($this->adminRw())->get(route('rw.dashboard'))->assertOk()->getContent();

        $posSurat = strpos($html, 'Surat Surat Domisili untuk Pemohon Surat');
        $posUmkm = strpos($html, 'Usaha Lama'); // tanda kutip di-escape Blade
        $this->assertNotFalse($posSurat);
        $this->assertNotFalse($posUmkm);
        $this->assertLessThan($posUmkm, $posSurat);
        $this->assertStringContainsString('disetujui RT 02', $html);
    }

    public function test_ringkasan_kas_rw_tampil_dari_transaksi_nyata(): void
    {
        $this->actingAs($this->adminRw())
            ->get(route('rw.dashboard'))
            ->assertOk()
            ->assertSee('data-testid="ringkasan-kas-rw"', false)
            ->assertSee('Rp 0')
            ->assertSee('Belum ada laporan keuangan RW.')
            ->assertSee(route('laporan-keuangan.publik'), false);
    }
}
