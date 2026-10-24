<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Paket Pejabat (Opsi B) — penetapan pejabat, riwayat, penanda aktif, dan
 * integrasinya ke nama ketua pada surat.
 */
class PejabatTest extends TestCase
{
    use RefreshDatabase;

    private function penduduk(MasterRt $rt, string $nik, string $nama): Penduduk
    {
        $keluarga = Keluarga::create([
            'no_kk' => $nik, // NIK sudah 16 digit & unik; cukup untuk no_kk uji.
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

    public function test_set_aktif_menutup_periode_lama_dan_menjaga_satu_aktif(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->penduduk($rt, '3217010100000011', 'Ahmad Zaki');
        $this->penduduk($rt, '3217010100000012', 'Budi Baru');

        Pejabat::setAktif('ketua_rt', $rt->id, '3217010100000011');
        Pejabat::setAktif('ketua_rt', $rt->id, '3217010100000012');

        // Hanya satu aktif, riwayat dua baris.
        $this->assertSame(1, Pejabat::aktif()->where('jabatan', 'ketua_rt')->where('rt_id', $rt->id)->count());
        $this->assertSame(2, Pejabat::where('jabatan', 'ketua_rt')->where('rt_id', $rt->id)->count());

        // Pejabat lama ditutup periodenya.
        $lama = Pejabat::where('penduduk_nik', '3217010100000011')->first();
        $this->assertFalse($lama->is_active);
        $this->assertNotNull($lama->selesai_menjabat);

        $this->assertSame('Budi Baru', Pejabat::namaKetuaRt($rt->id));
    }

    public function test_helper_nama_ketua_rw_dan_rt(): void
    {
        $rt = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $this->penduduk($rt, '3217010100000021', 'Dedi Mulyadi');
        $this->penduduk($rt, '3217010100000022', 'Ketua RT Dua');

        Pejabat::setAktif('ketua_rw', null, '3217010100000021');
        Pejabat::setAktif('ketua_rt', $rt->id, '3217010100000022');

        $this->assertSame('Dedi Mulyadi', Pejabat::namaKetuaRw());
        $this->assertSame('Ketua RT Dua', Pejabat::namaKetuaRt($rt->id));
        $this->assertNull(Pejabat::namaKetuaRt(null));
    }

    public function test_superadmin_dapat_menetapkan_pejabat_via_http(): void
    {
        Role::findOrCreate('Admin Aplikasi', 'web');
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => 'RT 03']);
        $this->penduduk($rt, '3217010100000031', 'Pak Ketua');

        $admin = User::create([
            'nik' => null,
            'username' => 'superadmin_uji',
            'role' => 'Admin Aplikasi',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
        $admin->syncRoles(['Admin Aplikasi']);

        $this->actingAs($admin)
            ->post(route('admin.pejabat.store'), [
                'jabatan' => 'ketua_rt',
                'rt_id' => $rt->id,
                'penduduk_nik' => '3217010100000031',
            ])
            ->assertRedirect(route('admin.pejabat.index'))
            ->assertSessionHas('success');

        $this->assertSame('Pak Ketua', Pejabat::namaKetuaRt($rt->id));
    }

    public function test_preview_surat_rt_menampilkan_nama_pejabat_bukan_username(): void
    {
        Role::findOrCreate('Ketua RT', 'web');
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketuaRwPenduduk = $this->penduduk($rt, '3217010100000040', 'Dedi Mulyadi');
        $ketuaRtPenduduk = $this->penduduk($rt, '3217010100000041', 'Ahmad Zaki');
        $pemohon = $this->penduduk($rt, '3217010100000042', 'Warga Pemohon');

        Pejabat::setAktif('ketua_rw', null, $ketuaRwPenduduk->nik);
        Pejabat::setAktif('ketua_rt', $rt->id, $ketuaRtPenduduk->nik);

        // Akun Ketua RT (pengurus) tanpa NIK, username berbeda dari nama pejabat.
        $ketuaAkun = User::create([
            'nik' => null,
            'username' => 'ketuart_uji',
            'role' => 'Ketua RT',
            'status_akun' => 'Active',
            'rt_id' => $rt->id,
            'password' => 'password123',
        ]);
        $ketuaAkun->syncRoles(['Ketua RT']);

        $surat = PengajuanSurat::create([
            'penduduk_id' => $pemohon->id,
            'tipe_surat' => 'Surat Keterangan Domisili',
            'status' => 'Selesai',
            'keterangan_tambahan' => 'Uji',
            'tanggal_selesai' => now(),
        ]);

        $html = $this->actingAs($ketuaAkun)
            ->get(route('rt.surat.preview', $surat->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Ahmad Zaki', $html);
        $this->assertStringContainsString('Dedi Mulyadi', $html);
        $this->assertStringNotContainsString('ketuart_uji', $html);
    }
}
