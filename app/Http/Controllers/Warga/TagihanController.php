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
            ->get();

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
        ], [
            'metode.required' => 'Pilih metode pembayaran terlebih dahulu.',
        ]);

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
            'warga_id' => Auth::id(),
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dikirim dan menunggu verifikasi bendahara.');
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

        $pdf = Pdf::loadView('pdf.resi-pembayaran', compact('pembayaran'));
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

