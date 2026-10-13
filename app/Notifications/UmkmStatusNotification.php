<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\UmkmUsaha;

class UmkmStatusNotification extends Notification
{
    use Queueable;

    public $usaha;
    public $status;
    public $catatan;

    /**
     * Create a new notification instance.
     */
    public function __construct(UmkmUsaha $usaha, string $status, ?string $catatan = null)
    {
        $this->usaha = $usaha;
        $this->status = $status;
        $this->catatan = $catatan;
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
        if ($this->status === 'Approved') {
            return [
                'umkm_usaha_id' => $this->usaha->id,
                'status'        => 'Approved',
                'type'          => 'success',
                'icon'          => 'bi-check-circle-fill',
                'judul'         => 'Pendaftaran UMKM Disetujui',
                'pesan'         => "Selamat! Pendaftaran usaha '{$this->usaha->nama_usaha}' telah disetujui oleh RW.",
                'url'           => route('warga.umkm.kelola', ['usaha_id' => $this->usaha->id]),
            ];
        } elseif ($this->status === 'Rejected') {
            $pesan = "Pendaftaran usaha '{$this->usaha->nama_usaha}' ditolak oleh RW.";
            if (!empty($this->catatan)) {
                $pesan .= " Alasan: " . $this->catatan;
            }
            return [
                'umkm_usaha_id' => $this->usaha->id,
                'status'        => 'Rejected',
                'type'          => 'danger',
                'icon'          => 'bi-x-circle-fill',
                'judul'         => 'Pendaftaran UMKM Ditolak',
                'pesan'         => $pesan,
                'url'           => route('warga.umkm.galeri'),
            ];
        } else {
            return [
                'umkm_usaha_id' => $this->usaha->id,
                'status'        => 'Pending',
                'type'          => 'warning',
                'icon'          => 'bi-hourglass-split',
                'judul'         => 'Pendaftaran UMKM Terkirim',
                'pesan'         => "Pendaftaran usaha '{$this->usaha->nama_usaha}' telah diajukan dan sedang menunggu verifikasi RW.",
                'url'           => route('warga.umkm.kelola'),
            ];
        }
    }
}
