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
}
