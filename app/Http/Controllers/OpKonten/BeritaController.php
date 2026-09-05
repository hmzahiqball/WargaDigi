<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Berita::with('operator')->where('operator_id', auth()->id());

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('judul_berita', 'like', '%' . $request->search . '%');
        }

        $berita = $query->latest()->paginate(10)->withQueryString();

        return view('opKonten.berita.index', compact('berita'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_berita' => 'required|string|max:255',
            'kategori' => 'required|string',
            'isi_berita' => 'required|string',
            'foto_utama' => 'nullable|image|max:5120', // 5MB max
            'action' => 'required|in:draft,review'
        ]);

        $data = $request->only(['judul_berita', 'kategori', 'isi_berita']);
        $data['slug'] = \Illuminate\Support\Str::slug($request->judul_berita) . '-' . uniqid();
        $data['status'] = $request->action === 'review' ? 'Review' : 'Draft';
        $data['operator_id'] = auth()->id();

        if ($request->hasFile('foto_utama')) {
            $path = $request->file('foto_utama')->store('berita', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        \App\Models\Berita::create($data);

        $msg = $request->action === 'review' ? 'Berita berhasil diajukan untuk direview!' : 'Draft berita berhasil disimpan!';
        return back()->with('success', $msg);
    }

    public function update(Request $request, \App\Models\Berita $berita)
    {
        // Ensure user owns this
        if ($berita->operator_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'judul_berita' => 'required|string|max:255',
            'kategori' => 'required|string',
            'isi_berita' => 'required|string',
            'foto_utama' => 'nullable|image|max:5120',
            'action' => 'required|in:draft,review'
        ]);

        $data = $request->only(['judul_berita', 'kategori', 'isi_berita']);
        if ($request->judul_berita !== $berita->judul_berita) {
            $data['slug'] = \Illuminate\Support\Str::slug($request->judul_berita) . '-' . uniqid();
        }
        
        $data['status'] = $request->action === 'review' ? 'Review' : 'Draft';

        if ($request->hasFile('foto_utama')) {
            $path = $request->file('foto_utama')->store('berita', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        $berita->update($data);

        $msg = $request->action === 'review' ? 'Berita berhasil diajukan untuk direview!' : 'Draft berita berhasil diperbarui!';
        return back()->with('success', $msg);
    }
    public function submit(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || !in_array($berita->status, ['Draft', 'Revisi'])) abort(403);
        $berita->update(['status' => 'Review']);
        return back()->with('success', 'Berita berhasil diajukan untuk direview!');
    }

    public function destroy(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || $berita->status !== 'Draft') abort(403);
        $berita->delete();
        return back()->with('success', 'Berita berhasil dihapus.');
    }

    public function archive(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || $berita->status !== 'Publish') abort(403);
        $berita->update(['status' => 'Archive']);
        return back()->with('success', 'Berita berhasil diarsipkan.');
    }

    public function unarchive(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || $berita->status !== 'Archive') abort(403);
        $berita->update(['status' => 'Publish']);
        return back()->with('success', 'Arsip berita berhasil dibuka.');
    }

    public function revoke(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || $berita->status !== 'Review') abort(403);
        $berita->update(['status' => 'Draft']);
        return back()->with('success', 'Berita berhasil ditarik kembali ke Draft.');
    }
}
