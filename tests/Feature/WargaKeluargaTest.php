<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\MutasiWarga;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Pengelolaan keluarga oleh role Warga dengan pola RT namun tetap melalui
 * mekanisme pengajuan (MutasiWarga): tambah, edit anggota, dan edit KK.
 */
class WargaKeluargaTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        foreach (['Ketua RT', 'Admin RW', 'Warga'] as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    /** Buat satu keluarga dengan kepala keluarga (NIK = no_kk untuk kemudahan). */
    private function createFamily(MasterRt $rt, string $nik, string $name): Keluarga
    {
        $family = Keluarga::create([
            'no_kk' => $nik,
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $family->id,
            'nik' => $nik,
            'nama_lengkap' => $name,
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Kawin',
        ]);

        return $family;
    }

    private function wargaPemegang(Keluarga $family, MasterRt $rt): User
    {
        $this->ensureRoles();
        $user = User::create([
            'nik' => $family->nik_kepala_keluarga,
            'username' => 'warga_' . substr($family->nik_kepala_keluarga, -4),
            'role' => 'Warga',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        return $user;
    }

    public function test_warga_pengajuan_edit_kk_membuat_mutasi_edit_kk_menunggu_rt(): void
    {
        $rt = MasterRt::create(['kode_rt' => '011', 'nama_rt' => 'RT 011']);
        $family = $this->createFamily($rt, '3217010101010101', 'Kepala EditKK');
        $warga = $this->wargaPemegang($family, $rt);

        $this->actingAs($warga)
            ->put(route('warga.keluarga.update'), [
                'alamat' => 'Jl. Baru No. 10',
                'alamat_domisili' => 'Jl. Domisili No. 5',
                'no_wa' => '082111222333',
                'pemilik_rumah' => '1',
                'status_warga' => 'tetap',
            ])
            ->assertRedirect(route('warga.keluarga.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mutasi_warga', [
            'keluarga_id' => $family->id,
            'jenis_mutasi' => 'edit_kk',
            'status' => 'menunggu_rt',
        ]);

        // Data keluarga BELUM berubah (menunggu approval).
        $this->assertDatabaseHas('keluarga', [
            'id' => $family->id,
            'alamat' => 'Alamat Uji',
        ]);
    }

    public function test_rw_approve_edit_kk_memperbarui_data_keluarga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '012', 'nama_rt' => 'RT 012']);
        $family = $this->createFamily($rt, '3217010101010102', 'Kepala Approve');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => null,
            'jenis_mutasi' => 'edit_kk',
            'data_pengajuan' => [
                'alamat' => 'Jl. Disetujui No. 99',
                'alamat_domisili' => 'Jl. Domisili Disetujui',
                'no_wa' => '083999888777',
                'pemilik_rumah' => true,
                'status_warga' => 'tetap',
            ],
            'status' => 'menunggu_rw',
        ]);

        $this->ensureRoles();
        $rwUser = User::create([
            'nik' => '3217010101010199',
            'username' => 'admin_rw_editkk',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $rwUser->syncRoles(['Admin RW']);

        $this->actingAs($rwUser)
            ->post(route('rw.mutasi.approve', $mutasi->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mutasi_warga', [
            'id' => $mutasi->id,
            'status' => 'disetujui',
        ]);

        $this->assertDatabaseHas('keluarga', [
            'id' => $family->id,
            'alamat' => 'Jl. Disetujui No. 99',
            'alamat_domisili' => 'Jl. Domisili Disetujui',
            'no_wa' => '083999888777',
            'status_warga' => 'tetap',
        ]);
    }

    public function test_non_pemegang_akun_tidak_dapat_akses_create_dan_edit(): void
    {
        $rt = MasterRt::create(['kode_rt' => '013', 'nama_rt' => 'RT 013']);
        $family = $this->createFamily($rt, '3217010101010103', 'Kepala Keluarga 013');

        // Tambah anggota non-kepala pada keluarga.
        $anggota = Penduduk::create([
            'keluarga_id' => $family->id,
            'nik' => '3217010101010203',
            'nama_lengkap' => 'Anggota Biasa',
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-02-02',
            'agama' => 'Islam',
            'pekerjaan' => 'Pelajar',
            'status_hubungan_keluarga' => 'Anak',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        $this->ensureRoles();

        // Akun Warga yang BUKAN pemegang (NIK berbeda dari kepala keluarga),
        // namun tetap tertaut ke penduduk anggota keluarga ini.
        $nonPemegang = User::create([
            'nik' => $anggota->nik,
            'username' => 'warga_non_pemegang',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $nonPemegang->syncRoles(['Warga']);

        $this->actingAs($nonPemegang)
            ->get(route('warga.keluarga.anggota.create'))
            ->assertForbidden();

        $this->actingAs($nonPemegang)
            ->get(route('warga.keluarga.anggota.edit', $anggota->id))
            ->assertForbidden();
    }

    /** Tambah satu anggota (non-kepala) ke sebuah keluarga. */
    private function tambahAnggota(Keluarga $family, string $nik, string $nama, string $hubungan = 'Anak'): Penduduk
    {
        return Penduduk::create([
            'keluarga_id' => $family->id,
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2000-02-02',
            'agama' => 'Islam',
            'pekerjaan' => 'Pelajar',
            'status_hubungan_keluarga' => $hubungan,
            'status_perkawinan' => 'Belum Kawin',
        ]);
    }

    public function test_pemegang_akun_mengajukan_pindah_akun_membuat_mutasi_menunggu_rt(): void
    {
        $rt = MasterRt::create(['kode_rt' => '021', 'nama_rt' => 'RT 021']);
        $family = $this->createFamily($rt, '3217010101010201', 'Kepala Pindah');
        $warga = $this->wargaPemegang($family, $rt);
        $anggota = $this->tambahAnggota($family, '3217010101010301', 'Anak Tujuan');

        $this->actingAs($warga)
            ->post(route('warga.keluarga.pindah-akun.store'), [
                'nik_tujuan' => $anggota->nik,
            ])
            ->assertRedirect(route('warga.keluarga.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('mutasi_warga', [
            'keluarga_id' => $family->id,
            'jenis_mutasi' => 'pindah_akun',
            'status' => 'menunggu_rt',
            'penduduk_id' => $anggota->id,
        ]);

        // Akun lama masih Active (belum berubah sebelum ACC) & pemegang belum pindah.
        $this->assertDatabaseHas('users', [
            'nik' => $family->nik_kepala_keluarga,
            'status_akun' => 'Active',
        ]);
        $this->assertSame($family->nik_kepala_keluarga, $family->fresh()->nikPemegangAkun());
    }

    public function test_rt_approve_pindah_akun_memindahkan_pemegang_dan_menonaktifkan_akun_lama(): void
    {
        $rt = MasterRt::create(['kode_rt' => '022', 'nama_rt' => 'RT 022']);
        $family = $this->createFamily($rt, '3217010101010202', 'Kepala Pindah 2');
        $this->wargaPemegang($family, $rt);
        $anggota = $this->tambahAnggota($family, '3217010101010302', 'Anak Tujuan 2');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'pindah_akun',
            'data_pengajuan' => [
                'nik_tujuan' => $anggota->nik,
                'nama_tujuan' => $anggota->nama_lengkap,
                'nik_lama' => $family->nik_kepala_keluarga,
            ],
            'status' => 'menunggu_rt',
        ]);

        $this->ensureRoles();
        $ketuaRt = User::create([
            'nik' => '3217010101010299',
            'username' => 'ketua_rt_pindah',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaRt->syncRoles(['Ketua RT']);

        $this->actingAs($ketuaRt)
            ->post(route('rt.mutasi.approve', $mutasi->id))
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionHas('activation_link');

        // Keluarga: pemegang akun berpindah ke anggota tujuan.
        $this->assertSame($anggota->nik, $family->fresh()->nik_pemegang_akun);

        // Akun lama nonaktif.
        $this->assertDatabaseHas('users', [
            'nik' => $family->nik_kepala_keluarga,
            'status_akun' => 'Nonaktif',
        ]);

        // Akun tujuan dibuat Unverified + punya activation_token.
        $userTujuan = User::where('nik', $anggota->nik)->first();
        $this->assertNotNull($userTujuan);
        $this->assertSame('Unverified', $userTujuan->status_akun);
        $this->assertNotEmpty($userTujuan->activation_token);

        // Mutasi selesai (disetujui), tidak diteruskan ke RW.
        $this->assertDatabaseHas('mutasi_warga', [
            'id' => $mutasi->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_pindah_akun_pemegang_multi_role_hanya_mencabut_role_warga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '024', 'nama_rt' => 'RT 024']);
        $family = $this->createFamily($rt, '3217010101010204', 'Budi Ketua RT');
        $anggota = $this->tambahAnggota($family, '3217010101010304', 'Anak Tujuan 4');

        // Pemegang akun yang juga Ketua RT (satu akun, dua peran).
        $this->ensureRoles();
        $budi = User::create([
            'nik' => $family->nik_kepala_keluarga,
            'username' => 'budi_multi',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $budi->syncRoles(['Ketua RT', 'Warga']);

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'pindah_akun',
            'data_pengajuan' => [
                'nik_tujuan' => $anggota->nik,
                'nama_tujuan' => $anggota->nama_lengkap,
                'nik_lama' => $family->nik_kepala_keluarga,
            ],
            'status' => 'menunggu_rt',
        ]);

        // Budi menyetujui pengajuannya sendiri dengan peran aktif Ketua RT (dibiarkan).
        $this->actingAs($budi)
            ->withSession([User::ACTIVE_ROLE_SESSION_KEY => 'Ketua RT'])
            ->post(route('rt.mutasi.approve', $mutasi->id))
            ->assertRedirect()
            ->assertSessionHas('activation_link');

        $budi = $budi->fresh();

        // Akun Budi TETAP aktif; hanya peran Warga yang dicabut.
        $this->assertSame('Active', $budi->status_akun);
        $this->assertFalse($budi->hasRole('Warga'));
        $this->assertTrue($budi->hasRole('Ketua RT'));
        $this->assertSame(['Ketua RT'], $budi->roleNames());
        $this->assertSame('Ketua RT', $budi->role);

        // Pemegang akun keluarga pindah ke anggota tujuan (Unverified + token).
        $this->assertSame($anggota->nik, $family->fresh()->nik_pemegang_akun);
        $tujuan = User::where('nik', $anggota->nik)->first();
        $this->assertNotNull($tujuan);
        $this->assertSame('Unverified', $tujuan->status_akun);
        $this->assertNotEmpty($tujuan->activation_token);
        $this->assertTrue($tujuan->hasRole('Warga'));
    }

    public function test_pindah_akun_tujuan_sudah_punya_akun_aktif_hanya_diberi_role_warga(): void
    {
        $rt = MasterRt::create(['kode_rt' => '025', 'nama_rt' => 'RT 025']);
        $family = $this->createFamily($rt, '3217010101010205', 'Kepala Pindah 5');
        $this->wargaPemegang($family, $rt);
        $anggota = $this->tambahAnggota($family, '3217010101010305', 'Anak Sudah Punya Akun');

        // Anggota tujuan sudah punya akun aktif dengan peran lain.
        Role::findOrCreate('Op Konten RT', 'web');
        $tujuan = User::create([
            'nik' => $anggota->nik,
            'username' => 'anak_konten',
            'role' => 'Op Konten RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'rahasia12345',
        ]);
        $tujuan->syncRoles(['Op Konten RT']);

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'pindah_akun',
            'data_pengajuan' => [
                'nik_tujuan' => $anggota->nik,
                'nama_tujuan' => $anggota->nama_lengkap,
                'nik_lama' => $family->nik_kepala_keluarga,
            ],
            'status' => 'menunggu_rt',
        ]);

        $this->ensureRoles();
        $ketuaRt = User::create([
            'nik' => '3217010101010298',
            'username' => 'ketua_rt_tujuan_aktif',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaRt->syncRoles(['Ketua RT']);

        $this->actingAs($ketuaRt)
            ->post(route('rt.mutasi.approve', $mutasi->id))
            ->assertRedirect()
            ->assertSessionHas('success')
            ->assertSessionMissing('activation_link');

        $tujuan = $tujuan->fresh();

        // Kredensial & status akun tujuan TIDAK disentuh; tanpa token aktivasi.
        $this->assertSame('Active', $tujuan->status_akun);
        $this->assertSame('anak_konten', $tujuan->username);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('rahasia12345', $tujuan->password));
        $this->assertNull($tujuan->activation_token);

        // Tujuan kini memegang peran lamanya + Warga.
        $this->assertTrue($tujuan->hasRole('Op Konten RT'));
        $this->assertTrue($tujuan->hasRole('Warga'));
        $this->assertSame($anggota->nik, $family->fresh()->nik_pemegang_akun);

        // Akun lama (hanya Warga) dinonaktifkan seperti sebelumnya.
        $this->assertDatabaseHas('users', [
            'nik' => $family->nik_kepala_keluarga,
            'status_akun' => 'Nonaktif',
        ]);
    }

    public function test_anggota_tujuan_dapat_aktivasi_akun_setelah_pindah(): void
    {
        $rt = MasterRt::create(['kode_rt' => '023', 'nama_rt' => 'RT 023']);
        $family = $this->createFamily($rt, '3217010101010203', 'Kepala Pindah 3');
        $this->wargaPemegang($family, $rt);
        $anggota = $this->tambahAnggota($family, '3217010101010303', 'Anak Tujuan 3');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'pindah_akun',
            'data_pengajuan' => [
                'nik_tujuan' => $anggota->nik,
                'nama_tujuan' => $anggota->nama_lengkap,
                'nik_lama' => $family->nik_kepala_keluarga,
            ],
            'status' => 'menunggu_rt',
        ]);

        $this->ensureRoles();
        $ketuaRt = User::create([
            'nik' => '3217010101010399',
            'username' => 'ketua_rt_pindah3',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaRt->syncRoles(['Ketua RT']);

        $this->actingAs($ketuaRt)->post(route('rt.mutasi.approve', $mutasi->id));

        $userTujuan = User::where('nik', $anggota->nik)->firstOrFail();

        // Anggota tujuan mengaktivasi akun dengan username + password sendiri.
        $this->post(route('aktivasi.token.proses', ['token' => $userTujuan->activation_token]), [
            'username' => 'warga_tujuan_baru',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ])->assertRedirect(route('login'));

        $userTujuan->refresh();
        $this->assertSame('Active', $userTujuan->status_akun);
        $this->assertSame('warga_tujuan_baru', $userTujuan->username);
        $this->assertNull($userTujuan->activation_token);
    }

    /**
     * (a) Submit edit anggota: mutasi mencatat pengaju + waktu + snapshot data lama.
     */
    public function test_submit_edit_anggota_mencatat_pengaju_dan_data_lama(): void
    {
        $rt = MasterRt::create(['kode_rt' => '031', 'nama_rt' => 'RT 031']);
        $family = $this->createFamily($rt, '3217010101010401', 'Kepala Track');
        $warga = $this->wargaPemegang($family, $rt);
        $anggota = $this->tambahAnggota($family, '3217010101010501', 'Anak Track');

        $this->actingAs($warga)->withHeader('Accept', 'application/json')
            ->put(route('warga.keluarga.anggota.update', $anggota->id), [
                'nama_lengkap' => 'Anak Track',
                'nik' => $anggota->nik,
                'pekerjaan' => 'Karyawan',
                'status_perkawinan' => 'Belum Kawin',
                'pendidikan_terakhir' => 'SMA/Sederajat',
            ])
            ->assertOk();

        $mutasi = MutasiWarga::where('penduduk_id', $anggota->id)
            ->where('jenis_mutasi', 'edit')
            ->firstOrFail();

        $this->assertSame($warga->id, $mutasi->diajukan_oleh);
        $this->assertNotNull($mutasi->diajukan_at);
        // Data lama berisi nilai LAMA penduduk sebelum diubah.
        $this->assertIsArray($mutasi->data_lama);
        $this->assertSame('Pelajar', $mutasi->data_lama['pekerjaan'] ?? null);
    }

    /**
     * (b) RT approve edit: disetujui_rt_oleh + disetujui_rt_at terisi, status menunggu_rw.
     * (c) RW approve edit: diproses_rw_oleh + diproses_rw_at terisi, status disetujui,
     *     activity_log penduduk bertambah (perubahan old→new).
     */
    public function test_approval_rt_lalu_rw_mencatat_approver_waktu_dan_activity_log(): void
    {
        $rt = MasterRt::create(['kode_rt' => '032', 'nama_rt' => 'RT 032']);
        $family = $this->createFamily($rt, '3217010101010402', 'Kepala Track2');
        $anggota = $this->tambahAnggota($family, '3217010101010502', 'Anak Track2');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'edit',
            'data_pengajuan' => [
                'nama_lengkap' => 'Anak Track2',
                'nik' => $anggota->nik,
                'pekerjaan' => 'Wiraswasta',
                'status_perkawinan' => 'Belum Kawin',
            ],
            'data_lama' => [
                'nama_lengkap' => 'Anak Track2',
                'nik' => $anggota->nik,
                'pekerjaan' => 'Pelajar',
                'status_perkawinan' => 'Belum Kawin',
            ],
            'status' => 'menunggu_rt',
        ]);

        $this->ensureRoles();
        $ketuaRt = User::create([
            'nik' => '3217010101010492',
            'username' => 'ketua_rt_track',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaRt->syncRoles(['Ketua RT']);

        // RT approve.
        $this->actingAs($ketuaRt)
            ->post(route('rt.mutasi.approve', $mutasi->id))
            ->assertRedirect();

        $mutasi->refresh();
        $this->assertSame('menunggu_rw', $mutasi->status);
        $this->assertSame($ketuaRt->id, $mutasi->disetujui_rt_oleh);
        $this->assertNotNull($mutasi->disetujui_rt_at);

        // RW approve → data penduduk berubah + activity log.
        $rwUser = User::create([
            'nik' => '3217010101010493',
            'username' => 'admin_rw_track',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $rwUser->syncRoles(['Admin RW']);

        $logPendudukSebelum = \Spatie\Activitylog\Models\Activity::where('log_name', 'penduduk')->count();

        $this->actingAs($rwUser)
            ->post(route('rw.mutasi.approve', $mutasi->id))
            ->assertRedirect();

        $mutasi->refresh();
        $this->assertSame('disetujui', $mutasi->status);
        $this->assertSame($rwUser->id, $mutasi->diproses_rw_oleh);
        $this->assertNotNull($mutasi->diproses_rw_at);

        // Data penduduk aktual berubah.
        $this->assertSame('Wiraswasta', $anggota->fresh()->pekerjaan);

        // Activity log penduduk bertambah (audit old→new otomatis).
        $logPendudukSesudah = \Spatie\Activitylog\Models\Activity::where('log_name', 'penduduk')->count();
        $this->assertGreaterThan($logPendudukSebelum, $logPendudukSesudah);
    }

    /**
     * (d) Reject menyimpan ditolak_oleh + keterangan_tolak.
     */
    public function test_reject_rt_menyimpan_penolak_dan_keterangan(): void
    {
        $rt = MasterRt::create(['kode_rt' => '033', 'nama_rt' => 'RT 033']);
        $family = $this->createFamily($rt, '3217010101010403', 'Kepala Reject');
        $anggota = $this->tambahAnggota($family, '3217010101010503', 'Anak Reject');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'edit',
            'data_pengajuan' => ['nama_lengkap' => 'Anak Reject', 'nik' => $anggota->nik],
            'status' => 'menunggu_rt',
        ]);

        $this->ensureRoles();
        $ketuaRt = User::create([
            'nik' => '3217010101010493',
            'username' => 'ketua_rt_reject',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaRt->syncRoles(['Ketua RT']);

        $this->actingAs($ketuaRt)
            ->post(route('rt.mutasi.reject', $mutasi->id), [
                'alasan_penolakan' => 'Data NIK tidak sesuai dokumen.',
            ])
            ->assertRedirect();

        $mutasi->refresh();
        $this->assertSame('ditolak', $mutasi->status);
        $this->assertSame($ketuaRt->id, $mutasi->ditolak_oleh);
        $this->assertSame('Data NIK tidak sesuai dokumen.', $mutasi->keterangan_tolak);
    }

    /**
     * (e) timeline() mengembalikan tahap yang benar untuk status disetujui.
     */
    public function test_timeline_untuk_status_disetujui(): void
    {
        $rt = MasterRt::create(['kode_rt' => '034', 'nama_rt' => 'RT 034']);
        $family = $this->createFamily($rt, '3217010101010404', 'Kepala Timeline');
        $anggota = $this->tambahAnggota($family, '3217010101010504', 'Anak Timeline');

        $mutasi = MutasiWarga::create([
            'keluarga_id' => $family->id,
            'penduduk_id' => $anggota->id,
            'jenis_mutasi' => 'edit',
            'data_pengajuan' => ['nama_lengkap' => 'Anak Timeline', 'nik' => $anggota->nik],
            'status' => 'disetujui',
            'diajukan_at' => now()->subDays(2),
            'disetujui_rt_at' => now()->subDay(),
            'diproses_rw_at' => now(),
        ]);

        $timeline = $mutasi->timeline();

        // Diajukan, Persetujuan RT, Persetujuan RW, Selesai — semua done.
        $this->assertNotEmpty($timeline);
        $states = array_column($timeline, 'state');
        $this->assertNotContains('rejected', $states);
        $this->assertSame('done', $timeline[0]['state']); // tahap diajukan
        $this->assertSame('done', end($timeline)['state']); // tahap selesai
    }

    public function test_non_pemegang_akun_tidak_dapat_akses_form_pindah_akun(): void
    {
        $rt = MasterRt::create(['kode_rt' => '024', 'nama_rt' => 'RT 024']);
        $family = $this->createFamily($rt, '3217010101010204', 'Kepala Pindah 4');
        $anggota = $this->tambahAnggota($family, '3217010101010304', 'Anggota Non Pemegang');

        $this->ensureRoles();
        $nonPemegang = User::create([
            'nik' => $anggota->nik,
            'username' => 'warga_non_pemegang_pindah',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $nonPemegang->syncRoles(['Warga']);

        $this->actingAs($nonPemegang)
            ->get(route('warga.keluarga.pindah-akun'))
            ->assertRedirect(route('warga.keluarga.index'))
            ->assertSessionHas('error');
    }
}
