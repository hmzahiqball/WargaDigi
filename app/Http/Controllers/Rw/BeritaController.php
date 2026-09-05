<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        // Hanya tampilkan yang berstatus Review, atau sudah Publish/Archive untuk riwayat
        $query = Berita::with('operator')->whereIn('status', ['Review', 'Publish', 'Revisi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $berita = $query->latest('updated_at')->paginate(10);

        return view('rw.berita.index', compact('berita'));
    }

    public function approve(Request $request, Berita $berita)
    {
        if ($berita->status !== 'Review') {
            return back()->with('error', 'Status berita tidak valid untuk disetujui.');
        }

        $berita->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null // bersihkan catatan jika ada
        ]);

        return back()->with('success', 'Berita berhasil disetujui dan dipublikasikan!');
    }

    public function reject(Request $request, Berita $berita)
    {
        if ($berita->status !== 'Review') {
            return back()->with('error', 'Status berita tidak valid untuk ditolak.');
        }

        $request->validate([
            'catatan_revisi' => 'required|string'
        ]);

        $berita->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        return back()->with('success', 'Berita dikembalikan ke Operator dengan catatan revisi.');
    }
}
