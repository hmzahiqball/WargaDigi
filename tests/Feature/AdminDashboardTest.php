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

    public function test_assign_operator_keuangan_rt_menyimpan_rt_id(): void
    {
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '07', 'nama_rt' => 'RT 07']);

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'baru',
                'username' => 'opkeurt07',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Keuangan RT'],
                'rt_id' => $rt->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['username' => 'opkeurt07', 'rt_id' => $rt->id]);
    }

    public function test_assign_operator_rt_tanpa_rt_ditolak(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'baru',
                'username' => 'opkeurt08',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Keuangan RT'],
            ])
            ->assertSessionHasErrors('rt_id');

        $this->assertDatabaseMissing('users', ['username' => 'opkeurt08']);
    }

    public function test_assign_operator_rw_tidak_menyimpan_rt_id(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'baru',
                'username' => 'opkeurw',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Keuangan RW'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['username' => 'opkeurw', 'rt_id' => null]);
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

    public function test_log_aktivitas_default_tampil_7_hari_terakhir(): void
    {
        $admin = $this->admin();

        activity('pengguna')->causedBy($admin)->event('created')->log('Aktivitas baru hari ini.');
        $lama = activity('pengguna')->causedBy($admin)->event('created')->log('Aktivitas 30 hari lalu.');
        \Spatie\Activitylog\Models\Activity::where('description', 'Aktivitas 30 hari lalu.')
            ->update(['created_at' => now()->subDays(30)]);

        $this->actingAs($admin)
            ->get(route('admin.log-aktivitas'))
            ->assertOk()
            ->assertSee('Aktivitas baru hari ini.')
            ->assertDontSee('Aktivitas 30 hari lalu.');
    }

    public function test_filter_rentang_tanggal_menampilkan_log_lama(): void
    {
        $admin = $this->admin();
        activity('pengguna')->causedBy($admin)->event('created')->log('Aktivitas 30 hari lalu.');
        \Spatie\Activitylog\Models\Activity::where('description', 'Aktivitas 30 hari lalu.')
            ->update(['created_at' => now()->subDays(30)]);

        $this->actingAs($admin)
            ->get(route('admin.log-aktivitas', [
                'tgl_dari' => now()->subDays(40)->toDateString(),
                'tgl_sampai' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee('Aktivitas 30 hari lalu.');
    }

    public function test_hapus_log_rentang_dan_mencatat_meta_log(): void
    {
        $admin = $this->admin();
        activity('pengguna')->causedBy($admin)->event('created')->log('Akan dihapus.');
        \Spatie\Activitylog\Models\Activity::where('description', 'Akan dihapus.')
            ->update(['created_at' => now()->subDays(3)]);

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => now()->subDays(5)->toDateString(),
                'sampai' => now()->subDays(1)->toDateString(),
            ])
            ->assertRedirect(route('admin.log-aktivitas'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('activity_log', ['description' => 'Akan dihapus.']);
        // Meta-log penghapusan tercatat.
        $this->assertDatabaseHas('activity_log', ['log_name' => 'log-aktivitas', 'event' => 'deleted']);
    }

    public function test_hapus_log_validasi_tanggal_akhir_tidak_sebelum_mulai(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.log-aktivitas.hapus'), [
                'dari' => now()->toDateString(),
                'sampai' => now()->subDays(3)->toDateString(),
            ])
            ->assertSessionHasErrors('sampai');
    }
}
