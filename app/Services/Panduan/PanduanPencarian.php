<?php

namespace App\Services\Panduan;

use App\Models\PanduanTopik;
use Illuminate\Support\Collection;

/**
 * Pencarian Pusat Bantuan untuk satu peran.
 *
 * Jumlah topik per peran kecil (puluhan), jadi pencarian dilakukan di PHP dengan pembobotan:
 * judul > kata kunci > nama bab > ringkasan > isi. Kata umum pertanyaan ("bagaimana cara",
 * "saya", "yang") diabaikan, kata dicocokkan sebagai bagian kata (mis. "ajukan" cocok dengan
 * "mengajukan"), dan beberapa sinonim sehari-hari ikut dicari (ttd, kk, login, dll.).
 * Hanya topik untuk peran tersebut dan panduan umum yang ikut dicari.
 */
class PanduanPencarian
{
    private const KATA_ABAIKAN = [
        'bagaimana', 'gimana', 'cara', 'caranya', 'apa', 'apakah', 'yang', 'saya', 'aku', 'kami', 'kita',
        'untuk', 'dan', 'atau', 'di', 'ke', 'dari', 'ini', 'itu', 'ada', 'dengan', 'dong', 'ya', 'kah',
        'mau', 'ingin', 'bisa', 'agar', 'supaya', 'pada', 'dalam', 'jika', 'kalau', 'kok', 'sih', 'nya',
        'tolong', 'mohon', 'harus', 'perlu', 'buat', 'the', 'how', 'to',
    ];

    /** Kata pengguna -> kata lain yang dipakai di panduan. */
    private const SINONIM = [
        'ttd' => ['tanda tangan'],
        'tandatangan' => ['tanda tangan'],
        'kk' => ['kartu keluarga'],
        'ktp' => ['kartu tanda penduduk'],
        'login' => ['masuk'],
        'logout' => ['keluar'],
        'signin' => ['masuk'],
        'password' => ['kata sandi'],
        'sandi' => ['password'],
        'pw' => ['password'],
        'wa' => ['whatsapp'],
        'bayar' => ['pembayaran', 'iuran'],
        'iuran' => ['tagihan'],
        'tagihan' => ['iuran'],
        'umkm' => ['usaha'],
        'usaha' => ['umkm'],
        'toko' => ['usaha'],
        'jualan' => ['produk', 'usaha'],
        'berita' => ['konten'],
        'acara' => ['agenda'],
        'kegiatan' => ['agenda'],
        'warga' => ['penduduk'],
        'anggota' => ['keluarga'],
        'hapus' => ['menghapus'],
        'ubah' => ['edit', 'perubahan'],
        'edit' => ['ubah'],
        'tolak' => ['ditolak'],
        'setuju' => ['menyetujui', 'persetujuan'],
        'approve' => ['menyetujui', 'persetujuan'],
        'unduh' => ['download', 'pdf'],
        'download' => ['unduh'],
        'notif' => ['notifikasi'],
        'lonceng' => ['notifikasi'],
        'peran' => ['role'],
        'role' => ['peran'],
        'laporan' => ['laporan keuangan'],
    ];

    private const BOBOT = ['judul' => 8, 'kata_kunci' => 5, 'bab_judul' => 3, 'ringkasan' => 2, 'isi_teks' => 1];

    public function __construct(private PanduanRepository $repo)
    {
    }

    /**
     * @return Collection<int, PanduanTopik> diurutkan dari yang paling relevan
     */
    public function cari(string $pertanyaan, ?string $peran, int $batas = 20): Collection
    {
        $kata = $this->kataKunci($pertanyaan);
        if ($kata === []) {
            return collect();
        }
        $frasa = $this->normalisasi($pertanyaan);

        $topik = $this->repo->untukPeran($peran, ['id', 'peran', 'buku', 'bab_nomor', 'bab_judul', 'nomor', 'judul', 'slug', 'jenis', 'ringkasan', 'isi_teks', 'kata_kunci', 'urutan']);

        $minimal = count($kata) >= 3 ? (int) ceil(count($kata) / 2) : count($kata);

        return $topik->map(function (PanduanTopik $t) use ($kata, $frasa, $minimal, $peran) {
            $medan = [];
            foreach (array_keys(self::BOBOT) as $kolom) {
                $medan[$kolom] = $this->normalisasi((string) $t->{$kolom});
            }

            $skor = 0;
            $cocok = 0;
            foreach ($kata as $alternatif) {
                $terbaik = 0;
                foreach ($alternatif as $k) {
                    foreach (self::BOBOT as $kolom => $bobot) {
                        if ($this->mengandung($medan[$kolom], $k)) {
                            $terbaik = max($terbaik, $bobot);
                        }
                    }
                }
                if ($terbaik > 0) {
                    $cocok++;
                    $skor += $terbaik;
                }
            }
            if ($cocok < $minimal) {
                return null;
            }

            if (mb_strlen($frasa) >= 5 && str_contains($medan['judul'], $frasa)) {
                $skor += 12;
            } elseif (mb_strlen($frasa) >= 5 && str_contains($medan['isi_teks'], $frasa)) {
                $skor += 3;
            }
            // Panduan peran sendiri didahulukan dari panduan umum.
            if ($t->peran !== PanduanTopik::PERAN_SEMUA && $peran !== null) {
                $skor += 1;
            }
            $t->setAttribute('skor', $skor + $cocok * 2);

            return $t;
        })
            ->filter()
            ->sortBy([['skor', 'desc'], ['urutan', 'asc']])
            ->take($batas)
            ->values();
    }

    /**
     * Kata kunci beserta sinonimnya: list<list<string>> (satu kelompok per kata pengguna).
     */
    public function kataKunci(string $pertanyaan): array
    {
        $kata = preg_split('/\s+/u', $this->normalisasi($pertanyaan), -1, PREG_SPLIT_NO_EMPTY);
        $hasil = [];
        foreach ($kata as $k) {
            if (in_array($k, self::KATA_ABAIKAN, true) || mb_strlen($k) < 2) {
                continue;
            }
            $hasil[$k] = array_values(array_unique(array_merge([$k], self::SINONIM[$k] ?? [])));
        }

        return array_values(array_slice($hasil, 0, 8));
    }

    private function mengandung(string $teks, string $kata): bool
    {
        if ($teks === '') {
            return false;
        }
        // Kata pendek dicocokkan di awal kata agar "rt" tidak cocok dengan "partner".
        if (mb_strlen($kata) <= 3) {
            return (bool) preg_match('/(^|\s)' . preg_quote($kata, '/') . '/u', $teks);
        }

        return str_contains($teks, $kata);
    }

    private function normalisasi(string $teks): string
    {
        $teks = mb_strtolower($teks);
        $teks = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $teks);

        return trim(preg_replace('/\s+/u', ' ', $teks));
    }
}
