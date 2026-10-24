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
 * Paket 2B — login multi-peran + pemilihan peran aktif, login via username/no_wa,
 * dan akun pengurus tanpa NIK.
 */
class RoleSwitchTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(array $roles): void
    {
        foreach ($roles as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    private function wargaKepalaKeluarga(string $wa = '081234567890', string $username = 'kepala01'): User
    {
        $this->ensureRoles(['Warga', 'Ketua RT']);
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $nik = '3217010101010003';
        $keluarga = Keluarga::create([
            'no_kk' => '3217010101000001',
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => $wa,
        ]);
        Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => $nik,
            'nama_lengkap' => 'Warga Kepala',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1985-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);
        $user = User::create([
            'nik' => $nik,
            'username' => $username,
            'role' => 'Warga',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        return $user;
    }

    public function test_login_akun_satu_peran_langsung_masuk_tanpa_pilih_peran(): void
    {
        $user = $this->wargaKepalaKeluarga();

        $response = $this->post(route('login'), ['login' => 'kepala01', 'password' => 'password123']);

        $response->assertRedirect();
        $this->assertFalse(str_contains($response->headers->get('Location', ''), 'pilih-peran'));
        $this->assertAuthenticatedAs($user->fresh());
    }

    public function test_login_via_username_berhasil(): void
    {
        $this->wargaKepalaKeluarga(username: 'wargaunik');

        $this->post(route('login'), ['login' => 'wargaunik', 'password' => 'password123'])
            ->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_login_via_nomor_whatsapp_tetap_berhasil(): void
    {
        $this->wargaKepalaKeluarga(wa: '081234567890');

        $this->post(route('login'), ['login' => '081234567890', 'password' => 'password123'])
            ->assertRedirect();
        $this->assertAuthenticated();
    }

    public function test_akun_multi_peran_diarahkan_ke_pilih_peran_dan_belum_set_aktif(): void
    {
        $user = $this->wargaKepalaKeluarga();
        $user->syncRoles(['Warga', 'Ketua RT']);

        $this->post(route('login'), ['login' => 'kepala01', 'password' => 'password123'])
            ->assertRedirect(route('pilih-peran'));

        $this->assertAuthenticated();
        $this->assertNull(session(User::ACTIVE_ROLE_SESSION_KEY));
    }

    public function test_memilih_peran_menetapkan_peran_aktif_dan_melanjutkan(): void
    {
        $user = $this->wargaKepalaKeluarga();
        $user->syncRoles(['Warga', 'Ketua RT']);

        $this->actingAs($user->fresh());

        $this->post(route('pilih-peran.store'), ['role' => 'Ketua RT'])
            ->assertRedirect();

        $this->assertSame('Ketua RT', session(User::ACTIVE_ROLE_SESSION_KEY));
    }

    public function test_memilih_peran_yang_tidak_dimiliki_ditolak(): void
    {
        $user = $this->wargaKepalaKeluarga();
        $user->syncRoles(['Warga', 'Ketua RT']);

        $this->actingAs($user->fresh());

        $this->post(route('pilih-peran.store'), ['role' => 'Admin Aplikasi'])
            ->assertSessionHasErrors('role');

        $this->assertNull(session(User::ACTIVE_ROLE_SESSION_KEY));
    }

    public function test_ganti_peran_mereset_peran_aktif(): void
    {
        $user = $this->wargaKepalaKeluarga();
        $user->syncRoles(['Warga', 'Ketua RT']);

        $this->actingAs($user->fresh());
        session([User::ACTIVE_ROLE_SESSION_KEY => 'Warga']);

        $this->post(route('ganti-peran'))
            ->assertRedirect(route('pilih-peran'));

        $this->assertNull(session(User::ACTIVE_ROLE_SESSION_KEY));
    }

    public function test_akun_pengurus_tanpa_nik_login_via_username(): void
    {
        $this->ensureRoles(['Admin RW']);
        $pengurus = User::create([
            'nik' => null,
            'username' => 'adminrw_uji',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $pengurus->syncRoles(['Admin RW']);

        $this->post(route('login'), ['login' => 'adminrw_uji', 'password' => 'password123'])
            ->assertRedirect();
        $this->assertAuthenticatedAs($pengurus->fresh());
        $this->assertNull($pengurus->fresh()->nik);
    }

    public function test_logout_membersihkan_peran_aktif(): void
    {
        $user = $this->wargaKepalaKeluarga();
        $this->actingAs($user->fresh());
        session([User::ACTIVE_ROLE_SESSION_KEY => 'Warga']);

        $this->post(route('logout'))->assertRedirect('/login');

        $this->assertGuest();
        $this->assertNull(session(User::ACTIVE_ROLE_SESSION_KEY));
    }
}
