<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Fitur dua mode TTD/stempel pengesahan surat dan penyimpanan default per akun.
 */
class TandaTanganDefaultTest extends TestCase
{
    use RefreshDatabase;

    private const PNG_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';

    private function ketuaRt(): User
    {
        Role::findOrCreate('Ketua RT', 'web');
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => 'RT 03']);
        $u = User::create([
            'nik' => null, 'username' => 'ketuart03', 'role' => 'Ketua RT',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $u->syncRoles(['Ketua RT']);

        return $u;
    }

    private function suratDiajukan(User $ketua): PengajuanSurat
    {
        Role::findOrCreate('Warga', 'web');
        $nik = '3217010100000801';
        $keluarga = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Uji',
            'rt_id' => $ketua->rt_id, 'no_wa' => '081200000801',
        ]);
        $penduduk = Penduduk::create([
            'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => 'Warga Uji',
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-02',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);

        return PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Keterangan Domisili', 'status' => 'Diajukan',
        ]);
    }

    public function test_simpan_ttd_default_dari_gambar(): void
    {
        $ketua = $this->ketuaRt();

        $this->actingAs($ketua)
            ->post(route('pengaturan.ttd.update'), ['ttd_base64' => self::PNG_BASE64])
            ->assertRedirect(route('pengaturan.ttd.edit'));

        $ketua->refresh();
        $this->assertNotNull($ketua->ttd_default);
        $this->assertTrue(Storage::disk('local')->exists($ketua->ttd_default));
    }

    public function test_simpan_stempel_default_dari_file(): void
    {
        $ketua = $this->ketuaRt();

        $this->actingAs($ketua)
            ->post(route('pengaturan.ttd.update'), [
                'stempel_file' => UploadedFile::fake()->image('stempel.png', 200, 200),
            ])
            ->assertRedirect();

        $ketua->refresh();
        $this->assertNotNull($ketua->stempel_default);
    }

    public function test_hapus_ttd_default(): void
    {
        $ketua = $this->ketuaRt();
        $this->actingAs($ketua)->post(route('pengaturan.ttd.update'), ['ttd_base64' => self::PNG_BASE64]);
        $ketua->refresh();
        $pathLama = $ketua->ttd_default;

        $this->actingAs($ketua)->post(route('pengaturan.ttd.update'), ['hapus_ttd' => '1'])->assertRedirect();

        $ketua->refresh();
        $this->assertNull($ketua->ttd_default);
        $this->assertFalse(Storage::disk('local')->exists($pathLama));
    }

    public function test_file_ttd_hanya_bisa_diakses_pemilik(): void
    {
        $ketua = $this->ketuaRt();
        $this->actingAs($ketua)->post(route('pengaturan.ttd.update'), ['ttd_base64' => self::PNG_BASE64]);

        $this->actingAs($ketua->fresh())
            ->get(route('pengaturan.ttd.file', ['jenis' => 'ttd']))
            ->assertOk();
    }

    public function test_approve_rt_pakai_ttd_default_menyalin_ke_pengajuan(): void
    {
        $ketua = $this->ketuaRt();
        $this->actingAs($ketua)->post(route('pengaturan.ttd.update'), ['ttd_base64' => self::PNG_BASE64]);
        $ketua->refresh();

        $surat = $this->suratDiajukan($ketua);

        $this->actingAs($ketua)
            ->post(route('rt.surat.approve', $surat->id), ['pakai_ttd_default_rt' => '1', 'nomor_urut' => '021'])
            ->assertRedirect();

        $surat->refresh();
        $this->assertSame('Disetujui RT', $surat->status);
        $this->assertNotNull($surat->ttd_rt);
        $this->assertTrue(Storage::disk('public')->exists($surat->ttd_rt));
    }

    public function test_approve_rt_gambar_manual_dan_simpan_default(): void
    {
        $ketua = $this->ketuaRt();
        $surat = $this->suratDiajukan($ketua);

        $this->assertNull($ketua->ttd_default);

        $this->actingAs($ketua)
            ->post(route('rt.surat.approve', $surat->id), [
                'signature_rt' => self::PNG_BASE64,
                'simpan_ttd_default_rt' => '1',
            ])
            ->assertRedirect();

        $surat->refresh();
        $ketua->refresh();
        $this->assertNotNull($surat->ttd_rt);
        $this->assertNotNull($ketua->ttd_default, 'TTD manual harus tersimpan sebagai default.');
    }
}
