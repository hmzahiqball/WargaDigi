<?php

namespace App\Services;

use App\Models\SystemSetting;
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

        // WhatsApp dispatch logic (using configured driver / API / log)
        Log::info("Notifikasi WhatsApp terkirim ke {$recipientPhone}: {$message}");
        return true;
    }
}
