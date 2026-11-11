<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Channel pengiriman notifikasi via Web Push (browser/perangkat).
 *
 * Fail-safe: bila VAPID belum dikonfigurasi atau user tidak punya subscription,
 * channel ini tidak melakukan apa-apa dan tidak melempar error. Subscription
 * yang sudah kedaluwarsa (410/404) dibersihkan otomatis.
 */
class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $publicKey = config('webpush.public_key');
        $privateKey = config('webpush.private_key');

        // Tanpa VAPID, Web Push tidak dapat berjalan (mode non-aktif).
        if (empty($publicKey) || empty($privateKey)) {
            return;
        }

        if (! method_exists($notifiable, 'pushSubscriptions')) {
            return;
        }

        $subscriptions = $notifiable->pushSubscriptions()->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        // Ambil payload dari toArray notifikasi (payload in-app yang seragam).
        $data = method_exists($notification, 'toArray')
            ? $notification->toArray($notifiable)
            : [];

        $payload = json_encode([
            'judul' => $data['judul'] ?? 'WargaDigi',
            'pesan' => $data['pesan'] ?? '',
            'url'   => $data['url'] ?? config('app.url'),
            'icon'  => '/icons/icon-192.png',
            'badge' => '/icons/icon-192.png',
        ]);

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => config('webpush.subject'),
                    'publicKey'  => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);

            foreach ($subscriptions as $sub) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint'        => $sub->endpoint,
                        'publicKey'       => $sub->public_key,
                        'authToken'       => $sub->auth_token,
                        'contentEncoding' => $sub->content_encoding ?: 'aesgcm',
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if (! $report->isSuccess()) {
                    // Hapus subscription yang kedaluwarsa/invalid.
                    if (in_array($report->getResponse()?->getStatusCode(), [404, 410], true)) {
                        \App\Models\PushSubscription::where('endpoint', $endpoint)->delete();
                    } else {
                        Log::warning("Web Push gagal ke {$endpoint}: " . $report->getReason());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Web Push error: ' . $e->getMessage());
        }
    }
}
