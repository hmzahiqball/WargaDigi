<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Bagian statis "Transparansi Keuangan RW 21" disembunyikan dari dashboard warga.
 */
class WargaDashboardTransparansiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_warga_tidak_menampilkan_blok_transparansi_statis(): void
    {
        Role::findOrCreate('Warga', 'web');
        $rt = MasterRt::create(['kode_rt' => '401', 'nama_rt' => 'RT 401']);
        $nik = '3217010101014001';
        $kel = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Uji',
            'rt_id' => $rt->id, 'no_wa' => '081200004001',
        ]);
        Penduduk::create([
            'keluarga_id' => $kel->id, 'nik' => $nik, 'nama_lengkap' => 'Warga Dashboard',
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);
        $user = User::create([
            'nik' => $nik, 'username' => 'wargadash', 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        $this->actingAs($user)
            ->get(route('warga.dashboard'))
            ->assertOk()
            ->assertDontSee('Transparansi Keuangan RW 21')
            ->assertDontSee('Aktif / Transparan')
            ->assertSee('Update Berita Terkini');
    }
}
