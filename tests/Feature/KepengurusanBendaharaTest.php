<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\Pejabat;
use App\Models\Penduduk;
use App\Models\User;
use App\Services\PenandatanganDokumen;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Kepengurusan: Sekretaris dan Bendahara (RW, RT, DKM) diambil dari warga,
 * dan nama Bendahara/Ketua dipakai pada dokumen sesuai periode.
 */
class KepengurusanBendaharaTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rt = MasterRt::create(['kode_rt' => '951', 'nama_rt' => 'RT 951']);
        $keluarga = Keluarga::create([
            'no_kk' => '3217010101013301', 'nik_kepala_keluarga' => '3217010101013301', 'alamat' => 'Kp. Uji',
            'rt_id' => $this->rt->id, 'no_wa' => '081234567890',
        ]);
        foreach ([['3217010101013301', 'Warga A'], ['3217010101013302', 'Warga B'], ['3217010101013303', 'Warga C']] as [$nik, $nama]) {
            Penduduk::create([
                'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1985-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
                'status_hubungan_keluarga' => $nik === '3217010101013301' ? 'Kepala Keluarga' : 'Anak', 'status_perkawinan' => 'Kawin',
            ]);
        }

        Role::findOrCreate('Admin Aplikasi', 'web');
        $this->admin = User::create([
            'nik' => null, 'username' => 'superadmin_bdh', 'role' => 'Admin Aplikasi',
            'status_akun' => 'Active', 'password' => 'password123',
        ]);
        $this->admin->syncRoles(['Admin Aplikasi']);
    }

    private function tetapkan(array $data)
    {
        return $this->actingAs($this->admin)->post(route('admin.pejabat.store'), array_merge([
            'mulai_menjabat' => '2026-01-01',
        ], $data));
    }

    public function test_admin_menetapkan_bendahara_dan_sekretaris_dari_warga(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301'])
            ->assertRedirect(route('admin.pejabat.index'))->assertSessionHas('success');
        $this->tetapkan(['jabatan' => 'sekretaris_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302'])
            ->assertSessionHas('success');
        $this->tetapkan(['jabatan' => 'bendahara_rw', 'penduduk_nik' => '3217010101013303'])->assertSessionHas('success');
        $this->tetapkan(['jabatan' => 'sekretaris_rw', 'penduduk_nik' => '3217010101013302'])->assertSessionHas('success');
        $this->tetapkan(['jabatan' => 'bendahara_dkm', 'penduduk_nik' => '3217010101013301'])->assertSessionHas('success');

        $this->assertSame('Warga A', Pejabat::namaPada('bendahara_rt', $this->rt->id));
        $this->assertSame('Warga B', Pejabat::namaPada('sekretaris_rt', $this->rt->id));
        $this->assertSame('Warga C', Pejabat::namaPada('bendahara_rw', null));
        $this->assertSame('Warga B', Pejabat::namaPada('sekretaris_rw', null));
        $this->assertSame('Warga A', Pejabat::namaPada('bendahara_dkm', null));

        // Jabatan tingkat RW dan DKM tidak terikat RT, walau rt_id ikut terkirim.
        $this->tetapkan(['jabatan' => 'bendahara_rw', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302']);
        $this->assertNull(Pejabat::aktif()->where('jabatan', 'bendahara_rw')->first()->rt_id);
    }

    public function test_validasi_jabatan_dan_rt(): void
    {
        // Jabatan tingkat RT wajib memilih RT.
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'penduduk_nik' => '3217010101013301'])->assertSessionHasErrors('rt_id');
        // Jabatan tidak dikenal ditolak.
        $this->tetapkan(['jabatan' => 'wakil_ketua', 'penduduk_nik' => '3217010101013301'])->assertSessionHasErrors('jabatan');
        // Penduduk harus ada.
        $this->tetapkan(['jabatan' => 'bendahara_rw', 'penduduk_nik' => '9999999999999999'])->assertSessionHasErrors('penduduk_nik');
        $this->assertSame(0, Pejabat::count());
    }

    public function test_pergantian_bendahara_hanya_satu_aktif_dan_riwayat_tersimpan(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301']);
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302', 'mulai_menjabat' => '2026-06-01']);

        $this->assertSame(1, Pejabat::aktif()->where('jabatan', 'bendahara_rt')->where('rt_id', $this->rt->id)->count());
        $this->assertSame(2, Pejabat::where('jabatan', 'bendahara_rt')->count());
    }

    public function test_nama_dipilih_sesuai_periode_bukan_hanya_pejabat_aktif(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301', 'mulai_menjabat' => '2026-01-01']);
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302', 'mulai_menjabat' => '2026-07-01']);

        $this->assertSame('Warga A', Pejabat::namaPada('bendahara_rt', $this->rt->id, '2026-03-15'));
        $this->assertSame('Warga B', Pejabat::namaPada('bendahara_rt', $this->rt->id, '2026-08-15'));
        // Tanpa tanggal: pejabat aktif saat ini.
        $this->assertSame('Warga B', Pejabat::namaPada('bendahara_rt', $this->rt->id));
        // Sebelum siapa pun menjabat: kembali ke pejabat aktif.
        $this->assertSame('Warga B', Pejabat::namaPada('bendahara_rt', $this->rt->id, '2025-01-01'));
    }

    public function test_penandatangan_laporan_memakai_pejabat_pada_akhir_periode(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301', 'mulai_menjabat' => '2026-01-01']);
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302', 'mulai_menjabat' => '2026-07-01']);
        $this->tetapkan(['jabatan' => 'ketua_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013303', 'mulai_menjabat' => '2026-01-01']);
        $this->tetapkan(['jabatan' => 'ketua_rw', 'penduduk_nik' => '3217010101013301', 'mulai_menjabat' => '2026-01-01']);

        $mei = new LaporanKeuangan(['unit' => 'RT', 'rt_id' => $this->rt->id, 'periode_bulan' => 5, 'periode_tahun' => 2026]);
        $agustus = new LaporanKeuangan(['unit' => 'RT', 'rt_id' => $this->rt->id, 'periode_bulan' => 8, 'periode_tahun' => 2026]);

        $this->assertSame(['ketua' => 'Warga C', 'bendahara' => 'Warga A'], PenandatanganDokumen::laporan($mei));
        $this->assertSame('Warga B', PenandatanganDokumen::laporan($agustus)['bendahara']);

        // Laporan RW disetujui Ketua RW; Bendahara RW belum ditetapkan -> null (area nama dikosongkan).
        $rw = new LaporanKeuangan(['unit' => 'RW', 'rt_id' => null, 'periode_bulan' => 5, 'periode_tahun' => 2026]);
        $this->assertSame(['ketua' => 'Warga A', 'bendahara' => null], PenandatanganDokumen::laporan($rw));
    }

    public function test_halaman_kepengurusan_memuat_bendahara_dan_sekretaris(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301']);

        $this->actingAs($this->admin)->get(route('admin.pejabat.index'))
            ->assertOk()
            ->assertSee('Bendahara')
            ->assertSee('Sekretaris')
            ->assertSee('tabel-kepengurusan', false)
            ->assertSee('Set Ketua RT ' . $this->rt->kode_rt)
            ->assertSee('Set Sekretaris RT ' . $this->rt->kode_rt)
            ->assertSee('Set Bendahara RT ' . $this->rt->kode_rt)
            ->assertSee('Set Bendahara DKM')
            ->assertDontSee('Set Ketua DKM')
            ->assertSee('Warga A');
    }

    public function test_riwayat_dimuat_per_jabatan_dan_unit(): void
    {
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013301', 'mulai_menjabat' => '2026-01-01']);
        $this->tetapkan(['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013302', 'mulai_menjabat' => '2026-07-01']);
        $this->tetapkan(['jabatan' => 'sekretaris_rt', 'rt_id' => $this->rt->id, 'penduduk_nik' => '3217010101013303']);

        $res = $this->actingAs($this->admin)->get(route('admin.pejabat.riwayat', ['jabatan' => 'bendahara_rt', 'rt_id' => $this->rt->id]));
        $res->assertOk()->assertSee('Warga A')->assertSee('Warga B')->assertDontSee('Warga C');

        $this->actingAs($this->admin)->get(route('admin.pejabat.riwayat', ['jabatan' => 'wakil']))->assertSessionHasErrors('jabatan');
    }
}