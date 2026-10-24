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
}
