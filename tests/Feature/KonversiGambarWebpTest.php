<?php

namespace Tests\Feature;

use App\Models\Keluarga;
use App\Models\KategoriKonten;
use App\Models\MasterRt;
use App\Models\MutasiWarga;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use App\Models\User;
use App\Services\DokumenSementara;
use App\Services\KonversiGambarWebp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Konversi gambar ke WebP:
 * - foto konten dan UMKM dikonversi saat diunggah (profil KONTEN);
 * - KTP/KK diunggah asli ke folder sementara privat (untuk OCR) lalu dikonversi
 *   saat data disimpan (profil DOKUMEN), tanpa meninggalkan berkas sampah.
 */
class KonversiGambarWebpTest extends TestCase
{
    use RefreshDatabase;

    private array $berkasPublik = [];

    /** @var list<string> berkas di temp sistem yang dibuat test */
    private array $berkasTemp = [];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
        config(['ocr.provider' => 'null']);
        foreach (['Ketua RT', 'Warga', 'Op Konten RW'] as $r) {
            Role::findOrCreate($r, 'web');
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->berkasTemp as $f) {
            @unlink($f);
        }
        foreach ($this->berkasPublik as $path) {
            if (is_file(public_path($path))) {
                @unlink(public_path($path));
            }
        }
        parent::tearDown();
    }

    // ───────────── Helper ─────────────

    private function temp(string $ekstensi): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'webp_uji_' . bin2hex(random_bytes(8)) . '.' . $ekstensi;
        $this->berkasTemp[] = $path;

        return $path;
    }

    private function tmpGambar(int $w, int $h, string $jenis = 'jpg', bool $transparan = false): string
    {
        $img = imagecreatetruecolor($w, $h);
        if ($transparan) {
            imagealphablending($img, false);
            imagesavealpha($img, true);
            imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
            imagefilledellipse($img, (int) ($w / 2), (int) ($h / 2), (int) ($w / 2), (int) ($h / 2), imagecolorallocatealpha($img, 200, 30, 30, 0));
        } else {
            // Gradasi + noise agar ukuran file realistis (bukan warna rata).
            for ($y = 0; $y < $h; $y += 4) {
                for ($x = 0; $x < $w; $x += 4) {
                    $c = imagecolorallocate($img, ($x * 7 + $y) % 256, ($y * 3) % 256, mt_rand(0, 255));
                    imagefilledrectangle($img, $x, $y, $x + 3, $y + 3, $c);
                }
            }
        }
        $path = $this->temp($jenis);
        $jenis === 'png' ? imagepng($img, $path) : imagejpeg($img, $path, 95);
        imagedestroy($img);

        return $path;
    }

    private function upload(string $path, string $nama, string $mime = 'image/jpeg'): UploadedFile
    {
        return new UploadedFile($path, $nama, $mime, null, true);
    }

    private function dimensiWebp(string $biner): array
    {
        $info = getimagesizefromstring($biner);

        return [$info[0], $info[1], $info['mime']];
    }

    private function ketuaRt(MasterRt $rt, string $nik = '3217010101019001'): User
    {
        $u = User::create(['nik' => $nik, 'username' => 'k' . $nik, 'role' => 'Ketua RT', 'status_akun' => 'Active',
            'rt_id' => $rt->id, 'password' => 'password123']);
        $u->syncRoles(['Ketua RT']);

        return $u;
    }

    private function keluargaDanWarga(MasterRt $rt, string $nik = '3217010101010101'): array
    {
        $kk = Keluarga::create(['no_kk' => $nik, 'nik_kepala_keluarga' => $nik, 'alamat' => 'Alamat Uji', 'rt_id' => $rt->id, 'no_wa' => '081234567890']);
        $p = Penduduk::create(['keluarga_id' => $kk->id, 'nik' => $nik, 'nama_lengkap' => 'Kepala Uji', 'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1990-01-01', 'agama' => 'Islam', 'pekerjaan' => 'Wiraswasta',
            'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin']);
        $u = User::create(['nik' => $nik, 'username' => 'w' . $nik, 'role' => 'Warga', 'status_akun' => 'Active',
            'rt_id' => $rt->id, 'password' => 'password123']);
        $u->syncRoles(['Warga']);

        return [$kk, $p, $u];
    }

    private function dataKeluarga(?string $kkPath, ?string $ktpPath): array
    {
        return [
            'no_kk' => '3217010100000001', 'alamat' => 'Jl. Uji No. 1', 'rw' => '21', 'kelurahan' => 'Tanimulya',
            'file_kk_path' => $kkPath,
            'penduduk' => [[
                'nik' => '3217010100000011', 'nama_lengkap' => 'Kepala Uji', 'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '1985-05-05', 'agama' => 'Islam',
                'pekerjaan' => 'Wiraswasta', 'pendidikan_terakhir' => 'SMA/SMK',
                'status_hubungan_keluarga' => 'Kepala Keluarga', 'status_perkawinan' => 'Kawin',
                'kewarganegaraan' => 'WNI', 'no_wa' => '081200000001', 'file_ktp_path' => $ktpPath,
            ]],
            'pemegang_akun' => 0,
        ];
    }

    // ───────────── Service ─────────────

    public function test_jpg_besar_diperkecil_ke_lebar_profil_dan_menjadi_webp(): void
    {
        $sumber = $this->tmpGambar(3000, 1500);
        $biner = KonversiGambarWebp::encode($sumber, KonversiGambarWebp::KONTEN);

        $this->assertNotNull($biner);
        [$w, $h, $mime] = $this->dimensiWebp($biner);
        $this->assertSame('image/webp', $mime);
        $this->assertSame(1920, $w);
        $this->assertSame(960, $h);
        $this->assertLessThan(filesize($sumber), strlen($biner));
    }

    public function test_profil_dokumen_memakai_lebar_2400(): void
    {
        $biner = KonversiGambarWebp::encode($this->tmpGambar(3200, 1600), KonversiGambarWebp::DOKUMEN);

        $this->assertSame(2400, $this->dimensiWebp($biner)[0]);
    }

    public function test_gambar_kecil_tidak_diperbesar(): void
    {
        $biner = KonversiGambarWebp::encode($this->tmpGambar(800, 600), KonversiGambarWebp::KONTEN);

        $this->assertNotNull($biner);
        $this->assertSame([800, 600], array_slice($this->dimensiWebp($biner), 0, 2));
    }

    public function test_png_transparan_tetap_transparan(): void
    {
        $biner = KonversiGambarWebp::encode($this->tmpGambar(2400, 1200, 'png', true), KonversiGambarWebp::KONTEN);

        $this->assertNotNull($biner);
        $img = imagecreatefromstring($biner);
        $alpha = (imagecolorat($img, 2, 2) >> 24) & 0x7F;
        $this->assertSame(127, $alpha, 'Sudut gambar harus tetap transparan.');
    }

    public function test_hasil_lebih_besar_tanpa_diperkecil_memakai_file_asli(): void
    {
        // PNG grafik/teks berwarna sedikit sangat efisien; WebP lossy-nya justru jauh lebih besar.
        $img = imagecreate(1000, 300);
        imagecolorallocate($img, 255, 255, 255);
        $hitam = imagecolorallocate($img, 0, 0, 0);
        for ($i = 0; $i < 40; $i++) {
            imagestring($img, 5, 10 + ($i % 8) * 120, 10 + intdiv($i, 8) * 40, 'Teks Uji ' . $i, $hitam);
        }
        $path = $this->temp('png');
        imagepng($img, $path, 9);

        $this->assertNull(KonversiGambarWebp::encode($path, KonversiGambarWebp::KONTEN));
    }

    public function test_gif_pdf_dan_file_rusak_tidak_dikonversi(): void
    {
        $gif = imagecreatetruecolor(50, 50);
        $pathGif = $this->temp('gif');
        imagegif($gif, $pathGif);
        $pathPdf = $this->temp('pdf');
        file_put_contents($pathPdf, "%PDF-1.4\n%uji");
        $pathRusak = $this->temp('jpg');
        file_put_contents($pathRusak, 'bukan gambar');

        $this->assertNull(KonversiGambarWebp::encode($pathGif, KonversiGambarWebp::KONTEN));
        $this->assertNull(KonversiGambarWebp::encode($pathPdf, KonversiGambarWebp::DOKUMEN));
        $this->assertNull(KonversiGambarWebp::encode($pathRusak, KonversiGambarWebp::KONTEN));
    }

    public function test_simpan_ke_disk_fallback_ke_file_asli_bila_tidak_bisa_dikonversi(): void
    {
        $gif = imagecreatetruecolor(50, 50);
        $pathGif = $this->temp('gif');
        imagegif($gif, $pathGif);

        $path = KonversiGambarWebp::simpanKeDisk($this->upload($pathGif, 'a.gif', 'image/gif'), 'umkm-photos', 'public', KonversiGambarWebp::KONTEN);

        $this->assertStringEndsWith('.gif', $path);
        Storage::disk('public')->assertExists($path);
    }

    // ───────────── Konten & UMKM (dikonversi saat upload) ─────────────

    public function test_foto_utama_berita_tersimpan_sebagai_webp_dan_foto_lama_dihapus(): void
    {
        $op = User::create(['nik' => '3217010101012601', 'username' => 'oprw', 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);
        $kategori = KategoriKonten::create(['nama' => 'Uji', 'warna' => 'hijau']);
        $data = ['judul_berita' => 'Kerja Bakti', 'kategori_id' => $kategori->id, 'isi_berita' => '<p>Isi</p>', 'action' => 'draft'];

        $this->actingAs($op)->post(route('opkonten.berita.store'), $data + ['foto_utama' => $this->upload($this->tmpGambar(2500, 1400), 'foto.jpg')])
            ->assertSessionHasNoErrors();
        $berita = \App\Models\Berita::firstOrFail();
        $this->assertStringEndsWith('.webp', $berita->featured_image);
        $lama = substr($berita->featured_image, strlen('/storage/'));
        Storage::disk('public')->assertExists($lama);

        $this->actingAs($op)->put(route('opkonten.berita.update', $berita), $data + ['foto_utama' => $this->upload($this->tmpGambar(1200, 800), 'baru.jpg')])
            ->assertSessionHasNoErrors();

        $baru = substr($berita->fresh()->featured_image, strlen('/storage/'));
        $this->assertNotSame($lama, $baru);
        Storage::disk('public')->assertExists($baru);
        Storage::disk('public')->assertMissing($lama);
    }

    public function test_banner_agenda_tersimpan_sebagai_webp(): void
    {
        $op = User::create(['nik' => '3217010101012601', 'username' => 'oprw', 'role' => 'Op Konten RW', 'status_akun' => 'Active', 'password' => 'password123']);
        $kategori = KategoriKonten::create(['nama' => 'Uji', 'warna' => 'hijau']);

        $this->actingAs($op)->post(route('opkonten.agenda.store'), [
            'judul_agenda' => 'Rapat Warga', 'kategori_id' => $kategori->id, 'tanggal' => now()->addDays(3)->toDateString(),
            'waktu_mulai' => '19:00', 'waktu_selesai' => '21:00', 'lokasi' => 'Balai RW',
            'detail_pengumuman' => '<p>Detail</p>', 'action' => 'draft',
            'banner_flyer' => $this->upload($this->tmpGambar(2000, 2000), 'banner.jpg'),
        ])->assertSessionHasNoErrors();

        $banner = \App\Models\Agenda::firstOrFail()->banner_flyer;
        $this->berkasPublik[] = $banner;
        $this->assertMatchesRegularExpression('#^assets/images/agenda/agenda_\d+_[A-Za-z0-9]{5}\.webp$#', $banner);
        $this->assertFileExists(public_path($banner));
        $this->assertSame(1920, getimagesize(public_path($banner))[0]);
    }

    public function test_foto_galeri_tersimpan_sebagai_webp_ke_folder_publik(): void
    {
        $path = KonversiGambarWebp::simpanKePublik($this->upload($this->tmpGambar(2048, 2048), 'g.jpg'), 'assets/images/galeri', 'galeri', KonversiGambarWebp::KONTEN);
        $this->berkasPublik[] = $path;

        $this->assertMatchesRegularExpression('#^assets/images/galeri/galeri_\d+_[A-Za-z0-9]{5}\.webp$#', $path);
        $this->assertFileExists(public_path($path));
    }

    public function test_foto_produk_umkm_tersimpan_sebagai_webp(): void
    {
        $path = KonversiGambarWebp::simpanKeDisk($this->upload($this->tmpGambar(1600, 1600), 'produk.jpg'), 'umkm-photos', 'public', KonversiGambarWebp::KONTEN);

        $this->assertStringStartsWith('umkm-photos/', $path);
        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);
    }

    // ───────────── KTP/KK: sementara saat upload, dikonversi saat disimpan ─────────────

    public function test_ocr_menyimpan_berkas_asli_di_folder_sementara_privat(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $ketua = $this->ketuaRt($rt);

        $res = $this->actingAs($ketua)
            ->postJson(route('ocr.baca-ktp'), ['file' => $this->upload($this->tmpGambar(1000, 630), 'ktp.jpg')])
            ->assertOk();

        $path = $res->json('path');
        $this->assertMatchesRegularExpression('#^dokumen-sementara/' . preg_quote($ketua->id, '#') . '/[A-Za-z0-9]{40}\.jpg$#', $path);
        Storage::disk('local')->assertExists($path);       // asli, belum dikonversi
        $this->assertSame([], Storage::disk('public')->allFiles());   // tidak ada berkas publik
        $this->assertSame(route('dokumen-sementara.lihat', basename($path)), $res->json('url'));

        // Preview hanya untuk pemilik.
        $this->actingAs($ketua)->get($res->json('url'))->assertOk();
        $lain = $this->ketuaRt($rt, '3217010101019002');
        $this->actingAs($lain)->get($res->json('url'))->assertNotFound();
    }

    public function test_simpan_keluarga_mengonversi_ktp_kk_dan_menghapus_berkas_sementara(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $ketua = $this->ketuaRt($rt);
        $kk = $this->actingAs($ketua)->postJson(route('ocr.baca-kk'), ['file' => $this->upload($this->tmpGambar(3500, 2400), 'kk.jpg')])->json('path');
        $ktp = $this->actingAs($ketua)->postJson(route('ocr.baca-ktp'), ['file' => $this->upload($this->tmpGambar(1000, 630), 'ktp.jpg')])->json('path');

        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKeluarga($kk, $ktp))
            ->assertRedirect(route('rt.penduduk.index'))
            ->assertSessionHasNoErrors();

        $p = Penduduk::where('nik', '3217010100000011')->firstOrFail();
        $this->assertMatchesRegularExpression('#^dokumen/kk/[A-Za-z0-9]{40}\.webp$#', $p->file_kk);
        $this->assertMatchesRegularExpression('#^dokumen/ktp/[A-Za-z0-9]{40}\.webp$#', $p->file_ktp);
        Storage::disk('public')->assertExists([$p->file_kk, $p->file_ktp]);
        $this->assertSame(2400, getimagesizefromstring(Storage::disk('public')->get($p->file_kk))[0]);

        // Tidak ada sampah: berkas sementara terhapus.
        Storage::disk('local')->assertMissing([$kk, $ktp]);
    }

    public function test_pdf_kk_dipindah_tanpa_dikonversi(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $ketua = $this->ketuaRt($rt);
        $kk = $this->actingAs($ketua)->postJson(route('ocr.baca-kk'), [
            'file' => UploadedFile::fake()->create('kk.pdf', 50, 'application/pdf'),
        ])->json('path');

        $this->actingAs($ketua)->post(route('rt.keluarga.store'), $this->dataKeluarga($kk, null))->assertSessionHasNoErrors();

        $p = Penduduk::where('nik', '3217010100000011')->firstOrFail();
        $this->assertMatchesRegularExpression('#^dokumen/kk/[A-Za-z0-9]{40}\.pdf$#', $p->file_kk);
        Storage::disk('local')->assertMissing($kk);
    }

    public function test_path_berkas_milik_akun_lain_atau_sembarang_ditolak(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $ketuaA = $this->ketuaRt($rt);
        $ketuaB = $this->ketuaRt($rt, '3217010101019002');
        $milikB = $this->actingAs($ketuaB)->postJson(route('ocr.baca-ktp'), ['file' => $this->upload($this->tmpGambar(800, 500), 'ktp.jpg')])->json('path');
        Storage::disk('public')->put('dokumen/ktp/orang-lain.jpg', 'x');

        foreach ([$milikB, 'dokumen/ktp/orang-lain.jpg', '../../.env'] as $path) {
            $this->actingAs($ketuaA)->post(route('rt.keluarga.store'), $this->dataKeluarga(null, $path))
                ->assertSessionHasErrors('penduduk.0.file_ktp_path');
        }
        $this->assertDatabaseMissing('keluarga', ['no_kk' => '3217010100000001']);
        Storage::disk('local')->assertExists($milikB);   // berkas B tidak tersentuh
    }

    public function test_penyimpanan_gagal_tidak_meninggalkan_berkas_final(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $user = $this->ketuaRt($rt);
        $sementara = DokumenSementara::simpanUpload($this->upload($this->tmpGambar(900, 600), 'ktp.jpg'), $user);

        $berkas = DokumenSementara::untuk($user);
        try {
            $berkas->jalankan(function () use ($berkas, $sementara) {
                $berkas->finalisasi($sementara, 'dokumen/ktp');
                throw new \RuntimeException('gagal simpan');
            });
            $this->fail('Exception seharusnya diteruskan.');
        } catch (\RuntimeException $e) {
            $this->assertSame('gagal simpan', $e->getMessage());
        }

        $this->assertSame([], Storage::disk('public')->allFiles('dokumen/ktp'));
        Storage::disk('local')->assertExists($sementara); // masih bisa dicoba lagi
    }

    public function test_perintah_bersihkan_menghapus_berkas_sementara_kedaluwarsa_saja(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        $user = $this->ketuaRt($rt);
        $lama = DokumenSementara::simpanUpload($this->upload($this->tmpGambar(300, 200), 'a.jpg'), $user);
        $baru = DokumenSementara::simpanUpload($this->upload($this->tmpGambar(300, 200), 'b.jpg'), $user);
        touch(Storage::disk('local')->path($lama), now()->subHours(25)->getTimestamp());

        $this->artisan('dokumen:bersihkan-sementara')->expectsOutput('1 berkas sementara dihapus.')->assertSuccessful();

        Storage::disk('local')->assertMissing($lama);
        Storage::disk('local')->assertExists($baru);

        touch(Storage::disk('local')->path($baru), now()->subHours(25)->getTimestamp());
        $this->artisan('dokumen:bersihkan-sementara')->assertSuccessful();
        $this->assertSame([], Storage::disk('local')->directories(DokumenSementara::FOLDER)); // folder kosong ikut dihapus
    }

    public function test_mutasi_tambah_anggota_mengonversi_kk_dan_ktp_saat_pengajuan(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        [, , $warga] = $this->keluargaDanWarga($rt);
        $ktp = $this->actingAs($warga)->postJson(route('ocr.baca-ktp'), ['file' => $this->upload($this->tmpGambar(1000, 630), 'ktp.jpg')])->json('path');

        $this->actingAs($warga)->post(route('warga.keluarga.anggota.store'), [
            'nama_lengkap' => 'Anak Uji', 'nik' => '3217010101010102', 'status_hubungan_keluarga' => 'Anak',
            'jenis_kelamin' => 'L', 'tempat_lahir' => 'Bandung', 'tanggal_lahir' => '2010-01-01', 'agama' => 'Islam',
            'pekerjaan' => 'Pelajar', 'status_perkawinan' => 'Belum Kawin', 'kewarganegaraan' => 'WNI',
            'file_ktp_path' => $ktp,
            'file_kk' => $this->upload($this->tmpGambar(3000, 2000), 'kk.jpg'),
        ])->assertSessionHasNoErrors();

        $mutasi = MutasiWarga::where('jenis_mutasi', 'tambah')->firstOrFail();
        $this->assertMatchesRegularExpression('#^mutasi_kk/[A-Za-z0-9]{40}\.webp$#', $mutasi->file_bukti);
        $this->assertMatchesRegularExpression('#^dokumen/ktp/[A-Za-z0-9]{40}\.webp$#', $mutasi->data_pengajuan['file_ktp_path']);
        Storage::disk('public')->assertExists([$mutasi->file_bukti, $mutasi->data_pengajuan['file_ktp_path']]);
        Storage::disk('local')->assertMissing($ktp);
    }

    public function test_permohonan_surat_menyimpan_ktp_dan_kk_sebagai_webp(): void
    {
        $rt = MasterRt::create(['kode_rt' => '01', 'nama_rt' => '01']);
        [, $pemohon, $warga] = $this->keluargaDanWarga($rt);

        $this->actingAs($warga)->post(route('warga.surat.store'), [
            'tipe_surat' => 'Kartu Keluarga',
            'keperluan' => 'Uji', 'pernyataan' => '1',
            'file_ktp' => $this->upload($this->tmpGambar(1200, 760), 'ktp.jpg'),
            'file_kk' => $this->upload($this->tmpGambar(3000, 2000), 'kk.jpg'),
        ])->assertSessionHasNoErrors();

        $surat = PengajuanSurat::firstOrFail();
        $this->assertStringEndsWith('.webp', $surat->file_ktp);
        $this->assertStringEndsWith('.webp', $surat->file_kk);
        $this->assertSame($surat->file_ktp, $pemohon->fresh()->file_ktp);
        Storage::disk('public')->assertExists([$surat->file_ktp, $surat->file_kk]);
    }
}
