<?php

namespace App\Services;

use App\Models\GaleriDokumentasi;
use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Pengaturan Halaman Utama (landing page): teks banner, tiga gambar banner, footer
 * (deskripsi, sekretariat, kontak, copyright) dan daftar partner / "Didukung oleh".
 *
 * Disimpan sebagai satu nilai JSON `halaman_utama` di system_settings.
 * Sumber gambar banner:
 *   - bawaan : gambar bawaan di public/images
 *   - galeri : foto dari Galeri Dokumentasi (path public/assets/images/galeri/...)
 *   - unggah : unggahan baru (disk public, folder beranda), dikonversi ke WebP
 */
class HalamanUtama
{
    public const KUNCI = 'halaman_utama';

    public const FOLDER = 'beranda';

    public const MAKS_PARTNER = 12;

    /** Slot gambar banner: kunci => [label, gambar bawaan]. */
    public const SLOT = [
        'utama'    => ['Gambar Utama', 'images/hero-main.jpg'],
        'samping1' => ['Gambar Samping Atas', 'images/hero-side-1.jpg'],
        'samping2' => ['Gambar Samping Bawah', 'images/hero-side-2.jpg'],
    ];

    public static function bawaan(): array
    {
        return [
            'banner' => [
                'badge'       => 'Halo Warga RW 21',
                'judul'       => 'Digitalisasi Gotong Royong RW 21 Tanimulya',
                'deskripsi'   => 'Mewujudkan lingkungan RW 21 yang modern, transparan, dan saling mendukung melalui platform digital terpadu untuk pelayanan warga dan penyelenggaraan kegiatan.',
                'tombol_teks' => 'Layanan Warga',
                'tombol_url'  => '/layanan-mandiri',
                'gambar'      => [
                    'utama'    => ['sumber' => 'bawaan', 'path' => null, 'label' => '', 'sublabel' => ''],
                    'samping1' => ['sumber' => 'bawaan', 'path' => null, 'label' => 'Harmoni Warga', 'sublabel' => 'Kebersamaan Kita'],
                    'samping2' => ['sumber' => 'bawaan', 'path' => null, 'label' => 'Dasar & Aman', 'sublabel' => 'Untuk Kita'],
                ],
            ],
            'footer' => [
                'nama'        => 'WargaDigi 21',
                'deskripsi'   => "Digital Gotong Royong.\nMembangun lingkungan yang lebih baik melalui teknologi.",
                'sekretariat' => "Balai RW 21\nDesa Tanimulya, Kec. Ngamprah\nKabupaten Bandung Barat, Jawa Barat",
                'email'       => '',
                'telepon'     => '',
                'peta_url'    => '',
                'copyright'   => '© {tahun} WargaDigi 21 Desa Tanimulya. Digital Gotong Royong.',
                'judul_partner' => 'Didukung oleh',
                'partner'     => [],
            ],
        ];
    }

    /** Konfigurasi tersimpan digabung dengan bawaan (kunci baru otomatis terisi). */
    public static function data(): array
    {
        $tersimpan = SystemSetting::get(self::KUNCI);
        if (is_string($tersimpan)) {
            $tersimpan = json_decode($tersimpan, true);
        }

        $data = array_replace_recursive(self::bawaan(), is_array($tersimpan) ? $tersimpan : []);
        // Daftar partner bukan digabung per indeks: pakai yang tersimpan apa adanya.
        $data['footer']['partner'] = array_values($tersimpan['footer']['partner'] ?? []);

        return $data;
    }

    public static function simpanData(array $data): void
    {
        SystemSetting::set(self::KUNCI, $data); // tipe json (lihat SystemSetting::defaults)
    }

    /** URL gambar banner sesuai sumbernya; kembali ke bawaan bila berkas tidak ada lagi. */
    public static function urlGambar(string $slot, array $gambar): string
    {
        $bawaan = asset(self::SLOT[$slot][1] ?? 'images/hero-main.jpg');
        $path = $gambar['path'] ?? null;

        return match ($gambar['sumber'] ?? 'bawaan') {
            'galeri' => $path && self::fotoGaleriSah($path) && is_file(public_path($path)) ? asset($path) : $bawaan,
            'unggah' => $path && Storage::disk('public')->exists($path) ? asset('storage/' . $path) : $bawaan,
            default  => $bawaan,
        };
    }

    public static function urlLogoPartner(?string $path): ?string
    {
        return $path && Storage::disk('public')->exists($path) ? asset('storage/' . $path) : null;
    }

    /** Teks copyright dengan placeholder {tahun}. */
    public static function copyright(array $footer): string
    {
        return str_replace('{tahun}', date('Y'), (string) ($footer['copyright'] ?? ''));
    }

    /**
     * Foto Galeri Dokumentasi yang dapat dipilih: hanya agenda tingkat RW yang terbit, karena konten RT
     * hanya untuk warga RT tersebut sedangkan halaman utama bersifat publik.
     * terbaru lebih dulu: [['path' => ..., 'url' => ..., 'agenda' => ...], ...].
     */
    public static function fotoGaleri(int $batas = 60): array
    {
        $hasil = [];
        $galeri = GaleriDokumentasi::with('agenda:id,judul_agenda,status')
            ->whereHas('agenda', fn ($q) => $q->where('status', 'Publish')->whereNull('rt_id'))
            ->latest()
            ->get();

        foreach ($galeri as $g) {
            foreach ((array) ($g->foto ?? []) as $path) {
                if (! is_string($path) || ! str_starts_with($path, 'assets/images/galeri/') || ! is_file(public_path($path))) {
                    continue;
                }
                $hasil[] = ['path' => $path, 'url' => asset($path), 'agenda' => $g->agenda?->judul_agenda ?? 'Dokumentasi'];
                if (count($hasil) >= $batas) {
                    return $hasil;
                }
            }
        }

        return $hasil;
    }

    /** Path benar-benar foto Galeri Dokumentasi dari agenda RW yang terbit (mencegah path sembarang). */
    public static function fotoGaleriSah(string $path): bool
    {
        if (! str_starts_with($path, 'assets/images/galeri/') || str_contains($path, '..')) {
            return false;
        }

        return GaleriDokumentasi::whereHas('agenda', fn ($q) => $q->where('status', 'Publish')->whereNull('rt_id'))
            ->get(['foto'])
            ->contains(fn ($g) => in_array($path, (array) ($g->foto ?? []), true));
    }

    public static function unggah(UploadedFile $file, string $subfolder = ''): string
    {
        return KonversiGambarWebp::simpanKeDisk($file, trim(self::FOLDER . '/' . $subfolder, '/'), 'public', KonversiGambarWebp::KONTEN);
    }

    /** Hapus berkas unggahan halaman utama yang tidak dipakai lagi (hanya folder beranda). */
    public static function hapusBerkas(?string $path): void
    {
        if ($path && Str::startsWith($path, self::FOLDER . '/') && ! str_contains($path, '..')) {
            Storage::disk('public')->delete($path);
        }
    }
}
