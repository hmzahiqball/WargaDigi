<?php

namespace App\Support;

use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Cakupan konten (berita, agenda, pengumuman, galeri) per tingkat, seperti pemisahan unit pada keuangan.
 *
 * - rt_id NULL  : konten RW, tampil ke semua warga.
 * - rt_id terisi: konten RT, hanya dikelola Operator Konten RT / Ketua RT pada RT itu
 *                 dan hanya tampil ke warga RT tersebut.
 *
 * Peran RT tanpa RT di akunnya ditolak (403), tidak diam-diam dialihkan ke RT lain.
 */
class KontenScope
{
    public const PERAN_RT = ['Op Konten RT', 'Op. Konten RT', 'Ketua RT'];

    /** RT milik akun: kolom users.rt_id, atau RT keluarga bila kosong. Tanpa fallback ke RT mana pun. */
    public static function rtIdUser(?User $user): ?string
    {
        if (! $user) {
            return null;
        }
        $rt = $user->getRawOriginal('rt_id');
        if (! empty($rt)) {
            return $rt;
        }
        if (! $user->nik) {
            return null;
        }
        $penduduk = Penduduk::where('nik', $user->nik)->first();

        return $penduduk?->keluarga_id ? Keluarga::find($penduduk->keluarga_id)?->rt_id : null;
    }

    public static function peranRt(User $user): bool
    {
        return in_array(trim($user->activeRole() ?? ''), self::PERAN_RT, true);
    }

    /**
     * RT yang dikelola akun, atau null untuk tingkat RW.
     * Peran RT yang belum dikaitkan ke RT menghasilkan 403.
     */
    public static function cakupanKelola(User $user): ?string
    {
        if (! self::peranRt($user)) {
            return null;
        }
        $rt = self::rtIdUser($user);
        abort_if(empty($rt), 403, 'Akun Anda belum dikaitkan dengan RT. Hubungi admin.');

        return $rt;
    }

    /** Batasi query ke konten yang boleh dikelola akun (konten RT-nya, atau konten RW). */
    public static function untukKelola($query, User $user, string $kolom = 'rt_id')
    {
        $rt = self::cakupanKelola($user);

        return $rt ? $query->where($kolom, $rt) : $query->whereNull($kolom);
    }

    public static function bolehKelola(Model $model, User $user): bool
    {
        $rt = self::cakupanKelola($user);

        return $rt ? (string) $model->rt_id === (string) $rt : empty($model->rt_id);
    }

    /** Batasi query ke konten yang boleh dilihat warga: konten RW ditambah konten RT-nya. Tamu hanya konten RW. */
    public static function untukWarga($query, ?User $user, string $kolom = 'rt_id')
    {
        $rt = self::rtIdUser($user);

        return $query->where(function ($q) use ($rt, $kolom) {
            $q->whereNull($kolom);
            if ($rt) {
                $q->orWhere($kolom, $rt);
            }
        });
    }

    public static function adminRw(?User $user): bool
    {
        return $user !== null && trim($user->activeRole() ?? '') === 'Admin RW';
    }

    /**
     * Penyetuju konten (berita, agenda, pengumuman; RT maupun RW) hanya Admin RW.
     * Ketua RT dan Ketua RW tidak menyetujui konten, hanya dapat memantau yang sudah terbit.
     */
    public static function bolehMenyetujui(string $jenis, ?User $user): bool
    {
        return self::adminRw($user);
    }

    /** Batasi query ke konten yang boleh ditinjau akun ini sebagai penyetuju: semua bagi Admin RW, kosong bagi lainnya. */
    public static function untukSetuju($query, string $jenis, User $user, string $kolom = 'rt_id')
    {
        return self::adminRw($user) ? $query : $query->whereRaw('1 = 0');
    }

    /** Nama penyetuju yang ditampilkan kepada operator konten. */
    public static function penyetuju(string $jenis, ?User $user): string
    {
        return 'Admin RW';
    }
    /**
     * Konten yang boleh dipantau akun ini di Pusat Informasi (hanya untuk dilihat):
     * - Ketua RT: konten RW dan konten RT-nya, sama seperti yang tampil bagi warga RT itu.
     * - Admin RW, Ketua RW, dan pimpinan lain: seluruh konten (RW dan semua RT).
     */
    public static function untukPantau($query, User $user, string $kolom = 'rt_id')
    {
        return self::peranRt($user) ? self::untukWarga($query, $user, $kolom) : $query;
    }

    /** Konten tertentu boleh dilihat akun ini sebagai warga. */
    public static function bolehDilihatWarga(Model $model, ?User $user): bool
    {
        if (empty($model->rt_id)) {
            return true;
        }

        return self::rtIdUser($user) === (string) $model->rt_id;
    }
}
