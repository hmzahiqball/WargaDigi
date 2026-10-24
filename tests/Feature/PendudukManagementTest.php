<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menu "Manajemen Penduduk" untuk Admin RW: halaman daftar penduduk dari database,
 * dengan pencarian dan filter RT.
 */
class PendudukManagementTest extends TestCase
{
    use RefreshDatabase;

    private function buatPenduduk(MasterRt $rt, string $nik, string $nama): Penduduk
    {
        $keluarga = Keluarga::create([
            'no_kk' => $nik, // gunakan NIK (unik) sebagai no_kk uji agar tidak bentrok
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081200000000',
        ]);

        return Penduduk::create([
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

    public function test_admin_rw_dapat_membuka_daftar_penduduk_dari_database(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index'))
            ->assertOk()
            ->assertSee('Ahmad Dahlan')
            ->assertSee('Siti Walidah');
    }

    public function test_pencarian_penduduk_berfungsi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['q' => 'Ahmad']))
            ->assertOk()
            ->assertSee('Ahmad Dahlan')
            ->assertDontSee('Siti Walidah');
    }

    public function test_filter_rt_berfungsi(): void
    {
        $rtA = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $rtB = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $this->buatPenduduk($rtA, '3217010101011001', 'Warga RT Satu');
        $this->buatPenduduk($rtB, '3217010101011002', 'Warga RT Dua');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['rt_id' => $rtA->id]))
            ->assertOk()
            ->assertSee('Warga RT Satu')
            ->assertDontSee('Warga RT Dua');
    }

    public function test_warga_biasa_tidak_dapat_mengakses_manajemen_penduduk(): void
    {
        $warga = User::create([
            'nik' => '3217010101011099',
            'username' => 'Warga Biasa',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        // Middleware role menolak akses (redirect keluar dari route RW).
        $response = $this->actingAs($warga)->get(route('rw.penduduk.index'));
        $this->assertFalse($response->isOk(), 'Warga seharusnya tidak bisa membuka manajemen penduduk.');
        $response->assertRedirect();
    }
}
