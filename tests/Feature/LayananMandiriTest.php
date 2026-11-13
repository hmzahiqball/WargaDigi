<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Layanan Mandiri: kartu sesuai jenis surat yang ada, wajib login, jenis terpilih otomatis. */
class LayananMandiriTest extends TestCase
{
    use RefreshDatabase;

    public function test_kartu_layanan_sama_dengan_jenis_surat_yang_bisa_diajukan(): void
    {
        $res = $this->get(route('layanan-mandiri'))->assertOk();
        $judul = collect($res->viewData('services'))->pluck('title')->all();

        $this->assertSame(PengajuanSurat::TIPE_SURAT, $judul);
        $res->assertDontSee('Izin Keramaian')->assertDontSee('Pengantar KTP/KK');
        $this->assertSame(count(PengajuanSurat::TIPE_SURAT), substr_count($res->getContent(), 'data-testid="kartu-layanan"'));
        foreach (collect($res->viewData('services')) as $s) {
            $this->assertNotSame('', $s['desc'], "Deskripsi {$s['title']} kosong.");
        }
    }

    public function test_klik_kartu_tanpa_login_diarahkan_ke_halaman_login(): void
    {
        $url = collect($this->get(route('layanan-mandiri'))->viewData('services'))->firstWhere('title', 'Surat Kelahiran')['url'];

        $this->get(route('layanan-mandiri'))->assertSee('Masuk untuk Mengajukan');
        $this->get($url)->assertRedirect(route('login'));
    }

    public function test_warga_login_langsung_mendapat_jenis_surat_terpilih(): void
    {
        Role::findOrCreate('Warga', 'web');
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $nik = '3217010101016001';
        $kk = Keluarga::create(['no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat', 'rt_id' => $rt->id, 'no_wa' => '081200000000']);
        Penduduk::create(['keluarga_id' => $kk->id, 'nik' => $nik, 'nama_lengkap' => 'Warga Uji', 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin']);
        $warga = User::create(['nik' => $nik, 'username' => 'wargauji', 'role' => 'Warga', 'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123']);
        $warga->syncRoles(['Warga']);

        $this->actingAs($warga)->get(route('warga.surat.index', ['jenis' => 'Surat Kelahiran']))
            ->assertOk()->assertSee('const jenisAwal = "Surat Kelahiran"', false);

        // Jenis yang tidak dikenal diabaikan.
        $this->actingAs($warga)->get(route('warga.surat.index', ['jenis' => 'Izin Keramaian']))
            ->assertOk()->assertSee('const jenisAwal = null', false);
    }
}
