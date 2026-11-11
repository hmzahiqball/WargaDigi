<?php

namespace App\Services\Ocr;

/**
 * Parser HEADER Kartu Keluarga dari teks OCR (OCR.space).
 *
 * Mengekstrak field header: no_kk, alamat, RT, RW, kelurahan, kecamatan,
 * kabupaten, provinsi, kode pos. Data anggota (tabel) TIDAK diekstrak — data
 * penduduk diisi via OCR KTP. Best-effort: field tak yakin null (diisi manual).
 */
class KkParser
{
    private const PROVINSI = [
        'ACEH', 'SUMATERA UTARA', 'SUMATERA BARAT', 'RIAU', 'KEPULAUAN RIAU', 'JAMBI',
        'SUMATERA SELATAN', 'BANGKA BELITUNG', 'BENGKULU', 'LAMPUNG', 'DKI JAKARTA',
        'JAWA BARAT', 'BANTEN', 'JAWA TENGAH', 'DI YOGYAKARTA', 'DAERAH ISTIMEWA YOGYAKARTA',
        'JAWA TIMUR', 'BALI', 'NUSA TENGGARA BARAT', 'NUSA TENGGARA TIMUR',
        'KALIMANTAN BARAT', 'KALIMANTAN TENGAH', 'KALIMANTAN SELATAN', 'KALIMANTAN TIMUR',
        'KALIMANTAN UTARA', 'SULAWESI UTARA', 'SULAWESI TENGAH', 'SULAWESI SELATAN',
        'SULAWESI TENGGARA', 'GORONTALO', 'SULAWESI BARAT', 'MALUKU', 'MALUKU UTARA',
        'PAPUA', 'PAPUA BARAT',
    ];

    public static function parse(?string $teks): array
    {
        $hasil = [
            'no_kk' => null, 'alamat' => null, 'rt' => null, 'rw' => null,
            'kelurahan' => null, 'kecamatan' => null, 'kabupaten' => null,
            'provinsi' => null, 'kode_pos' => null, 'anggota' => [],
        ];

        if (empty($teks)) {
            return $hasil;
        }

        $lines = array_values(array_filter(
            array_map('trim', preg_split('/\r?\n/', $teks)),
            fn ($l) => $l !== ''
        ));

        // No. KK: 16 digit pertama.
        foreach ($lines as $line) {
            if (preg_match('/(\d{16})/', preg_replace('/\s+/', '', $line), $m)) {
                $hasil['no_kk'] = $m[1];
                break;
            }
        }

        // RT/RW: token NNN/NNN.
        foreach ($lines as $line) {
            if (preg_match('/\b(\d{1,3})\s*\/\s*(\d{1,3})\b/', $line, $m)) {
                $hasil['rt'] = ltrim($m[1], '0') ?: '0';
                $hasil['rw'] = ltrim($m[2], '0') ?: '0';
                break;
            }
        }

        // Kode pos: setelah label "KODE POS" atau 5 digit berdiri sendiri.
        foreach ($lines as $line) {
            if (preg_match('/KODE\s*POS\s*[:\-]?\s*(\d{5})/i', $line, $m)) { $hasil['kode_pos'] = $m[1]; break; }
        }
        if (empty($hasil['kode_pos'])) {
            foreach ($lines as $line) {
                $t = trim($line, " :-\t");
                if (preg_match('/^(\d{5})$/', $t, $m)) { $hasil['kode_pos'] = $m[1]; break; }
            }
        }

        // Provinsi: cocokkan daftar (andal via kamus).
        $allUp = strtoupper($teks);
        foreach (self::PROVINSI as $prov) {
            if (preg_match('/\b' . preg_quote($prov, '/') . '\b/', $allUp)) {
                $hasil['provinsi'] = static::tc($prov);
                break;
            }
        }

        // Alamat: baris berawalan penanda alamat (ambil mulai penanda, potong label menyambung).
        foreach ($lines as $line) {
            $t = trim($line, " :-\t");
            $t = preg_replace('/^ALAMAT\s*[:\-]?\s*/i', '', $t);
            if (preg_match('/\b(KP\.?|KAMPUNG|JL\.?|JALAN|GG\.?|GANG|BLOK|DUSUN|PERUM|KOMP|DS\.?)\b/i', $t, $m, PREG_OFFSET_CAPTURE)) {
                $alamat = trim(substr($t, $m[0][1]));
                $alamat = preg_split('/\b(KECAMATAN|KELURAHAN|DESA|KABUPATEN|KOTA|PROVINSI|KODE\s*POS|RT\s*\/?\s*RW)\b/i', $alamat)[0] ?? $alamat;
                $alamat = trim($alamat, " :-\t");
                if (strlen($alamat) >= 4) { $hasil['alamat'] = static::tc($alamat); break; }
            }
        }

        // Kelurahan/Kecamatan/Kabupaten: nilai setelah label (OCR.space umumnya
        // menaruh nilai pada baris setelah label atau pada baris sama).
        $hasil['kelurahan'] = static::nilaiSetelahLabel($lines, ['DESA/KELURAHAN', 'DESA / KELURAHAN', 'KELURAHAN', 'DESA'], $hasil);
        $hasil['kecamatan'] = static::nilaiSetelahLabel($lines, ['KECAMATAN'], $hasil);
        $hasil['kabupaten'] = static::nilaiSetelahLabel($lines, ['KABUPATEN/KOTA', 'KABUPATEN / KOTA', 'KABUPATEN', 'KOTA'], $hasil);

        return $hasil;
    }

    /**
     * Ambil nilai setelah label (baris sama atau beberapa baris berikutnya),
     * melewati angka/label/provinsi/alamat yang sudah dipakai.
     */
    private static function nilaiSetelahLabel(array $lines, array $labels, array $hasil): ?string
    {
        $terpakai = array_map('strtoupper', array_filter([
            $hasil['provinsi'] ?? null, $hasil['alamat'] ?? null,
            $hasil['kelurahan'] ?? null, $hasil['kecamatan'] ?? null,
        ]));
        $n = count($lines);

        foreach ($lines as $i => $line) {
            $up = strtoupper(trim($line, " :-\t"));
            foreach ($labels as $label) {
                // Nilai pada baris yang sama (setelah ':' atau label).
                if (preg_match('/' . preg_quote($label, '/') . '\s*[:\-]\s*([A-Z][A-Z\s\.]{2,40})/', strtoupper($line), $m)) {
                    $val = static::bersihWilayah($m[1], $terpakai);
                    if ($val) { return $val; }
                }
                // Label berdiri sendiri → cari baris berikutnya yang berupa teks.
                if ($up === $label) {
                    for ($j = $i + 1; $j < min($i + 6, $n); $j++) {
                        $cand = trim($lines[$j], " :-\t");
                        if ($cand === '' || static::isLabel($cand) || preg_match('/\d/', $cand)) { continue; }
                        $val = static::bersihWilayah($cand, $terpakai);
                        if ($val) { return $val; }
                    }
                }
            }
        }

        return null;
    }

    private static function bersihWilayah(string $raw, array $terpakai): ?string
    {
        $val = preg_split('/\b(KECAMATAN|KELURAHAN|DESA|KABUPATEN|KOTA|PROVINSI|KODE|RT|RW|AGAMA|PEKERJAAN|GOL)\b/', strtoupper($raw))[0] ?? $raw;
        $val = trim($val, " :-.\t");
        if ($val === '' || strlen($val) < 3) { return null; }
        if (in_array($val, self::PROVINSI, true)) { return null; }
        if (in_array($val, $terpakai, true)) { return null; }
        if (str_word_count($val) > 3) { return null; }

        return static::tc($val);
    }

    private static function isLabel(string $v): bool
    {
        $up = strtoupper(trim($v, " :-\t"));
        $labels = [
            'ALAMAT', 'RT/RW', 'KODE POS', 'DESA/KELURAHAN', 'DESA / KELURAHAN',
            'KELURAHAN', 'DESA', 'KECAMATAN', 'KABUPATEN/KOTA', 'KABUPATEN / KOTA',
            'KABUPATEN', 'KOTA', 'PROVINSI', 'PROPINSI', 'NIK', 'NAMA LENGKAP', 'NO',
            'NO.', 'KARTU KELUARGA', 'REPUBLIK INDONESIA', 'NAMA KEPALA KELUARGA',
            'JENIS', 'KELAMIN', 'TEMPAT LAHIR', 'TANGGAL', 'LAHIR', 'AGAMA', 'PENDIDIKAN',
            'JENIS PEKERJAAN', 'STATUS', 'PERKAWINAN', 'KEWARGANEGARAAN', 'GOLONGAN', 'DARAH',
        ];

        return in_array($up, $labels, true);
    }

    private static function tc(string $v): string
    {
        $v = preg_replace('/\s+/', ' ', trim($v, " :-.\t"));

        return $v === strtoupper($v) ? ucwords(strtolower($v)) : $v;
    }
}
