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
 * Paket 2B — RT membuat Keluarga + Kepala Keluarga (dengan akun warga) dan
 * menambah anggota keluarga tanpa akun.
 */
class RtKeluargaManagementTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        foreach (['Ketua RT', 'Warga'] as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    private function ketuaRt(MasterRt $rt): User
    {
        $this->ensureRoles();
        $user = User::create([
            'nik' => '3217010101019001',
            'username' => 'ketua_rt_uji',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $user->syncRoles(['Ketua RT']);

        return $user;
    }

    private function dataKepalaKeluarga(array $override = []): array
    {
        return array_merge([
            'no_kk' => '3217010100000001',
            'no_wa' => '081200000001',
            'alamat' => 'Jl. Uji No. 1',
            'nik' => '3217010100000011',
            'nama_lengkap' => 'Kepala Uji',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1985-05-05',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'pendidikan_terakhir' => 'SMA/SMK',
            'status_perkawinan' => 'Kawin',
            'kewarganegaraan' => 'WNI',
            'username' => 'kepala_uji',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $override);
    }

    public function test_rt_membuat_keluarga_kepala_keluarga_beserta_akun_warga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga())
            ->assertRedirect(route('rt.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('keluarga', [
            'no_kk' => '3217010100000001',
            'nik_kepala_keluarga' => '3217010100000011',
            'rt_id' => $rt->id,
        ]);
        $this->assertDatabaseHas('penduduk', [
            'nik' => '3217010100000011',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
        ]);
        $akun = User::where('username', 'kepala_uji')->first();
        $this->assertNotNull($akun);
        $this->assertSame('Warga', $akun->role);
        $this->assertSame('3217010100000011', $akun->nik);
    }

    public function test_username_duplikat_ditolak_saat_membuat_kepala_keluarga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        // ketua_rt_uji sudah memakai username tertentu; pakai itu agar bentrok.
        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga(['username' => 'ketua_rt_uji']))
            ->assertSessionHasErrors('username');

        $this->assertDatabaseMissing('penduduk', ['nik' => '3217010100000011']);
    }

    public function test_rt_menambah_anggota_keluarga_tanpa_membuat_akun(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());
        $keluarga = Keluarga::where('no_kk', '3217010100000001')->firstOrFail();

        $jumlahUserSebelum = User::count();

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.anggota.store', $keluarga->id), [
                'nik' => '3217010100000022',
                'nama_lengkap' => 'Anggota Uji',
                'status_hubungan_keluarga' => 'Anak',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2010-01-01',
                'agama' => 'Islam',
                'pekerjaan' => 'Pelajar',
                'pendidikan_terakhir' => 'SD',
                'status_perkawinan' => 'Belum Kawin',
                'kewarganegaraan' => 'WNI',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('penduduk', [
            'nik' => '3217010100000022',
            'keluarga_id' => $keluarga->id,
            'status_hubungan_keluarga' => 'Anak',
        ]);
        // Anggota tidak boleh punya akun: jumlah user tidak bertambah.
        $this->assertSame($jumlahUserSebelum, User::count());
        $this->assertDatabaseMissing('users', ['nik' => '3217010100000022']);
    }
}
