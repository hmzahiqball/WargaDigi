<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard Admin Aplikasi (superadmin) harus menampilkan data nyata dari database.
 */
class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'nik' => '3217010101010001',
            'username' => 'superadmin',
            'role' => 'Admin Aplikasi',
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

    public function test_dashboard_admin_menampilkan_statistik_nyata(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $response = $this->actingAs($this->admin())->get(route('admin.dashboard'));

        $response->assertOk();
        // 2 penduduk + 2 keluarga nyata; tidak ada angka fiktif lama.
        $response->assertSee('data-admin-count="2"', false);
        $response->assertDontSee('1446');
        $response->assertDontSee('416');
    }

    public function test_dashboard_admin_menampilkan_instance_dari_pengaturan(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('RW 21 Tanimulya');
    }

    public function test_warga_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $warga = User::create([
            'nik' => '3217010101011099',
            'username' => 'Warga',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $response = $this->actingAs($warga)->get(route('admin.dashboard'));
        $this->assertFalse($response->isOk());
    }
}
