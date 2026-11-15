<?php

namespace App\Services\Panduan;

use Closure;
use DOMElement;
use DOMXPath;
use RuntimeException;
use ZipArchive;

/**
 * Membaca satu file Manual Book (.docx) menjadi daftar topik Pusat Bantuan.
 *
 * - Heading 1 = bab, Heading 2 = topik, Heading 3 = subjudul di dalam topik.
 *   Bab tanpa sub-bab menjadi satu topik. Bab FAQ dipecah menjadi satu topik per pertanyaan.
 * - Bagian khusus buku cetak dibuang: daftar isi, daftar gambar, ringkasan alur lintas
 *   peran, tokoh/data demo, blok simulasi tokoh ("Contoh penggunaan", gaya Simulasi/BlockText),
 *   dan paragraf rujukan cerita ("Lanjutan proses", "Cerita ...").
 * - Label "Langkah-langkah", "Hasil yang terlihat", "Catatan penting" menjadi bagian
 *   (section); daftar bernomor menjadi langkah berurutan; "Tips" menjadi kotak tips.
 * - Gambar diserahkan ke callback penyimpan (konversi WebP dilakukan pemanggil).
 *
 * Mendukung dua gaya buku: gaya standar (Compact/BodyText/BlockText + numbering Word) dan
 * gaya khusus Buku 00 (Simulasi, Step, Hasil, Perhatian, Tips, FAQQuestion).
 * Seluruh teks di-escape; HTML yang dihasilkan hanya memakai tag yang dibuat parser ini.
 */
class DocxPanduanParser
{
    private const NS_W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const NS_R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const NS_A = 'http://schemas.openxmlformats.org/drawingml/2006/main';

    private const LABEL = [
        'langkah-langkah' => ['langkah', 'Langkah-langkah'],
        'hasil yang terlihat' => ['hasil', 'Hasil yang terlihat'],
        'catatan penting' => ['catatan', 'Catatan penting'],
    ];

    private const BAB_DIBUANG = '/^(daftar isi|ringkasan alur|lampiran|daftar gambar)/iu';
    private const SUBBAB_DIBUANG = '/(tokoh|data keluarga demo|daftar skenario|cara menggunakan buku|seri buku panduan|pengambilan (gambar|screenshot))/iu';
    private const PARAGRAF_DIBUANG = '/^(lanjutan proses|cerita berlanjut|cerita c-\d+|cerita lengkap|cerita ini|selanjutnya\s*:|prasyarat\s*:|masuk sebagai\s*:)/iu';

    private ZipArchive $zip;
    private DOMXPath $xp;

    /** @var array<string,string> rId => path di dalam zip */
    private array $rels = [];

    /** @var array<string,array<string,string>> numId => [ilvl => numFmt] */
    private array $formatNomor = [];

    /** @var list<array<string,mixed>> */
    private array $hasil = [];

    private ?array $bab = null;
    private ?array $topik = null;
    private ?array $faq = null;
    private ?string $kategoriFaq = null;
    private bool $modeFaq = false;
    private bool $lewatBab = false;
    private bool $lewatSubbab = false;
    private bool $simulasi = false;

    /** Gambar yang menunggu keterangan (Buku 00: gambar dan caption di paragraf terpisah). */
    private array $gambarTertunda = [];

    /**
     * @param  Closure(string $biner, string $ekstensi): ?array{url:string, lebar:int, tinggi:int, potret:bool}  $simpanGambar
     */
    public function __construct(private Closure $simpanGambar)
    {
    }

    /**
     * @return list<array{bab_nomor:?string, bab_judul:string, nomor:?string, judul:string, jenis:string, isi_html:string, kata_kunci:list<string>}>
     */
    public function parse(string $pathDocx): array
    {
        $this->zip = new ZipArchive();
        if ($this->zip->open($pathDocx) !== true) {
            throw new RuntimeException("Tidak dapat membuka {$pathDocx}");
        }

        try {
            $dokumen = $this->zip->getFromName('word/document.xml');
            if ($dokumen === false) {
                throw new RuntimeException("{$pathDocx} bukan dokumen Word yang valid.");
            }

            $this->bacaRelasi();
            $this->bacaPenomoran();

            $dom = new \DOMDocument();
            $dom->loadXML($dokumen, LIBXML_NONET | LIBXML_COMPACT);
            $this->xp = new DOMXPath($dom);
            $this->xp->registerNamespace('w', self::NS_W);
            $this->xp->registerNamespace('r', self::NS_R);
            $this->xp->registerNamespace('a', self::NS_A);

            $this->resetStatus();
            $body = $this->xp->query('//w:body')->item(0);
            foreach ($body?->childNodes ?? [] as $node) {
                if (! $node instanceof DOMElement) {
                    continue;
                }
                if ($node->localName === 'p') {
                    $this->paragraf($node);
                } elseif ($node->localName === 'tbl') {
                    $this->tabel($node);
                }
            }
            $this->selesaikanTopik();
            $this->selesaikanFaq();

            return $this->hasil;
        } finally {
            $this->zip->close();
        }
    }

    private function resetStatus(): void
    {
        $this->hasil = [];
        $this->bab = $this->topik = $this->faq = null;
        $this->kategoriFaq = null;
        $this->modeFaq = $this->lewatBab = $this->lewatSubbab = $this->simulasi = false;
        $this->gambarTertunda = [];
    }

    // ------------------------------------------------------------------ Struktur dokumen

    private function bacaRelasi(): void
    {
        $this->rels = [];
        $xml = $this->zip->getFromName('word/_rels/document.xml.rels');
        if ($xml === false) {
            return;
        }
        $dom = new \DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET);
        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            $target = $rel->getAttribute('Target');
            $this->rels[$rel->getAttribute('Id')] = str_starts_with($target, '/') ? ltrim($target, '/') : 'word/' . $target;
        }
    }

    private function bacaPenomoran(): void
    {
        $this->formatNomor = [];
        $xml = $this->zip->getFromName('word/numbering.xml');
        if ($xml === false) {
            return;
        }
        $dom = new \DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET);
        $xp = new DOMXPath($dom);
        $xp->registerNamespace('w', self::NS_W);

        $abstrak = [];
        foreach ($xp->query('//w:abstractNum') as $an) {
            $id = $an->getAttributeNS(self::NS_W, 'abstractNumId');
            foreach ($xp->query('w:lvl', $an) as $lvl) {
                $abstrak[$id][$lvl->getAttributeNS(self::NS_W, 'ilvl')] = $xp->query('w:numFmt/@w:val', $lvl)->item(0)?->nodeValue ?? 'decimal';
            }
        }
        foreach ($xp->query('//w:num') as $num) {
            $aid = $xp->query('w:abstractNumId/@w:val', $num)->item(0)?->nodeValue;
            $this->formatNomor[$num->getAttributeNS(self::NS_W, 'numId')] = $abstrak[$aid] ?? [];
        }
    }

    // ------------------------------------------------------------------ Paragraf

    private function paragraf(DOMElement $p): void
    {
        $gaya = $this->gaya($p);
        $teks = $this->teksPolos($p);

        if (preg_match('/^Heading([1-3])$/', $gaya, $m)) {
            $this->heading((int) $m[1], $teks);

            return;
        }
        if ($this->bab === null || $this->lewatBab || $this->lewatSubbab) {
            return;
        }
        if ($this->modeFaq) {
            $this->paragrafFaq($p, $gaya, $teks);

            return;
        }
        if ($this->topik === null) {
            return;
        }

        // Blok simulasi tokoh beserta daftar yang menyertainya dibuang.
        if (in_array($gaya, ['BlockText', 'Simulasi'], true) || preg_match('/^(contoh penggunaan|simulasi)\s*:/iu', $teks)) {
            $this->simulasi = true;

            return;
        }
        if ($this->simulasi) {
            $bagianSimulasi = $this->punyaNomor($p) || str_starts_with($teks, '•') || $teks === '';
            if ($bagianSimulasi && $this->label($teks, $gaya) === null && ! $this->punyaGambar($p)) {
                return;
            }
            $this->simulasi = false;
        }

        if ($gaya === 'SmallNote' || preg_match(self::PARAGRAF_DIBUANG, $teks)) {
            return;
        }

        if ($this->punyaGambar($p)) {
            $gambar = $this->simpanGambarParagraf($p);
            if ($gaya === 'Caption' && $teks !== '') {
                $this->tambahFigur(array_merge($this->ambilGambarTertunda(), $gambar), $teks);
            } else {
                $this->gambarTertunda = array_merge($this->gambarTertunda, $gambar);
            }

            return;
        }
        if ($gaya === 'Caption') {
            if ($this->gambarTertunda !== []) {
                $this->tambahFigur($this->ambilGambarTertunda(), $teks);
            }

            return;
        }
        if ($this->gambarTertunda !== []) {
            $this->tambahFigur($this->ambilGambarTertunda(), '');
        }
        if ($teks === '') {
            return;
        }

        if ($label = $this->label($teks, $gaya)) {
            $this->bukaBagian($label[0], $label[1]);

            return;
        }

        if ($gaya === 'Tips' || preg_match('/^tips(\s+penggunaan)?\s*:/iu', $teks)) {
            $isi = preg_replace('/^\s*(<strong>)?\s*tips(\s+penggunaan)?\s*:\s*(<\/strong>)?\s*/iu', '', $this->inline($p));
            // Tips di tengah langkah tetap berada di dalam bagian langkah (penomoran berlanjut).
            $this->tambahHtml('<div class="pb-tips"><strong>Tips:</strong> ' . $isi . '</div>');

            return;
        }

        // Buku 00: langkah ditulis manual "1. Judul langkah." dengan gaya Step.
        if ($gaya === 'Step' && preg_match('/^\d+\.\s*(.+)$/u', $teks, $m)) {
            if (($this->topik['bagian'] ?? null) !== 'langkah') {
                $this->bukaBagian('langkah', 'Langkah-langkah');
            }
            $this->itemBernomor('<strong>' . e(trim($m[1])) . '</strong>');
            $this->topik['kata_kunci'][] = trim($m[1], " .");

            return;
        }

        if ($this->punyaNomor($p)) {
            $format = $this->formatNomor($p);
            $html = $this->inline($p);
            if ($format === 'bullet' || $this->levelNomor($p) > 0) {
                $this->itemButir($html, $this->dalamLangkah());
            } else {
                $this->itemBernomor($html);
                if ($this->dalamLangkah()) {
                    $this->topik['kata_kunci'][] = $this->judulTebal($p);
                }
            }

            return;
        }

        if (preg_match('/^[•·▪]\s*/u', $teks)) {
            $html = preg_replace('/^(\s*<strong>)?\s*[•·▪]\s*/u', '$1', $this->inline($p));
            $this->itemButir($html, $this->dalamLangkah());

            return;
        }

        // Buku 00: uraian langkah berada di paragraf polos setelah judul langkah.
        if ($this->dalamLangkah() && ($this->topik['daftar']['tag'] ?? null) === 'ol' && $gaya === '') {
            $this->tambahKeItemTerakhir('<p>' . $this->inline($p) . '</p>');

            return;
        }

        $this->tambahHtml('<p>' . $this->inline($p) . '</p>');
    }

    private function heading(int $level, string $teks): void
    {
        if ($teks === '') {
            return;
        }
        [$nomor, $judul] = $this->pisahNomor($teks);

        if ($level === 1) {
            $this->selesaikanTopik();
            $this->selesaikanFaq();
            $this->bab = ['nomor' => $nomor, 'judul' => $judul];
            $this->lewatBab = (bool) preg_match(self::BAB_DIBUANG, $judul);
            $this->lewatSubbab = false;
            $this->modeFaq = ! $this->lewatBab && (bool) preg_match('/pertanyaan yang sering|faq/iu', $judul);
            $this->kategoriFaq = null;
            if (! $this->lewatBab && ! $this->modeFaq) {
                $this->mulaiTopik($nomor, $judul, true);
            }
        } elseif ($level === 2) {
            if ($this->lewatBab || $this->modeFaq || $this->bab === null) {
                return;
            }
            $this->selesaikanTopik();
            $this->lewatSubbab = (bool) preg_match(self::SUBBAB_DIBUANG, $judul);
            if (! $this->lewatSubbab) {
                $this->mulaiTopik($nomor, $judul, false);
            }
        } elseif ($this->topik !== null && ! $this->lewatSubbab) {
            $this->tutupBagian();
            $this->tambahHtml('<h3>' . e($judul) . '</h3>');
        }
        $this->simulasi = false;
    }

    private function tabel(DOMElement $tbl): void
    {
        if ($this->topik === null || $this->modeFaq || $this->lewatBab || $this->lewatSubbab) {
            return;
        }
        $this->simulasi = false;
        if ($this->gambarTertunda !== []) {
            $this->tambahFigur($this->ambilGambarTertunda(), '');
        }

        $baris = [];
        foreach ($this->xp->query('w:tr', $tbl) as $tr) {
            $sel = [];
            foreach ($this->xp->query('w:tc', $tr) as $tc) {
                $isi = [];
                foreach ($this->xp->query('.//w:p', $tc) as $p) {
                    $h = $this->inline($p);
                    if ($h !== '') {
                        $isi[] = $h;
                    }
                }
                $sel[] = implode('<br>', $isi);
            }
            $baris[] = $sel;
        }
        if ($baris === []) {
            return;
        }

        $html = '<div class="table-responsive"><table class="table table-sm table-bordered pb-tabel"><thead><tr>';
        foreach (array_shift($baris) as $sel) {
            $html .= '<th>' . $sel . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($baris as $sel) {
            $html .= '<tr>' . implode('', array_map(fn ($s) => '<td>' . $s . '</td>', $sel)) . '</tr>';
        }
        $this->tambahHtml($html . '</tbody></table></div>');
    }

    // ------------------------------------------------------------------ FAQ

    private function paragrafFaq(DOMElement $p, string $gaya, string $teks): void
    {
        if ($teks === '' || preg_match(self::PARAGRAF_DIBUANG, $teks) || $gaya === 'SmallNote') {
            return;
        }
        if ($gaya === 'FAQQuestion') {
            $this->mulaiFaq($teks);

            return;
        }

        [$tebal, $sisa] = $this->awalTebal($p);
        $tebal = trim($tebal);
        $sisaPolos = trim(strip_tags($sisa));

        if ($tebal !== '' && $sisaPolos !== '' && mb_strlen($tebal) <= 220) {
            $this->mulaiFaq($tebal);
            $this->faq['html'] .= '<p>' . $sisa . '</p>';

            return;
        }
        if ($tebal !== '' && $sisaPolos === '' && mb_strlen($tebal) < 60 && ! preg_match('/[?.”"]$/u', $tebal)) {
            $this->selesaikanFaq();
            $this->kategoriFaq = $tebal;

            return;
        }
        if ($this->faq !== null) {
            $this->faq['html'] .= '<p>' . $this->inline($p) . '</p>';
        }
    }

    private function mulaiFaq(string $pertanyaan): void
    {
        $this->selesaikanFaq();
        $this->faq = ['judul' => $pertanyaan, 'html' => '', 'kategori' => $this->kategoriFaq];
    }

    private function selesaikanFaq(): void
    {
        if ($this->faq === null) {
            return;
        }
        if (trim(strip_tags($this->faq['html'])) !== '') {
            $this->hasil[] = [
                'bab_nomor' => $this->bab['nomor'] ?? null,
                'bab_judul' => $this->bab['judul'] ?? 'Pertanyaan yang sering muncul',
                'nomor' => null,
                'judul' => $this->faq['judul'],
                'jenis' => 'faq',
                'isi_html' => $this->faq['html'],
                'kata_kunci' => array_values(array_filter([$this->faq['kategori']])),
            ];
        }
        $this->faq = null;
    }

    // ------------------------------------------------------------------ Penyusun topik

    private function mulaiTopik(?string $nomor, string $judul, bool $pengantarBab): void
    {
        $this->topik = [
            'nomor' => $nomor,
            'judul' => $judul,
            'pengantar' => $pengantarBab,
            'html' => '',
            'bagian' => null,
            'daftar' => null,
            'hitung' => 0,
            'kata_kunci' => [],
        ];
    }

    private function selesaikanTopik(): void
    {
        if ($this->topik === null) {
            return;
        }
        if ($this->gambarTertunda !== []) {
            $this->tambahFigur($this->ambilGambarTertunda(), '');
        }
        $this->tutupBagian();

        $teks = trim(strip_tags($this->topik['html']));
        // Pengantar bab yang hanya beberapa kata (sebelum sub-bab pertama) tidak dijadikan topik.
        $layak = $this->topik['pengantar'] ? mb_strlen($teks) >= 80 : $teks !== '';

        if ($layak) {
            $this->hasil[] = [
                'bab_nomor' => $this->bab['nomor'] ?? null,
                'bab_judul' => $this->bab['judul'] ?? '',
                'nomor' => $this->topik['nomor'],
                'judul' => $this->topik['judul'],
                'jenis' => 'topik',
                'isi_html' => $this->topik['html'],
                'kata_kunci' => array_values(array_unique(array_filter($this->topik['kata_kunci']))),
            ];
        }
        $this->topik = null;
    }

    private function dalamLangkah(): bool
    {
        return ($this->topik['bagian'] ?? null) === 'langkah';
    }

    private function bukaBagian(string $jenis, string $judul): void
    {
        $this->tutupBagian();
        $this->topik['html'] .= '<section class="pb-bagian pb-bagian-' . $jenis . '"><h4>' . e($judul) . '</h4>';
        $this->topik['bagian'] = $jenis;
        $this->topik['hitung'] = 0;
    }

    private function tutupBagian(): void
    {
        $this->tutupDaftar();
        if ($this->topik['bagian'] ?? null) {
            $this->topik['html'] .= '</section>';
            $this->topik['bagian'] = null;
        }
    }

    private function tambahHtml(string $html): void
    {
        $this->tutupDaftar();
        $this->topik['html'] .= $html;
    }

    private function itemBernomor(string $html): void
    {
        if (($this->topik['daftar']['tag'] ?? null) !== 'ol') {
            $this->tutupDaftar();
            $this->topik['daftar'] = ['tag' => 'ol', 'mulai' => $this->topik['hitung'] + 1, 'item' => []];
        }
        $this->topik['daftar']['item'][] = $html;
        $this->topik['hitung']++;
    }

    private function itemButir(string $html, bool $bersarang): void
    {
        $daftar = $this->topik['daftar'];
        if ($bersarang && ($daftar['tag'] ?? null) === 'ol' && $daftar['item'] !== []) {
            $i = array_key_last($daftar['item']);
            $terakhir = $daftar['item'][$i];
            $this->topik['daftar']['item'][$i] = str_ends_with($terakhir, '</ul>')
                ? substr($terakhir, 0, -5) . '<li>' . $html . '</li></ul>'
                : $terakhir . '<ul><li>' . $html . '</li></ul>';

            return;
        }
        if (($daftar['tag'] ?? null) !== 'ul') {
            $this->tutupDaftar();
            $this->topik['daftar'] = ['tag' => 'ul', 'mulai' => 1, 'item' => []];
        }
        $this->topik['daftar']['item'][] = $html;
    }

    private function tambahKeItemTerakhir(string $html): void
    {
        $i = array_key_last($this->topik['daftar']['item']);
        $this->topik['daftar']['item'][$i] .= $html;
    }

    private function tutupDaftar(): void
    {
        $daftar = $this->topik['daftar'] ?? null;
        if (! $daftar) {
            return;
        }
        $buka = $daftar['tag'] === 'ol'
            ? '<ol class="pb-langkah"' . ($daftar['mulai'] > 1 ? ' start="' . $daftar['mulai'] . '"' : '') . '>'
            : '<ul>';
        $this->topik['html'] .= $buka . implode('', array_map(fn ($i) => '<li>' . $i . '</li>', $daftar['item'])) . '</' . $daftar['tag'] . '>';
        $this->topik['daftar'] = null;
    }

    // ------------------------------------------------------------------ Gambar

    /** @return list<array{url:string, lebar:int, tinggi:int, potret:bool}> */
    private function simpanGambarParagraf(DOMElement $p): array
    {
        $hasil = [];
        foreach ($this->xp->query('.//a:blip/@r:embed', $p) as $attr) {
            $path = $this->rels[$attr->nodeValue] ?? null;
            $biner = $path ? $this->zip->getFromName($path) : false;
            if ($biner === false || $biner === '') {
                continue;
            }
            $simpan = ($this->simpanGambar)($biner, strtolower(pathinfo($path, PATHINFO_EXTENSION)));
            if ($simpan) {
                $hasil[] = $simpan;
            }
        }

        return $hasil;
    }

    private function ambilGambarTertunda(): array
    {
        $g = $this->gambarTertunda;
        $this->gambarTertunda = [];

        return $g;
    }

    private function tambahFigur(array $gambar, string $keterangan): void
    {
        if ($gambar === [] || $this->topik === null) {
            return;
        }
        $keterangan = trim(preg_replace('/^Gambar\s+[A-Z]+-\d+[a-z]?\s*[.:]?\s*/u', '', $keterangan));
        $alt = $keterangan !== '' ? $keterangan : $this->topik['judul'];

        $layar = array_values(array_filter($gambar, fn ($g) => ! $g['potret']));
        $hp = array_values(array_filter($gambar, fn ($g) => $g['potret']));
        $html = '<figure class="pb-gambar">';
        foreach ($gambar as $g) {
            // Pasangan desktop + HP: tampilkan yang sesuai ukuran layar pengguna.
            $kelas = $layar && $hp ? ($g['potret'] ? 'd-md-none' : 'd-none d-md-block') : '';
            $kelas .= $g['potret'] ? ' pb-gambar-hp' : '';
            $src = '/' . ltrim($g['url'], '/');
            $html .= '<a href="' . e($src) . '" target="_blank" rel="noopener" class="pb-gambar-tautan ' . trim($kelas) . '">'
                . '<img src="' . e($src) . '" alt="' . e($alt) . '" loading="lazy" width="' . (int) $g['lebar'] . '" height="' . (int) $g['tinggi'] . '"></a>';
        }
        if ($keterangan !== '') {
            $html .= '<figcaption>' . e($keterangan) . '</figcaption>';
        }
        $html .= '</figure>';

        if (($this->topik['daftar']['tag'] ?? null) === 'ol') {
            $this->tambahKeItemTerakhir($html);
        } else {
            $this->tambahHtml($html);
        }
    }

    // ------------------------------------------------------------------ Teks

    private function gaya(DOMElement $p): string
    {
        return $this->xp->query('w:pPr/w:pStyle/@w:val', $p)->item(0)?->nodeValue ?? '';
    }

    private function punyaNomor(DOMElement $p): bool
    {
        return $this->xp->query('w:pPr/w:numPr/w:numId', $p)->length > 0;
    }

    private function levelNomor(DOMElement $p): int
    {
        return (int) ($this->xp->query('w:pPr/w:numPr/w:ilvl/@w:val', $p)->item(0)?->nodeValue ?? 0);
    }

    private function formatNomor(DOMElement $p): string
    {
        $id = $this->xp->query('w:pPr/w:numPr/w:numId/@w:val', $p)->item(0)?->nodeValue ?? '';

        return $this->formatNomor[$id][(string) $this->levelNomor($p)] ?? 'decimal';
    }

    private function punyaGambar(DOMElement $p): bool
    {
        return $this->xp->query('.//a:blip', $p)->length > 0;
    }

    private function label(string $teks, string $gaya): ?array
    {
        if ($gaya === 'Hasil') {
            return self::LABEL['hasil yang terlihat'];
        }
        if ($gaya === 'Perhatian') {
            return self::LABEL['catatan penting'];
        }

        return self::LABEL[mb_strtolower(rtrim(trim($teks), ':'))] ?? null;
    }

    private function pisahNomor(string $teks): array
    {
        if (preg_match('/^(\d+(?:\.\d+)*)\.?\s+(.+)$/u', $teks, $m)) {
            return [$m[1], trim($m[2])];
        }

        return [null, $teks];
    }

    private function teksPolos(DOMElement $p): string
    {
        $teks = '';
        foreach ($this->segmen($p) as [$t]) {
            $teks .= $t === "\n" ? ' ' : $t;
        }

        return trim(preg_replace('/\s+/u', ' ', $teks));
    }

    /** Segmen teks berurutan: [teks, tebal]. Baris baru ditandai "\n". */
    private function segmen(DOMElement $p): array
    {
        $hasil = [];
        foreach ($this->xp->query('.//w:r', $p) as $r) {
            if ($this->xp->query('ancestor::w:del', $r)->length) {
                continue;
            }
            $b = $this->xp->query('w:rPr/w:b', $r)->item(0);
            $tebal = $b instanceof DOMElement && ! in_array($b->getAttributeNS(self::NS_W, 'val'), ['0', 'false', 'off'], true);
            foreach ($r->childNodes as $c) {
                if (! $c instanceof DOMElement) {
                    continue;
                }
                $t = match ($c->localName) {
                    't' => $c->textContent,
                    'tab' => ' ',
                    'br', 'cr' => "\n",
                    'noBreakHyphen' => '-',
                    default => null,
                };
                if ($t !== null) {
                    $hasil[] = [$t, $tebal];
                }
            }
        }

        return $hasil;
    }

    /** HTML sebaris (teks di-escape, tebal menjadi <strong>, baris baru menjadi <br>). */
    private function inline(DOMElement $p, ?array $segmen = null): string
    {
        $segmen ??= $this->segmen($p);
        $html = '';
        $kelompok = null;
        $isi = '';
        $tutup = function () use (&$html, &$kelompok, &$isi) {
            if ($isi === '') {
                return;
            }
            $html .= $kelompok ? '<strong>' . $isi . '</strong>' : $isi;
            $isi = '';
        };
        foreach ($segmen as [$t, $tebal]) {
            if ($t === "\n") {
                $tutup();
                $html .= '<br>';

                continue;
            }
            // Spasi tidak memutus kelompok tebal.
            if ($kelompok !== null && $tebal !== $kelompok && trim($t) !== '') {
                $tutup();
            }
            if (trim($t) !== '' || $kelompok === null) {
                $kelompok = $tebal;
            }
            $isi .= e($t);
        }
        $tutup();

        return trim(preg_replace('/^(<br>)+|(<br>)+$/', '', trim($html)));
    }

    /** Teks tebal di awal paragraf (judul langkah/pertanyaan) dan HTML sisanya. */
    private function awalTebal(DOMElement $p): array
    {
        $segmen = $this->segmen($p);
        $tebal = '';
        $i = 0;
        foreach ($segmen as $i => [$t, $b]) {
            if (trim($t) === '' || $b) {
                $tebal .= $t === "\n" ? ' ' : $t;

                continue;
            }
            break;
        }
        if ($i === array_key_last($segmen) && ($segmen[$i][1] ?? false)) {
            return [$tebal, ''];
        }

        return [$tebal, $this->inline($p, array_slice($segmen, $i))];
    }

    private function judulTebal(DOMElement $p): string
    {
        return trim(trim($this->awalTebal($p)[0]), ' .:');
    }
}
