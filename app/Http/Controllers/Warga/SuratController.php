<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    public function index()
    {
        $riwayat = \App\Models\SuratPengajuan::where('nik', auth()->user()->nik)->latest()->get();
        return view('warga.surat.index', compact('riwayat'));
    }

    public function create()
    {
        return view('warga.surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|string',
            'keperluan' => 'required|string',
            'file_lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['jenis_surat', 'keperluan']);
        $data['nik'] = auth()->user()->nik;
        $data['status'] = 'Menunggu Verifikasi RT';

        if ($request->hasFile('file_lampiran')) {
            $data['file_lampiran'] = $request->file('file_lampiran')->store('lampiran_surat', 'public');
        }

        \App\Models\SuratPengajuan::create($data);

        return redirect()->route('warga.surat.index')->with('success', 'Permohonan surat berhasil dikirim.');
    }

    public function downloadPdf($id)
    {
        $surat = \App\Models\SuratPengajuan::with('penduduk')->findOrFail($id);
        
        if ($surat->nik !== auth()->user()->nik) {
            abort(403, 'Unauthorized action.');
        }

        if ($surat->status !== 'Selesai' && $surat->status !== 'Disetujui') {
             return back()->with('error', 'Surat belum selesai diproses.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('warga.surat.pdf', compact('surat'));
        return $pdf->download('Surat_Keterangan_' . $surat->id . '.pdf');
    }
}
