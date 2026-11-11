<?php

namespace App\Notifications;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi generik in-app (+ Web Push) dengan payload seragam agar langsung
 * terbaca oleh dropdown notifikasi di topbar.
 *
 * Payload data: type (info|success|warning|danger), icon (bootstrap icon),
 * judul, pesan, url (tujuan saat diklik), kategori (opsional).
 */
class GeneralNotification extends Notification
{
    use Queueable;

    /**
     * @param array<string,mixed> $data
     */
    public function __construct(public array $data)
    {
    }

    /**
     * Kanal pengiriman: selalu simpan ke database (in-app). Tambahkan WebPush
     * bila fitur push aktif (channel menangani sendiri bila tak ada subscription).
     *
     * @return array<int,string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];

        if (NotificationService::isPushEnabled()) {
            $channels[] = 'database';
        }

        // Web Push ke perangkat/browser (fail-safe di channel bila VAPID kosong).
        if (NotificationService::isWebPushEnabled()) {
            $channels[] = \App\Notifications\Channels\WebPushChannel::class;
        }

        // Jaminan: minimal simpan ke database agar notifikasi tidak hilang.
        if (empty($channels)) {
            $channels[] = 'database';
        }

        return array_values(array_unique($channels));
    }

    /**
     * Representasi untuk disimpan di tabel notifications (in-app).
     *
     * @return array<string,mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'     => $this->data['type'] ?? 'info',
            'icon'     => $this->data['icon'] ?? 'bi-bell-fill',
            'judul'    => $this->data['judul'] ?? 'Notifikasi',
            'pesan'    => $this->data['pesan'] ?? '',
            'url'      => $this->data['url'] ?? null,
            'kategori' => $this->data['kategori'] ?? 'umum',
        ];
    }
}
