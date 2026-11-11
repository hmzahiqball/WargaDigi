<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Check if WhatsApp notifications are enabled in System Settings.
     */
    public static function isWhatsAppEnabled(): bool
    {
        return (bool) SystemSetting::get('notif_whatsapp', true);
    }

    /**
     * Check if Email notifications are enabled in System Settings.
     */
    public static function isEmailEnabled(): bool
    {
        return (bool) SystemSetting::get('notif_email', true);
    }

    /**
     * Check if Push / In-App notifications are enabled in System Settings.
     */
    public static function isPushEnabled(): bool
    {
        return (bool) SystemSetting::get('notif_push', true);
    }

    /**
     * Apakah Web Push (notifikasi browser) aktif.
     *
     * Mengikuti toggle Push/In-App yang sama, tetapi hanya efektif bila VAPID
     * key tersedia. Channel tetap fail-safe bila subscription/VAPID kosong.
     */
    public static function isWebPushEnabled(): bool
    {
        return static::isPushEnabled() && ! empty(config('webpush.public_key'));
    }

    // ===================================================================
    // Dispatcher terpusat: kirim notifikasi in-app (+ Web Push) ke user.
    // ===================================================================

    /**
     * Kirim notifikasi ke satu user.
     *
     * @param array<string,mixed> $data payload (type, icon, judul, pesan, url, kategori)
     */
    public static function kirimKeUser(?\App\Models\User $user, array $data): void
    {
        if (! $user) {
            return;
        }

        try {
            $user->notify(new \App\Notifications\GeneralNotification($data));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengirim notifikasi ke user ' . $user->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke banyak user (duplikat dibuang berdasarkan id).
     *
     * @param iterable<\App\Models\User> $users
     * @param array<string,mixed> $data
     */
    public static function kirimKeBanyak(iterable $users, array $data): void
    {
        $terkirim = [];
        foreach ($users as $user) {
            if (! $user || in_array($user->id, $terkirim, true)) {
                continue;
            }
            $terkirim[] = $user->id;
            static::kirimKeUser($user, $data);
        }
    }

    // ===================================================================
    // Helper penargetan: memetakan peran/relasi ke daftar User.
    // ===================================================================

    /**
     * User berdasarkan NIK kependudukan (mis. pemohon surat, pemilik UMKM).
     */
    public static function userDariNik(?string $nik): ?\App\Models\User
    {
        if (empty($nik)) {
            return null;
        }

        return \App\Models\User::where('nik', $nik)->first();
    }

    /** User berdasarkan id (mis. operator_id pembuat konten). */
    public static function userDariId(?string $id): ?\App\Models\User
    {
        if (empty($id)) {
            return null;
        }

        return \App\Models\User::find($id);
    }

    /**
     * Semua user dengan salah satu peran tertentu (gabung Spatie + kolom legacy).
     *
     * @param array<int,string> $roles
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function usersByRole(array $roles): \Illuminate\Support\Collection
    {
        $query = \App\Models\User::query()
            ->where('status_akun', 'Active')
            ->where(function ($q) use ($roles) {
                $q->whereIn('role', $roles);
                try {
                    if (class_exists(\Spatie\Permission\Models\Role::class)) {
                        $q->orWhereHas('roles', fn ($r) => $r->whereIn('name', $roles));
                    }
                } catch (\Throwable $e) {
                    // Abaikan bila tabel permission belum ada.
                }
            });

        return $query->get()->unique('id')->values();
    }

    /**
     * Ketua RT untuk RT tertentu (berbasis peran + rt_id user).
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function ketuaRtDariRt(?string $rtId): \Illuminate\Support\Collection
    {
        if (empty($rtId)) {
            return collect();
        }

        return static::usersByRole(['Ketua RT'])
            ->filter(fn ($u) => (string) $u->rt_id === (string) $rtId)
            ->values();
    }

    /**
     * Semua Admin RW.
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function adminRw(): \Illuminate\Support\Collection
    {
        return static::usersByRole(['Admin RW']);
    }

    /**
     * Semua Ketua RW.
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function ketuaRw(): \Illuminate\Support\Collection
    {
        return static::usersByRole(['Ketua RW', 'Pimpinan RW', 'Pimpinan']);
    }

    /**
     * Op Keuangan sesuai unit. Untuk unit RT, difilter berdasarkan rt_id.
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function opKeuangan(string $unit, ?string $rtId = null): \Illuminate\Support\Collection
    {
        if ($unit === 'RT') {
            return static::usersByRole(['Op Keuangan RT', 'Op. Keuangan RT', 'Bendahara RT'])
                ->filter(fn ($u) => empty($rtId) || (string) $u->rt_id === (string) $rtId)
                ->values();
        }

        return static::usersByRole(['Op Keuangan RW', 'Op. Keuangan RW', 'Bendahara RW']);
    }

    /**
     * Semua warga aktif (untuk pengumuman & agenda).
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function semuaWarga(): \Illuminate\Support\Collection
    {
        return static::usersByRole(['Warga']);
    }

    /** Warga yang tinggal di RT tertentu (untuk konten tingkat RT). */
    public static function wargaDiRt(string $rtId): \Illuminate\Support\Collection
    {
        $niks = \App\Models\Penduduk::whereHas('keluarga', fn ($q) => $q->where('rt_id', $rtId))->pluck('nik')->all();

        return static::semuaWarga()->filter(fn ($u) => in_array($u->nik, $niks, true) || (string) $u->getRawOriginal('rt_id') === $rtId)->values();
    }

    /**
     * Approver surat tingkat RW (Admin RW + Ketua RW).
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\User>
     */
    public static function approverSuratRw(): \Illuminate\Support\Collection
    {
        return static::adminRw()->merge(static::ketuaRw())->unique('id')->values();
    }

    // ===================================================================
    // Event-spesifik: SURAT
    // ===================================================================

    /** Ambil user pemohon dari sebuah pengajuan surat. */
    public static function pemohonSurat(\App\Models\PengajuanSurat $surat): ?\App\Models\User
    {
        return static::userDariNik($surat->penduduk?->nik);
    }

    /** rt_id pemohon dari pengajuan surat (lewat keluarga). */
    public static function rtIdPemohonSurat(\App\Models\PengajuanSurat $surat): ?string
    {
        return $surat->penduduk?->keluarga?->rt_id;
    }

    /** Surat baru diajukan warga → Ketua RT di RT pemohon. */
    public static function notifikasiSuratDiajukan(\App\Models\PengajuanSurat $surat): void
    {
        $surat->loadMissing('penduduk.keluarga');
        $nama = $surat->penduduk?->nama_lengkap ?? 'Warga';
        $rtId = static::rtIdPemohonSurat($surat);

        static::kirimKeBanyak(static::ketuaRtDariRt($rtId), [
            'type'     => 'info',
            'icon'     => 'bi-file-earmark-text',
            'judul'    => 'Pengajuan Surat Baru',
            'pesan'    => "Pengajuan {$surat->tipe_surat} dari {$nama} menunggu persetujuan Anda.",
            'url'      => route('rt.persetujuan-dokumen'),
            'kategori' => 'surat',
        ]);
    }

    /** Surat disetujui RT → pemohon (info) + approver RW (perlu diterbitkan). */
    public static function notifikasiSuratDisetujuiRt(\App\Models\PengajuanSurat $surat): void
    {
        $surat->loadMissing('penduduk.keluarga');
        $nama = $surat->penduduk?->nama_lengkap ?? 'Warga';

        static::kirimKeUser(static::pemohonSurat($surat), [
            'type'     => 'info',
            'icon'     => 'bi-hourglass-split',
            'judul'    => 'Surat Disetujui RT',
            'pesan'    => "Pengajuan {$surat->tipe_surat} Anda telah disetujui RT dan diteruskan ke RW.",
            'url'      => route('warga.surat.index'),
            'kategori' => 'surat',
        ]);

        static::kirimKeBanyak(static::approverSuratRw(), [
            'type'     => 'info',
            'icon'     => 'bi-file-earmark-check',
            'judul'    => 'Surat Menunggu Penerbitan',
            'pesan'    => "Surat {$surat->tipe_surat} dari {$nama} sudah disetujui RT dan menunggu penerbitan RW.",
            'url'      => route('rw.persetujuan-dokumen'),
            'kategori' => 'surat',
        ]);
    }

    /** Surat terbit (Selesai) → pemohon. */
    public static function notifikasiSuratSelesai(\App\Models\PengajuanSurat $surat): void
    {
        $surat->loadMissing('penduduk');
        static::kirimKeUser(static::pemohonSurat($surat), [
            'type'     => 'success',
            'icon'     => 'bi-patch-check-fill',
            'judul'    => 'Surat Terbit',
            'pesan'    => "Surat {$surat->tipe_surat} Anda sudah terbit dan dapat diunduh.",
            'url'      => route('warga.surat.index'),
            'kategori' => 'surat',
        ]);
    }

    /** Surat ditolak (oleh RT atau RW) → pemohon. */
    public static function notifikasiSuratDitolak(\App\Models\PengajuanSurat $surat, string $level, ?string $catatan = null): void
    {
        $surat->loadMissing('penduduk');
        $pesan = "Pengajuan {$surat->tipe_surat} Anda ditolak di tingkat {$level}.";
        if (! empty($catatan)) {
            $pesan .= ' Alasan: ' . $catatan;
        }

        static::kirimKeUser(static::pemohonSurat($surat), [
            'type'     => 'danger',
            'icon'     => 'bi-x-circle-fill',
            'judul'    => "Surat Ditolak {$level}",
            'pesan'    => $pesan,
            'url'      => route('warga.surat.index'),
            'kategori' => 'surat',
        ]);
    }

    // ===================================================================
    // Event-spesifik: MUTASI WARGA (perubahan data)
    // ===================================================================

    /** User pengaju mutasi (kepala keluarga pemilik keluarga). */
    public static function pengajuMutasi(\App\Models\MutasiWarga $mutasi): ?\App\Models\User
    {
        $mutasi->loadMissing('keluarga.kepalaKeluarga');
        $nik = $mutasi->keluarga?->kepalaKeluarga?->nik ?? $mutasi->keluarga?->nik_kepala_keluarga;

        return static::userDariNik($nik);
    }

    /** Mutasi menunggu persetujuan RT → Ketua RT di RT keluarga. */
    public static function notifikasiMutasiMenungguRt(\App\Models\MutasiWarga $mutasi): void
    {
        $mutasi->loadMissing('keluarga');
        static::kirimKeBanyak(static::ketuaRtDariRt($mutasi->keluarga?->rt_id), [
            'type'     => 'info',
            'icon'     => 'bi-pencil-square',
            'judul'    => 'Pengajuan Perubahan Data',
            'pesan'    => 'Ada pengajuan perubahan data warga yang menunggu persetujuan RT.',
            'url'      => route('rt.mutasi.index'),
            'kategori' => 'mutasi',
        ]);
    }

    /** Mutasi menunggu persetujuan RW → Admin RW + Ketua RW. */
    public static function notifikasiMutasiMenungguRw(\App\Models\MutasiWarga $mutasi): void
    {
        $target = static::adminRw()->merge(static::ketuaRw())->unique('id')->values();
        static::kirimKeBanyak($target, [
            'type'     => 'info',
            'icon'     => 'bi-pencil-square',
            'judul'    => 'Pengajuan Perubahan Data',
            'pesan'    => 'Ada pengajuan perubahan data warga yang menunggu persetujuan RW.',
            'url'      => route('rw.mutasi.index'),
            'kategori' => 'mutasi',
        ]);
    }

    /** Mutasi selesai (disetujui/ditolak) → pengaju. */
    public static function notifikasiMutasiHasil(\App\Models\MutasiWarga $mutasi, string $level, bool $disetujui, ?string $catatan = null): void
    {
        if ($disetujui) {
            $data = [
                'type'  => 'success',
                'icon'  => 'bi-check-circle-fill',
                'judul' => 'Perubahan Data Disetujui',
                'pesan' => "Pengajuan perubahan data Anda disetujui di tingkat {$level}.",
            ];
        } else {
            $pesan = "Pengajuan perubahan data Anda ditolak di tingkat {$level}.";
            if (! empty($catatan)) {
                $pesan .= ' Alasan: ' . $catatan;
            }
            $data = [
                'type'  => 'danger',
                'icon'  => 'bi-x-circle-fill',
                'judul' => 'Perubahan Data Ditolak',
                'pesan' => $pesan,
            ];
        }

        $data['url'] = route('warga.keluarga.index');
        $data['kategori'] = 'mutasi';

        static::kirimKeUser(static::pengajuMutasi($mutasi), $data);
    }

    // ===================================================================
    // Event-spesifik: KEUANGAN (laporan)
    // ===================================================================

    /** Pembayaran iuran ditolak bendahara → pemegang akun keluarga (dengan alasan). */
    public static function notifikasiIuranDitolak(?\App\Models\Keluarga $keluarga, string $label, ?string $alasan, string $url): void
    {
        if (! $keluarga) {
            return;
        }
        $keluarga->loadMissing('kepalaKeluarga');
        $pesan = "Pembayaran {$label} ditolak.";
        if (! empty($alasan)) {
            $pesan .= ' Alasan: ' . $alasan;
        }
        static::kirimKeUser(static::userDariNik($keluarga->kepalaKeluarga?->nik), [
            'type'     => 'danger',
            'icon'     => 'bi-x-circle-fill',
            'judul'    => 'Pembayaran Ditolak',
            'pesan'    => $pesan,
            'url'      => $url,
            'kategori' => 'keuangan',
        ]);
    }

    /** Laporan keuangan disubmit → approver sesuai unit. */
    public static function notifikasiLaporanDisubmit(\App\Models\LaporanKeuangan $laporan): void
    {
        if ($laporan->unit === 'RT') {
            $target = static::ketuaRtDariRt($laporan->rt_id);
            $url = route('rt.persetujuan-keuangan.index');
        } else {
            // RW / DKM → Ketua RW + Admin RW.
            $target = static::ketuaRw()->merge(static::adminRw())->unique('id')->values();
            $url = route('rw.persetujuan-keuangan.index');
        }

        static::kirimKeBanyak($target, [
            'type'     => 'info',
            'icon'     => 'bi-cash-coin',
            'judul'    => 'Laporan Keuangan Menunggu Persetujuan',
            'pesan'    => "Laporan \"{$laporan->judul}\" ({$laporan->unit}) menunggu persetujuan Anda.",
            'url'      => $url,
            'kategori' => 'keuangan',
        ]);
    }

    /** Laporan disetujui/ditolak → pembuat laporan. */
    public static function notifikasiLaporanHasil(\App\Models\LaporanKeuangan $laporan, bool $disetujui, ?string $catatan = null): void
    {
        $laporan->loadMissing('pembuat');
        if ($disetujui) {
            $data = [
                'type'  => 'success',
                'icon'  => 'bi-check-circle-fill',
                'judul' => 'Laporan Keuangan Disetujui',
                'pesan' => "Laporan \"{$laporan->judul}\" telah disetujui.",
            ];
        } else {
            $pesan = "Laporan \"{$laporan->judul}\" ditolak.";
            if (! empty($catatan)) {
                $pesan .= ' Catatan: ' . $catatan;
            }
            $data = [
                'type'  => 'danger',
                'icon'  => 'bi-x-circle-fill',
                'judul' => 'Laporan Keuangan Ditolak',
                'pesan' => $pesan,
            ];
        }
        $data['url'] = route('opkeuangan.laporan.index');
        $data['kategori'] = 'keuangan';

        static::kirimKeUser($laporan->pembuat, $data);
    }

    // ===================================================================
    // Event-spesifik: KONTEN (berita/agenda/pengumuman)
    // ===================================================================

    /**
     * Konten dipublikasikan.
     * - Pengumuman & Agenda: beri tahu semua warga.
     * - Berita: TIDAK memberi notifikasi warga (hanya tampil di dashboard).
     */
    public static function notifikasiKontenPublish(string $jenis, string $judul, ?string $url = null, ?string $rtId = null): void
    {
        $jenisLower = strtolower($jenis);
        if ($jenisLower === 'berita') {
            return; // sesuai kebijakan: berita tanpa notifikasi.
        }

        $icon = $jenisLower === 'agenda' ? 'bi-calendar-event' : 'bi-megaphone';
        static::kirimKeBanyak($rtId ? static::wargaDiRt($rtId) : static::semuaWarga(), [
            'type'     => 'info',
            'icon'     => $icon,
            'judul'    => ucfirst($jenis) . ' Baru',
            'pesan'    => "{$jenis} baru: {$judul}",
            'url'      => $url ?? route('warga.dashboard'),
            'kategori' => 'konten',
        ]);
    }

    /** Konten butuh review → approver konten RW (Admin RW + Ketua RW). */
    public static function notifikasiKontenReview(string $jenis, string $judul, ?string $url = null): void
    {
        $target = static::adminRw()->merge(static::ketuaRw())->unique('id')->values();
        static::kirimKeBanyak($target, [
            'type'     => 'info',
            'icon'     => 'bi-eye',
            'judul'    => ucfirst($jenis) . ' Menunggu Review',
            'pesan'    => "{$jenis} \"{$judul}\" menunggu review untuk dipublikasikan.",
            'url'      => $url ?? route('rw.dashboard'),
            'kategori' => 'konten',
        ]);
    }

    /** Konten dikembalikan untuk revisi → pembuat (Op Konten). */
    public static function notifikasiKontenRevisi(?\App\Models\User $pembuat, string $jenis, string $judul, ?string $catatan = null, ?string $url = null): void
    {
        $pesan = "{$jenis} \"{$judul}\" dikembalikan untuk revisi.";
        if (! empty($catatan)) {
            $pesan .= ' Catatan: ' . $catatan;
        }

        static::kirimKeUser($pembuat, [
            'type'     => 'warning',
            'icon'     => 'bi-arrow-counterclockwise',
            'judul'    => ucfirst($jenis) . ' Perlu Revisi',
            'pesan'    => $pesan,
            'url'      => $url ?? route('home'),
            'kategori' => 'konten',
        ]);
    }

    /**
     * Get list of currently active notification channels.
     *
     * @return array<string>
     */
    public static function getActiveChannels(): array
    {
        $channels = [];
        if (static::isWhatsAppEnabled()) {
            $channels[] = 'WhatsApp';
        }
        if (static::isEmailEnabled()) {
            $channels[] = 'Email';
        }
        if (static::isPushEnabled()) {
            $channels[] = 'Push/Dashboard';
        }

        return $channels;
    }

    /**
     * Send WhatsApp notification message if enabled in System Settings.
     *
     * IR51: Mengirim pengingat via gateway WhatsApp berbasis HTTP bila dikonfigurasi.
     * Bersifat fail-safe: jika kanal dinonaktifkan atau kredensial gateway belum diisi,
     * pengiriman hanya dicatat ke log dan mengembalikan status tanpa melempar error,
     * sehingga notifikasi in-app lain tetap berjalan normal.
     */
    public static function sendWhatsApp(?string $recipientPhone, string $message): bool
    {
        if (!static::isWhatsAppEnabled()) {
            Log::info("Notifikasi WhatsApp ke {$recipientPhone} dicegah karena dinonaktifkan di Pengaturan Sistem.");
            return false;
        }

        if (empty($recipientPhone)) {
            return false;
        }

        $target = static::normalizeGatewayNumber($recipientPhone);

        $config = config('services.whatsapp');
        $url = $config['url'] ?? null;
        $token = $config['token'] ?? null;

        // Fail-safe: tanpa konfigurasi gateway, cukup catat ke log (perilaku lama dipertahankan).
        if (empty($url)) {
            Log::info("Notifikasi WhatsApp (mode log, gateway belum dikonfigurasi) ke {$target}: {$message}");
            return true;
        }

        try {
            $response = Http::timeout($config['timeout'] ?? 10)
                ->asForm()
                ->withHeaders(array_filter(['Authorization' => $token]))
                ->post($url, array_filter([
                    'target' => $target,
                    'message' => $message,
                    'sender' => $config['sender'] ?? null,
                    'token' => $token,
                ]));

            if ($response->successful()) {
                Log::info("Notifikasi WhatsApp terkirim ke {$target} via gateway.");
                return true;
            }

            Log::warning("Pengiriman WhatsApp ke {$target} gagal (HTTP {$response->status()}): " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("Pengiriman WhatsApp ke {$target} error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalisasi nomor ke format internasional tanpa '+' (mis. 62812xxxx)
     * yang umum dibutuhkan gateway WhatsApp.
     */
    public static function normalizeGatewayNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Kirim pengingat iuran bulanan ke pemegang akun KK yang belum lunas
     * pada bulan berjalan. Dipanggil oleh scheduler (tgl 1, 10, 28).
     *
     * @return int jumlah KK yang diingatkan
     */
    public static function reminderIuranBulanan(?string $rtId = null): int
    {
        $bulan = (int) now()->format('n');
        $tahun = (int) now()->format('Y');
        $namaBulan = \App\Services\IuranService::BULAN[$bulan] ?? $bulan;
        $dikirim = 0;

        // $rtId diisi saat dipicu manual oleh bendahara RT (hanya RT itu); null = semua RT (scheduler).
        $masterList = \App\Models\MasterIuran::where('tahun', $tahun)->where('aktif', true)
            ->when($rtId, fn ($q) => $q->where('rt_id', $rtId))->get();
        foreach ($masterList as $master) {
            // KK aktif di RT master.
            $keluargaList = \App\Models\Keluarga::where('rt_id', $master->rt_id)->get();
            // KK yang sudah lunas bulan ini.
            $sudah = \App\Models\IuranPembayaran::where('master_iuran_id', $master->id)
                ->where('bulan', $bulan)->where('status', 'lunas')
                ->pluck('keluarga_id')->all();

            foreach ($keluargaList as $kel) {
                if (in_array($kel->id, $sudah, true)) {
                    continue;
                }
                $nik = $kel->nik_kepala_keluarga ?? optional($kel->kepalaKeluarga)->nik;
                if (! $nik) {
                    continue;
                }
                $user = \App\Models\User::where('nik', $nik)->first();
                if (! $user) {
                    continue;
                }

                static::kirimKeUser($user, [
                    'type'     => 'warning',
                    'icon'     => 'bi-calendar-check',
                    'judul'    => 'Pengingat Iuran Bulanan',
                    'pesan'    => "Iuran {$namaBulan} {$tahun} sebesar Rp " . number_format($master->nominalUntukBulan($bulan), 0, ',', '.') . ' belum dibayar. Mohon segera melakukan pembayaran.',
                    'url'      => route('warga.iuran.index', ['tahun' => $tahun]),
                    'kategori' => 'iuran',
                ]);
                $dikirim++;
            }
        }

        return $dikirim;
    }

    /**
     * Kirim pengingat Iuran Lainnya (tagihan insidental) ke KK yang belum lunas,
     * untuk tagihan aktif yang jadwal remindernya memuat tanggal hari ini.
     *
     * @return int jumlah pengingat terkirim
     */
    public static function reminderIuranLainnya(): int
    {
        $hariIni = (int) now()->format('j');
        $dikirim = 0;

        $tagihanList = \App\Models\Tagihan::where('status', 'Active')
            ->whereNotNull('reminder_tanggal')->get();

        foreach ($tagihanList as $tagihan) {
            $tanggalReminder = is_array($tagihan->reminder_tanggal) ? $tagihan->reminder_tanggal : [];
            if (! in_array($hariIni, array_map('intval', $tanggalReminder), true)) {
                continue;
            }

            $belum = \App\Models\PembayaranTagihan::with('keluarga.kepalaKeluarga')
                ->where('tagihan_id', $tagihan->id)
                ->where('status', 'Unpaid')->get();

            foreach ($belum as $p) {
                $nik = optional(optional($p->keluarga)->kepalaKeluarga)->nik
                    ?? optional($p->keluarga)->nik_kepala_keluarga;
                if (! $nik) {
                    continue;
                }
                $user = \App\Models\User::where('nik', $nik)->first();
                if (! $user) {
                    continue;
                }

                static::kirimKeUser($user, [
                    'type'     => 'warning',
                    'icon'     => 'bi-receipt',
                    'judul'    => 'Pengingat Iuran',
                    'pesan'    => "Iuran \"{$tagihan->judul}\" sebesar Rp " . number_format($tagihan->nominal, 0, ',', '.') . ' belum dibayar. Mohon segera melakukan pembayaran.',
                    'url'      => route('warga.tagihan.index'),
                    'kategori' => 'iuran',
                ]);
                $dikirim++;
            }
        }

        return $dikirim;
    }
}
