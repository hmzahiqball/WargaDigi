<?php

namespace Tests\Feature;

use App\Services\Panduan\DocxPanduanParser;
use App\Services\Panduan\PanduanImportir;
use App\Services\Panduan\PenyesuaianTeksPanduan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

/**
 * Parser dan importir Manual Book: struktur topik, pembuangan simulasi/tokoh demo,
 * langkah bernomor, FAQ, gambar WebP, dan penyesuaian teks.
 */
class ImporPanduanTest extends TestCase
{
    private string $kerja;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kerja = storage_path('framework/testing/impor-panduan-' . uniqid());
        File::ensureDirectoryExists($this->kerja . '/sumber');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->kerja);
        parent::tearDown();
    }

    // ------------------------------------------------------------------ Pembuat docx uji

    private function p(string $teks, string $gaya = '', ?int $numId = null, bool $tebalAwal = false, string $sisa = ''): string
    {
        $pPr = $gaya !== '' || $numId !== null
            ? '<w:pPr>' . ($gaya !== '' ? '<w:pStyle w:val="' . $gaya . '"/>' : '') . ($numId !== null ? '<w:numPr><w:ilvl w:val="0"/><w:numId w:val="' . $numId . '"/></w:numPr>' : '') . '</w:pPr>'
            : '';
        $run = $tebalAwal
            ? '<w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve">' . htmlspecialchars($teks) . '</w:t></w:r>' . ($sisa !== '' ? '<w:r><w:t xml:space="preserve"> ' . htmlspecialchars($sisa) . '</w:t></w:r>' : '')
            : '<w:r><w:t xml:space="preserve">' . htmlspecialchars($teks) . '</w:t></w:r>';

        return '<w:p>' . $pPr . $run . '</w:p>';
    }

    private function gambar(string $rId, string $caption): string
    {
        return '<w:p><w:pPr><w:pStyle w:val="Caption"/></w:pPr><w:r><w:drawing><wp:inline><a:graphic><a:graphicData><pic:pic><pic:blipFill>'
            . '<a:blip r:embed="' . $rId . '"/></pic:blipFill></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r>'
            . '<w:r><w:t xml:space="preserve">' . htmlspecialchars($caption) . '</w:t></w:r></w:p>';
    }

    private function png(int $w, int $h): string
    {
        $img = imagecreatetruecolor($w, $h);
        imagefill($img, 0, 0, imagecolorallocate($img, 25, 135, 84));
        for ($x = 0; $x < $w; $x += 7) {
            imageline($img, $x, 0, $w - $x, $h, imagecolorallocate($img, ($x * 3) % 255, 200, 120));
        }
        ob_start();
        imagepng($img);
        imagedestroy($img);

        return (string) ob_get_clean();
    }

    private function buatDocx(string $nama, string $isiBody): string
    {
        $path = $this->kerja . '/sumber/' . $nama;
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?>'
            . '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"'
            . ' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
            . ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"'
            . ' xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><w:body>' . $isiBody . '</w:body></w:document>');
        $zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rIdL" Type="image" Target="media/layar.png"/><Relationship Id="rIdH" Type="image" Target="media/hp.png"/></Relationships>');
        $zip->addFromString('word/numbering.xml', '<?xml version="1.0"?><w:numbering xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">'
            . '<w:abstractNum w:abstractNumId="1"><w:lvl w:ilvl="0"><w:numFmt w:val="decimal"/></w:lvl></w:abstractNum>'
            . '<w:abstractNum w:abstractNumId="2"><w:lvl w:ilvl="0"><w:numFmt w:val="bullet"/></w:lvl></w:abstractNum>'
            . '<w:num w:numId="10"><w:abstractNumId w:val="1"/></w:num><w:num w:numId="20"><w:abstractNumId w:val="2"/></w:num></w:numbering>');
        $zip->addFromString('word/media/layar.png', $this->png(1600, 900));
        $zip->addFromString('word/media/hp.png', $this->png(390, 844));
        $zip->close();

        return $path;
    }

    private function bukuWarga(): string
    {
        return $this->buatDocx('WargaDigi21_Buku_01_Panduan_Warga.docx',
            $this->p('Daftar Isi', 'Heading1')
            . $this->p('1. Mengenal Peran Warga', 'Heading1')
            . $this->p('1.3 Data keluarga demo', 'Heading2')
            . $this->p('Keluarga Pak Budi Santoso berisi tiga orang.')
            . $this->p('4. Permohonan Surat', 'Heading1')
            . $this->p('4.2 Mengajukan Surat', 'Heading2')
            . $this->p('Menu ini dipakai untuk mengajukan surat pengantar RT/RW secara mandiri dari rumah.', 'FirstParagraph')
            . $this->p('Contoh penggunaan: Surat Keterangan Domisili', 'BlockText')
            . $this->p('Pak Budi Santoso mau membuka rekening di bank.', 'BlockText')
            . $this->p('Masuk sebagai: budisantoso', 'Compact', 20)
            . $this->p('Langkah-langkah', 'FirstParagraph')
            . $this->p('Buka menu Permohonan Surat.', 'Compact', 10, true, 'Klik di menu samping.')
            . $this->gambar('rIdL', 'Gambar W-12. Halaman Permohonan Surat')
            . $this->p('Tips penggunaan: jenis surat bisa dipilih dari beranda.', 'FirstParagraph')
            . $this->p('Pilih jenis surat.', 'Compact', 10, true, 'Pilih Surat Keterangan Domisili.')
            . $this->p('Pak Budi Santoso mendapat pemberitahuan setelah surat disetujui.', 'Compact', 20)
            . $this->p('Hasil yang terlihat', 'BodyText')
            . $this->p('Status surat menjadi Menunggu RT.', 'Compact', 20)
            . $this->p('Lanjutan proses: Ketua RT memeriksa pengajuan. Lihat Buku 02 - Ketua RT, cerita C-01 bagian 2.', 'BodyText')
            . $this->p('12. Pertanyaan yang sering muncul (FAQ)', 'Heading1')
            . $this->p('Surat saya ditolak. Apa yang harus dilakukan?', 'FirstParagraph', null, true, 'Baca alasannya lalu ajukan ulang.')
            . $this->p('Menu Bantuan di bagian bawah tidak membuka apa pun.', 'BodyText', null, true, 'Menu itu belum memiliki halaman.')
            . $this->p('13. Lampiran: Daftar Gambar', 'Heading1')
            . $this->p('W-12 Halaman Permohonan Surat'));
    }

    private function bukuUmum(): string
    {
        return $this->buatDocx('WargaDigi21_Buku_00_Umum.docx',
            $this->p('3. Login, Pilih Peran, dan Keluar', 'Heading1')
            . $this->p('3.1 Cara login ke aplikasi', 'Heading2')
            . $this->p('Halaman masuk dibuka melalui tombol Masuk di menu atas aplikasi WargaDigi.')
            . $this->p('Simulasi: Pak Budi Santoso masuk dari komputer', 'Simulasi')
            . $this->p('• Username: budisantoso', 'Simulasi')
            . $this->p('1. Buka halaman masuk.', 'Step')
            . $this->p('Klik tombol Masuk di kanan atas.')
            . '<w:p><w:r><w:drawing><a:blip r:embed="rIdH"/></w:drawing></w:r></w:p>'
            . $this->p('Gambar UM-12: Halaman Masuk ke Akun', 'Caption')
            . $this->p('2. Isi username.', 'Step')
            . $this->p('Ketik budisantoso pada kolom Username atau Nomor WhatsApp.')
            . $this->p('Catatan penting', 'Perhatian')
            . $this->p('• Password salah: ketik ulang.')
            . $this->p('6. Pertanyaan yang sering muncul (FAQ)', 'Heading1')
            . $this->p('Saya lupa password. Apa yang harus dilakukan?', 'FAQQuestion')
            . $this->p('Hubungi Ketua RT atau Admin Aplikasi.'));
    }

    // ------------------------------------------------------------------ Tes

    public function test_parser_membentuk_topik_langkah_dan_faq_tanpa_simulasi(): void
    {
        $gambar = [];
        $parser = new DocxPanduanParser(function (string $biner, string $ext) use (&$gambar) {
            $info = getimagesizefromstring($biner);
            $gambar[] = $ext;

            return ['url' => 'panduan/uji/' . count($gambar) . '.webp', 'lebar' => $info[0], 'tinggi' => $info[1], 'potret' => $info[1] > $info[0]];
        });

        $topik = collect($parser->parse($this->bukuWarga()));

        $this->assertSame(['Mengajukan Surat', 'Surat saya ditolak. Apa yang harus dilakukan?', 'Menu Bantuan di bagian bawah tidak membuka apa pun.'], $topik->pluck('judul')->all());

        $surat = $topik->first();
        $this->assertSame('4.2', $surat['nomor']);
        $this->assertSame('Permohonan Surat', $surat['bab_judul']);
        $html = $surat['isi_html'];
        $this->assertStringNotContainsString('Contoh penggunaan', $html);
        $this->assertStringNotContainsString('rekening di bank', $html);
        $this->assertStringNotContainsString('Masuk sebagai', $html);
        $this->assertStringNotContainsString('Lanjutan proses', $html);
        $this->assertStringContainsString('<section class="pb-bagian pb-bagian-langkah"><h4>Langkah-langkah</h4>', $html);
        $this->assertStringContainsString('<li><strong>Buka menu Permohonan Surat.</strong> Klik di menu samping.', $html);
        $this->assertStringContainsString('<figcaption>Halaman Permohonan Surat</figcaption>', $html);
        $this->assertStringContainsString('<div class="pb-tips"><strong>Tips:</strong> jenis surat bisa dipilih dari beranda.</div>', $html);
        $this->assertStringContainsString('<ol class="pb-langkah" start="2">', $html, 'Penomoran berlanjut setelah kotak tips.');
        $this->assertStringContainsString('pb-bagian-hasil', $html);
        $this->assertSame(['Buka menu Permohonan Surat', 'Pilih jenis surat'], $surat['kata_kunci']);
        $this->assertSame(['png'], $gambar);

        $faq = $topik[1];
        $this->assertSame('faq', $faq['jenis']);
        $this->assertSame('<p>Baca alasannya lalu ajukan ulang.</p>', $faq['isi_html']);
    }

    public function test_parser_gaya_buku_umum(): void
    {
        $parser = new DocxPanduanParser(fn ($b) => ['url' => 'panduan/u/1.webp', 'lebar' => 390, 'tinggi' => 844, 'potret' => true]);
        $topik = collect($parser->parse($this->bukuUmum()));

        $login = $topik->firstWhere('judul', 'Cara login ke aplikasi');
        $this->assertNotNull($login);
        $this->assertStringNotContainsString('Simulasi', $login['isi_html']);
        $this->assertStringContainsString('<li><strong>Buka halaman masuk.</strong><p>Klik tombol Masuk di kanan atas.</p><figure', $login['isi_html']);
        $this->assertStringContainsString('<figcaption>Halaman Masuk ke Akun</figcaption>', $login['isi_html']);
        $this->assertStringContainsString('pb-bagian-catatan', $login['isi_html']);
        $this->assertStringContainsString('<ul><li>Password salah: ketik ulang.</li></ul>', $login['isi_html']);
        $this->assertSame('faq', $topik->firstWhere('judul', 'Saya lupa password. Apa yang harus dilakukan?')['jenis']);
    }

    public function test_importir_menulis_json_gambar_dan_menyesuaikan_teks(): void
    {
        $this->bukuWarga();
        $this->bukuUmum();
        $json = $this->kerja . '/hasil/panduan.json';
        $folderGambar = $this->kerja . '/gambar';

        $hasil = app(PanduanImportir::class)->impor($this->kerja . '/sumber', $json, $folderGambar);
        $data = json_decode(File::get($json), true);
        $topik = collect($data['topik']);

        $this->assertSame($hasil['versi'], $data['versi']);
        $this->assertSame(['Semua', 'Warga'], $topik->pluck('peran')->unique()->values()->all(), 'Buku diurutkan menurut nomor.');
        $this->assertNull($topik->firstWhere('judul', 'Menu Bantuan di bagian bawah tidak membuka apa pun.'), 'FAQ menu bantuan lama dibuang.');
        $this->assertSame([], $hasil['sisa_nama']);

        $surat = $topik->firstWhere('slug', 'warga-mengajukan-surat');
        $this->assertNotNull($surat);
        $this->assertStringContainsString('Warga pemohon mendapat pemberitahuan', $surat['isi_html']);
        $this->assertStringNotContainsString('Buku 02', $surat['isi_html']);
        $this->assertSame('Menu ini dipakai untuk mengajukan surat pengantar RT/RW secara mandiri dari rumah.', $surat['ringkasan']);
        $this->assertStringContainsString('Permohonan Surat', $surat['kata_kunci']);

        $login = $topik->firstWhere('slug', 'umum-cara-login-ke-aplikasi');
        $this->assertStringContainsString('Ketik username Anda pada kolom', $login['isi_html']);
        $this->assertStringNotContainsString('budisantoso', $login['isi_teks']);
        $this->assertSame('umum-faq-saya-lupa-password-apa-yang-harus-dilakukan', $topik->firstWhere('jenis', 'faq')['slug']);

        // Gambar besar dikonversi ke WebP dan diperkecil; gambar kecil yang tidak lebih hemat
        // sebagai WebP tetap memakai berkas asli (perilaku KonversiGambarWebp).
        $berkas = collect(File::allFiles($folderGambar));
        $this->assertCount(2, $berkas);
        $this->assertSame(['webp'], $berkas->filter(fn ($f) => str_contains($f->getPath(), 'b01'))->map->getExtension()->values()->all());
        foreach ($berkas as $f) {
            $this->assertLessThanOrEqual(1366, getimagesize($f->getPathname())[0]);
        }
        preg_match('#src="/(panduan/b01/[a-f0-9]{16}\.webp)" alt="Halaman Permohonan Surat" loading="lazy" width="1366" height="768"#', $surat['isi_html'], $m);
        $this->assertNotEmpty($m, 'Atribut gambar memakai ukuran setelah diperkecil.');
    }

    public function test_penyesuaian_teks_menangani_nama_di_dalam_tebal(): void
    {
        $p = new PenyesuaianTeksPanduan();

        $this->assertSame('<p>Hasil: <strong>Warga pemohon mendapat</strong> notifikasi.</p>',
            $p->html('<p>Hasil: <strong>Pak Budi Santoso mendapat</strong> notifikasi.</p>'));
        $this->assertSame('<p>Data selesai.</p>', $p->html('<p>Pada data demo terlihat 9 surat. Data selesai.</p>'));
        $this->assertTrue($p->buangTopik('Menu Bantuan di bagian bawah tidak membuka apa pun.'));
        $this->assertFalse($p->buangTopik('Mengajukan Surat'));
    }
}
