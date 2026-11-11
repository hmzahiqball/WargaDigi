<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Services\SuratDataBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Surat pengantar langsung terisi dari DB sesuai jenis, dengan nomor urut manual.
 */
class SuratTerisiTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(string $tipe, string $status = 'Diajukan'): array
    {
        Role::findOrCreate('Warga', 'web');
        $rt = MasterRt::create(['kode_rt' => '03', 'nama_rt' => 'RT 03']);
        $nik = '3217010100000901';
        $keluarga = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Uji No. 1',
            'rt_id' => $rt->id, 'no_wa' => '081200000901',
        ]);
        $penduduk = Penduduk::create([
            'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => 'Budi Uji',
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-02',
            'agama' => 'Katolik', 'pekerjaan' => 'Pelajar/Mahasiswa',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Belum Kawin',
        ]);
        $user = User::create([
            'nik' => $nik, 'username' => 'wargauji', 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);
        $surat = PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => $tipe, 'status' => $status,
            'nomor_urut' => '012', 'keterangan_tambahan' => 'Pembuatan rekening',
        ]);

        return [$user, $surat];
    }

    public function test_semua_jenis_surat_dirender_langsung_terisi(): void
    {
        foreach (array_keys(config('surat.jenis')) as $tipe) {
            [, $surat] = $this->siapkan($tipe);
            $data = app(SuratDataBuilder::class)->build($surat->fresh(), true);
            View::share('errors', new ViewErrorBag);
            $html = view('pdf.surat', compact('data'))->render();

            $this->assertStringContainsString('Budi Uji', $html, $tipe);
            $this->assertStringContainsString('3217010100000901', $html, $tipe);
            $this->assertStringContainsString('012', $html, $tipe);
            $this->assertStringContainsString('Katolik', $html, $tipe);
            $this->assertStringNotContainsString('lingkari', strtolower($html), $tipe);

            \DB::table('pengajuan_surat')->delete();
            \DB::table('penduduk')->delete();
            \DB::table('keluarga')->delete();
            \DB::table('users')->delete();
            \DB::table('master_rt')->delete();
        }
    }

    public function test_nomor_urut_kosong_dicetak_titik_titik(): void
    {
        [, $surat] = $this->siapkan('Surat Keterangan Domisili');
        $surat->update(['nomor_urut' => null]);
        $data = app(SuratDataBuilder::class)->build($surat->fresh(), true);
        View::share('errors', new ViewErrorBag);
        $this->assertStringContainsString('............', view('pdf.surat', compact('data'))->render());
    }

    public function test_unduh_pdf_surat_selesai(): void
    {
        [$user, $surat] = $this->siapkan('Surat Keterangan Domisili', 'Selesai');
        $surat->update(['tanggal_selesai' => now()]);

        $this->actingAs($user)
            ->get(route('warga.surat.download', $surat->id))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_unduh_pdf_ditolak_bila_belum_selesai(): void
    {
        [$user, $surat] = $this->siapkan('Surat Keterangan Domisili', 'Diajukan');

        $this->actingAs($user)->get(route('warga.surat.download', $surat->id))->assertForbidden();
    }

    public function test_approve_rt_menyimpan_nomor_urut(): void
    {
        [$warga, $surat] = $this->siapkan('Surat Keterangan Domisili');
        $surat->update(['nomor_urut' => null]);
        Role::findOrCreate('Ketua RT', 'web');
        $rt = User::create([
            'nik' => null, 'username' => 'ketuart03', 'role' => 'Ketua RT',
            'status_akun' => 'Active', 'rt_id' => $warga->rt_id, 'password' => 'password123',
        ]);
        $rt->syncRoles(['Ketua RT']);

        $this->actingAs($rt)
            ->post(route('rt.surat.approve', $surat->id), ['nomor_urut' => '045'])
            ->assertRedirect();

        $this->assertSame('045', $surat->fresh()->nomor_urut);
        $this->assertSame('Disetujui RT', $surat->fresh()->status);
    }

    public function test_approve_rw_menyimpan_nomor_urut(): void
    {
        [, $surat] = $this->siapkan('Surat Keterangan Domisili', 'Disetujui RT');
        Role::findOrCreate('Ketua RW', 'web');
        $rw = User::create([
            'nik' => null, 'username' => 'ketuarw', 'role' => 'Ketua RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $rw->syncRoles(['Ketua RW']);

        $this->actingAs($rw)
            ->post(route('rw.surat.approve', $surat->id), ['nomor_urut' => '077'])
            ->assertRedirect();

        $this->assertSame('077', $surat->fresh()->nomor_urut);
        $this->assertSame('Selesai', $surat->fresh()->status);
    }

    public function test_snapshot_disimpan_saat_rw_sahkan_dan_tidak_berubah_saat_data_diedit(): void
    {
        [, $surat] = $this->siapkan('Surat Keterangan Domisili', 'Disetujui RT');
        Role::findOrCreate('Ketua RW', 'web');
        $rw = User::create([
            'nik' => null, 'username' => 'ketuarw2', 'role' => 'Ketua RW',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $rw->syncRoles(['Ketua RW']);

        $this->actingAs($rw)->post(route('rw.surat.approve', $surat->id), ['nomor_urut' => '088']);

        $surat = $surat->fresh();
        $this->assertSame('Budi Uji', $surat->snapshot_surat['nama_lengkap']);

        $surat->penduduk->update(['nama_lengkap' => 'Nama Sudah Diubah', 'pekerjaan' => 'Guru']);
        $data = app(SuratDataBuilder::class)->build($surat->fresh(), false);

        $this->assertSame('Budi Uji', $data->nama_lengkap);
        $this->assertSame('Pelajar/Mahasiswa', $data->pekerjaan);
        $this->assertSame('088', $data->nomor_urut);
    }

    public function test_data_tambahan_tersimpan_dan_tercetak_di_surat_kelahiran(): void
    {
        [$warga, $surat] = $this->siapkan('Surat Kelahiran');
        $surat->delete();

        $this->actingAs($warga)->post(route('warga.surat.store'), [
            'tipe_surat' => 'Surat Kelahiran',
            'keperluan' => 'akta kelahiran',
            'pernyataan' => '1',
            'data_tambahan' => [
                'nama_bayi' => 'Al Fatih',
                'jenis_kelamin_bayi' => 'Laki-laki',
                'tanggal_lahir_bayi' => '2026-09-01',
                'jam_lahir_bayi' => '04:30',
                'tempat_lahir_bayi' => 'RSUD Cibabat',
                'field_liar' => 'harus dibuang',
            ],
        ])->assertRedirect();

        $baru = PengajuanSurat::where('tipe_surat', 'Surat Kelahiran')->latest()->first();
        $this->assertSame('Al Fatih', $baru->data_tambahan['nama_bayi']);
        $this->assertArrayNotHasKey('field_liar', $baru->data_tambahan);

        $data = app(SuratDataBuilder::class)->build($baru, true);
        $labels = array_column($data->tambahan_rows, 'label');
        $nilai = array_column($data->tambahan_rows, 'nilai');
        $this->assertContains('Nama Bayi', $labels);
        $this->assertContains('Al Fatih', $nilai);
        $this->assertContains('1 September 2026', $nilai);
        $this->assertContains('04:30 WIB', $nilai);
    }

    public function test_isian_tambahan_kosong_tidak_disimpan(): void
    {
        [$warga, $surat] = $this->siapkan('Surat Keterangan Serbaguna');
        $surat->delete();

        $this->actingAs($warga)->post(route('warga.surat.store'), [
            'tipe_surat' => 'Surat Keterangan Serbaguna',
            'keperluan' => 'keperluan umum',
            'pernyataan' => '1',
        ])->assertRedirect();

        $baru = PengajuanSurat::where('tipe_surat', 'Surat Keterangan Serbaguna')->latest()->first();
        $this->assertNull($baru->data_tambahan);
    }
}
