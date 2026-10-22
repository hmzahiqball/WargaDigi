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

    public function test_activation_rejects_a_mismatched_whatsapp_number(): void
    {
        $this->createResident();

        $this->from('/register')
            ->post(route('aktivasi.verifikasi'), ['nik' => $this->nik, 'no_wa' => '081111111111'])
            ->assertRedirect('/register')
            ->assertSessionHasErrors('nik');
    }

    public function test_activation_with_mock_otp_creates_an_active_resident_account(): void
    {
        $keluarga = $this->createResident();

        $this->post(route('aktivasi.verifikasi'), ['nik' => $this->nik, 'no_wa' => '6281234567890'])
            ->assertRedirect(route('aktivasi.otp'));

        $this->post(route('aktivasi.otp.verify'), ['otp' => '123456'])
            ->assertRedirect(route('aktivasi.password'));

        $this->post(route('aktivasi.password.complete'), [
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'nik' => $this->nik,
            'role' => 'Warga',
            'status_akun' => 'Active',
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
            ->assertSessionHasErrors('no_wa');
    }
}
