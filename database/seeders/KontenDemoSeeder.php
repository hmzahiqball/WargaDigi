<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Berita;
use App\Models\GaleriDokumentasi;
use App\Models\KategoriKonten;
use App\Models\MasterRt;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Seeder DEMO: berita, agenda, pengumuman, dan galeri dokumentasi (tingkat RW dan RT).
 *
 * - Kategori dari master kategori konten, slug otomatis.
 * - Gambar demo disalin dari public/images ke folder unggahan (berita: storage/berita,
 *   agenda: public/assets/images/agenda, galeri: public/assets/images/galeri), sehingga
 *   menghapus/mengganti foto lewat aplikasi tidak menghapus gambar bawaan.
 * - Tanggal dihitung dari hari seeding (selalu ada agenda mendatang dan arsip).
 * - Status beragam untuk demo alur persetujuan (Publish, Review, Revisi, Draft).
 * Idempoten (updateOrCreate per slug).
 */
class KontenDemoSeeder extends Seeder
{
    private const GAMBAR = ['berita-1.jpg', 'berita-2.jpg', 'berita-3.jpg', 'hero-main.jpg', 'hero-side-1.jpg', 'hero-side-2.jpg'];

    public function run(): void
    {
        $op = [
            'RW' => User::where('username', 'opkontenrw21')->value('id'),
            '01' => User::where('username', 'opkontenrt01rw21')->value('id'),
            '03' => User::where('username', 'opkonten_3')->value('id'),
        ];
        $rt = ['RW' => null, '01' => MasterRt::where('kode_rt', '01')->value('id'), '03' => MasterRt::where('kode_rt', '03')->value('id')];
        $penyetuju = User::where('username', 'adminrw21')->value('id');
        $kategori = KategoriKonten::pluck('id', 'nama');

        $this->berita($op, $rt, $penyetuju, $kategori);
        $this->agenda($op, $rt, $penyetuju, $kategori);
        $this->pengumuman($op, $rt, $penyetuju, $kategori);
    }

    /** Salin gambar demo ke disk public (folder berita) lalu kembalikan nilai featured_image. */
    private function fotoBerita(int $i): string
    {
        $nama = self::GAMBAR[$i % count(self::GAMBAR)];
        $tujuan = 'berita/demo-' . $nama;
        if (! Storage::disk('public')->exists($tujuan) && is_file(public_path('images/' . $nama))) {
            Storage::disk('public')->put($tujuan, File::get(public_path('images/' . $nama)));
        }

        return '/storage/' . $tujuan;
    }

    /** Salin gambar demo ke folder publik unggahan (agenda/galeri) lalu kembalikan path relatif public. */
    private function fotoPublik(string $folder, string $prefix, int $i): ?string
    {
        $nama = self::GAMBAR[$i % count(self::GAMBAR)];
        $relatif = "assets/images/{$folder}/{$prefix}_demo_{$i}.jpg";
        if (! is_file(public_path($relatif)) && is_file(public_path('images/' . $nama))) {
            File::ensureDirectoryExists(public_path("assets/images/{$folder}"));
            File::copy(public_path('images/' . $nama), public_path($relatif));
        }

        return is_file(public_path($relatif)) ? $relatif : null;
    }

    private function atributStatus(string $status, ?string $penyetuju, $tanggal): array
    {
        return [
            'status'          => $status,
            'approval_id'     => $status === 'Publish' ? $penyetuju : null,
            'tanggal_publish' => $status === 'Publish' ? $tanggal : null,
            'catatan_revisi'  => $status === 'Revisi' ? 'Mohon lengkapi waktu dan lokasi kegiatan.' : null,
        ];
    }

    private function berita(array $op, array $rt, ?string $penyetuju, $kategori): void
    {
        // judul, unit (RW/01/03), kategori, status, hari lalu, isi
        $data = [
            ['Kerja Bakti Bersih Lingkungan Sukses Digelar', 'RW', 'Sosial', 'Publish', 2,
                'Ratusan warga RW 21 turun bersama membersihkan saluran air dan taman. Kegiatan ditutup dengan sarapan bersama di Balai RW.'],
            ['Perbaikan Jalan Utama Komplek Rampung', 'RW', 'Infrastruktur', 'Publish', 6,
                'Perbaikan jalan utama sepanjang 300 meter telah selesai. Terima kasih atas iuran dan dukungan seluruh warga.'],
            ['Posyandu Balita dan Lansia Bulan Ini', 'RW', 'Kesehatan', 'Publish', 10,
                'Posyandu melayani penimbangan balita, imunisasi, dan cek tekanan darah lansia. Warga diimbau membawa buku KIA.'],
            ['Jadwal Ronda Malam Diperbarui', 'RW', 'Keamanan', 'Publish', 15,
                'Jadwal ronda malam disusun ulang per RT agar setiap pos terjaga. Jadwal lengkap dapat dilihat di pos ronda masing-masing.'],
            ['Nonton Bareng Final Sepak Bola Warga', 'RW', 'Hiburan', 'Publish', 21,
                'Warga berkumpul menyaksikan final di lapangan RW. Acara berlangsung meriah dan tertib.'],
            ['Gotong Royong Pengecatan Pos RT 01', 'RT 01', 'Sosial', 'Publish', 4,
                'Warga RT 01 bergotong royong mengecat ulang pos ronda dan memasang lampu baru.'],
            ['Pemeriksaan Jentik Nyamuk RT 01', 'RT 01', 'Kesehatan', 'Publish', 12,
                'Kader jumantik memeriksa tempat penampungan air di setiap rumah untuk mencegah demam berdarah.'],
            ['Penghijauan Taman RT 03', 'RT 03', 'Sosial', 'Publish', 8,
                'Warga RT 03 menanam 50 bibit pohon di taman bermain anak.'],
            ['Rencana Pemasangan CCTV Lingkungan', 'RW', 'Keamanan', 'Review', 1,
                'Pengurus RW merencanakan pemasangan CCTV di empat titik masuk komplek. Masukan warga sangat diharapkan.'],
            ['Lomba Kebersihan Antar Gang', 'RT 01', 'Sosial', 'Draft', 0,
                'Draf pengumuman lomba kebersihan antar gang di RT 01.'],
            ['Perbaikan Saluran Air RT 03', 'RT 03', 'Infrastruktur', 'Revisi', 3,
                'Laporan perbaikan saluran air di gang utama RT 03.'],
        ];

        foreach ($data as $i => [$judul, $unit, $kat, $status, $hari, $isi]) {
            $kode = $unit === 'RW' ? 'RW' : substr($unit, -2);
            $slug = \Illuminate\Support\Str::slug($judul);
            Berita::updateOrCreate(['slug' => $slug], [
                'judul_berita'   => $judul,
                'kategori'       => $kat,
                'kategori_id'    => $kategori[$kat] ?? null,
                'isi_berita'     => '<p>' . e($isi) . '</p><p>Informasi lebih lanjut dapat ditanyakan kepada pengurus ' . e($unit) . '.</p>',
                'featured_image' => $this->fotoBerita($i),
                'operator_id'    => $op[$kode],
                'rt_id'          => $rt[$kode],
            ] + $this->atributStatus($status, $penyetuju, now()->subDays($hari)->setTime(9, 0)));
        }
    }

    private function agenda(array $op, array $rt, ?string $penyetuju, $kategori): void
    {
        // judul, unit, kategori, status, mulai (hari dari sekarang), jam mulai, durasi jam, lokasi, rsvp
        $data = [
            ['Rapat Pengurus dan Warga RW 21', 'RW', 'Organisasi', 'Publish', 3, 19, 2, 'Balai RW 21', true],
            ['Tabligh Akbar Akhir Bulan', 'RW', 'Sosial', 'Publish', 10, 18, 3, 'Masjid Jami Al-Ikhlas', true],
            ['Senam Sehat Bersama', 'RW', 'Kesehatan', 'Publish', 20, 6, 2, 'Lapangan RW 21', false],
            ['Bazar UMKM Warga', 'RW', 'Sosial', 'Publish', 0, -2, 8, 'Halaman Balai RW 21', false],
            ['Kerja Bakti Bulanan', 'RW', 'Sosial', 'Publish', -14, 7, 3, 'Seluruh lingkungan RW 21', false],
            ['Pelatihan Pertolongan Pertama', 'RW', 'Kesehatan', 'Publish', -30, 9, 3, 'Balai RW 21', true],
            ['Arisan Warga RT 01', 'RT 01', 'Sosial', 'Publish', 6, 16, 2, 'Rumah Ketua RT 01', false],
            ['Syukuran Pos Ronda Baru RT 03', 'RT 03', 'Sosial', 'Review', 12, 19, 2, 'Pos Ronda RT 03', false],
        ];

        foreach ($data as $i => [$judul, $unit, $kat, $status, $hari, $jam, $durasi, $lokasi, $rsvp]) {
            $kode = $unit === 'RW' ? 'RW' : substr($unit, -2);
            // Jam negatif = sudah dimulai beberapa jam lalu (agenda sedang berlangsung).
            $mulai = $jam < 0 ? now()->addHours($jam)->startOfHour() : now()->addDays($hari)->setTime($jam, 0);
            $agenda = Agenda::updateOrCreate(['slug' => \Illuminate\Support\Str::slug($judul)], [
                'judul_agenda'      => $judul,
                'kategori'          => $kat,
                'kategori_id'       => $kategori[$kat] ?? null,
                'tanggal_mulai'     => $mulai,
                'tanggal_selesai'   => $mulai->copy()->addHours($durasi),
                'lokasi'            => $lokasi,
                'detail_pengumuman' => '<p>Seluruh warga ' . e($unit === 'RW' ? 'RW 21' : $unit) . ' diundang hadir pada kegiatan ' . e($judul) . '.</p>',
                'banner_flyer'      => $this->fotoPublik('agenda', 'agenda', $i),
                'is_rsvp_enabled'   => $rsvp,
                'operator_id'       => $op[$kode],
                'rt_id'             => $rt[$kode],
            ] + array_diff_key($this->atributStatus($status, $penyetuju, null), ['tanggal_publish' => true]));

            // Galeri dokumentasi untuk agenda RW yang sudah selesai.
            if ($status === 'Publish' && $kode === 'RW' && $hari < 0) {
                GaleriDokumentasi::updateOrCreate(['agenda_id' => $agenda->id], [
                    'jumlah_peserta' => 40 + $i * 5,
                    'foto'           => array_values(array_filter([
                        $this->fotoPublik('galeri', 'galeri', $i * 2),
                        $this->fotoPublik('galeri', 'galeri', $i * 2 + 1),
                    ])),
                    'operator_id'    => $op['RW'],
                ]);
            }
        }
    }

    private function pengumuman(array $op, array $rt, ?string $penyetuju, $kategori): void
    {
        // judul, unit, kategori, status, prioritas, hari lalu, isi
        $data = [
            ['Pemadaman Listrik Terjadwal', 'RW', 'Infrastruktur', 'Publish', true, 1,
                'PLN akan melakukan pemadaman listrik terjadwal pada Sabtu pukul 09.00 sampai 14.00 WIB.'],
            ['Pembayaran Iuran Bisa Lewat Aplikasi', 'RW', 'Organisasi', 'Publish', false, 5,
                'Warga dapat membayar iuran melalui menu Iuran Warga dan mengunggah bukti transfer.'],
            ['Waspada Penipuan Mengatasnamakan Pengurus', 'RW', 'Keamanan', 'Publish', true, 9,
                'Pengurus tidak pernah meminta transfer ke rekening pribadi. Laporkan bila menerima pesan mencurigakan.'],
            ['Jadwal Pengangkutan Sampah RT 01', 'RT 01', 'Sosial', 'Publish', false, 3,
                'Sampah diangkut setiap Senin dan Kamis pagi. Mohon sampah dipilah organik dan anorganik.'],
            ['Pendataan Warga Baru RT 03', 'RT 03', 'Organisasi', 'Publish', false, 7,
                'Warga baru di RT 03 diminta melapor ke Ketua RT dengan membawa salinan KK.'],
            ['Rencana Perubahan Jadwal Posyandu', 'RW', 'Kesehatan', 'Draft', false, 0,
                'Draf perubahan jadwal posyandu.'],
        ];

        foreach ($data as [$judul, $unit, $kat, $status, $prioritas, $hari, $isi]) {
            $kode = $unit === 'RW' ? 'RW' : substr($unit, -2);
            Pengumuman::updateOrCreate(['slug' => \Illuminate\Support\Str::slug($judul)], [
                'judul_pengumuman' => $judul,
                'isi_pengumuman'   => '<p>' . e($isi) . '</p>',
                'is_priority'      => $prioritas,
                'kategori'         => $kat,
                'kategori_id'      => $kategori[$kat] ?? null,
                'operator_id'      => $op[$kode],
                'rt_id'            => $rt[$kode],
            ] + $this->atributStatus($status, $penyetuju, now()->subDays($hari)->setTime(8, 0)));
        }
    }
}
