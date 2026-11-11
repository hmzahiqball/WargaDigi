<?php

namespace App\Services\Ocr;

/**
 * Parser teks hasil OCR KTP (e-KTP) Indonesia menjadi field penduduk.
 *
 * Toleran terhadap kekacauan OCR: separator antara label dan nilai bisa
 * bermacam (: . - ~ " — dsb), dan label bisa salah eja ringan. Best-effort;
 * field yang tak ditemukan dikembalikan null agar diisi manual.
 */
class KtpParser
{
    public static function parse(?string $teks): array
    {
        $hasil = [
            'nik' => null, 'nama_lengkap' => null, 'tempat_lahir' => null,
            'tanggal_lahir' => null, 'jenis_kelamin' => null, 'alamat' => null,
            'agama' => null, 'status_perkawinan' => null, 'pekerjaan' => null,
            'kewarganegaraan' => null,
        ];

        if (empty($teks)) {
            return $hasil;
        }

        $lines = array_values(array_filter(
            array_map('trim', preg_split('/\r?\n/', $teks)),
            fn ($l) => $l !== ''
        ));
        $upperAll = strtoupper($teks);

        // --- NIK: cari baris "NIK", normalisasi huruf-mirip-angka, ambil 16 digit. ---
        foreach ($lines as $line) {
            if (preg_match('/NIK/i', $line)) {
                $digits = static::digitNik($line);
                if (strlen($digits) >= 16) {
                    $hasil['nik'] = substr($digits, 0, 16);
                    break;
                }
            }
        }
        // Fallback: cari rangkaian 16-18 "digit" (termasuk huruf mirip angka) di tiap baris.
        if (empty($hasil['nik'])) {
            foreach ($lines as $line) {
                $digits = static::digitNik($line);
                if (strlen($digits) >= 16 && strlen($digits) <= 18) {
                    $hasil['nik'] = substr($digits, 0, 16);
                    break;
                }
            }
        }

        // --- Nama: nilai setelah label "Nama" (bukan "Nama Lengkap" tabel KK). ---
        $nama = static::nilai($lines, ['NAMA']);
        $hasil['nama_lengkap'] = $nama ? static::tc($nama) : null;

        // --- Tempat/Tgl Lahir ---
        $ttl = static::nilai($lines, ['TEMPAT', 'TEMPAT/TGL LAHIR', 'TEMPAT TGL LAHIR', 'TTL']);
        if ($ttl) {
            if (preg_match('/([A-Z\s]+?)[,\.]?\s*(\d{2})[\-\/](\d{2})[\-\/](\d{4})/i', $ttl, $m)) {
                $hasil['tempat_lahir'] = static::tc(trim($m[1]));
                $hasil['tanggal_lahir'] = "{$m[4]}-{$m[3]}-{$m[2]}";
            } else {
                $hasil['tempat_lahir'] = static::tc($ttl);
            }
        }
        // Tanggal lahir fallback dari seluruh teks bila belum dapat.
        if (empty($hasil['tanggal_lahir']) && preg_match('/(\d{2})[\-\/](\d{2})[\-\/](\d{4})/', $upperAll, $m)) {
            $hasil['tanggal_lahir'] = "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // --- Jenis kelamin ---
        if (preg_match('/PEREMPUAN/i', $upperAll)) {
            $hasil['jenis_kelamin'] = 'P';
        } elseif (preg_match('/LAKI/i', $upperAll)) {
            $hasil['jenis_kelamin'] = 'L';
        }

        // --- Alamat ---
        $alamat = static::nilai($lines, ['ALAMAT']);
        if ($alamat) {
            $alamat = preg_replace('/[~_\-]{1,}/', ' ', $alamat);
            $alamat = trim(preg_replace('/\s+/', ' ', $alamat));
            // Buang ekor angka/karakter sisa OCR.
            $alamat = trim(preg_replace('/\s+\d+\s*\d*$/', '', $alamat));
            $hasil['alamat'] = static::tc($alamat);
        }

        // --- Agama (kamus) ---
        $agama = static::opsi($upperAll, ['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA', 'BUDHA', 'KONGHUCU']);
        if ($agama) {
            $hasil['agama'] = static::tc($agama === 'BUDHA' ? 'BUDDHA' : $agama);
        }

        // --- Status perkawinan (kamus; cek "BELUM KAWIN" dulu) ---
        if (preg_match('/BELUM\s*KAWIN/i', $upperAll)) {
            $hasil['status_perkawinan'] = 'Belum Kawin';
        } elseif (preg_match('/CERAI\s*HIDUP/i', $upperAll)) {
            $hasil['status_perkawinan'] = 'Cerai Hidup';
        } elseif (preg_match('/CERAI\s*MATI/i', $upperAll)) {
            $hasil['status_perkawinan'] = 'Cerai Mati';
        } elseif (preg_match('/\bKAWIN\b/i', $upperAll)) {
            $hasil['status_perkawinan'] = 'Kawin';
        }

        // --- Pekerjaan ---
        $pekerjaan = static::nilai($lines, ['PEKERJAAN']);
        if ($pekerjaan) {
            // Buang token sisa OCR (mis. "BANDUNG" cap kota) bila terselip di akhir.
            $pekerjaan = trim(preg_replace('/\s{2,}.*$/', '', $pekerjaan));
            $hasil['pekerjaan'] = static::tc($pekerjaan);
        }

        // --- Kewarganegaraan ---
        if (preg_match('/\bWNI\b/i', $upperAll)) {
            $hasil['kewarganegaraan'] = 'WNI';
        } elseif (preg_match('/\bWNA\b/i', $upperAll)) {
            $hasil['kewarganegaraan'] = 'WNA';
        }

        // --- Strategi "kolom terpisah" (khas OCR.space untuk KTP) ---
        // OCR.space sering mengeluarkan SEMUA label dulu (NIK, Nama, ...),
        // lalu SEMUA nilai berurutan. Pasangkan untuk field yang masih kosong.
        static::isiDariKolomTerpisah($lines, $hasil);

        return $hasil;
    }

    /**
     * Tangani layout OCR.space di mana label dan nilai terpisah dalam dua blok:
     *   [NIK, Nama, Tempat/Tgl Lahir, ...]  (blok label)
     *   [<nik>, <nama>, <ttl>, ...]         (blok nilai, urutan sama)
     * Mengisi field yang MASIH kosong pada $hasil (tidak menimpa yang sudah terisi).
     *
     * @param array<int,string> $lines
     */
    private static function isiDariKolomTerpisah(array $lines, array &$hasil): void
    {
        // Urutan label standar pada e-KTP.
        $urut = ['nik', 'nama', 'ttl', 'jk', 'alamat', 'rtrw', 'keldesa', 'kecamatan', 'agama', 'status', 'pekerjaan', 'kewarganegaraan'];
        $cocokLabel = [
            'nik' => '/^NIK/i', 'nama' => '/^NAMA/i', 'ttl' => '/^TEMPAT/i', 'jk' => '/^JENIS/i',
            'alamat' => '/^ALAMAT/i', 'rtrw' => '/^RT/i', 'keldesa' => '/^KEL/i', 'kecamatan' => '/^KEC/i',
            'agama' => '/^AGAMA/i', 'status' => '/^STATUS/i', 'pekerjaan' => '/^PEKERJAAN/i',
            'kewarganegaraan' => '/^KEWARGA/i',
        ];

        // Temukan baris awal blok label (NIK) dan akhir blok label (Berlaku Hingga / Kewarganegaraan).
        $idxBerlaku = null;
        foreach ($lines as $i => $l) {
            if (preg_match('/^BERLAKU/i', trim($l))) { $idxBerlaku = $i; break; }
        }
        if ($idxBerlaku === null) {
            return; // bukan pola blok-terpisah.
        }

        // Nilai-nilai adalah baris SETELAH "Berlaku Hingga", dibersihkan.
        $nilai = [];
        for ($i = $idxBerlaku + 1; $i < count($lines); $i++) {
            $v = static::bersihkanNilai($lines[$i]);
            if ($v !== '') {
                $nilai[] = $v;
            }
        }
        if (count($nilai) < 3) {
            return;
        }

        // Baris pertama blok nilai sering berupa "KABUPATEN/KOTA ..." (header), lewati.
        if (preg_match('/^(KABUPATEN|KOTA|PROVINSI)/i', $nilai[0])) {
            array_shift($nilai);
        }

        // Pasangkan berdasarkan urutan: nilai[0]=NIK, [1]=Nama, [2]=TTL, dst.
        $peta = [];
        foreach ($urut as $k => $field) {
            if (isset($nilai[$k])) {
                $peta[$field] = $nilai[$k];
            }
        }

        // Nama.
        if (empty($hasil['nama_lengkap']) && ! empty($peta['nama']) && preg_match('/[A-Z]{2,}/i', $peta['nama'])) {
            $hasil['nama_lengkap'] = static::tc($peta['nama']);
        }
        // Tempat & tanggal lahir.
        if (! empty($peta['ttl'])) {
            if (empty($hasil['tempat_lahir']) && preg_match('/([A-Z\s]+?)[,\.]?\s*(\d{2})[\-\/](\d{2})[\-\/](\d{4})/i', $peta['ttl'], $m)) {
                $hasil['tempat_lahir'] = static::tc(trim($m[1]));
                if (empty($hasil['tanggal_lahir'])) {
                    $hasil['tanggal_lahir'] = "{$m[4]}-{$m[3]}-{$m[2]}";
                }
            } elseif (empty($hasil['tempat_lahir'])) {
                $hasil['tempat_lahir'] = static::tc(preg_replace('/[,\.].*$/', '', $peta['ttl']));
            }
        }
        // Alamat (gabungkan alamat + RT/RW + kel + kec bila ada).
        if (empty($hasil['alamat']) && ! empty($peta['alamat'])) {
            $al = $peta['alamat'];
            if (! empty($peta['rtrw'])) { $al .= ', RT/RW ' . $peta['rtrw']; }
            if (! empty($peta['keldesa'])) { $al .= ', ' . static::tc($peta['keldesa']); }
            if (! empty($peta['kecamatan'])) { $al .= ', Kec. ' . static::tc($peta['kecamatan']); }
            $hasil['alamat'] = static::tc($peta['alamat']) . substr($al, strlen($peta['alamat']));
        }
        // Pekerjaan.
        if (empty($hasil['pekerjaan']) && ! empty($peta['pekerjaan'])) {
            $pk = trim(preg_replace('/\s{2,}.*$/', '', $peta['pekerjaan']));
            // Buang sisa OCR satu huruf di akhir (mis. "GURU E").
            $pk = trim(preg_replace('/\s+[A-Z]$/', '', $pk));
            $hasil['pekerjaan'] = static::tc($pk);
        }
    }

    /**
     * Ekstrak kandidat digit NIK dari sebuah baris, mengoreksi huruf yang
     * sering tertukar dengan angka pada hasil OCR (O→0, I/L→1, Z→2, dst).
     * Membuang label "NIK" dan karakter lain, lalu mengembalikan string digit.
     */
    private static function digitNik(string $line): string
    {
        // Hilangkan kata "NIK" agar huruf N/I/K tidak ikut dikonversi.
        $s = preg_replace('/NIK/i', ' ', $line);
        $s = strtoupper($s);
        $peta = [
            'O' => '0', 'Q' => '0', 'D' => '0',
            'I' => '1', 'L' => '1', 'J' => '1', '|' => '1', '!' => '1',
            'Z' => '2',
            'E' => '3',
            'A' => '4',
            'S' => '5',
            'G' => '6',
            'T' => '7',
            'B' => '8',
        ];
        $s = strtr($s, $peta);

        return preg_replace('/\D/', '', $s);
    }

    /**
     * Ambil nilai setelah salah satu label pada baris yang memuat label itu.
     * Separator antara label & nilai bisa apa pun (: . - ~ " — spasi).
     *
     * @param array<int,string> $lines
     * @param array<int,string> $labels (uppercase)
     */
    private static function nilai(array $lines, array $labels): ?string
    {
        $jml = count($lines);
        for ($i = 0; $i < $jml; $i++) {
            $line = $lines[$i];
            $up = strtoupper($line);
            foreach ($labels as $label) {
                $pos = strpos($up, $label);
                // Label harus di dekat awal baris (toleransi noise OCR di depan),
                // agar tidak salah menangkap kata yang kebetulan mengandung label.
                if ($pos === false || $pos > 5) {
                    continue;
                }
                // Ambil teks setelah label pada baris yang sama.
                $after = substr($line, $pos + strlen($label));
                $after = static::bersihkanNilai($after);
                // Buang sisa kata label majemuk (mis. "Tempat/Tgl Lahir" → sisa "/Tgl Lahir").
                $after = static::buangSisaLabel($after);
                if ($after !== '' && strlen($after) >= 2) {
                    return $after;
                }

                // Fallback: nilai ada di baris berikutnya (OCR.space sering memecah
                // label dan nilai ke baris terpisah bila titik dua tidak terbaca).
                if (isset($lines[$i + 1])) {
                    $next = static::bersihkanNilai($lines[$i + 1]);
                    // Baris berikutnya bukan label lain & cukup berisi.
                    if ($next !== '' && strlen($next) >= 2 && ! static::barisLabel($lines[$i + 1])) {
                        return $next;
                    }
                }
            }
        }

        return null;
    }

    /** Bersihkan separator & ekor OCR dari sebuah nilai. */
    private static function bersihkanNilai(string $after): string
    {
        $after = preg_replace('/^[\s:\.\-~"“”\'`—_]+/u', '', $after);
        $after = trim($after);
        // Buang ekor "Gol Darah ..." bila ikut terbaca di baris Jenis Kelamin.
        $after = preg_split('/\bGOL\b|\bGOLONGAN\b/i', $after)[0] ?? $after;

        return trim($after, " :.-~\"'`—_");
    }

    /**
     * Buang sisa kata-kata label yang menempel di depan nilai, mis.
     * "/Tgl Lahir" atau "Lahir" pada label majemuk "Tempat/Tgl Lahir".
     * Bila setelah dibuang hasilnya kosong, kembalikan '' agar pemanggil
     * jatuh ke baris berikutnya.
     */
    private static function buangSisaLabel(string $v): string
    {
        $kataLabel = ['TGL', 'TANGGAL', 'LAHIR', 'DARAH', 'GOL', 'PERKAWINAN', 'KELAMIN'];
        $tokens = preg_split('/\s+/', trim($v));
        $i = 0;
        while ($i < count($tokens)) {
            $t = strtoupper(preg_replace('/[^A-Z]/i', '', $tokens[$i]));
            if ($t === '' || in_array($t, $kataLabel, true)) {
                $i++;
                continue;
            }
            break;
        }
        $sisa = implode(' ', array_slice($tokens, $i));

        return static::bersihkanNilai($sisa);
    }

    /** Apakah sebuah baris tampak sebagai label field KTP (bukan nilai)? */
    private static function barisLabel(string $line): bool
    {
        $labels = [
            'NIK', 'NAMA', 'TEMPAT', 'TGL', 'LAHIR', 'JENIS KELAMIN', 'GOL',
            'ALAMAT', 'RT', 'RW', 'KEL', 'DESA', 'KECAMATAN', 'AGAMA',
            'STATUS PERKAWINAN', 'PEKERJAAN', 'KEWARGANEGARAAN', 'BERLAKU', 'PROVINSI',
        ];
        $up = strtoupper(trim($line));
        foreach ($labels as $l) {
            if (strpos($up, $l) === 0) {
                return true;
            }
        }

        return false;
    }

    private static function opsi(string $upper, array $options): ?string
    {
        foreach ($options as $opt) {
            if (str_contains($upper, $opt)) {
                return $opt;
            }
        }

        return null;
    }

    private static function tc(string $v): string
    {
        // Buang tanda kutip/karakter OCR di tepi sebelum menilai kapitalisasi.
        $v = trim($v, " :.-~\"'`—_\t");
        $v = preg_replace('/\s+/', ' ', $v);
        // Title Case bila tidak ada huruf kecil (abaikan tanda baca).
        if (! preg_match('/[a-z]/', $v)) {
            $v = ucwords(strtolower($v));
        }

        return $v;
    }
}
