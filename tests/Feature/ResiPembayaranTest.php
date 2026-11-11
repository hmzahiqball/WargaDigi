<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\Pejabat;
use App\Models\Penduduk;
use App\Models\Tagihan;
use App\Models\User;
use App\Services\ResiPembayaranBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Resi pembayaran Iuran Lainnya: kop dan penandatangan mengikuti penerbit
 * (Bendahara RT untuk iuran via RT, Bendahara RW untuk iuran langsung RW).
 */
class ResiPembayaranTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;
    private User $opRt;
    private User $opRw;
    private User $warga;
    private Keluarga $kk;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->rt = MasterRt::create(['kode_rt' => '901', 'nama_rt' => 'RT 901']);
        $this->opRt = $this->user('3217010101014401', 'oprt901', 'Op Keuangan RT', $this->rt);
        $this->opRw = $this->user('3217010101014402', 'oprw901', 'Op Keuangan RW', null);

        $this->kk = Keluarga::create([
            'no_kk' => '3217010101014411', 'nik_kepala_keluarga' => '3217010101014411', 'alamat' => 'Kp. Uji',
            'rt_id' => $this->rt->id, 'no_wa' => '081234567890',
        ]);
        foreach ([['3217010101014411', 'Warga Pembayar'], ['3217010101014401', 'Bendahara Satu'], ['3217010101014402', 'Bendahara Dua']] as [$nik, $nama]) {
            Penduduk::create([
                'keluarga_id' => $this->kk->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
                'status_hubungan_keluarga' => $nik === '3217010101014411' ? 'Kepala Keluarga' : 'Anak', 'status_perkawinan' => 'Belum Kawin',
            ]);
        }

        // Kepengurusan: Bendahara RT 901 = Bendahara Satu; Bendahara RW = Bendahara Dua.
        Pejabat::buatBaru('bendahara_rt', $this->rt->id, '3217010101014401', '2026-01-01');
        Pejabat::buatBaru('bendahara_rw', null, '3217010101014402', '2026-01-01');

        Role::findOrCreate('Warga', 'web');
        $this->warga = $this->user('3217010101014411', 'warga901', 'Warga', $this->rt);
        $this->warga->syncRoles(['Warga']);
    }

    private function user(string $nik, string $username, string $role, ?MasterRt $rt): User
    {
        return User::create([
            'nik' => $nik, 'username' => $username, 'role' => $role,
            'status_akun' => 'Active', 'rt_id' => $rt?->id, 'password' => 'password123',
        ]);
    }

    private function lunas(bool $viaRt, ?User $pengesah, int $nominal = 75000): PembayaranTagihan
    {
        $tagihan = Tagihan::create([
            'id' => (string) Str::uuid(), 'judul' => 'Iuran Perbaikan Got', 'jenis' => 'insidental',
            'nominal' => 50000, 'periode_bulan' => 11, 'periode_tahun' => 2026,
            'unit' => $viaRt ? 'RW' : 'RW', 'rt_id' => null, 'via_rt' => $viaRt, 'status' => 'Active',
        ]);

        return PembayaranTagihan::create([
            'id' => (string) Str::uuid(), 'tagihan_id' => $tagihan->id, 'keluarga_id' => $this->kk->id,
            'status' => 'Paid', 'nominal' => $nominal, 'metode' => 'Cash', 'approved_by' => $pengesah?->id,
            'approved_at' => '2026-11-10 12:00:00',
        ]);
    }

    public function test_resi_iuran_via_rt_berkop_rt_dan_ditandatangani_bendahara_rt(): void
    {
        Storage::disk('local')->put('ttd/ttd-uji.png', 'ttd-bytes');
        Storage::disk('local')->put('ttd/stempel-uji.png', 'stempel-bytes');
        $this->opRt->forceFill(['ttd_default' => 'ttd/ttd-uji.png', 'stempel_default' => 'ttd/stempel-uji.png'])->save();

        $resi = ResiPembayaranBuilder::bangun($this->lunas(true, $this->opRt));

        $this->assertSame('RT', $resi['unit']);
        $this->assertStringContainsString('RUKUN TETANGGA 901', $resi['kop']['baris_1']);
        $this->assertSame('Bendahara RT 901', $resi['jabatan']);
        $this->assertSame('Bendahara Satu', $resi['penandatangan']);
        $this->assertStringStartsWith('data:image/png;base64,', $resi['ttd']);
        $this->assertStringStartsWith('data:image/png;base64,', $resi['stempel']);
        $this->assertTrue($resi['via_rt']);
        $this->assertSame('10 November 2026', $resi['tanggal']);
    }

    public function test_resi_iuran_langsung_rw_berkop_rw_dan_bendahara_rw(): void
    {
        $resi = ResiPembayaranBuilder::bangun($this->lunas(false, $this->opRw));

        $this->assertSame('RW', $resi['unit']);
        $this->assertStringContainsString('RUKUN WARGA', $resi['kop']['baris_1']);
        $this->assertStringNotContainsString('TETANGGA', $resi['kop']['baris_1']);
        $this->assertStringStartsWith('Bendahara RW', $resi['jabatan']);
        $this->assertSame('Bendahara Dua', $resi['penandatangan']);
        $this->assertNull($resi['ttd']); // belum ada TTD default
    }

    public function test_resi_data_lama_tanpa_pengesah_tetap_memakai_bendahara_dari_kepengurusan(): void
    {
        $resi = ResiPembayaranBuilder::bangun($this->lunas(true, null));

        $this->assertSame('Bendahara Satu', $resi['penandatangan']);
        $this->assertSame('Bendahara RT 901', $resi['jabatan']);
        $this->assertNull($resi['ttd']);
    }

    public function test_nama_di_resi_mengikuti_kepengurusan_bukan_nama_akun_dan_kosong_bila_belum_ditetapkan(): void
    {
        // Bendahara diganti setelah pembayaran disahkan: resi lama tetap memakai bendahara pada tanggalnya.
        Pejabat::buatBaru('bendahara_rt', $this->rt->id, '3217010101014402', '2026-12-01');
        $resi = ResiPembayaranBuilder::bangun($this->lunas(true, $this->opRt)); // disahkan 10 Nov 2026
        $this->assertSame('Bendahara Satu', $resi['penandatangan']);

        // Jabatan belum ditetapkan: area nama dikosongkan (tidak memakai username akun).
        Pejabat::query()->where('jabatan', 'bendahara_rw')->delete();
        $rw = ResiPembayaranBuilder::bangun($this->lunas(false, $this->opRw));
        $this->assertNull($rw['penandatangan']);
    }

    public function test_template_resi_memuat_kop_nominal_aktual_dan_penandatangan(): void
    {
        $pembayaran = $this->lunas(true, $this->opRt, 75000);
        $resi = ResiPembayaranBuilder::bangun($pembayaran);

        $html = view('pdf.resi-pembayaran', compact('pembayaran', 'resi'))->render();

        $this->assertStringContainsString('RUKUN TETANGGA 901', $html);
        $this->assertStringContainsString('Rp 75.000', $html); // nominal aktual, bukan nominal minimal Rp 50.000
        $this->assertStringContainsString('Bendahara RT 901', $html);
        $this->assertStringContainsString('Bendahara Satu', $html);
        $this->assertStringContainsString('dikumpulkan lewat RT', $html);
        $this->assertStringNotContainsString('Sistem Informasi dan Manajemen Keuangan Warga', $html);
    }

    public function test_warga_dapat_mengunduh_resi_pdf_dengan_format_baru(): void
    {
        $pembayaran = $this->lunas(true, $this->opRt);

        $res = $this->actingAs($this->warga)->get(route('warga.tagihan.resi', $pembayaran->id));

        $res->assertOk();
        $this->assertStringContainsString('application/pdf', (string) $res->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $res->getContent());
    }

    public function test_halaman_iuran_lainnya_warga_berbentuk_tabel_berpaginasi_tanpa_tombol_resi(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $tagihan = Tagihan::create([
                'id' => (string) Str::uuid(), 'judul' => 'Iuran Uji ' . $i, 'jenis' => 'insidental',
                'nominal' => 10000, 'periode_bulan' => 11, 'periode_tahun' => 2026,
                'unit' => 'RT', 'rt_id' => $this->rt->id, 'status' => 'Active',
            ]);
            PembayaranTagihan::create([
                'id' => (string) Str::uuid(), 'tagihan_id' => $tagihan->id, 'keluarga_id' => $this->kk->id,
                'status' => $i % 2 ? 'Paid' : 'Unpaid', 'nominal' => $i % 2 ? 10000 : null,
            ]);
        }

        $hal1 = $this->actingAs($this->warga)->get(route('warga.tagihan.index'));
        $hal1->assertOk()
            ->assertSee('tabel-iuran-lainnya', false)
            ->assertSee('daftar-iuran-lainnya', false)
            ->assertDontSee('Download Resi');
        $this->assertSame(12, $hal1->viewData('tagihanList')->total());
        $this->assertCount(10, $hal1->viewData('tagihanList')->items());

        $hal2 = $this->actingAs($this->warga)->get(route('warga.tagihan.index', ['page' => 2]));
        $this->assertCount(2, $hal2->viewData('tagihanList')->items());
    }
}
