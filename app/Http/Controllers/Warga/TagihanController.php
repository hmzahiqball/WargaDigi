<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PembayaranTagihan;
use App\Models\RekeningBendahara;
use Illuminate\Support\Facades\Auth;
use App\Models\Penduduk;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanController extends Controller
{
    private function getKeluargaId()
    {
        $user = Auth::user();
        if (!$user->nik) return null;

        $penduduk = Penduduk::with('keluarga')->where('nik', $user->nik)->first();
        if ($penduduk && $penduduk->keluarga) {
            return $penduduk->keluarga->id;
        }
        return null;
    }

    public function index()
    {
        $keluargaId = $this->getKeluargaId();
        if (!$keluargaId) {
            return back()->with('error', 'Data keluarga tidak ditemukan. Pastikan profil Anda sudah diverifikasi.');
        }

        $tagihanList = PembayaranTagihan::with('tagihan')
            ->where('keluarga_id', $keluargaId)
            ->orderByRaw("
                CASE
                    WHEN status = 'Unpaid' THEN 1
                    WHEN status = 'Pending' THEN 2
                    WHEN status = 'Paid' THEN 3
                    ELSE 4
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get Rekening config for RT, RW, and DKM
        $penduduk = Penduduk::with('keluarga')->where('nik', Auth::user()->nik)->first();
        $rtId = ($penduduk && $penduduk->keluarga) ? $penduduk->keluarga->rt_id : null;

        $rekeningRt = $rtId ? RekeningBendahara::where('unit', 'RT')->where('rt_id', $rtId)->first() : null;
        $rekeningRw = RekeningBendahara::where('unit', 'RW')->first();
        $rekeningDkm = RekeningBendahara::where('unit', 'DKM')->first();

        return view('warga.tagihan.index', compact('tagihanList', 'rekeningRt', 'rekeningRw', 'rekeningDkm'));
    }

    public function bayar(Request $request, $id)
    {
        $keluargaId = $this->getKeluargaId();
        $pembayaran = PembayaranTagihan::where('keluarga_id', $keluargaId)->findOrFail($id);

        if ($pembayaran->status === 'Paid') {
            return back()->with('error', 'Tagihan ini sudah lunas.');
        }

        $request->validate([
            'metode' => 'required|in:Transfer,QRIS,Cash',
            'nominal' => 'nullable',
        ], [
            'metode.required' => 'Pilih metode pembayaran terlebih dahulu.',
        ]);

        // Nominal: bila tagihan fix, pakai nominal tagihan; bila tidak, warga mengisi
        // (minimal nominal tagihan). Fallback ke nominal tagihan bila kosong.
        $tagihan = $pembayaran->tagihan;
        $nominalInput = (int) preg_replace('/\D/', '', (string) $request->input('nominal'));
        if ($tagihan && $tagihan->nominal_fix) {
            $nominalBayar = (int) $tagihan->nominal;
        } else {
            $nominalBayar = $nominalInput ?: (int) optional($tagihan)->nominal;
            if ($tagihan && $nominalBayar < (int) $tagihan->nominal) {
                return back()->with('error', 'Nominal pembayaran minimal Rp ' . number_format($tagihan->nominal, 0, ',', '.') . '.')->withInput();
            }
        }

        $buktiPath = $pembayaran->bukti_file;

        if ($request->metode !== 'Cash') {
            $hasBase64 = $request->filled('bukti_base64') && str_starts_with($request->bukti_base64, 'data:image');
            $hasFile = $request->hasFile('bukti_file') && $request->file('bukti_file')->isValid();

            if (!$hasBase64 && !$hasFile) {
                if ($request->hasFile('bukti_file') && !$request->file('bukti_file')->isValid()) {
                    return redirect()->back()->with('error', 'File bukti pembayaran gagal diproses server: ' . $request->file('bukti_file')->getErrorMessage())->withInput();
                }
                return redirect()->back()->with('error', 'Silakan unggah foto atau tangkapan layar bukti pembayaran (JPG, PNG, atau WEBP).')->withInput();
            }

            if ($hasBase64) {
                $savedBase64 = $this->saveBase64Image($request->bukti_base64, 'bukti_pembayaran');
                if ($savedBase64) {
                    $buktiPath = $savedBase64;
                }
            } elseif ($hasFile) {
                $buktiPath = $request->file('bukti_file')->store('bukti_pembayaran', 'public');
                // Sinkronkan juga langsung ke public/storage
                try {
                    $dst = public_path('storage/' . $buktiPath);
                    $dstDir = dirname($dst);
                    if (!is_dir($dstDir)) {
                        @mkdir($dstDir, 0755, true);
                    }
                    @copy(storage_path('app/public/' . $buktiPath), $dst);
                } catch (\Throwable $e) {}
            }
        }

        $pembayaran->update([
            'metode' => $request->metode,
            'bukti_file' => $buktiPath,
            'nominal' => $nominalBayar,
            'warga_id' => Auth::id(),
            'status' => 'Pending',
            'catatan' => null,
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikirim dan menunggu verifikasi bendahara.');
    }

    /**
     * Halaman iuran bulanan warga: kartu 12 bulan untuk tahun berjalan.
     */
    public function iuranIndex(Request $request)
    {
        $keluargaId = $this->getKeluargaId();
        if (! $keluargaId) {
            return back()->with('error', 'Data keluarga tidak ditemukan.');
        }

        $keluarga = \App\Models\Keluarga::find($keluargaId);
        $tahun = (int) $request->input('tahun', now()->format('Y'));
        $master = \App\Models\MasterIuran::where('rt_id', $keluarga->rt_id)
            ->where('tahun', $tahun)->first();

        $peta = [];
        if ($master) {
            foreach (\App\Models\IuranPembayaran::where('master_iuran_id', $master->id)
                ->where('keluarga_id', $keluargaId)->get() as $p) {
                $peta[$p->bulan] = $p;
            }
        }

        $daftarTahun = range((int) now()->format('Y'), 2023);

        // Rekening & QRIS RT untuk petunjuk transfer (iuran bulanan lingkup RT).
        $rekeningRt = $keluarga->rt_id
            ? RekeningBendahara::where('unit', 'RT')->where('rt_id', $keluarga->rt_id)->first()
            : null;

        // Nominal tiap bulan (bisa berbeda bila nominal pernah berubah di tengah tahun).
        $nominalPerBulan = $master
            ? collect(range(1, 12))->mapWithKeys(fn ($b) => [$b => $master->nominalUntukBulan($b)])->all()
            : [];

        return view('warga.iuran.index', compact('master', 'tahun', 'peta', 'daftarTahun', 'keluargaId', 'rekeningRt', 'nominalPerBulan'));
    }

    /**
     * Warga mengajukan pembayaran iuran beberapa bulan sekaligus (satu batch,
     * menunggu verifikasi bendahara).
     */
    public function iuranAjukan(Request $request)
    {
        $keluargaId = $this->getKeluargaId();
        abort_unless($keluargaId, 403);

        $data = $request->validate([
            'master_iuran_id' => 'required|uuid',
            'bulan'           => 'required|array|min:1',
            'bulan.*'         => 'integer|min:1|max:12',
            'tanggal_bayar'   => 'required|date|before_or_equal:today',
            'metode'          => 'required|in:tunai,transfer',
            'bukti_file'      => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $master = \App\Models\MasterIuran::where('id', $data['master_iuran_id'])->where('aktif', true)->firstOrFail();
        // Pastikan master milik RT keluarga ini.
        $keluarga = \App\Models\Keluarga::findOrFail($keluargaId);
        abort_unless($master->rt_id === $keluarga->rt_id, 403);

        $buktiPath = null;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('dokumen/iuran', 'public');
        }

        $jumlah = \App\Services\IuranService::ajukanWargaBatch($master, $keluargaId, $data['bulan'], [
            'tanggal_bayar' => $data['tanggal_bayar'],
            'metode'        => $data['metode'],
            'bukti_file'    => $buktiPath,
            'catatan'       => $data['catatan'] ?? null,
        ]);

        if ($jumlah === 0) {
            return back()->with('error', 'Tidak ada bulan yang diajukan (mungkin sudah lunas atau sedang menunggu verifikasi).');
        }

        return back()->with('success', "Pengajuan pembayaran {$jumlah} bulan dikirim dan menunggu verifikasi bendahara.");
    }

    public function downloadResi($id)
    {
        $keluargaId = $this->getKeluargaId();
        $pembayaran = PembayaranTagihan::with(['tagihan', 'keluarga.kepalaKeluarga', 'warga'])
            ->where('keluarga_id', $keluargaId)
            ->findOrFail($id);

        if ($pembayaran->status !== 'Paid') {
            abort(403, 'Resi hanya tersedia untuk tagihan yang sudah lunas.');
        }

        // Kop dan penandatangan mengikuti penerbit (Bendahara RT/RW/DKM yang mengesahkan pembayaran).
        $resi = \App\Services\ResiPembayaranBuilder::bangun($pembayaran);

        $pdf = Pdf::loadView('pdf.resi-pembayaran', compact('pembayaran', 'resi'));
        return $pdf->download('Resi_Pembayaran_' . $pembayaran->tagihan->judul . '.pdf');
    }

    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('home'));
    }

    private function saveBase64Image(string $base64Data, string $folder): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp', 'svg+xml'])) {
                $type = 'jpg';
            }
            if ($type === 'svg+xml') $type = 'svg';

            $data = base64_decode($data);
            if ($data === false) {
                return null;
            }

            $filename = $folder . '/' . \Illuminate\Support\Str::uuid() . '.' . $type;
            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $data);

            // Simpan juga langsung ke public/storage untuk web native serving
            try {
                $dst = public_path('storage/' . $filename);
                $dstDir = dirname($dst);
                if (!is_dir($dstDir)) {
                    @mkdir($dstDir, 0755, true);
                }
                @file_put_contents($dst, $data);
            } catch (\Throwable $e) {}

            return $filename;
        }

        return null;
    }
}

