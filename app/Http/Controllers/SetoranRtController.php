<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\NormalizesMoney;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Models\SetoranRt;
use App\Models\Tagihan;
use App\Services\IuranService;
use App\Services\NotificationService;
use App\Services\SetoranRtService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Setoran kolektif RT ke RW.
 * - Op Keuangan RT: mencatat setoran + melihat kewajiban dan riwayat RT sendiri.
 * - Op Keuangan RW: rekap seluruh RT, konfirmasi/tolak/batalkan setoran, pengaturan nominal.
 * - Admin RW / Ketua RW / Pimpinan: rekap read-only (rekapRw).
 */
class SetoranRtController extends Controller
{
    use NormalizesMoney;

    /** @return 'RT'|'RW'|null */
    private function unit(): ?string
    {
        $role = trim(Auth::user()->activeRole() ?? '');

        return match (true) {
            in_array($role, ['Op Keuangan RT', 'Op. Keuangan RT', 'Bendahara RT'], true) => 'RT',
            in_array($role, ['Op Keuangan RW', 'Op. Keuangan RW', 'Bendahara RW'], true) => 'RW',
            default => null,
        };
    }

    private function rtId(): ?string
    {
        $user = Auth::user();
        $rtId = $user->rt_id;
        if (! $rtId && $user->nik) {
            $rtId = Penduduk::with('keluarga')->where('nik', $user->nik)->first()?->keluarga?->rt_id;
        }

        return $rtId;
    }

    public function index(Request $request)
    {
        return match ($this->unit()) {
            'RT' => $this->tampilanRt($request),
            'RW' => $this->tampilanRw($request, true),
            default => abort(403, 'Menu setoran hanya untuk Operator Keuangan RT dan RW.'),
        };
    }

    /** Rekap read-only untuk Admin RW, Ketua RW, dan Pimpinan. */
    public function rekapRw(Request $request)
    {
        return $this->tampilanRw($request, false);
    }

    // ───────────────────────── Tampilan ─────────────────────────

    private function tampilanRt(Request $request)
    {
        $rtId = $this->rtId();
        abort_unless($rtId, 403, 'Unit RT tidak terdefinisi untuk akun ini.');

        $tahun = (int) $request->input('tahun', now()->format('Y'));
        $sampai = $tahun === (int) now()->format('Y') ? (int) now()->format('n') : 12;

        $rekap = SetoranRtService::rekapRt($rtId, $tahun, $sampai);
        $riwayat = $this->paginasi(
            SetoranRtService::kelompok(
                SetoranRt::with('tagihan')->where('rt_id', $rtId)->where('periode_tahun', $tahun)->orderByDesc('created_at')->get()
            ),
            $request,
            'riwayat-setoran'
        );
        $bisaBulanan = SetoranRtService::bisaDisetorBulanan($rtId, $tahun, $sampai);

        return view('opKeuangan.setoran-rt', [
            'rt'          => MasterRt::find($rtId),
            'tahun'       => $tahun,
            'daftarTahun' => range((int) now()->format('Y') + 1, 2023),
            'rekap'       => $rekap,
            'bisaBulanan' => $bisaBulanan,
            'tagihanRt'   => SetoranRtService::rekapTagihanRt($rtId),
            'riwayat'     => $riwayat,
            'bulanSkrg'   => (int) now()->format('n'),
            'tahunSkrg'   => (int) now()->format('Y'),
        ]);
    }

    private function tampilanRw(Request $request, bool $bisaKelola)
    {
        $tahun = (int) $request->input('tahun', now()->format('Y'));
        $bulanDefault = $tahun === (int) now()->format('Y') ? (int) now()->format('n') : 12;
        $bulan = max(1, min(12, (int) $request->input('bulan', $bulanDefault)));

        $rekap = SetoranRtService::rekapSemuaRt($tahun, $bulan);
        $menunggu = SetoranRtService::kelompok(
            SetoranRt::with(['rt', 'pembuat', 'tagihan'])->where('status', 'menunggu')->orderBy('created_at')->get()
        );
        $riwayat = $this->paginasi(
            SetoranRtService::kelompok(
                SetoranRt::with(['rt', 'pembuat', 'tagihan'])->where('periode_tahun', $tahun)
                    ->whereIn('status', ['dikonfirmasi', 'ditolak', 'dibatalkan'])->orderByDesc('updated_at')->get()
            )->sortByDesc('updated_at')->values(),
            $request,
            'tabRiwayat'
        );

        return view('opKeuangan.setoran-rw', [
            'bisaKelola'  => $bisaKelola,
            'tahun'       => $tahun,
            'bulan'       => $bulan,
            'daftarTahun' => range((int) now()->format('Y') + 1, 2023),
            'rekap'       => $rekap,
            'menunggu'    => $menunggu,
            'tagihanRw'   => Tagihan::where('unit', 'RW')->where('via_rt', true)->orderByDesc('created_at')->get()
                ->map(fn (Tagihan $t) => (object) ['tagihan' => $t, 'rekap' => SetoranRtService::rekapTagihanSemuaRt($t)]),
            'riwayat'     => $riwayat,
            'pengaturan'  => SetoranRtService::pengaturan($tahun),
            'riwayatPengaturan' => SetoranRtService::riwayatPengaturan($tahun),
            'bulanMulaiTerawal' => SetoranRtService::bulanMulaiTerawal($tahun),
            'pengaturanAwal'    => SetoranRtService::belumAdaPengaturan($tahun),
            'namaBulan'   => IuranService::BULAN,
        ]);
    }

    /** Paginasi koleksi setoran (satu entri per batch): 10 per halaman, parameter `halaman`. */
    private function paginasi(Collection $items, Request $request, string $fragment, int $perHalaman = 10): LengthAwarePaginator
    {
        $halaman = LengthAwarePaginator::resolveCurrentPage('halaman');

        return (new LengthAwarePaginator(
            $items->forPage($halaman, $perHalaman)->values(),
            $items->count(),
            $perHalaman,
            $halaman,
            ['path' => $request->url(), 'pageName' => 'halaman']
        ))->withQueryString()->fragment($fragment);
    }

    // ───────────────────────── Aksi RT ─────────────────────────

    /**
     * Catat satu setoran (satu transfer/serah terima) untuk beberapa bulan iuran bulanan
     * ("jenis" = bulanan, items[bulan] = nominal) atau beberapa Iuran Lainnya dari RW
     * ("jenis" = lainnya, items[tagihan_id] = nominal).
     */
    public function store(Request $request)
    {
        abort_unless($this->unit() === 'RT', 403);
        $rtId = $this->rtId();
        abort_unless($rtId, 403);

        // Nominal per item datang berformat ribuan ("50.000"): ambil digitnya saja.
        $request->merge([
            'items' => collect((array) $request->input('items', []))
                ->map(fn ($n) => (int) preg_replace('/\D/', '', (string) $n))->all(),
        ]);

        $data = $request->validate([
            'jenis'         => 'required|in:bulanan,lainnya',
            'tahun'         => 'required_if:jenis,bulanan|nullable|integer|min:2023|max:' . ((int) now()->format('Y') + 1),
            'items'         => 'required|array|min:1',
            'items.*'       => 'integer|min:0',
            'tanggal_setor' => 'required|date|before_or_equal:today',
            'metode'        => 'required|in:tunai,transfer',
            'bukti_file'    => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $bukti = $request->hasFile('bukti_file') ? $request->file('bukti_file')->store('dokumen/setoran', 'public') : null;
        $isi = [
            'tanggal_setor' => $data['tanggal_setor'],
            'metode'        => $data['metode'],
            'bukti_file'    => $bukti,
            'catatan'       => $data['catatan'] ?? null,
        ];

        try {
            $batch = $data['jenis'] === 'bulanan'
                ? SetoranRtService::buatBatchBulanan($rtId, (int) $data['tahun'], $data['items'], $isi, Auth::id())
                : SetoranRtService::buatBatchLainnya($rtId, $data['items'], $isi, Auth::id());
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $grup = SetoranRtService::kelompok(SetoranRt::with('tagihan')->where('batch_id', $batch)->get())->first();
        $rt = MasterRt::find($rtId);
        NotificationService::kirimKeBanyak(NotificationService::opKeuangan('RW'), [
            'type'     => 'info',
            'icon'     => 'bi-box-arrow-in-down',
            'judul'    => 'Setoran RT Menunggu Konfirmasi',
            'pesan'    => 'RT ' . ($rt->kode_rt ?? '') . ' mencatat setoran Rp ' . number_format($grup->nominal, 0, ',', '.')
                . ' untuk ' . $grup->periode_label . '.',
            'url'      => route('opkeuangan.setoran.index'),
            'kategori' => 'keuangan',
        ]);

        return back()->with('success', 'Setoran ' . $grup->periode_label . ' sebesar Rp ' . number_format($grup->nominal, 0, ',', '.')
            . ' dicatat dan menunggu konfirmasi Operator Keuangan RW.');
    }

    public function destroy(string $id)
    {
        abort_unless($this->unit() === 'RT', 403);
        $setoran = SetoranRt::findOrFail($id);
        abort_unless($setoran->rt_id === $this->rtId(), 403);

        $grup = $setoran->grup();
        if ($grup->contains(fn ($s) => ! in_array($s->status, ['menunggu', 'ditolak'], true))) {
            return back()->with('error', 'Setoran yang sudah dikonfirmasi hanya dapat dibatalkan oleh Operator Keuangan RW.');
        }
        SetoranRt::whereIn('id', $grup->pluck('id'))->delete();

        return back()->with('success', 'Catatan setoran dihapus.');
    }
    // ───────────────────────── Aksi RW ─────────────────────────

    public function konfirmasi(string $id)
    {
        abort_unless($this->unit() === 'RW', 403);
        $setoran = SetoranRt::findOrFail($id);

        if (! SetoranRtService::konfirmasi($setoran, Auth::id())) {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }
        $this->kabariRt($setoran->fresh(), true);

        return back()->with('success', 'Setoran dikonfirmasi dan tercatat di kas RT (pengeluaran) dan kas RW (pemasukan).');
    }

    public function tolak(Request $request, string $id)
    {
        abort_unless($this->unit() === 'RW', 403);
        $request->validate(['alasan' => 'required|string|max:500']);
        $setoran = SetoranRt::findOrFail($id);

        if (! SetoranRtService::tolak($setoran, $request->input('alasan'), Auth::id())) {
            return back()->with('error', 'Setoran ini sudah diproses.');
        }
        $this->kabariRt($setoran->fresh(), false);

        return back()->with('success', 'Setoran ditolak dan RT diberi tahu.');
    }

    public function batalkan(Request $request, string $id)
    {
        abort_unless($this->unit() === 'RW', 403);
        $request->validate(['alasan' => 'required|string|max:500']);
        $setoran = SetoranRt::findOrFail($id);

        $ringkas = $this->ringkasGrup($setoran);
        if (! SetoranRtService::batalkan($setoran, $request->input('alasan'))) {
            return back()->with('error', 'Hanya setoran yang sudah dikonfirmasi yang dapat dibatalkan.');
        }
        NotificationService::kirimKeBanyak(NotificationService::opKeuangan('RT', $setoran->rt_id), [
            'type'     => 'warning',
            'icon'     => 'bi-arrow-counterclockwise',
            'judul'    => 'Setoran Dibatalkan',
            'pesan'    => 'Setoran ' . $ringkas->periode_label
                . ' dibatalkan oleh RW. Alasan: ' . $request->input('alasan'),
            'url'      => route('opkeuangan.setoran.index'),
            'kategori' => 'keuangan',
        ]);

        return back()->with('success', 'Setoran dibatalkan dan transaksi kas terkait dihapus.');
    }

    public function simpanPengaturan(Request $request)
    {
        abort_unless($this->unit() === 'RW', 403);

        $request->merge([
            'default' => $this->normalizeMoney($request->input('default')),
            'per_rt'  => collect((array) $request->input('per_rt', []))
                ->map(fn ($n) => ($n === null || trim((string) $n) === '') ? null : $this->normalizeMoney($n))->all(),
        ]);
        $data = $request->validate([
            'tahun'             => 'required|integer|min:2023|max:2100',
            'default'           => 'required|integer|min:0',
            'per_rt'            => 'nullable|array',
            'per_rt.*'          => 'nullable|integer|min:0',
            'berlaku_mulai'     => 'nullable|integer|min:1|max:12',
            'konfirmasi_cutoff' => 'nullable|boolean',
        ]);
        $tahun = (int) $data['tahun'];

        // Bandingkan dengan nominal yang berlaku sekarang untuk mengetahui apa yang berubah.
        $sekarang = SetoranRtService::pengaturan($tahun);
        $perubahan = [];
        if ((int) $data['default'] !== $sekarang['default']) {
            $perubahan[] = [null, (int) $data['default']];
        }
        $rtValid = MasterRt::pluck('id')->all();
        foreach ((array) ($data['per_rt'] ?? []) as $rtId => $nominal) {
            if (! in_array($rtId, $rtValid, true)) {
                continue;
            }
            $baru = $nominal === null ? null : (int) $nominal;
            $lama = $sekarang['per_rt'][$rtId] ?? null;
            if ($baru !== $lama) {
                $perubahan[] = [$rtId, $baru];
            }
        }

        if (empty($perubahan)) {
            return back()->with('success', 'Tidak ada perubahan nominal setoran.');
        }

        $pengaturanAwal = SetoranRtService::belumAdaPengaturan($tahun);
        $terawal = SetoranRtService::bulanMulaiTerawal($tahun);
        $bulanMulai = (int) ($data['berlaku_mulai'] ?? 0);

        if ($terawal > 12) {
            return back()->withInput()->with('error', "Nominal setoran {$tahun} tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki setoran. Atur nominal pada tahun berikutnya.");
        }
        if ($bulanMulai < $terawal) {
            return back()->withInput()->with('error', 'Perubahan nominal setoran tidak boleh berlaku surut. Pilih bulan mulai berlaku ' . IuranService::BULAN[$terawal] . " {$tahun} atau sesudahnya.");
        }
        if (! $pengaturanAwal && ! $request->boolean('konfirmasi_cutoff')) {
            return back()->withInput()->with('error', 'Centang konfirmasi perubahan nominal setoran terlebih dahulu.');
        }

        foreach ($perubahan as [$rtId, $nominal]) {
            SetoranRtService::simpanPengaturan($tahun, $rtId, $nominal, $bulanMulai);
        }

        return back()->with('success', 'Nominal setoran per KK berlaku mulai ' . IuranService::BULAN[$bulanMulai] . " {$tahun}. Kewajiban bulan sebelumnya tidak berubah.");
    }
    /** Ringkasan satu setoran (seluruh batch) untuk label dan nominal pada pesan. */
    private function ringkasGrup(SetoranRt $setoran): object
    {
        return SetoranRtService::kelompok(
            SetoranRt::with('tagihan')->whereIn('id', $setoran->grup()->pluck('id'))->get()
        )->first();
    }

    private function kabariRt(SetoranRt $setoran, bool $diterima): void
    {
        $g = $this->ringkasGrup($setoran);
        NotificationService::kirimKeBanyak(NotificationService::opKeuangan('RT', $setoran->rt_id), [
            'type'     => $diterima ? 'success' : 'danger',
            'icon'     => $diterima ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
            'judul'    => $diterima ? 'Setoran Dikonfirmasi' : 'Setoran Ditolak',
            'pesan'    => $diterima
                ? "Setoran {$g->periode_label} Rp " . number_format($g->nominal, 0, ',', '.') . ' telah dikonfirmasi RW.'
                : "Setoran {$g->periode_label} ditolak RW. Alasan: " . $g->alasan,
            'url'      => route('opkeuangan.setoran.index'),
            'kategori' => 'keuangan',
        ]);
    }
}