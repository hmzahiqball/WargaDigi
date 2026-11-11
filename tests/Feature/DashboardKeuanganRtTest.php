<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\IuranPembayaran;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dashboard Operator Keuangan RT: angka dibatasi ketat pada RT sendiri,
 * tidak menampilkan kas RW/DKM, dan memuat progres iuran + hal yang perlu tindakan.
 */
class DashboardKeuanganRtTest extends TestCase
{
    use RefreshDatabase;

    private function bendahara(MasterRt $rt): User
    {
        return User::create([
            'nik' => '3217010101018801', 'username' => 'bendahararut', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
    }

    private function kk(MasterRt $rt, string $nik, string $nama): Keluarga
    {
        $k = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id, 'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $k->id, 'nik' => $nik, 'nama_lengkap' => $nama, 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta', 'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);

        return $k;
    }

    private function trx(string $unit, ?string $rtId, string $tipe, int $jumlah, string $status = 'Verified', ?string $tanggal = null): void
    {
        static $n = 0;
        $n++;
        TransaksiKeuangan::create([
            'kode_transaksi' => 'TRX-DSB-' . $n, 'tipe' => $tipe, 'kategori' => $tipe === 'pemasukan' ? 'Iuran' : 'Kebersihan',
            'judul' => 'Uji ' . $n, 'jumlah' => $jumlah, 'tanggal' => $tanggal ?? now()->toDateString(),
            'status' => $status, 'unit_sumber' => $unit, 'rt_id' => $rtId,
        ]);
    }

    public function test_saldo_hanya_dari_rt_sendiri_dan_tanpa_kas_rw_dkm(): void
    {
        $rt = MasterRt::create(['kode_rt' => '501', 'nama_rt' => 'RT 501']);
        $rtLain = MasterRt::create(['kode_rt' => '502', 'nama_rt' => 'RT 502']);
        $user = $this->bendahara($rt);

        $this->trx('RT', $rt->id, 'pemasukan', 500000);
        $this->trx('RT', $rt->id, 'pengeluaran', 120000);
        $this->trx('RT', $rt->id, 'pengeluaran', 999000, 'Rejected'); // tidak dihitung
        $this->trx('RT', $rtLain->id, 'pemasukan', 7000000);            // RT lain tidak bocor
        $this->trx('RW', null, 'pemasukan', 8000000);                   // kas RW tidak tampil

        $res = $this->actingAs($user)->get(route('opkeuangan.dashboard'));
        $res->assertOk()
            ->assertSee('Rp 380.000')
            ->assertDontSee('7.000.000')
            ->assertDontSee('8.000.000')
            ->assertDontSee('Kas RW')
            ->assertDontSee('Sheet Saldo');
    }

    public function test_progres_iuran_dan_penunggak(): void
    {
        $rt = MasterRt::create(['kode_rt' => '503', 'nama_rt' => 'RT 503']);
        $user = $this->bendahara($rt);
        $kkA = $this->kk($rt, '3217010101018811', 'Warga Lunas');
        $this->kk($rt, '3217010101018812', 'Warga Nunggak');

        $master = MasterIuran::create([
            'rt_id' => $rt->id, 'tahun' => (int) now()->format('Y'), 'nama' => 'Iuran Wajib',
            'nominal' => 50000, 'aktif' => true,
        ]);
        // Warga Lunas membayar semua bulan sampai bulan berjalan.
        for ($b = 1; $b <= (int) now()->format('n'); $b++) {
            IuranPembayaran::create([
                'master_iuran_id' => $master->id, 'keluarga_id' => $kkA->id, 'bulan' => $b,
                'status' => 'lunas', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai', 'sumber' => 'bendahara',
            ]);
        }

        $res = $this->actingAs($user)->get(route('opkeuangan.dashboard'));
        $res->assertOk()
            ->assertSee('1 dari 2 KK lunas')
            ->assertSee('Warga Nunggak')
            ->assertDontSee('Warga Lunas')
            ->assertSee('Rp 50.000 dari target Rp 100.000', false);
    }

    public function test_perlu_tindakan_menampilkan_pengajuan_iuran_menunggu(): void
    {
        $rt = MasterRt::create(['kode_rt' => '504', 'nama_rt' => 'RT 504']);
        $user = $this->bendahara($rt);
        $kk = $this->kk($rt, '3217010101018821', 'Warga Ajukan');
        $master = MasterIuran::create([
            'rt_id' => $rt->id, 'tahun' => (int) now()->format('Y'), 'nama' => 'Iuran Wajib',
            'nominal' => 50000, 'aktif' => true,
        ]);
        $batch = (string) \Illuminate\Support\Str::uuid();
        foreach ([1, 2] as $b) {
            IuranPembayaran::create([
                'master_iuran_id' => $master->id, 'keluarga_id' => $kk->id, 'bulan' => $b,
                'status' => 'menunggu', 'tanggal_bayar' => now()->toDateString(), 'metode' => 'transfer',
                'sumber' => 'warga', 'batch_id' => $batch,
            ]);
        }

        // Satu batch dua bulan = satu pengajuan.
        $this->actingAs($user)->get(route('opkeuangan.dashboard'))
            ->assertOk()
            ->assertSee('Pengajuan iuran bulanan menunggu verifikasi');
    }

    public function test_lonceng_iuran_lainnya_kirim_notifikasi_aplikasi_tanpa_wa_email(): void
    {
        $rt = MasterRt::create(['kode_rt' => '506', 'nama_rt' => 'RT 506']);
        $user = $this->bendahara($rt);
        $kk = $this->kk($rt, '3217010101018831', 'Warga Pengingat');
        $akunWarga = User::create([
            'nik' => '3217010101018831', 'username' => 'wargapengingat', 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $tagihan = \App\Models\Tagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'judul' => 'Iuran Kegiatan',
            'jenis' => 'insidental', 'nominal' => 50000, 'periode_bulan' => (int) now()->format('n'),
            'periode_tahun' => (int) now()->format('Y'), 'unit' => 'RT', 'rt_id' => $rt->id,
            'pembuat_id' => $user->id, 'status' => 'Active',
        ]);
        $bayar = \App\Models\PembayaranTagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'tagihan_id' => $tagihan->id,
            'keluarga_id' => $kk->id, 'status' => 'Unpaid',
        ]);

        \Illuminate\Support\Facades\Http::fake();
        \App\Models\SystemSetting::setMany(['notif_push' => true]);

        $this->actingAs($user)->post(route('opkeuangan.pembayaran.broadcast', $bayar->id))
            ->assertRedirect()
            ->assertSessionHas('success');
        // Masuk ke notifikasi aplikasi warga; tidak ada panggilan WhatsApp/email.
        $notif = $akunWarga->notifications()->first();
        $this->assertNotNull($notif);
        $this->assertSame('Pengingat Pembayaran', $notif->data['judul']);
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_tombol_pengingat_hanya_untuk_rt_sendiri(): void
    {
        $rt = MasterRt::create(['kode_rt' => '505', 'nama_rt' => 'RT 505']);
        $user = $this->bendahara($rt);

        $this->actingAs($user)->post(route('opkeuangan.iuran-bulanan.ingatkan'))
            ->assertRedirect()
            ->assertSessionHas('success');
    }
}
