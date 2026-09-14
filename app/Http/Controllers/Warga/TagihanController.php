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

        // Get Rekening config for RT and DKM
        $penduduk = Penduduk::with('keluarga')->where('nik', Auth::user()->nik)->first();
        $rtId = $penduduk->keluarga->rt_id;
        
        $rekeningRt = RekeningBendahara::where('unit', 'RT')->where('rt_id', $rtId)->first();
        $rekeningDkm = RekeningBendahara::where('unit', 'DKM')->first();

        return view('warga.tagihan.index', compact('tagihanList', 'rekeningRt', 'rekeningDkm'));
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
            'bukti_file' => 'required_unless:metode,Cash|image|max:2048',
        ]);

        $buktiPath = $pembayaran->bukti_file;
        if ($request->hasFile('bukti_file')) {
            $buktiPath = $request->file('bukti_file')->store('bukti_pembayaran', 'public');
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
}
