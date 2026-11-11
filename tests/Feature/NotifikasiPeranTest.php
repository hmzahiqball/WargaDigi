<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\LaporanKeuangan;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Notifikasi per-peran: memastikan event membuat notifikasi in-app (DB) ke
 * target yang benar, dan endpoint Web Push menyimpan subscription.
 */
class NotifikasiPeranTest extends TestCase
{
    use RefreshDatabase;

    private function warga(MasterRt $rt, string $nik, string $username): array
    {
        Role::findOrCreate('Warga', 'web');
        $keluarga = Keluarga::create([
            'no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id, 'no_wa' => '0812' . substr($nik, -8),
        ]);
        $penduduk = Penduduk::create([
            'keluarga_id' => $keluarga->id, 'nik' => $nik, 'nama_lengkap' => 'Warga ' . $username,
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
        ]);
        $user = User::create([
            'nik' => $nik, 'username' => $username, 'role' => 'Warga',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $user->syncRoles(['Warga']);

        return [$user, $penduduk, $keluarga];
    }

    private function ketuaRt(MasterRt $rt, string $username): User
    {
        Role::findOrCreate('Ketua RT', 'web');
        $u = User::create([
            'nik' => null, 'username' => $username, 'role' => 'Ketua RT',
            'status_akun' => 'Active', 'rt_id' => $rt->id, 'password' => 'password123',
        ]);
        $u->syncRoles(['Ketua RT']);

        return $u;
    }

    public function test_surat_diajukan_memberi_notif_ke_ketua_rt(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        [$warga, $penduduk] = $this->warga($rt, '3217010100000301', 'warga1');
        $ketua = $this->ketuaRt($rt, 'ketuart01');

        $surat = PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Keterangan Domisili', 'status' => 'Diajukan',
        ]);

        NotificationService::notifikasiSuratDiajukan($surat);

        $this->assertSame(1, $ketua->notifications()->count());
        $this->assertStringContainsString('Pengajuan Surat Baru', $ketua->notifications()->first()->data['judul']);
    }

    public function test_surat_selesai_memberi_notif_ke_pemohon(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        [$warga, $penduduk] = $this->warga($rt, '3217010100000302', 'warga2');

        $surat = PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Keterangan Domisili', 'status' => 'Selesai',
        ]);

        NotificationService::notifikasiSuratSelesai($surat);

        $this->assertSame(1, $warga->notifications()->count());
        $this->assertSame('Surat Terbit', $warga->notifications()->first()->data['judul']);
    }

    public function test_laporan_disubmit_memberi_notif_ke_ketua_rt(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        $ketua = $this->ketuaRt($rt, 'ketuart01');

        $laporan = LaporanKeuangan::create([
            'judul' => 'Laporan Januari', 'periode_bulan' => 1, 'periode_tahun' => 2026,
            'unit' => 'RT', 'rt_id' => $rt->id, 'status' => 'Submitted',
            'total_pemasukan' => 0, 'total_pengeluaran' => 0, 'saldo_awal' => 0, 'saldo_akhir' => 0,
        ]);

        NotificationService::notifikasiLaporanDisubmit($laporan);

        $this->assertSame(1, $ketua->notifications()->count());
        $this->assertSame('keuangan', $ketua->notifications()->first()->data['kategori']);
    }

    public function test_pengumuman_publish_memberi_notif_ke_semua_warga_tapi_berita_tidak(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        [$warga] = $this->warga($rt, '3217010100000303', 'warga3');

        NotificationService::notifikasiKontenPublish('Berita', 'Judul Berita');
        $this->assertSame(0, $warga->notifications()->count(), 'Berita tidak boleh mengirim notif warga.');

        NotificationService::notifikasiKontenPublish('Pengumuman', 'Kerja Bakti');
        $this->assertSame(1, $warga->notifications()->count(), 'Pengumuman harus mengirim notif warga.');
    }

    public function test_klik_notifikasi_menandai_terbaca_dan_redirect(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        [$warga, $penduduk] = $this->warga($rt, '3217010100000305', 'warga5');

        $surat = PengajuanSurat::create([
            'penduduk_id' => $penduduk->id, 'tipe_surat' => 'Surat Keterangan Domisili', 'status' => 'Selesai',
        ]);
        NotificationService::notifikasiSuratSelesai($surat);

        $this->assertSame(1, $warga->unreadNotifications()->count());
        $notif = $warga->notifications()->first();

        // Klik notifikasi → tandai terbaca lalu redirect ke url konten.
        $this->actingAs($warga)
            ->get(route('notifications.read.generic', $notif->id))
            ->assertRedirect(route('warga.surat.index'));

        // Badge berkurang: tidak ada lagi notifikasi belum terbaca.
        $this->assertSame(0, $warga->fresh()->unreadNotifications()->count());
    }

    public function test_endpoint_subscribe_menyimpan_push_subscription(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => 'RT 01']);
        [$warga] = $this->warga($rt, '3217010100000304', 'warga4');

        $this->actingAs($warga)
            ->postJson(route('push.subscribe'), [
                'endpoint' => 'https://push.example.com/abc123',
                'keys' => ['p256dh' => 'kunci-publik', 'auth' => 'token-auth'],
                'content_encoding' => 'aesgcm',
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'user_id' => $warga->id,
            'endpoint' => 'https://push.example.com/abc123',
        ]);
    }
}
