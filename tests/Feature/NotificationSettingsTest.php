<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\MasterRt;
use App\Models\PembayaranTagihan;
use App\Models\Penduduk;
use App\Models\SystemSetting;
use App\Models\Tagihan;
use App\Notifications\TagihanReminder;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Regression test IR47: saklar notifikasi (WhatsApp/Email/Push) di Pengaturan Sistem
 * benar-benar mengendalikan kanal pengiriman notifikasi.
 */
class NotificationSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function buatPembayaran(): PembayaranTagihan
    {
        $rt = MasterRt::create(['kode_rt' => '201', 'nama_rt' => 'RT 201']);
        $keluarga = Keluarga::create([
            'no_kk' => '3217010201000001',
            'nik_kepala_keluarga' => '3217010201000001',
            'alamat' => 'Alamat Uji',
            'rt_id' => $rt->id,
            'no_wa' => '081234567890',
        ]);
        Penduduk::create([
            'keluarga_id' => $keluarga->id,
            'nik' => '3217010201000001',
            'nama_lengkap' => 'Kepala Uji',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Islam',
            'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga',
            'status_perkawinan' => 'Belum Kawin',
        ]);
        $tagihan = Tagihan::create([
            'id' => Str::uuid(),
            'judul' => 'Iuran Uji',
            'jenis' => 'insidental',
            'nominal' => 50000,
            'periode_bulan' => 10,
            'periode_tahun' => 2026,
            'unit' => 'RT',
            'rt_id' => $rt->id,
            'status' => 'Active',
        ]);

        return PembayaranTagihan::create([
            'id' => Str::uuid(),
            'tagihan_id' => $tagihan->id,
            'keluarga_id' => $keluarga->id,
            'status' => 'Unpaid',
        ]);
    }

    public function test_getActiveChannels_mengikuti_saklar_sistem(): void
    {
        SystemSetting::setMany([
            'notif_whatsapp' => true,
            'notif_email' => false,
            'notif_push' => false,
        ]);

        $this->assertSame(['WhatsApp'], NotificationService::getActiveChannels());

        SystemSetting::setMany([
            'notif_whatsapp' => false,
            'notif_email' => true,
            'notif_push' => true,
        ]);

        $channels = NotificationService::getActiveChannels();
        $this->assertContains('Email', $channels);
        $this->assertContains('Push/Dashboard', $channels);
        $this->assertNotContains('WhatsApp', $channels);
    }

    public function test_tagihan_reminder_via_mengikuti_saklar_email_dan_push(): void
    {
        $pembayaran = $this->buatPembayaran();
        $notifiable = new \App\Models\User();

        // Hanya push aktif → hanya channel database
        SystemSetting::setMany(['notif_email' => false, 'notif_push' => true]);
        $via = (new TagihanReminder($pembayaran))->via($notifiable);
        $this->assertSame(['database'], $via);

        // Hanya email aktif → hanya channel mail
        SystemSetting::setMany(['notif_email' => true, 'notif_push' => false]);
        $via = (new TagihanReminder($pembayaran))->via($notifiable);
        $this->assertSame(['mail'], $via);

        // Keduanya mati → tidak ada channel
        SystemSetting::setMany(['notif_email' => false, 'notif_push' => false]);
        $via = (new TagihanReminder($pembayaran))->via($notifiable);
        $this->assertSame([], $via);
    }

    public function test_sendWhatsApp_ditolak_saat_saklar_whatsapp_mati(): void
    {
        SystemSetting::setMany(['notif_whatsapp' => false]);
        Http::fake();

        $result = NotificationService::sendWhatsApp('081234567890', 'Pesan uji');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }
}
