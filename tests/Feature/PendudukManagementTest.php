<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menu "Manajemen Penduduk" untuk Admin RW: halaman daftar penduduk dari database,
 * dengan pencarian dan filter RT.
 */
class PendudukManagementTest extends TestCase
{
    use RefreshDatabase;

    private function buatPenduduk(MasterRt $rt, string $nik, string $nama): Penduduk
    {
        $keluarga = Keluarga::create([
            'no_kk' => $nik, // gunakan NIK (unik) sebagai no_kk uji agar tidak bentrok
            'nik_kepala_keluarga' => $nik,
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081200000000',
        ]);

        return Penduduk::create([
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

    private function adminRw(): User
    {
        return User::create([
            'nik' => '3217010101010002',
            'username' => 'Admin RW',
            'role' => 'Admin RW',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);
    }

    public function test_admin_rw_dapat_membuka_daftar_penduduk_dari_database(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index'))
            ->assertOk()
            ->assertSee('Ahmad Dahlan')
            ->assertSee('Siti Walidah');
    }

    public function test_pencarian_penduduk_berfungsi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $html = $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['q' => 'Ahmad']))
            ->assertOk()
            ->getContent();

        // Pencarian `q` hanya memfilter tab Daftar Semua Penduduk; tab Profil Keluarga punya pencarian sendiri (q_kk).
        $tabPenduduk = \Illuminate\Support\Str::after($html, 'id="tab-penduduk"');
        $this->assertStringContainsString('Ahmad Dahlan', $tabPenduduk);
        $this->assertStringNotContainsString('Siti Walidah', \Illuminate\Support\Str::before($tabPenduduk, 'id="modalExportPenduduk"'));
    }

    public function test_pencarian_keluarga_berfungsi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        $this->buatPenduduk($rt, '3217010101011002', 'Siti Walidah');

        $html = $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['q_kk' => 'Siti']))
            ->assertOk()
            ->getContent();

        $tabKk = \Illuminate\Support\Str::between($html, 'id="tab-kk"', 'id="tab-penduduk"');
        $this->assertStringContainsString('Siti Walidah', $tabKk);
        $this->assertStringNotContainsString('Ahmad Dahlan', $tabKk);
    }

    public function test_filter_rt_berfungsi(): void
    {
        $rtA = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $rtB = MasterRt::create(['kode_rt' => '02', 'nama_rt' => 'RT 02']);
        $this->buatPenduduk($rtA, '3217010101011001', 'Warga RT Satu');
        $this->buatPenduduk($rtB, '3217010101011002', 'Warga RT Dua');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['rt_id' => $rtA->id]))
            ->assertOk()
            ->assertSee('Warga RT Satu')
            ->assertDontSee('Warga RT Dua');
    }

    public function test_export_excel_mengunduh_file_xlsx(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');

        $response = $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.export', ['kolom' => ['nik', 'nama_lengkap', 'no_kk', 'rt']]));

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml.sheet',
            (string) $response->headers->get('content-type')
        );
        $this->assertStringContainsString(
            '.xlsx',
            (string) $response->headers->get('content-disposition')
        );
    }

    public function test_export_tanpa_kolom_ditolak(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.export'))
            ->assertSessionHasErrors('kolom');
    }

    public function test_warga_biasa_tidak_dapat_mengakses_manajemen_penduduk(): void
    {
        $warga = User::create([
            'nik' => '3217010101011099',
            'username' => 'Warga Biasa',
            'role' => 'Warga',
            'status_akun' => 'Active',
            'password' => 'password123',
        ]);

        // Middleware role menolak akses (redirect keluar dari route RW).
        $response = $this->actingAs($warga)->get(route('rw.penduduk.index'));
        $this->assertFalse($response->isOk(), 'Warga seharusnya tidak bisa membuka manajemen penduduk.');
        $response->assertRedirect();
    }

    public function test_tampilan_rw_sama_dengan_rt_tanpa_tombol_tambah_data(): void
    {
        $rt = MasterRt::create(['kode_rt' => '07', 'nama_rt' => '07']);
        $warga = $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index'))
            ->assertOk()
            ->assertSee('Profil Keluarga')
            ->assertSee('Daftar Semua Penduduk')
            ->assertSee('name="rt_id"', false)
            ->assertSee('RT 07')
            ->assertSee(route('rw.keluarga.show', $warga->keluarga_id), false)
            ->assertDontSee('Tambah Keluarga')
            ->assertDontSee('data-testid="btn-tambah-keluarga"', false);
    }

    public function test_filter_rt_juga_berlaku_pada_tab_profil_keluarga_dan_export(): void
    {
        $rtA = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $rtB = MasterRt::create(['kode_rt' => '02', 'nama_rt' => '02']);
        $this->buatPenduduk($rtA, '3217010101011001', 'Kepala Satu');
        $this->buatPenduduk($rtB, '3217010101011002', 'Kepala Dua');

        $res = $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['rt_id' => $rtA->id]))
            ->assertOk()
            ->assertSee('3217010101011001')
            ->assertDontSee('3217010101011002');

        // Form export membawa rt_id terpilih.
        $res->assertSee('name="rt_id" value="' . $rtA->id . '"', false);
    }

    public function test_rt_id_tidak_valid_diabaikan(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');

        $this->actingAs($this->adminRw())
            ->get(route('rw.penduduk.index', ['rt_id' => 'bukan-rt']))
            ->assertOk()
            ->assertSee('Ahmad Dahlan')
            ->assertSee('seluruh RW');
    }

    public function test_admin_rw_melihat_detail_keluarga_hanya_baca(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $kepala = $this->buatPenduduk($rt, '3217010101011001', 'Ahmad Dahlan');
        User::create([
            'nik' => $kepala->nik,
            'username' => 'ahmad',
            'role' => 'Warga',
            'status_akun' => 'Unverified',
            'activation_token' => str_repeat('a', 40),
            'password' => 'password123',
        ]);

        $this->actingAs($this->adminRw())
            ->get(route('rw.keluarga.show', $kepala->keluarga_id))
            ->assertOk()
            ->assertSee('Detail Keluarga')
            ->assertSee('Ahmad Dahlan')
            ->assertSee(route('rw.penduduk.index'), false)
            ->assertSee(route('rw.penduduk.show', $kepala->id), false)
            ->assertDontSee('Buat Ulang Link Aktivasi');
    }

    public function test_admin_rw_tidak_punya_route_tambah_keluarga(): void
    {
        $this->actingAs($this->adminRw())->get('/rw/keluarga/create')->assertNotFound();
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('rw.keluarga.create'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('rw.keluarga.store'));
    }
}
