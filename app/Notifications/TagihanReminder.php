<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PembayaranTagihan;

class TagihanReminder extends Notification
{
    use Queueable;

    public $pembayaran;

    /**
     * Create a new notification instance.
     */
    public function __construct(PembayaranTagihan $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pembayaran_id' => $this->pembayaran->id,
            'judul' => 'Pengingat Pembayaran ' . $this->pembayaran->tagihan->unit,
            'pesan' => 'Anda memiliki tagihan "' . $this->pembayaran->tagihan->judul . '" sebesar ' . $this->pembayaran->tagihan->formatted_nominal . ' yang belum dibayar.',
            'url' => route('warga.tagihan.index'),
        ];
    }
}
