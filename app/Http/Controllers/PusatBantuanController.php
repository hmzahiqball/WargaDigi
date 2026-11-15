<?php

namespace App\Http\Controllers;

use App\Models\PanduanTopik;
use App\Services\Panduan\PanduanPencarian;
use App\Services\Panduan\PanduanRepository;
use App\Support\TemaPanduan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Pusat Bantuan: panduan aplikasi yang bisa dicari, khusus untuk pengguna yang masuk.
 * Topik yang tampil (daftar, hasil cari, maupun halaman topik) hanya untuk peran aktif
 * pengguna ditambah panduan umum.
 */
class PusatBantuanController extends Controller
{
    private const FAQ_PER_HALAMAN = 8;

    public function __construct(private PanduanRepository $repo, private PanduanPencarian $pencarian)
    {
    }

    public function index(Request $request)
    {
        $this->repo->sinkron();
        $peran = $this->peranAktif();
        $q = $this->pertanyaan($request);

        $hasil = $q !== '' ? $this->pencarian->cari($q, $peran, 20) : collect();
        $topik = $this->repo->untukPeran($peran, ['id', 'peran', 'buku', 'bab_nomor', 'bab_judul', 'nomor', 'judul', 'slug', 'jenis', 'ringkasan', 'urutan']);

        $topikPeran = $topik->where('peran', '!=', PanduanTopik::PERAN_SEMUA);
        $bab = $topikPeran->where('jenis', 'topik')->groupBy('bab_judul');
        $umum = $topik->where('peran', PanduanTopik::PERAN_SEMUA)->where('jenis', 'topik')->groupBy('bab_judul');

        // FAQ ditampilkan sebagai akordeon (isi jawaban ikut diambil), dipecah per halaman.
        // Parameter halaman terpisah (?faq=) dan tautan kembali ke bagian FAQ (#faq).
        $faq = PanduanTopik::query()->untukPeran($peran)->where('jenis', 'faq')
            ->orderByRaw('CASE WHEN peran = ? THEN 1 ELSE 0 END', [PanduanTopik::PERAN_SEMUA])
            ->orderBy('urutan')->orderBy('id')
            ->paginate(self::FAQ_PER_HALAMAN, ['id', 'peran', 'judul', 'slug', 'isi_html'], 'faq')
            ->withQueryString()->fragment('faq');

        $populer = $this->topikPopuler($bab);

        return view('pusat-bantuan.index', compact('q', 'hasil', 'bab', 'faq', 'umum', 'peran', 'populer'));
    }

    /** Saran untuk kotak pencarian (dipanggil saat pengguna mengetik). */
    public function cari(Request $request): JsonResponse
    {
        $this->repo->sinkron();
        $q = $this->pertanyaan($request);
        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $data = $this->pencarian->cari($q, $this->peranAktif(), 8)->map(fn (PanduanTopik $t) => [
            'judul' => $t->judul,
            'bab' => $t->jenis === 'faq' ? 'Pertanyaan umum' : $t->bab_judul,
            'ringkasan' => Str::limit((string) $t->ringkasan, 110),
            'url' => route('bantuan.show', $t->slug),
        ]);

        return response()->json(['data' => $data]);
    }

    public function show(Request $request, string $slug)
    {
        $this->repo->sinkron();
        $peran = $this->peranAktif();
        $topik = $this->repo->cariSlug($slug, $peran);
        abort_if($topik === null, 404);

        $sebab = PanduanTopik::query()->where('peran', $topik->peran)->where('jenis', $topik->jenis)
            ->where('bab_judul', $topik->bab_judul)->orderBy('urutan')
            ->get(['id', 'judul', 'slug', 'nomor', 'urutan']);
        $i = $sebab->search(fn ($t) => $t->id === $topik->id);
        $sebelum = $i !== false && $i > 0 ? $sebab[$i - 1] : null;
        $sesudah = $i !== false && $i < $sebab->count() - 1 ? $sebab[$i + 1] : null;

        [$isiHtml, $daftarIsi] = TemaPanduan::daftarIsi($topik->isi_html);
        $tema = TemaPanduan::untuk($topik->isFaq() ? 'Pertanyaan' : $topik->bab_judul);

        return view('pusat-bantuan.show', compact('topik', 'sebab', 'sebelum', 'sesudah', 'peran', 'isiHtml', 'daftarIsi', 'tema'));
    }

    /**
     * Tombol "Sering dicari": topik pertama dari bab-bab tugas utama (bukan bab perkenalan),
     * mengikuti urutan menu peran.
     */
    private function topikPopuler($bab, int $jumlah = 5)
    {
        $tugas = $bab->reject(fn ($daftar, $judul) => preg_match('/mengenal|mulai menggunakan|^keluar$/iu', (string) $judul));

        // Utamakan topik tindakan ("Mengajukan ...", "Membayar ...", "Mendaftarkan ..."), satu per bab.
        $tindakan = '/^(meng|mem|men|meny|ber|cara|jika)/iu';
        $pilihan = $tugas->map(fn ($daftar) => $daftar->first(fn ($t) => preg_match($tindakan, $t->judul) && ! preg_match('/^melihat/iu', $t->judul)))
            ->filter()->values();

        // Bila kurang, lengkapi dengan topik pertama bab lain.
        foreach ($tugas as $daftar) {
            if ($pilihan->count() >= $jumlah) {
                break;
            }
            $pertama = $daftar->first();
            if ($pertama && ! $pilihan->contains('id', $pertama->id) && ! $daftar->contains(fn ($t) => $pilihan->contains('id', $t->id))) {
                $pilihan->push($pertama);
            }
        }

        return $pilihan->take($jumlah)->values();
    }

    private function peranAktif(): ?string
    {
        return $this->repo->normalisasiPeran(Auth::user()?->activeRole());
    }

    private function pertanyaan(Request $request): string
    {
        return Str::limit(trim(strip_tags((string) $request->query('q', ''))), 100, '');
    }
}
