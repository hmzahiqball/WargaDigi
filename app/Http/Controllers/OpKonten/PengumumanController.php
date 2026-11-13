<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;
use App\Support\KontenScope;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /** Pengumuman dalam cakupan akun ini (RT-nya, atau RW). Di luar cakupan dianggap tidak ada. */
    private function dasar()
    {
        return KontenScope::untukKelola(Pengumuman::query(), Auth::user());
    }

    public function index(Request $request)
    {
        // 1. Get statistics
        $stats = [
            'draft' => $this->dasar()->where('status', 'Draft')->count(),
            'pending_rw' => $this->dasar()->where('status', 'Review')->count(),
            'revisi' => $this->dasar()->where('status', 'Revisi')->count(),
            'published' => $this->dasar()->where('status', 'Publish')->count(),
        ];

        // 2. Query Builder with filtering
        $query = $this->dasar();

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

        $kategoriPilihan = \App\Models\KategoriKonten::pilihan('pengumuman');

        $teks = \App\Services\KontenWorkflow::teksOperator('pengumuman', auth()->user());

        return view('opKonten.pengumuman.index', compact('stats', 'pengumuman', 'kategoriPilihan', 'teks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_pengumuman' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('pengumuman'),
            'isi_pengumuman' => 'required|string',
            'action' => 'nullable|in:draft,review',
        ], [
            'isi_pengumuman.required' => 'Isi pengumuman wajib diisi.'
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_pengumuman))) === '') {
            return back()->withInput()->withErrors(['isi_pengumuman' => 'Isi pengumuman wajib diisi.']);
        }

        $data = $request->only(['judul_pengumuman', 'isi_pengumuman']);
        $data['kategori_id'] = $request->kategori_id;
        $data['kategori'] = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        // Slug dibuat otomatis (unik) oleh model saat pengumuman dibuat.
        $data['is_priority'] = $request->has('is_priority');
        $data += $request->action === 'review' ? \App\Services\KontenWorkflow::atributAjukan('pengumuman') : ['status' => 'Draft'];
        $data['operator_id'] = Auth::id();
        $data['rt_id'] = KontenScope::cakupanKelola(Auth::user());

        $pengumuman = Pengumuman::create($data);
        if ($request->action === 'review') {
            \App\Services\KontenWorkflow::sesudahDiajukan($pengumuman, 'pengumuman');
        }

        $msg = $request->action === 'review'
            ? \App\Services\KontenWorkflow::pesanAjukan('pengumuman', $pengumuman, auth()->user())
            : 'Pengumuman baru berhasil disimpan sebagai draf.';

        return redirect()->back()->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $pengumuman = $this->dasar()->findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah pengumuman ini.');
        }

        $request->validate([
            'judul_pengumuman' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('pengumuman', $pengumuman->kategori_id),
            'isi_pengumuman' => 'required|string',
            'action' => 'nullable|in:draft,review',
        ], [
            'isi_pengumuman.required' => 'Isi pengumuman wajib diisi.'
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_pengumuman))) === '') {
            return back()->withInput()->withErrors(['isi_pengumuman' => 'Isi pengumuman wajib diisi.']);
        }

        if (! in_array($pengumuman->status, ['Draft', 'Revisi'])) {
            abort(403, 'Pengumuman yang sudah berstatus ' . $pengumuman->status . ' tidak dapat diubah sepihak.');
        }

        // Pengumuman draf/revisi belum pernah terbit, jadi slug boleh mengikuti judul baru.
        if ($request->judul_pengumuman !== $pengumuman->judul_pengumuman) {
            $pengumuman->slug = \App\Support\SlugKonten::buat('pengumuman', $request->judul_pengumuman, $pengumuman->id);
        }
        $pengumuman->kategori_id = $request->kategori_id;
        $pengumuman->kategori = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        $pengumuman->judul_pengumuman = $request->judul_pengumuman;
        $pengumuman->isi_pengumuman = $request->isi_pengumuman;
        $pengumuman->is_priority = $request->has('is_priority');

        // Revisi yang disimpan otomatis diajukan kembali (Review, atau langsung terbit bila persetujuan dimatikan).
        $ajukan = $pengumuman->status === 'Revisi' || $request->action === 'review';
        $pengumuman->fill($ajukan ? \App\Services\KontenWorkflow::atributAjukan('pengumuman') : ['status' => 'Draft']);
        $pengumuman->save();

        if ($ajukan) {
            \App\Services\KontenWorkflow::sesudahDiajukan($pengumuman, 'pengumuman');
        }

        $msg = $ajukan
            ? \App\Services\KontenWorkflow::pesanAjukan('pengumuman', $pengumuman, auth()->user(), true)
            : 'Draf pengumuman berhasil diperbarui.';

        return redirect()->back()->with('success', $msg);
    }

    /** Pembuat memastikan pengumuman milik akunnya dan masih dalam cakupannya, lalu menjaga status yang diizinkan. */
    private function milikSaya($id, array $statusBoleh, string $pesanStatus): Pengumuman
    {
        $pengumuman = $this->dasar()->findOrFail($id);

        if ($pengumuman->operator_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses atas pengumuman ini.');
        }
        if (! in_array($pengumuman->status, $statusBoleh, true)) {
            abort(403, $pesanStatus);
        }

        return $pengumuman;
    }

    /** Hanya draf atau revisi yang dapat dihapus; yang sudah terbit cukup diarsipkan. */
    public function destroy($id)
    {
        $pengumuman = $this->milikSaya($id, ['Draft', 'Revisi'], 'Hanya pengumuman berstatus Draf atau Revisi yang dapat dihapus. Pengumuman yang sudah terbit dapat diarsipkan.');
        $pengumuman->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus permanen.');
    }

    public function submit($id)
    {
        $pengumuman = $this->milikSaya($id, ['Draft', 'Revisi'], 'Hanya pengumuman berstatus Draf atau Revisi yang dapat diajukan.');
        $pengumuman->update(\App\Services\KontenWorkflow::atributAjukan('pengumuman'));
        \App\Services\KontenWorkflow::sesudahDiajukan($pengumuman, 'pengumuman');

        return redirect()->back()->with('success', \App\Services\KontenWorkflow::pesanAjukan('pengumuman', $pengumuman, auth()->user()));

        return redirect()->back()->with('success', 'Pengumuman berhasil diajukan untuk ditinjau oleh ' . \App\Support\KontenScope::penyetuju('pengumuman', auth()->user()) . '.');
    }

    public function cancelSubmit($id)
    {
        $pengumuman = $this->milikSaya($id, ['Review'], 'Hanya pengajuan yang sedang ditinjau yang dapat dibatalkan.');
        $pengumuman->update(['status' => 'Draft']);

        return redirect()->back()->with('success', 'Pengajuan pengumuman berhasil dibatalkan.');
    }

    /** Mencabut pengumuman terbit dari tampilan warga tanpa menghapus datanya (seperti berita). */
    public function archive($id)
    {
        $pengumuman = $this->milikSaya($id, ['Publish'], 'Hanya pengumuman yang sudah terbit yang dapat diarsipkan.');
        $pengumuman->update(['status' => 'Archive']);

        return redirect()->back()->with('success', 'Pengumuman berhasil diarsipkan.');
    }

    public function unarchive($id)
    {
        $pengumuman = $this->milikSaya($id, ['Archive'], 'Hanya pengumuman yang diarsipkan yang dapat dibuka kembali.');
        $pengumuman->update(['status' => 'Publish']);

        return redirect()->back()->with('success', 'Arsip pengumuman berhasil dibuka.');
    }
}