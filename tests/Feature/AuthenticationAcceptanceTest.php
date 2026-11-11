<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    private string $nik = '3217010101010003';

    private function createResident(string $whatsapp = '081234567890'): Keluarga
    {
        $rt = MasterRt::create(['kode_rt' => '001', 'nama_rt' => 'RT 001']);
        $keluarga = Keluarga::create([
            'no_kk' => '3217010101010001',
            'nik_kepala_keluarga' => $this->nik,
            'alamat' => 'Alamat UAT',
            'rt_id' => $rt->id,
            'no_wa' => $whatsapp,
        ]);
        Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => $this->nik,
            'nama_lengkap' => 'Warga UAT',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $keluarga;
    }

    public function test_activation_token_invalid_ditolak(): void
    {
        $this->createResident();

        $this->get(route('aktivasi.token', ['token' => 'token-tidak-ada']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('username');
    }

    public function test_activation_via_token_creates_an_active_resident_account(): void
    {
        $keluarga = $this->createResident();
        $token = \Illuminate\Support\Str::random(40);
        User::create([
            'nik' => $this->nik,
            'username' => 'warga_uat',
            'role' => 'Warga',
            'password' => \Illuminate\Support\Str::random(16),
            'status_akun' => 'Unverified',
            'activation_token' => $token,
            'activation_token_created_at' => now(),
        ]);

        // Buka form aktivasi via token → tampil OK.
        $this->get(route('aktivasi.token', ['token' => $token]))->assertOk();

        // Set username + password → akun aktif, token terhapus.
        $this->post(route('aktivasi.token.proses', ['token' => $token]), [
            'username' => 'warga_uat_final',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'nik' => $this->nik,
            'role' => 'Warga',
            'status_akun' => 'Active',
            'activation_token' => null,
        ]);
        $this->assertDatabaseHas('keluarga', [
            'id' => $keluarga->id,
            'status_aktivasi' => 'Active',
        ]);
        $this->assertTrue(Hash::check('password123', User::where('nik', $this->nik)->first()->password));
    }

    public function test_unverified_account_cannot_login(): void
    {
        $this->createResident();
        User::create([
            'nik' => $this->nik,
            'username' => 'Warga UAT',
            'role' => 'Warga',
            'password' => 'password123',
            'status_akun' => 'Unverified',
        ]);

        $this->post(route('login'), [
            'no_wa' => '081234567890',
            'password' => 'password123',
        ])->assertRedirect()
            ->assertSessionHasErrors('login');
    }

    public function test_login_berhasil_dengan_whatsapp_dan_password(): void
    {
        $this->createResident();
        User::create([
            'nik' => $this->nik,
            'username' => 'Warga UAT',
            'role' => 'Warga',
            'password' => 'password123',
            'status_akun' => 'Active',
        ]);

        $this->post(route('login'), [
            'no_wa' => '081234567890',
            'password' => 'password123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs(User::where('nik', $this->nik)->first());
    }

    /**
     * Satu nomor WhatsApp dipakai dua keluarga/akun aktif.
     * Login harus memilih akun berdasarkan kecocokan PASSWORD, bukan akun pertama.
     */
    public function test_login_memilih_akun_yang_benar_saat_nomor_whatsapp_dipakai_bersama(): void
    {
        $sharedWa = '081234567890';

        // Akun pertama (mis. pengurus) berbagi nomor yang sama.
        $this->createResident($sharedWa);
        $userA = User::create([
            'nik' => $this->nik,
            'username' => 'Akun A',
            'role' => 'Admin RW',
            'password' => 'passwordA',
            'status_akun' => 'Active',
        ]);

        // Akun kedua (warga biasa) di keluarga berbeda, nomor WA sama.
        $rtB = MasterRt::create(['kode_rt' => '002', 'nama_rt' => 'RT 002']);
        $nikB = '3217010101019999';
        $keluargaB = Keluarga::create([
            'no_kk' => '3217010101019000',
            'nik_kepala_keluarga' => $nikB,
            'alamat' => 'Alamat B',
            'rt_id' => $rtB->id,
            'no_wa' => $sharedWa,
        ]);
        Penduduk::create([
            'keluarga_id' => $keluargaB->id,
            'nik' => $nikB,
            'nama_lengkap' => 'Warga B',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1992-02-02',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);
        $userB = User::create([
            'nik' => $nikB,
            'username' => 'Akun B',
            'role' => 'Warga',
            'password' => 'passwordB',
            'status_akun' => 'Active',
        ]);

        // Login dengan password milik B harus masuk ke B (bukan A).
        $this->post(route('login'), ['no_wa' => $sharedWa, 'password' => 'passwordB'])
            ->assertRedirect();
        $this->assertAuthenticatedAs($userB);

        auth()->logout();

        // Login dengan password milik A harus masuk ke A (bukan B).
        $this->post(route('login'), ['no_wa' => $sharedWa, 'password' => 'passwordA'])
            ->assertRedirect();
        $this->assertAuthenticatedAs($userA);
    }

    public function test_login_gagal_dengan_password_salah_pada_nomor_bersama(): void
    {
        $this->createResident('081234567890');
        User::create([
            'nik' => $this->nik,
            'username' => 'Akun A',
            'role' => 'Warga',
            'password' => 'passwordA',
            'status_akun' => 'Active',
        ]);

        $this->post(route('login'), [
            'no_wa' => '081234567890',
            'password' => 'passwordSalah',
        ])->assertRedirect()->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
