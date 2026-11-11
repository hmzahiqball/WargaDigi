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

/**
 * Memastikan halaman Permohonan Surat ter-render dengan tata letak baru:
 * tombol Riwayat, card "Sedang Diproses", dan judul "Ajukan Permohonan Surat".
 */
class PermohonanSuratRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_permohonan_surat_render_dengan_tata_letak_baru(): void
    {
        Role::findOrCreate('Warga', 'web');
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => 'RT 03']);
        $nik = '3217010100000701';
        $keluarga = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Uji',
            'rt_id' => $rt->id, 'no_wa' => '081200000701',
        ]);
        $penduduk = Penduduk::create([
            'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => 'Warga Uji',
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-02',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);
        $user = User::create([
            'nik' => $nik, 'username' => 'wargauji7', 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        foreach (['Diajukan', 'Selesai', 'Ditolak RT'] as $i => $status) {
            PengajuanSurat::create([
                'penduduk_id' => $penduduk->id,
                'tipe_surat' => 'Surat Keterangan Domisili',
                'nomor_urut' => (string) (10 + $i),
                'status' => $status,
                'tanggal_selesai' => $status === 'Selesai' ? now() : null,
                'catatan_rt' => $status === 'Ditolak RT' ? 'Data belum lengkap' : null,
            ]);
        }

        $this->actingAs($user)
            ->get(route('warga.surat.index'))
            ->assertOk()
            ->assertSee('Ajukan Permohonan Surat')
            ->assertSee('Riwayat Pengajuan Surat')
            ->assertSee('Sedang Diproses')
            ->assertSee('Menunggu RT')
            ->assertSee('Data belum lengkap');
    }
}
