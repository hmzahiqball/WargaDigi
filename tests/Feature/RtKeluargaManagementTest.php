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
            'alamat' => 'Jl. Uji No. 1',
            'rw' => '21',
            'kelurahan' => 'Tanimulya',
            'penduduk' => [[
                'nik' => '3217010100000011',
                'nama_lengkap' => 'Kepala Uji',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1985-05-05',
                'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta',
                'pendidikan_terakhir' => 'SMA/SMK',
                'status_hubungan_keluarga' => 'Kepala Keluarga',
                'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI',
                'no_wa' => '081200000001',
            ]],
            'pemegang_akun' => 0,
        ], $override);
    }

    public function test_rt_membuat_keluarga_kepala_keluarga_beserta_akun_warga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga())
            ->assertRedirect(route('rt.penduduk.index'))
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
        $akun = User::where('nik', '3217010100000011')->first();
        $this->assertNotNull($akun);
        $this->assertSame('Warga', $akun->role);
        $this->assertSame('3217010100000011', $akun->nik);
    }

    public function test_akun_dibuat_unverified_dengan_token_dan_link_aktivasi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga())
            ->assertRedirect(route('rt.penduduk.index'))
            ->assertSessionHas('activation_link');

        $akun = User::where('nik', '3217010100000011')->firstOrFail();
        $this->assertSame('Unverified', $akun->status_akun);
        $this->assertNotEmpty($akun->activation_token);
    }

    public function test_rt_bisa_buat_ulang_link_aktivasi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());

        $keluarga = Keluarga::where('no_kk', '3217010100000001')->firstOrFail();
        $tokenLama = User::where('nik', '3217010100000011')->first()->activation_token;

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.aktivasi-ulang', $keluarga->id))
            ->assertRedirect()
            ->assertSessionHas('activation_link');

        $tokenBaru = User::where('nik', '3217010100000011')->first()->activation_token;
        $this->assertNotEmpty($tokenBaru);
        $this->assertNotSame($tokenLama, $tokenBaru);
    }

    public function test_pemegang_akun_wajib_nomor_wa(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        // Pemegang akun (index 0) tanpa no_wa → ditolak.
        $data = $this->dataKepalaKeluarga();
        $data['penduduk'][0]['no_wa'] = '';
        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $data)
            ->assertSessionHasErrors('penduduk.0.no_wa');

        $this->assertDatabaseMissing('penduduk', ['nik' => '3217010100000011']);
    }

    public function test_nomor_wa_tersimpan_untuk_pemegang_dan_keluarga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $data = $this->dataKepalaKeluarga();
        $data['penduduk'][0]['no_wa'] = '081234567890';

        $this->actingAs($ketua)
            ->post(route('rt.keluarga.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('penduduk', ['nik' => '3217010100000011', 'no_wa' => '081234567890']);
        $this->assertDatabaseHas('keluarga', ['no_kk' => '3217010100000001', 'no_wa' => '081234567890']);
    }

    public function test_halaman_penduduk_menampilkan_dua_tab_kk_dan_penduduk(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());

        $this->actingAs($ketua)
            ->get(route('rt.penduduk.index'))
            ->assertOk()
            ->assertSee('Profil Keluarga')
            ->assertSee('Daftar Semua Penduduk')
            ->assertSee('3217010100000001')   // No. KK di tab Profil Keluarga
            ->assertSee('Kepala Uji')          // kepala keluarga
            ->assertSee('data-testid="btn-tambah-keluarga"', false) // RT boleh menambah data
            ->assertDontSee('name="rt_id"', false);                 // tanpa filter RT
    }

    public function test_ketua_rt_bisa_export_excel_lingkup_rt_sendiri(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());

        $res = $this->actingAs($ketua)->get(route('rt.penduduk.export', ['kolom' => ['nik', 'nama_lengkap', 'no_wa']]));
        $res->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            strtolower($res->headers->get('content-type'))
        );
    }

    public function test_export_rt_wajib_pilih_kolom(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        $this->actingAs($ketua)
            ->get(route('rt.penduduk.export'))
            ->assertSessionHasErrors('kolom');
    }

    public function test_detail_keluarga_menampilkan_anggota(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());
        $keluarga = Keluarga::where('no_kk', '3217010100000001')->firstOrFail();

        $this->actingAs($ketua)
            ->get(route('rt.keluarga.show', $keluarga->id))
            ->assertOk()
            ->assertSee('Detail Keluarga')
            ->assertSee('3217010100000001')
            ->assertSee('Kepala Uji');
    }

    public function test_ketua_rt_tidak_bisa_lihat_detail_keluarga_rt_lain(): void
    {
        $rtA = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $rtB = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $ketuaA = $this->ketuaRt($rtA);

        // Keluarga milik RT B.
        $kelB = Keluarga::create([
            'no_kk' => '3217010100000777', 'nik_kepala_keluarga' => '3217010100000778',
            'alamat' => 'Jl. B', 'rt_id' => $rtB->id, 'status_aktivasi' => 'Active',
        ]);

        $this->actingAs($ketuaA)
            ->get(route('rt.keluarga.show', $kelB->id))
            ->assertForbidden();
    }

    public function test_username_duplikat_ditolak_saat_aktivasi_akun_warga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt);

        // RT membuat keluarga; username pemegang akun masih sementara (auto).
        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKepalaKeluarga());
        $akun = User::where('nik', '3217010100000011')->firstOrFail();

        // Warga mencoba aktivasi dengan username yang sudah dipakai akun lain
        // (ketua_rt_uji) → ditolak oleh validasi unique saat aktivasi.
        $this->post(route('aktivasi.token.proses', ['token' => $akun->activation_token]), [
            'username' => 'ketua_rt_uji',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertSessionHasErrors('username');

        $this->assertSame('Unverified', $akun->fresh()->status_akun);
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
