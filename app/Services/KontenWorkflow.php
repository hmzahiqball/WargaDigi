<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\User;
use App\Support\KontenScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/**
 * Alur pengajuan konten (berita, agenda, pengumuman).
 *
 * Pengaturan Admin Aplikasi `konten_approval_{jenis}` menentukan hasil saat operator mengajukan:
 * - aktif    : status Review, penyetuju diberi tahu, konten tayang setelah disetujui.
 * - nonaktif : status Publish langsung, warga diberi tahu seperti pada konten yang disetujui.
 * Konten yang sudah berstatus Review saat pengaturan dimatikan tetap diproses penyetujunya.
 */
class KontenWorkflow
{
    public const JENIS = ['berita', 'agenda', 'pengumuman'];

    public static function perluPersetujuan(string $jenis): bool
    {
        return (bool) SystemSetting::get('konten_approval_' . $jenis, true);
    }

    /** Atribut yang disimpan saat konten diajukan: Review, atau langsung Publish bila persetujuan dimatikan. */
    public static function atributAjukan(string $jenis): array
    {
        if (self::perluPersetujuan($jenis)) {
            return ['status' => 'Review'];
        }

        $atribut = ['status' => 'Publish', 'approval_id' => null, 'catatan_revisi' => null];
        // Agenda tidak punya kolom tanggal_publish.
        if (Schema::hasColumn($jenis, 'tanggal_publish')) {
            $atribut['tanggal_publish'] = now();
        }

        return $atribut;
    }

    /** Beri tahu pihak yang berkepentingan setelah konten tersimpan sebagai hasil pengajuan. */
    public static function sesudahDiajukan(Model $konten, string $jenis): void
    {
        $judul = (string) $konten->{'judul_' . $jenis};
        $label = ucfirst($jenis);

        if ($konten->status === 'Review') {
            NotificationService::notifikasiKontenReview($label, $judul, $konten->rt_id);

            return;
        }

        if ($konten->status === 'Publish') {
            $url = match ($jenis) {
                'agenda'     => Route::has('warga.agenda.show') ? route('warga.agenda.show', $konten->kunci_url) : null,
                'pengumuman' => Route::has('warga.dashboard') ? route('warga.dashboard') : null,
                default      => null,
            };
            NotificationService::notifikasiKontenPublish($label, $judul, $url, $konten->rt_id);
        }
    }

    /** Pesan untuk operator sesuai hasil pengajuan (satu pesan saja, tidak ganda). */
    public static function pesanAjukan(string $jenis, Model $konten, ?User $user, bool $diperbarui = false): string
    {
        $label = ucfirst($jenis);

        if ($konten->status === 'Publish') {
            return $diperbarui ? "{$label} berhasil diperbarui dan diterbitkan." : "{$label} berhasil diterbitkan.";
        }

        $penyetuju = KontenScope::penyetuju($jenis, $user);

        return $diperbarui
            ? "{$label} berhasil diperbarui dan diajukan, menunggu persetujuan {$penyetuju}."
            : "{$label} berhasil diajukan dan menunggu persetujuan {$penyetuju}.";
    }

    /** Teks tombol dan dialog di panel operator: "Ajukan" bila perlu persetujuan, "Terbitkan" bila tidak. */
    public static function teksOperator(string $jenis, ?User $user): array
    {
        $label = ucfirst($jenis);

        if (self::perluPersetujuan($jenis)) {
            $penyetuju = KontenScope::penyetuju($jenis, $user);

            return [
                'perlu'    => true,
                'tombol'   => "Ajukan {$label}",
                'singkat'  => 'Ajukan',
                'judul'    => "Ajukan {$jenis} ke {$penyetuju}?",
                'isi'      => "{$label} akan masuk tahap review dan menunggu persetujuan {$penyetuju}.",
                'konfirmasi' => 'Ya, ajukan!',
                'penyetuju' => $penyetuju,
            ];
        }

        return [
            'perlu'    => false,
            'tombol'   => "Terbitkan {$label}",
            'singkat'  => 'Terbitkan',
            'judul'    => "Terbitkan {$jenis} sekarang?",
            'isi'      => "{$label} akan langsung tayang bagi warga tanpa persetujuan.",
            'konfirmasi' => 'Ya, terbitkan!',
            'penyetuju' => null,
        ];
    }
}
