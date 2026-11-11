<?php

namespace App\Http\Controllers;

use App\Models\IuranPembayaran;
use App\Models\KategoriTransaksi;
use App\Models\Keluarga;
use App\Models\MasterIuran;
use App\Models\MasterRt;
use App\Models\Penduduk;
use App\Services\IuranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Iuran bulanan warga (dikelola Bendahara/Operator Keuangan RT).
 * Grid tahunan: KK × 12 bulan, dengan tab input (ceklis) dan tab verifikasi
 * pengajuan pembayaran dari warga.
 */
class IuranController extends Controller
{
    use \App\Http\Controllers\Concerns\NormalizesMoney;

    /** RT id milik operator keuangan yang login. */
    private function rtId(): ?string
    {
        $user = Auth::user();
        $rtId = $user->rt_id;
        if (! $rtId && $user->nik) {
            $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();
            $rtId = $penduduk?->keluarga?->rt_id;
        }

        return $rtId ?: optional(MasterRt::first())->id;
    }

    public function index(Request $request)
    {
        $rtId = $this->rtId();
        abort_unless($rtId, 403, 'Unit RT tidak terdefinisi untuk akun ini.');

        $tahun = (int) $request->input('tahun', now()->format('Y'));
        $master = MasterIuran::where('rt_id', $rtId)->where('tahun', $tahun)->first();

        // Daftar KK aktif terkini di RT (dinamis), dengan nama kepala keluarga.
        $keluargaQuery = Keluarga::where('rt_id', $rtId)->with('kepalaKeluarga');

        if ($cari = trim((string) $request->input('cari'))) {
            $keluargaQuery->where(function ($q) use ($cari) {
                $q->where('no_kk', 'like', "%{$cari}%")
                    ->orWhereHas('kepalaKeluarga', fn ($p) => $p->where('nama_lengkap', 'like', "%{$cari}%"));
            });
        }

        $keluarga = $keluargaQuery->get()->sortBy(fn ($k) => optional($k->kepalaKeluarga)->nama_lengkap ?? $k->no_kk)->values();

        // Peta pembayaran: [keluarga_id][bulan] => IuranPembayaran
        $peta = [];
        $pengajuanWarga = collect();
        if ($master) {
            $semua = IuranPembayaran::where('master_iuran_id', $master->id)->get();
            foreach ($semua as $p) {
                $peta[$p->keluarga_id][$p->bulan] = $p;
            }
            // Pengajuan warga dikelompokkan per batch (satu pengajuan = satu kartu).
            $pengajuanWarga = IuranPembayaran::with('keluarga.kepalaKeluarga')
                ->where('master_iuran_id', $master->id)
                ->where('status', 'menunggu')
                ->orderByDesc('created_at')
                ->get()
                ->groupBy(fn ($p) => $p->batch_id ?? $p->id)
                ->map(fn ($g) => (object) [
                    'wakil'   => $g->first(),
                    'bulan'   => $g->pluck('bulan')->sort()->values(),
                    'jumlah'  => $g->count(),
                ])
                ->values();
        }

        $kategoriPemasukan = KategoriTransaksi::where('tipe', 'pemasukan')->where('aktif', true)
            ->untukUnit('RT', $rtId)->orderBy('urutan')->get();

        $daftarTahun = range((int) now()->format('Y') + 1, 2023);

        $riwayatNominal = $master ? $master->riwayatUrut() : collect();
        $bulanMulaiTerawal = $master ? $master->bulanMulaiTerawal() : 1;
        $bulanTerkunci = $master ? $master->bulanTerkunciTerakhir() : 0;

        return view('opKeuangan.iuran-bulanan', compact(
            'master', 'tahun', 'keluarga', 'peta', 'pengajuanWarga', 'kategoriPemasukan', 'daftarTahun',
            'riwayatNominal', 'bulanMulaiTerawal', 'bulanTerkunci'
        ));
    }

    /** Buat atau perbarui master iuran tahun berjalan. */
    public function simpanMaster(Request $request)
    {
        $rtId = $this->rtId();
        abort_unless($rtId, 403);

        // Normalisasi nominal berformat ribuan (mis. "60.000" -> 60000).
        $request->merge(['nominal' => $this->normalizeMoney($request->input('nominal'))]);

        $data = $request->validate([
            'tahun'       => 'required|integer|min:2023|max:2100',
            'nama'        => 'required|string|max:100',
            'nominal'     => 'required|numeric|min:0',
            'kategori_id' => 'nullable|uuid',
            'aktif'       => 'nullable|boolean',
            'berlaku_mulai'     => 'nullable|integer|min:1|max:12',
            'konfirmasi_cutoff' => 'nullable|boolean',
        ]);

        $tahun = (int) $data['tahun'];
        $nominalBaru = (int) $data['nominal'];
        $master = MasterIuran::where('rt_id', $rtId)->where('tahun', $tahun)->first();

        // Iuran tahun ini baru dibuat: nominal awal berlaku sejak Januari.
        if (! $master) {
            MasterIuran::create([
                'rt_id'       => $rtId,
                'tahun'       => $tahun,
                'nama'        => $data['nama'],
                'nominal'     => $nominalBaru,
                'kategori_id' => $data['kategori_id'] ?? null,
                'aktif'       => $request->boolean('aktif', true),
            ]);

            return back()->with('success', 'Pengaturan iuran berhasil disimpan.');
        }

        $nominalLama = $master->nominal;
        $berubah = $nominalBaru !== $nominalLama;
        $bulanMulai = (int) ($data['berlaku_mulai'] ?? 0);

        if ($berubah) {
            $terawal = $master->bulanMulaiTerawal();
            if ($terawal > 12) {
                return back()->withInput()->with('error', "Nominal iuran {$tahun} tidak dapat diubah lagi karena seluruh bulan tahun ini sudah memiliki pembayaran atau laporan. Atur nominal pada iuran tahun berikutnya.");
            }
            if ($bulanMulai < $terawal) {
                return back()->withInput()->with('error', 'Perubahan nominal tidak boleh berlaku surut. Pilih bulan mulai berlaku ' . IuranService::BULAN[$terawal] . " {$tahun} atau sesudahnya.");
            }
            if (! $request->boolean('konfirmasi_cutoff')) {
                return back()->withInput()->with('error', 'Centang konfirmasi perubahan nominal terlebih dahulu.');
            }
        }

        $master->update([
            'nama'        => $data['nama'],
            'kategori_id' => $data['kategori_id'] ?? null,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        if ($berubah) {
            $master->ubahNominal($nominalBaru, $bulanMulai, Auth::id());
            $mulai = IuranService::BULAN[$bulanMulai] . ' ' . $tahun;

            return back()->with('success', 'Nominal iuran Rp ' . number_format($nominalBaru, 0, ',', '.') . " berlaku mulai {$mulai}. Pembayaran dan perhitungan bulan sebelumnya tetap memakai Rp " . number_format($nominalLama, 0, ',', '.') . '.');
        }

        return back()->with('success', 'Pengaturan iuran berhasil disimpan.');
    }

    /** Tandai lunas satu atau beberapa bulan untuk satu KK (dialog bendahara). */
    public function tandaiLunas(Request $request)
    {
        $rtId = $this->rtId();
        $data = $request->validate([
            'master_iuran_id' => 'required|uuid',
            'keluarga_id'     => 'required|uuid',
            'bulan'           => 'required|array|min:1',
            'bulan.*'         => 'integer|min:1|max:12',
            'tanggal_bayar'   => 'required|date|before_or_equal:today',
            'metode'          => 'required|in:tunai,transfer',
            'bukti_file'      => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $master = MasterIuran::where('id', $data['master_iuran_id'])->where('rt_id', $rtId)->firstOrFail();
        $this->pastikanKeluargaDiRt($data['keluarga_id'], $rtId);

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('dokumen/iuran', 'public');
        }

        $jumlah = IuranService::tandaiLunasBatch($master, $data['keluarga_id'], $data['bulan'], [
            'tanggal_bayar' => $data['tanggal_bayar'],
            'metode'        => $data['metode'],
            'bukti_file'    => $buktiPath,
            'sumber'        => 'bendahara',
            'catatan'       => $data['catatan'] ?? null,
        ], Auth::id());

        if ($jumlah === 0) {
            return back()->with('error', 'Tidak ada bulan baru yang dicatat (mungkin sudah lunas).');
        }

        return back()->with('success', "Pembayaran {$jumlah} bulan berhasil dicatat sebagai satu transaksi kas.");
    }

    /** Kirim pengingat iuran bulan berjalan ke KK yang belum lunas (hanya RT ini). */
    public function ingatkan()
    {
        $rtId = $this->rtId();
        abort_unless($rtId, 403);

        $jumlah = \App\Services\NotificationService::reminderIuranBulanan($rtId);

        return back()->with('success', "Pengingat iuran terkirim ke {$jumlah} KK yang belum lunas bulan ini.");
    }

    /** Batalkan satu sel lunas (dan hapus transaksi kasnya). */
    public function batalkan(Request $request, string $id)
    {
        $rtId = $this->rtId();
        $bayar = IuranPembayaran::with('master')->findOrFail($id);
        abort_unless($bayar->master && $bayar->master->rt_id === $rtId, 403);

        IuranService::batalkanLunas($bayar);

        return back()->with('success', 'Pembayaran dibatalkan dan transaksi kas terkait dihapus.');
    }

    /** Setujui pengajuan pembayaran dari warga (seluruh batch bila ada). */
    public function setujui(Request $request, string $id)
    {
        $rtId = $this->rtId();
        $bayar = IuranPembayaran::with('master')->findOrFail($id);
        abort_unless($bayar->master && $bayar->master->rt_id === $rtId, 403);

        if ($bayar->batch_id) {
            $jumlah = IuranService::setujuiBatch($bayar->batch_id, Auth::id());

            return back()->with('success', "Pembayaran {$jumlah} bulan disetujui dan masuk ke kas.");
        }

        IuranService::setujuiPengajuan($bayar, Auth::id());

        return back()->with('success', 'Pembayaran warga disetujui dan masuk ke kas.');
    }

    /** Tolak pengajuan pembayaran dari warga (seluruh batch bila ada). */
    public function tolak(Request $request, string $id)
    {
        $rtId = $this->rtId();
        $request->validate(['alasan' => 'required|string|max:500']);
        $alasan = $request->input('alasan');
        $bayar = IuranPembayaran::with(['master', 'keluarga'])->findOrFail($id);
        abort_unless($bayar->master && $bayar->master->rt_id === $rtId, 403);

        $keluarga = $bayar->keluarga;
        $label = 'Iuran ' . ($bayar->master->nama ?? 'Bulanan') . ' ' . ($bayar->master->tahun ?? '');

        if ($bayar->batch_id) {
            IuranService::tolakBatch($bayar->batch_id, $alasan);
        } else {
            IuranService::tolakPengajuan($bayar, $alasan);
        }

        \App\Services\NotificationService::notifikasiIuranDitolak($keluarga, $label, $alasan, route('warga.iuran.index'));

        return back()->with('success', 'Pengajuan pembayaran ditolak.');
    }

    private function pastikanKeluargaDiRt(string $keluargaId, ?string $rtId): void
    {
        $ok = Keluarga::where('id', $keluargaId)->where('rt_id', $rtId)->exists();
        abort_unless($ok, 403, 'Keluarga bukan bagian dari RT Anda.');
    }
}
