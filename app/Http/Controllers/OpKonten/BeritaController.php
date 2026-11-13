<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    /** Berita milik operator ini, sebatas cakupannya (RT-nya, atau RW). */
    private function milikSaya()
    {
        return \App\Support\KontenScope::untukKelola(\App\Models\Berita::where('operator_id', auth()->id()), auth()->user());
    }

    public function index(Request $request)
    {
        $query = $this->milikSaya()->with('operator');

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

        $stats = [
            'draft' => $this->milikSaya()->where('status', 'Draft')->count(),
            'pending_rw' => $this->milikSaya()->where('status', 'Review')->count(),
            'revisi' => $this->milikSaya()->where('status', 'Revisi')->count(),
            'published' => $this->milikSaya()->where('status', 'Publish')->count(),
            'archive' => $this->milikSaya()->where('status', 'Archive')->count(),
        ];

        // Pilihan form: kategori aktif untuk berita. Filter: semua kategori berita (termasuk yang sudah nonaktif).
        $kategoriPilihan = \App\Models\KategoriKonten::pilihan('berita');
        $kategoriFilter = \App\Models\KategoriKonten::query()->untuk('berita')->urut()->get();

        $teks = \App\Services\KontenWorkflow::teksOperator('berita', auth()->user());

        return view('opKonten.berita.index', compact('berita', 'stats', 'kategoriPilihan', 'kategoriFilter', 'teks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_berita' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('berita'),
            'isi_berita' => 'required|string',
            'foto_utama' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120', // 5MB max
            'action' => 'required|in:draft,review'
        ], [
            'isi_berita.required' => 'Isi konten berita wajib diisi.',
            'foto_utama.required' => 'Foto utama wajib diunggah.',
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_berita))) === '') {
            return back()->withInput()->withErrors(['isi_berita' => 'Isi konten berita wajib diisi.']);
        }

        $data = $request->only(['judul_berita', 'isi_berita']);
        $data['kategori_id'] = $request->kategori_id;
        $data['kategori'] = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        // Slug dibuat otomatis (unik) oleh model saat berita dibuat.
        $data += $request->action === 'review' ? \App\Services\KontenWorkflow::atributAjukan('berita') : ['status' => 'Draft'];
        $data['operator_id'] = auth()->id();
        $data['rt_id'] = \App\Support\KontenScope::cakupanKelola(auth()->user());

        if ($request->hasFile('foto_utama')) {
            $path = \App\Services\KonversiGambarWebp::simpanKeDisk($request->file('foto_utama'), 'berita', 'public', \App\Services\KonversiGambarWebp::KONTEN);
            $data['featured_image'] = '/storage/' . $path;
        }

        $berita = \App\Models\Berita::create($data);
        if ($request->action === 'review') {
            \App\Services\KontenWorkflow::sesudahDiajukan($berita, 'berita');
        }

        $msg = $request->action === 'review' ? \App\Services\KontenWorkflow::pesanAjukan('berita', $berita, auth()->user()) : 'Draft berita berhasil disimpan!';
        return back()->with('success', $msg);
    }

    public function update(Request $request, \App\Models\Berita $berita)
    {
        // Ensure user owns this
        if ($berita->operator_id !== auth()->id()) {
            abort(403);
        }

        // Validate status: Only Draft and Revisi can be edited
        if (! in_array($berita->status, ['Draft', 'Revisi'])) {
            abort(403, 'Berita yang sudah berstatus ' . $berita->status . ' tidak dapat diubah sepihak.');
        }

        $request->validate([
            'judul_berita' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('berita', $berita->kategori_id),
            'isi_berita' => 'required|string',
            // Wajib hanya bila berita belum punya foto utama (saat edit tidak perlu unggah ulang).
            'foto_utama' => [\Illuminate\Validation\Rule::requiredIf(empty($berita->featured_image)), 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'action' => 'required|in:draft,review'
        ], [
            'isi_berita.required' => 'Isi konten berita wajib diisi.',
            'foto_utama.required' => 'Foto utama wajib diunggah.',
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->isi_berita))) === '') {
            return back()->withInput()->withErrors(['isi_berita' => 'Isi konten berita wajib diisi.']);
        }

        $data = $request->only(['judul_berita', 'isi_berita']);
        $data['kategori_id'] = $request->kategori_id;
        $data['kategori'] = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        // Berita yang masih draf/revisi belum pernah terbit, jadi slug boleh mengikuti judul baru.
        if ($request->judul_berita !== $berita->judul_berita) {
            $data['slug'] = \App\Support\SlugKonten::buat('berita', $request->judul_berita, $berita->id);
        }

        $data += $request->action === 'review' ? \App\Services\KontenWorkflow::atributAjukan('berita') : ['status' => 'Draft'];

        if ($request->hasFile('foto_utama')) {
            $path = \App\Services\KonversiGambarWebp::simpanKeDisk($request->file('foto_utama'), 'berita', 'public', \App\Services\KonversiGambarWebp::KONTEN);
            $data['featured_image'] = '/storage/' . $path;
        }

        $fotoLama = $berita->featured_image;
        $berita->update($data);
        // Foto lama dibuang setelah berita memakai foto baru.
        if (isset($data['featured_image']) && $fotoLama !== $data['featured_image']) {
            self::hapusFotoUtama($fotoLama);
        }
        if ($request->action === 'review') {
            \App\Services\KontenWorkflow::sesudahDiajukan($berita, 'berita');
        }

        $msg = $request->action === 'review' ? \App\Services\KontenWorkflow::pesanAjukan('berita', $berita, auth()->user()) : 'Draft berita berhasil diperbarui!';
        return back()->with('success', $msg);
    }
    public function submit(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || !in_array($berita->status, ['Draft', 'Revisi'])) abort(403);
        $berita->update(\App\Services\KontenWorkflow::atributAjukan('berita'));
        \App\Services\KontenWorkflow::sesudahDiajukan($berita, 'berita');
        return back()->with('success', \App\Services\KontenWorkflow::pesanAjukan('berita', $berita, auth()->user()));
    }

    public function destroy(\App\Models\Berita $berita)
    {
        if ($berita->operator_id !== auth()->id() || $berita->status !== 'Draft') abort(403);
        $foto = $berita->featured_image;
        $berita->delete();
        self::hapusFotoUtama($foto);
        return back()->with('success', 'Berita berhasil dihapus.');
    }

    /** Hapus berkas foto utama berita di storage (hanya folder berita milik aplikasi). */
    private static function hapusFotoUtama(?string $featuredImage): void
    {
        $prefix = '/storage/berita/';
        if (! $featuredImage || ! str_starts_with($featuredImage, $prefix) || str_contains($featuredImage, '..')) {
            return;
        }
        \Illuminate\Support\Facades\Storage::disk('public')->delete(substr($featuredImage, strlen('/storage/')));
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
