<?php

namespace Tests\Feature;

use App\Models\IuranPembayaran;
use App\Models\KategoriTransaksi;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\TransaksiKeuangan;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Modul iuran bulanan warga: master, ceklis bendahara (masuk kas), pengajuan &
 * verifikasi warga, pembatalan, dan pengingat.
 */
class IuranBulananTest extends TestCase
{
    use RefreshDatabase;

    private MasterRt $rt;

    private function bendahara(): User
    {
        $this->rt = MasterRt::create(['kode_rt' => '301', 'nama_rt' => 'RT 301']);
        $u = User::create([
            'nik' => '3217010101019701', 'username' => 'bendahara_iuran', 'role' => 'Op Keuangan RT',
            'status_akun' => 'Active', 'rt_id' => $this->rt->id, 'password' => 'password123',
        ]);

        return $u;
    }

    private function keluargaWarga(string $nik, string $nama): array
    {
        Role::findOrCreate('Warga', 'web');
        $kel = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Jl. Uji',
            'rt_id' => $this->rt->id, 'no_wa' => '0812' . substr($nik, -8),
        ]);
        $pend = Penduduk::create([
            'keluarga_id' => $kel->id, 'nik' => $nik, 'nama_lengkap' => $nama,
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);
        $user = User::create([
            'nik' => $nik, 'username' => 'warga' . substr($nik, -4), 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $this->rt->id, 'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        return [$kel, $user];
    }

    private function master(): MasterIuran
    {
        return MasterIuran::create([
            'rt_id' => $this->rt->id, 'tahun' => (int) now()->format('Y'),
            'nama' => 'Iuran Bulanan Warga', 'nominal' => 90000, 'aktif' => true,
        ]);
    }

    public function test_bendahara_buat_master_iuran(): void
    {
        $bendahara = $this->bendahara();

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.iuran-bulanan.master'), [
                'tahun' => now()->format('Y'), 'nama' => 'Iuran Bulanan Warga',
                'nominal' => '90000', 'aktif' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('master_iuran', ['rt_id' => $this->rt->id, 'nominal' => 90000]);
    }

    public function test_ceklis_lunas_masuk_ke_kas(): void
    {
        $bendahara = $this->bendahara();
        [$kel] = $this->keluargaWarga('3217010101013001', 'Warga Satu');
        $master = $this->master();

        $this->actingAs($bendahara)
            ->post(route('opkeuangan.iuran-bulanan.lunas'), [
                'master_iuran_id' => $master->id,
                'keluarga_id' => $kel->id,
                'bulan' => [1, 2, 3],
                'tanggal_bayar' => now()->toDateString(),
                'metode' => 'tunai',
            ])
            ->assertRedirect();

        // 3 sel lunas.
        $this->assertSame(3, IuranPembayaran::where('master_iuran_id', $master->id)->where('status', 'lunas')->count());
        // Batch = 1 transaksi pemasukan gabungan senilai 3 x 90000.
        $this->assertSame(1, TransaksiKeuangan::where('tipe', 'pemasukan')->where('rt_id', $this->rt->id)->count());
        $this->assertSame(270000, (int) TransaksiKeuangan::where('tipe', 'pemasukan')->where('rt_id', $this->rt->id)->value('jumlah'));
        // Ketiga sel berbagi batch & transaksi yang sama.
        $this->assertSame(1, IuranPembayaran::where('master_iuran_id', $master->id)->distinct('batch_id')->count('batch_id'));
    }

    public function test_batalkan_lunas_menghapus_transaksi_kas(): void
    {
        $bendahara = $this->bendahara();
        [$kel] = $this->keluargaWarga('3217010101013002', 'Warga Dua');
        $master = $this->master();

        $this->actingAs($bendahara)->post(route('opkeuangan.iuran-bulanan.lunas'), [
            'master_iuran_id' => $master->id, 'keluarga_id' => $kel->id, 'bulan' => [5],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai',
        ]);

        $sel = IuranPembayaran::where('master_iuran_id', $master->id)->first();
        $txId = $sel->transaksi_id;
        $this->assertNotNull($txId);

        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.iuran-bulanan.batal', $sel->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('iuran_pembayaran', ['id' => $sel->id]);
        $this->assertDatabaseMissing('transaksi_keuangan', ['id' => $txId]);
    }

    public function test_warga_bayar_iuran_lainnya_dengan_nominal_di_atas_minimal(): void
    {
        $bendahara = $this->bendahara();
        [$kel, $warga] = $this->keluargaWarga('3217010101013020', 'Warga Lainnya');

        $tagihan = \App\Models\Tagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'judul' => 'Iuran Kegiatan', 'jenis' => 'insidental', 'nominal' => 50000,
            'nominal_fix' => false, 'periode_bulan' => (int) now()->format('n'), 'periode_tahun' => (int) now()->format('Y'),
            'unit' => 'RT', 'rt_id' => $this->rt->id, 'pembuat_id' => $bendahara->id, 'status' => 'Active',
        ]);
        $bayar = \App\Models\PembayaranTagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'tagihan_id' => $tagihan->id, 'keluarga_id' => $kel->id, 'status' => 'Unpaid',
        ]);

        $this->actingAs($warga)
            ->post(route('warga.tagihan.bayar', $bayar->id), [
                'metode' => 'Cash', 'nominal' => '75.000',
            ])
            ->assertRedirect();

        $bayar->refresh();
        $this->assertSame('Pending', $bayar->status);
        $this->assertSame(75000, (int) $bayar->nominal);
    }

    public function test_warga_bayar_iuran_lainnya_kurang_dari_minimal_ditolak(): void
    {
        $bendahara = $this->bendahara();
        [$kel, $warga] = $this->keluargaWarga('3217010101013021', 'Warga Kurang');

        $tagihan = \App\Models\Tagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'judul' => 'Iuran Kegiatan 2', 'jenis' => 'insidental', 'nominal' => 50000,
            'nominal_fix' => false, 'periode_bulan' => (int) now()->format('n'), 'periode_tahun' => (int) now()->format('Y'),
            'unit' => 'RT', 'rt_id' => $this->rt->id, 'pembuat_id' => $bendahara->id, 'status' => 'Active',
        ]);
        $bayar = \App\Models\PembayaranTagihan::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'tagihan_id' => $tagihan->id, 'keluarga_id' => $kel->id, 'status' => 'Unpaid',
        ]);

        $this->actingAs($warga)
            ->post(route('warga.tagihan.bayar', $bayar->id), [
                'metode' => 'Cash', 'nominal' => '30.000',
            ])
            ->assertSessionHas('error');

        $this->assertSame('Unpaid', $bayar->fresh()->status);
    }

    public function test_transaksi_iuran_tidak_bisa_dihapus_dari_buku_kas(): void
    {
        $bendahara = $this->bendahara();
        [$kel] = $this->keluargaWarga('3217010101013010', 'Warga Kunci');
        $master = $this->master();

        $this->actingAs($bendahara)->post(route('opkeuangan.iuran-bulanan.lunas'), [
            'master_iuran_id' => $master->id, 'keluarga_id' => $kel->id, 'bulan' => [1],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai',
        ]);
        $txId = IuranPembayaran::where('master_iuran_id', $master->id)->value('transaksi_id');

        // Hapus dari buku kas harus ditolak (transaksi tetap ada).
        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.transaksi.destroy', $txId))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('transaksi_keuangan', ['id' => $txId]);
    }

    public function test_batalkan_membatalkan_seluruh_batch(): void
    {
        $bendahara = $this->bendahara();
        [$kel] = $this->keluargaWarga('3217010101013009', 'Warga Batch');
        $master = $this->master();

        // Bayar batch 3 bulan.
        $this->actingAs($bendahara)->post(route('opkeuangan.iuran-bulanan.lunas'), [
            'master_iuran_id' => $master->id, 'keluarga_id' => $kel->id, 'bulan' => [1, 2, 3],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai',
        ]);

        $selMana = IuranPembayaran::where('master_iuran_id', $master->id)->first();
        $txId = $selMana->transaksi_id;

        // Batalkan lewat salah satu sel → seluruh batch (3 sel) dan transaksinya hilang.
        $this->actingAs($bendahara)
            ->delete(route('opkeuangan.iuran-bulanan.batal', $selMana->id))
            ->assertRedirect();

        $this->assertSame(0, IuranPembayaran::where('master_iuran_id', $master->id)->count());
        $this->assertDatabaseMissing('transaksi_keuangan', ['id' => $txId]);
    }

    public function test_warga_ajukan_lalu_bendahara_setujui_masuk_kas(): void
    {
        Storage::fake('public');
        $bendahara = $this->bendahara();
        [$kel, $warga] = $this->keluargaWarga('3217010101013003', 'Warga Tiga');
        $master = $this->master();

        // Warga mengajukan pembayaran bulan 4.
        $this->actingAs($warga)
            ->post(route('warga.iuran.ajukan'), [
                'master_iuran_id' => $master->id, 'bulan' => [4],
                'tanggal_bayar' => now()->toDateString(), 'metode' => 'transfer',
                'bukti_file' => UploadedFile::fake()->image('bukti.jpg'),
            ])
            ->assertRedirect();

        $sel = IuranPembayaran::where('master_iuran_id', $master->id)->where('bulan', 4)->first();
        $this->assertSame('menunggu', $sel->status);
        $this->assertNull($sel->transaksi_id);

        // Bendahara menyetujui → lunas + masuk kas.
        $this->actingAs($bendahara)
            ->put(route('opkeuangan.iuran-bulanan.setujui', $sel->id))
            ->assertRedirect();

        $sel->refresh();
        $this->assertSame('lunas', $sel->status);
        $this->assertNotNull($sel->transaksi_id);
        $this->assertDatabaseHas('transaksi_keuangan', ['id' => $sel->transaksi_id, 'tipe' => 'pemasukan']);
    }

    public function test_warga_ajukan_banyak_bulan_lalu_disetujui_sebagai_satu_batch(): void
    {
        $bendahara = $this->bendahara();
        [$kel, $warga] = $this->keluargaWarga('3217010101013007', 'Warga Batch Ajuan');
        $master = $this->master();

        // Warga mengajukan 3 bulan sekaligus.
        $this->actingAs($warga)
            ->post(route('warga.iuran.ajukan'), [
                'master_iuran_id' => $master->id, 'bulan' => [7, 8, 9],
                'tanggal_bayar' => now()->toDateString(), 'metode' => 'transfer',
            ])
            ->assertRedirect();

        $sel = IuranPembayaran::where('master_iuran_id', $master->id)->where('status', 'menunggu')->get();
        $this->assertCount(3, $sel);
        $this->assertSame(1, $sel->pluck('batch_id')->unique()->count());

        // Bendahara menyetujui salah satu → seluruh batch lunas, 1 transaksi gabungan.
        $this->actingAs($bendahara)
            ->put(route('opkeuangan.iuran-bulanan.setujui', $sel->first()->id))
            ->assertRedirect();

        $this->assertSame(3, IuranPembayaran::where('master_iuran_id', $master->id)->where('status', 'lunas')->count());
        $txIds = IuranPembayaran::where('master_iuran_id', $master->id)->pluck('transaksi_id')->unique();
        $this->assertSame(1, $txIds->count());
        $this->assertSame(270000, (int) TransaksiKeuangan::find($txIds->first())->jumlah);
    }

    public function test_bendahara_tolak_pengajuan_warga(): void
    {
        $bendahara = $this->bendahara();
        [$kel, $warga] = $this->keluargaWarga('3217010101013004', 'Warga Empat');
        $master = $this->master();

        $this->actingAs($warga)->post(route('warga.iuran.ajukan'), [
            'master_iuran_id' => $master->id, 'bulan' => [6],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'transfer',
        ]);
        $sel = IuranPembayaran::where('master_iuran_id', $master->id)->first();

        $this->actingAs($bendahara)
            ->put(route('opkeuangan.iuran-bulanan.tolak', $sel->id), ['alasan' => 'Bukti tidak jelas'])
            ->assertRedirect();

        // Baris dipertahankan berstatus ditolak (bukan masuk kas) beserta alasannya.
        $sel->refresh();
        $this->assertSame('ditolak', $sel->status);
        $this->assertSame('Bukti tidak jelas', $sel->catatan);
        $this->assertNull($sel->transaksi_id);

        // Warga melihat alasan penolakan di halaman iuran.
        $this->actingAs($warga)->get(route('warga.iuran.index'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Bukti tidak jelas');

        // Warga dapat mengajukan ulang bulan yang ditolak.
        $this->actingAs($warga)->post(route('warga.iuran.ajukan'), [
            'master_iuran_id' => $master->id, 'bulan' => [6],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'transfer',
        ]);
        $this->assertSame('menunggu', $sel->fresh()->status);
    }

    public function test_reminder_hanya_ke_kk_belum_lunas(): void
    {
        $bendahara = $this->bendahara();
        [$kelA, $wargaA] = $this->keluargaWarga('3217010101013005', 'Warga Lunas');
        [$kelB, $wargaB] = $this->keluargaWarga('3217010101013006', 'Warga Nunggak');
        $master = $this->master();

        $bulanIni = (int) now()->format('n');
        // Warga A lunas bulan ini.
        $this->actingAs($bendahara)->post(route('opkeuangan.iuran-bulanan.lunas'), [
            'master_iuran_id' => $master->id, 'keluarga_id' => $kelA->id, 'bulan' => [$bulanIni],
            'tanggal_bayar' => now()->toDateString(), 'metode' => 'tunai',
        ]);

        $jumlah = NotificationService::reminderIuranBulanan();

        // Hanya warga B (nunggak) yang diingatkan.
        $this->assertSame(1, $jumlah);
        $this->assertSame(1, $wargaB->fresh()->notifications()->count());
        $this->assertSame(0, $wargaA->fresh()->notifications()->count());
    }
}
