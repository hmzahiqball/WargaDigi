<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Models\GaleriDokumentasi;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $nowWib = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        // Only show agendas that are "Publish" AND whose end date has passed
        $query = Agenda::with('galeriDokumentasi')
            ->where('status', 'Publish')
            ->where('tanggal_selesai', '<=', $nowWib);

        // Filters
        if ($request->filled('search')) {
            $query->where('judul_agenda', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_mulai', $request->tanggal);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('dokumentasi')) {
            if ($request->dokumentasi === 'sudah') {
                $query->whereHas('galeriDokumentasi');
            } elseif ($request->dokumentasi === 'belum') {
                $query->doesntHave('galeriDokumentasi');
            }
        }

        $agendaSelesai = $query->latest('tanggal_selesai')->paginate(9)->withQueryString();

        // Stats
        $baseQuery = Agenda::where('status', 'Publish')->where('tanggal_selesai', '<=', $nowWib);
        $totalSelesai = (clone $baseQuery)->count();
        $sudahDokumentasi = (clone $baseQuery)->whereHas('galeriDokumentasi')->count();
        $belumDokumentasi = $totalSelesai - $sudahDokumentasi;

        // Total foto across all galeri
        $totalFoto = 0;
        $allGaleri = GaleriDokumentasi::all();
        foreach ($allGaleri as $g) {
            $totalFoto += count($g->foto ?? []);
        }

        $stats = [
            'total_selesai' => $totalSelesai,
            'sudah_dokumentasi' => $sudahDokumentasi,
            'belum_dokumentasi' => $belumDokumentasi,
            'total_foto' => $totalFoto,
        ];

        return view('opKonten.galeri.index', compact('agendaSelesai', 'stats'));
    }

    public function show($id)
    {
        $agenda = Agenda::with('galeriDokumentasi.operator')->findOrFail($id);

        if ($agenda->status !== 'Publish') {
            abort(404);
        }

        return view('opKonten.galeri.show', compact('agenda'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agenda_id' => 'required|uuid|exists:agenda,id',
            'jumlah_peserta' => 'nullable|integer|min:0',
            'foto' => 'required|array|min:1|max:10',
            'foto.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'foto.required' => 'Minimal 1 foto harus diunggah.',
            'foto.max' => 'Maksimal 10 foto yang dapat diunggah.',
            'foto.*.max' => 'Ukuran foto tidak boleh melebihi 5MB.',
            'foto.*.image' => 'File harus berupa gambar.',
            'foto.*.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau WEBP.',
        ]);

        // Check agenda is eligible
        $agenda = Agenda::findOrFail($request->agenda_id);
        $nowWib = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $selesaiWib = Carbon::parse($agenda->tanggal_selesai)->format('Y-m-d H:i:s');

        if ($agenda->status !== 'Publish' || $selesaiWib > $nowWib) {
            return back()->with('error', 'Agenda belum memenuhi syarat untuk dokumentasi.');
        }

        // Check no existing documentation
        if (GaleriDokumentasi::where('agenda_id', $agenda->id)->exists()) {
            return back()->with('error', 'Dokumentasi untuk agenda ini sudah ada.');
        }

        // Upload photos
        $photoPaths = [];
        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $filename = 'galeri_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/galeri'), $filename);
                $photoPaths[] = 'assets/images/galeri/' . $filename;
            }
        }

        GaleriDokumentasi::create([
            'agenda_id' => $agenda->id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'foto' => $photoPaths,
            'operator_id' => auth()->id(),
        ]);

        return redirect()->route('opkonten.galeri.show', $agenda->id)->with('success', 'Dokumentasi berhasil diunggah dan langsung tayang!');
    }

    public function update(Request $request, $id)
    {
        $galeri = GaleriDokumentasi::findOrFail($id);

        if ($galeri->operator_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah dokumentasi ini.');
        }

        $request->validate([
            'jumlah_peserta' => 'nullable|integer|min:0',
            'foto_baru' => 'nullable|array|max:10',
            'foto_baru.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_hapus' => 'nullable|array',
            'foto_hapus.*' => 'string',
        ]);

        // Handle deleted photos: hanya file yang terdaftar sah di data galeri ini
        $existingPhotos = $galeri->foto ?? [];
        $photosToDelete = $request->input('foto_hapus', []);
        $allowedPrefix = realpath(public_path('assets/images/galeri'));

        foreach ($photosToDelete as $photoPath) {
            if (in_array($photoPath, $existingPhotos)) {
                $actualPath = realpath(public_path($photoPath));
                if ($actualPath && $allowedPrefix && str_starts_with($actualPath, $allowedPrefix) && file_exists($actualPath)) {
                    unlink($actualPath);
                }
                $existingPhotos = array_filter($existingPhotos, fn($p) => $p !== $photoPath);
            }
        }

        // Handle new photos
        if ($request->hasFile('foto_baru')) {
            foreach ($request->file('foto_baru') as $file) {
                if (count($existingPhotos) >= 10) break;
                $filename = 'galeri_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/images/galeri'), $filename);
                $existingPhotos[] = 'assets/images/galeri/' . $filename;
            }
        }

        $galeri->update([
            'jumlah_peserta' => $request->jumlah_peserta,
            'foto' => array_values($existingPhotos),
        ]);

        return redirect()->route('opkonten.galeri.show', $galeri->agenda_id)->with('success', 'Dokumentasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $galeri = GaleriDokumentasi::findOrFail($id);

        if ($galeri->operator_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus dokumentasi ini.');
        }

        $agendaId = $galeri->agenda_id;
        $allowedPrefix = realpath(public_path('assets/images/galeri'));

        // Delete all photos from disk securely
        if ($galeri->foto) {
            foreach ($galeri->foto as $photoPath) {
                $actualPath = realpath(public_path($photoPath));
                if ($actualPath && $allowedPrefix && str_starts_with($actualPath, $allowedPrefix) && file_exists($actualPath)) {
                    unlink($actualPath);
                }
            }
        }

        $galeri->delete();

        return redirect()->route('opkonten.galeri.index')->with('success', 'Dokumentasi berhasil dihapus.');
    }
}
