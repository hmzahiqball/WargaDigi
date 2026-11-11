<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Manajemen Hak Akses — validasi pemberian peran.
 *
 * Aturan inti: peran "Ketua RT" dan "Ketua RW" hanya dapat diberikan kepada
 * akun ber-NIK yang sudah ditetapkan sebagai pejabat AKTIF (menu Kepengurusan).
 * Peran lain tidak terpengaruh validasi ini.
 */
class ManajemenHakAksesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        Role::findOrCreate('Admin Aplikasi', 'web');
        $admin = User::create([
            'nik' => null,
            'username' => 'superadmin_uji',
            'role' => 'Admin Aplikasi',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $admin->syncRoles(['Admin Aplikasi']);

        return $admin;
    }

    private function penduduk(MasterRt $rt, string $nik, string $nama): Penduduk
    {
        $keluarga = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '0812' . substr($nik, -8),
        ]);

        return Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1980-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);
    }

    private function akunWarga(MasterRt $rt, string $nik, string $username): User
    {
        $this->penduduk($rt, $nik, 'Warga ' . $username);
        $u = User::create([
            'nik' => $nik,
            'username' => $username,
            'role' => 'Warga',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $u->syncRoles(['Warga']);

        return $u;
    }

    /**
     * Anggota keluarga NON kepala keluarga tanpa akun.
     * Dibuat dalam keluarga baru dengan kepala keluarga berbeda NIK.
     */
    private function anggota(MasterRt $rt, string $nik, string $nama, string $hubungan = 'Anak'): Penduduk
    {
        $nikKepala = '99' . substr($nik, 2);
        $keluarga = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nikKepala,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '0813' . substr($nik, -8),
        ]);

        return Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Pelajar',
            'status_hubungan_keluarga' => $hubungan,
            'status_perkawinan' => 'Belum Kawin',
        ]);
    }

    public function test_buat_pengurus_menolak_peran_ketua_rt_dan_rw(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'username' => 'ketuabaru',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Ketua RT'],
            ])
            ->assertSessionHasErrors('roles');

        $this->assertDatabaseMissing('users', ['username' => 'ketuabaru']);
    }

    public function test_buat_pengurus_berhasil_untuk_peran_non_pejabat(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'username' => 'opkontenbaru',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Konten RW'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertDatabaseHas('users', ['username' => 'opkontenbaru', 'role' => 'Op Konten RW']);
    }

    public function test_update_ketua_rt_ditolak_bila_akun_tanpa_nik(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $admin = $this->admin();

        $akun = User::create([
            'nik' => null,
            'username' => 'pengurus_tanpa_nik',
            'role' => 'Op Konten RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.manajemen-hak-akses.update', $akun->id), [
                'roles' => ['Ketua RT'],
            ])
            ->assertSessionHasErrors('roles');

        $akun->refresh();
        $this->assertNotSame('Ketua RT', $akun->role);
    }

    public function test_update_ketua_rt_ditolak_bila_bukan_pejabat_aktif(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $akun = $this->akunWarga($rt, '3217010100000101', 'calonketua');

        // Tidak ditetapkan sebagai pejabat aktif.
        $this->actingAs($admin)
            ->put(route('admin.manajemen-hak-akses.update', $akun->id), [
                'roles' => ['Ketua RT'],
            ])
            ->assertSessionHasErrors('roles');

        $this->assertFalse($akun->fresh()->hasRole('Ketua RT'));
    }

    public function test_update_ketua_rt_berhasil_bila_pejabat_aktif(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $akun = $this->akunWarga($rt, '3217010100000111', 'ketuart_valid');

        Pejabat::setAktif('ketua_rt', $rt->id, '3217010100000111');

        $this->actingAs($admin)
            ->put(route('admin.manajemen-hak-akses.update', $akun->id), [
                'roles' => ['Warga', 'Ketua RT'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertTrue($akun->fresh()->hasRole('Ketua RT'));
    }

    public function test_update_ketua_rw_berhasil_bila_pejabat_aktif(): void
    {
        Role::findOrCreate('Ketua RW', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $akun = $this->akunWarga($rt, '3217010100000121', 'ketuarw_valid');

        Pejabat::setAktif('ketua_rw', null, '3217010100000121');

        $this->actingAs($admin)
            ->put(route('admin.manajemen-hak-akses.update', $akun->id), [
                'roles' => ['Warga', 'Ketua RW'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertTrue($akun->fresh()->hasRole('Ketua RW'));
    }

    public function test_buat_pengurus_dari_warga_sudah_berakun_ditolak(): void
    {
        Role::findOrCreate('Op Konten RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        // Warga sudah punya akun → tidak boleh dibuat akun lagi di sini.
        $this->akunWarga($rt, '3217010100000201', 'warga_berakun');

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000201',
                'username' => 'opkonten_rt',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Konten RT'],
            ])
            ->assertSessionHasErrors('nik');

        $this->assertDatabaseMissing('users', ['username' => 'opkonten_rt']);
    }

    public function test_buat_pengurus_dari_kepala_keluarga_tanpa_akun_berhasil(): void
    {
        Role::findOrCreate('Op Konten RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        // Kepala keluarga tanpa akun → boleh dibuatkan akun pengurus.
        $this->penduduk($rt, '3217010100000205', 'Kepala Tanpa Akun');

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000205',
                'username' => 'kepala_pengurus',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Konten RT'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertDatabaseHas('users', [
            'nik' => '3217010100000205',
            'username' => 'kepala_pengurus',
            'role' => 'Op Konten RT',
        ]);
    }

    public function test_buat_pengurus_dari_anggota_non_kepala_membuat_akun_baru(): void
    {
        Role::findOrCreate('Op Keuangan RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->anggota($rt, '3217010100000211', 'Anggota Keluarga');

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000211',
                'username' => 'anggota_pengurus',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Keuangan RT'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertDatabaseHas('users', [
            'nik' => '3217010100000211',
            'username' => 'anggota_pengurus',
            'role' => 'Op Keuangan RT',
        ]);
    }

    public function test_buat_pengurus_dari_anggota_ditolak_bila_sudah_punya_akun(): void
    {
        Role::findOrCreate('Op Konten RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        // Anggota non-kepala, tetapi sudah terlanjur punya akun.
        $this->anggota($rt, '3217010100000215', 'Anggota Berakun');
        User::create([
            'nik' => '3217010100000215',
            'username' => 'anggota_lama',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000215',
                'username' => 'anggota_baru2',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Op Konten RT'],
            ])
            ->assertSessionHasErrors('nik');

        $this->assertDatabaseMissing('users', ['username' => 'anggota_baru2']);
    }

    public function test_buat_pengurus_dari_anggota_ketua_rt_ditolak_bila_bukan_pejabat_aktif(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->anggota($rt, '3217010100000221', 'Calon Ketua');

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000221',
                'username' => 'calonketua',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Ketua RT'],
            ])
            ->assertSessionHasErrors('roles');

        $this->assertDatabaseMissing('users', ['username' => 'calonketua']);
    }

    public function test_buat_pengurus_dari_anggota_ketua_rt_berhasil_bila_pejabat_aktif(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $admin = $this->admin();
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->anggota($rt, '3217010100000231', 'Ketua Sah');
        Pejabat::setAktif('ketua_rt', $rt->id, '3217010100000231');

        $this->actingAs($admin)
            ->post(route('admin.manajemen-hak-akses.pengurus.store'), [
                'sumber' => 'warga',
                'nik' => '3217010100000231',
                'username' => 'ketuasah',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'roles' => ['Ketua RT'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertDatabaseHas('users', ['nik' => '3217010100000231', 'username' => 'ketuasah']);
        $this->assertTrue(User::where('nik', '3217010100000231')->first()->hasRole('Ketua RT'));
    }

    public function test_update_peran_non_pejabat_tidak_butuh_pejabat_aktif(): void
    {
        Role::findOrCreate('Op Keuangan RW', 'web');
        $admin = $this->admin();

        $akun = User::create([
            'nik' => null,
            'username' => 'pengurus_biasa',
            'role' => 'Op Konten RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.manajemen-hak-akses.update', $akun->id), [
                'roles' => ['Op Keuangan RW'],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.manajemen-hak-akses'));

        $this->assertTrue($akun->fresh()->hasRole('Op Keuangan RW'));
    }
}
