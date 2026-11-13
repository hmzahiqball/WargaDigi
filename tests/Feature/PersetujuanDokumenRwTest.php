<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\PengajuanSurat;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi: halaman Persetujuan Dokumen RW sempat galat 500 ("Undefined property stdClass::$nomor_urut")
 * karena data yang dikirim ke tampilan tidak memuat semua properti yang dipakai modal tanda tangan.
 */
class PersetujuanDokumenRwTest extends TestCase
{
    use RefreshDatabase;

    private function pengajuan(string $status, ?string $nomorUrut = null): PengajuanSurat
    {
        $rt = MasterRt::firstOrCreate(['kode_rt' => '07'], ['nama_rt' => 'RT 07']);
        $nik = '32170101010' . str_pad((string) random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
        $keluarga = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Mawar 1', 'rt_id' => $rt->id, 'no_wa' => '081234567890',
        ]);
        $penduduk = Penduduk::create([
            'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => 'Pemohon ' . $status, 'jenis_kelamin' => 'P',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-05-01', 'agama' => 'Islam', 'pekerjaan' => 'Guru',
            'pendidikan_terakhir' => 'S1', 'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);

        return PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Keterangan Domisili', 'keterangan_tambahan' => 'Keperluan administrasi',
            'status' => $status, 'nomor_urut' => $nomorUrut,
        ]);
    }

    private function adminRw(): User
    {
        return User::create([
            'nik' => '3217010101013201', 'username' => 'adminrwdok', 'role' => 'Admin RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
    }

    public function test_halaman_persetujuan_dokumen_rw_terbuka_dengan_pengajuan_yang_ada(): void
    {
        $this->pengajuan('Disetujui RT', '012');
        $this->pengajuan('Diajukan');

        $this->actingAs($this->adminRw())->get(route('rw.persetujuan-dokumen'))
            ->assertOk()
            ->assertSee('Pemohon Disetujui RT')
            ->assertSee('Pemohon Diajukan');
    }

    public function test_modal_pengesahan_memuat_nomor_urut_rt_dan_pendidikan_pemohon(): void
    {
        $item = $this->pengajuan('Disetujui RT', '045');

        $html = $this->actingAs($this->adminRw())->get(route('rw.persetujuan-dokumen'))->assertOk()->getContent();
        file_put_contents(sys_get_temp_dir() . '/dok_html.txt', $html);

        // Modal pengesahan memuat nomor urut tersimpan; modal detail memuat RT dan pendidikan pemohon (bukan nilai bawaan).
        $this->assertStringContainsString('id="nomorUrutRW' . $item->id . '"', $html);
        $this->assertMatchesRegularExpression('/id="nomorUrutRW' . preg_quote((string) $item->id, '/') . '"[^>]*value="045"/', $html);
        $this->assertStringContainsString('RT 07 / RW', $html);
        $this->assertStringContainsString('>S1<', $html);
    }

    public function test_pengajuan_tanpa_nomor_urut_tetap_terbuka_dengan_isian_kosong(): void
    {
        $item = $this->pengajuan('Disetujui RT', null);

        $this->actingAs($this->adminRw())->get(route('rw.persetujuan-dokumen'))->assertOk()
            ->assertSee('id="nomorUrutRW' . $item->id . '"', false);
    }
}
