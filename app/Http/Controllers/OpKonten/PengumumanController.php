<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get statistics
        $stats = [
            'draft' => Pengumuman::where('status', 'Draft')->count(),
            'pending_rw' => Pengumuman::where('status', 'Review')->count(),
            'revisi' => Pengumuman::where('status', 'Revisi')->count(),
            'published' => Pengumuman::where('status', 'Publish')->count(),
        ];

        // 2. Query Builder with filtering
        $query = Pengumuman::query();

        // Search by Judul or Isi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_pengumuman', 'like', "%{$search}%")
                  ->orWhere('isi_pengumuman', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($request->filled('status') && $request->status !== 'Semua') {
            $query->where('status', $request->status);
        }

        // Filter by Date (tanggal_publish or created_at)
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        // Filter by Priority
        if ($request->filled('priority')) {
            if ($request->priority === 'Penting') {
                $query->where('is_priority', true);
            } elseif ($request->priority === 'Biasa') {
                $query->where('is_priority', false);
            }
        }

        // Pagination
        $pengumuman = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('opKonten.pengumuman.index', compact('stats', 'pengumuman'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_pengumuman' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
        ], [
            'isi_pengumuman.required' => 'Isi pengumuman wajib diisi.'
        ]);

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_pengumuman))) === '') {
            return back()->withInput()->withErrors(['isi_pengumuman' => 'Isi pengumuman wajib diisi.']);
        }

        $data = $request->only(['judul_pengumuman', 'isi_pengumuman']);
        $data['is_priority'] = $request->has('is_priority');
        $data['status'] = 'Draft';
        $data['operator_id'] = Auth::id() ?? \App\Models\User::where('role', 'Op Konten RW')->first()->id;

        Pengumuman::create($data);

        return redirect()->back()->with('success', 'Pengumuman baru berhasil disimpan sebagai Draft.');
    }

    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengumuman ini.');
        }

        $request->validate([
            'judul_pengumuman' => 'required|string|max:255',
            'isi_pengumuman' => 'required|string',
        ], [
            'isi_pengumuman.required' => 'Isi pengumuman wajib diisi.'
        ]);

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_pengumuman))) === '') {
            return back()->withInput()->withErrors(['isi_pengumuman' => 'Isi pengumuman wajib diisi.']);
        }

        if (! in_array($pengumuman->status, ['Draft', 'Revisi'])) {
            abort(403, 'Pengumuman yang sudah berstatus ' . $pengumuman->status . ' tidak dapat diubah sepihak.');
        }

        $pengumuman->judul_pengumuman = $request->judul_pengumuman;
        $pengumuman->isi_pengumuman = $request->isi_pengumuman;
        $pengumuman->is_priority = $request->has('is_priority');
        
        // Sesuai AC-06.4: Konten berstatus Revisi otomatis kembali ke proses Review saat disimpan
        if ($pengumuman->status === 'Revisi') {
            $pengumuman->status = 'Review';
            $msg = 'Pengumuman revisi berhasil diperbarui dan otomatis diajukan kembali untuk ditinjau oleh Ketua RW.';
        } else {
            $msg = 'Pengumuman berhasil diperbarui.';
        }
        
        $pengumuman->save();

        return redirect()->back()->with('success', $msg);
    }

    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus pengumuman ini.');
        }

        if ($pengumuman->status === 'Review') {
            abort(403, 'Pengumuman yang sedang dalam proses peninjauan tidak dapat dihapus.');
        }

        $pengumuman->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus permanen.');
    }

    public function submit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengajukan pengumuman ini.');
        }

        $pengumuman->status = 'Review';
        $pengumuman->save();

        return redirect()->back()->with('success', 'Pengumuman berhasil diajukan untuk ditinjau oleh Ketua RW.');
    }

    public function cancelSubmit($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan pengajuan pengumuman ini.');
        }

        $pengumuman->status = 'Draft';
        $pengumuman->save();

        return redirect()->back()->with('success', 'Pengajuan pengumuman berhasil dibatalkan.');
    }
}
