<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use App\Support\KontenScope;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    /** Agenda milik operator ini, sebatas cakupannya (RT-nya, atau RW). */
    private function milikSaya()
    {
        return KontenScope::untukKelola(Agenda::where('operator_id', auth()->id()), auth()->user());
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
            $query->where('judul_agenda', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_mulai', $request->tanggal);
        }

        $agenda = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'draft' => $this->milikSaya()->where('status', 'Draft')->count(),
            'pending_rw' => $this->milikSaya()->where('status', 'Review')->count(),
            'revisi' => $this->milikSaya()->where('status', 'Revisi')->count(),
            'published' => $this->milikSaya()->where('status', 'Publish')->count(),
        ];

        $kategoriPilihan = \App\Models\KategoriKonten::pilihan('agenda');
        $kategoriFilter = \App\Models\KategoriKonten::query()->untuk('agenda')->urut()->get();

        $teks = \App\Services\KontenWorkflow::teksOperator('agenda', auth()->user());

        return view('opKonten.agenda.index', compact('stats', 'agenda', 'kategoriPilihan', 'kategoriFilter', 'teks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_agenda' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('agenda'),
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'tanggal_akhir' => 'nullable|required_if:is_multi_day,1|date|after_or_equal:tanggal',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'detail_pengumuman' => 'required|string',
            'banner_flyer' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'action' => 'required|in:draft,review'
        ], [
            'detail_pengumuman.required' => 'Deskripsi agenda wajib diisi.',
            'banner_flyer.required' => 'Foto utama (banner) agenda wajib diunggah.',
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->detail_pengumuman))) === '') {
            return back()->withInput()->withErrors(['detail_pengumuman' => 'Deskripsi agenda wajib diisi.']);
        }

        $data = $request->only(['judul_agenda', 'lokasi', 'latitude', 'longitude', 'detail_pengumuman']);
        $data['kategori_id'] = $request->kategori_id;
        $data['kategori'] = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        $data['is_rsvp_enabled'] = $request->has('is_rsvp_enabled') ? 1 : 0;
        $data['tanggal_mulai'] = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        if ($request->has('is_multi_day') && $request->is_multi_day == '1') {
            $data['tanggal_selesai'] = $request->tanggal_akhir . ' ' . $request->waktu_selesai . ':00';
        } else {
            $data['tanggal_selesai'] = $request->tanggal . ' ' . $request->waktu_selesai . ':00';
        }
        $data += $request->action === 'review' ? \App\Services\KontenWorkflow::atributAjukan('agenda') : ['status' => 'Draft'];
        $data['operator_id'] = auth()->id();
        $data['rt_id'] = KontenScope::cakupanKelola(auth()->user());

        if ($request->hasFile('banner_flyer')) {
            $file = $request->file('banner_flyer');
            $data['banner_flyer'] = \App\Services\KonversiGambarWebp::simpanKePublik($file, 'assets/images/agenda', 'agenda', \App\Services\KonversiGambarWebp::KONTEN);
        }

        $agendaBaru = Agenda::create($data);
        if ($request->action === 'review') {
            \App\Services\KontenWorkflow::sesudahDiajukan($agendaBaru, 'agenda');
        }

        $msg = $request->action === 'review' ? \App\Services\KontenWorkflow::pesanAjukan('agenda', $agendaBaru, auth()->user()) : 'Agenda berhasil disimpan sebagai draf.';
        return redirect()->route('opkonten.agenda.index')->with('success', $msg);
    }

    public function update(Request $request, Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah agenda ini.');
        }

        if (! in_array($agenda->status, ['Draft', 'Revisi'])) {
            abort(403, 'Agenda yang sudah berstatus ' . $agenda->status . ' tidak dapat diubah secara sepihak.');
        }

        $request->validate([
            'judul_agenda' => 'required|string|max:255',
            'kategori_id' => \App\Models\KategoriKonten::aturan('agenda', $agenda->kategori_id),
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'tanggal_akhir' => 'nullable|required_if:is_multi_day,1|date|after_or_equal:tanggal',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'detail_pengumuman' => 'required|string',
            // Wajib bila agenda belum punya foto, atau fotonya sedang dihapus (harus diganti).
            'banner_flyer' => [\Illuminate\Validation\Rule::requiredIf(fn () => $request->boolean('remove_banner') || empty($agenda->banner_flyer)), 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'action' => 'required|in:draft,review'
        ], [
            'detail_pengumuman.required' => 'Deskripsi agenda wajib diisi.',
            'banner_flyer.required' => 'Foto utama (banner) agenda wajib diunggah.',
        ] + \App\Models\KategoriKonten::pesan());

        if (trim(strip_tags(str_replace('&nbsp;', ' ', $request->detail_pengumuman))) === '') {
            return back()->withInput()->withErrors(['detail_pengumuman' => 'Deskripsi agenda wajib diisi.']);
        }

        $data = $request->only(['judul_agenda', 'lokasi', 'latitude', 'longitude', 'detail_pengumuman']);
        $data['kategori_id'] = $request->kategori_id;
        $data['kategori'] = \App\Models\KategoriKonten::findOrFail($request->kategori_id)->nama;
        // Agenda draf/revisi belum pernah terbit, jadi slug boleh mengikuti judul baru.
        if ($request->judul_agenda !== $agenda->judul_agenda) {
            $data['slug'] = \App\Support\SlugKonten::buat('agenda', $request->judul_agenda, $agenda->id);
        }
        $data['is_rsvp_enabled'] = $request->has('is_rsvp_enabled') ? 1 : 0;
        $data['tanggal_mulai'] = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        if ($request->has('is_multi_day') && $request->is_multi_day == '1') {
            $data['tanggal_selesai'] = $request->tanggal_akhir . ' ' . $request->waktu_selesai . ':00';
        } else {
            $data['tanggal_selesai'] = $request->tanggal . ' ' . $request->waktu_selesai . ':00';
        }
        
        // Revisi yang disimpan otomatis diajukan kembali (Review, atau langsung terbit bila persetujuan dimatikan).
        $ajukan = $agenda->status === 'Revisi' || $request->action === 'review';
        $data += $ajukan ? \App\Services\KontenWorkflow::atributAjukan('agenda') : ['status' => 'Draft'];
        $msg = 'Agenda berhasil diperbarui sebagai draf.';

        if ($request->hasFile('banner_flyer')) {
            if ($agenda->banner_flyer && file_exists(public_path($agenda->banner_flyer))) {
                unlink(public_path($agenda->banner_flyer));
            }
            $file = $request->file('banner_flyer');
            $data['banner_flyer'] = \App\Services\KonversiGambarWebp::simpanKePublik($file, 'assets/images/agenda', 'agenda', \App\Services\KonversiGambarWebp::KONTEN);
        } elseif ($request->boolean('remove_banner')) {
            if ($agenda->banner_flyer && file_exists(public_path($agenda->banner_flyer))) {
                unlink(public_path($agenda->banner_flyer));
            }
            $data['banner_flyer'] = null;
        }

        $agenda->update($data);
        if ($ajukan) {
            \App\Services\KontenWorkflow::sesudahDiajukan($agenda, 'agenda');
            $msg = \App\Services\KontenWorkflow::pesanAjukan('agenda', $agenda, auth()->user(), true);
        }

        return redirect()->route('opkonten.agenda.index')->with('success', $msg);
    }

    public function destroy(Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus agenda ini.');
        }

        if ($agenda->status !== 'Draft') {
            abort(403, 'Hanya agenda berstatus Draf yang dapat dihapus.');
        }

        if ($agenda->banner_flyer && file_exists(public_path($agenda->banner_flyer))) {
            unlink(public_path($agenda->banner_flyer));
        }

        $agenda->delete();
        return back()->with('success', 'Agenda berhasil dihapus.');
    }

    public function submitToReview(Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id() || !in_array($agenda->status, ['Draft', 'Revisi'])) {
            abort(403);
        }

        $agenda->update(\App\Services\KontenWorkflow::atributAjukan('agenda'));
        \App\Services\KontenWorkflow::sesudahDiajukan($agenda, 'agenda');
        return back()->with('success', \App\Services\KontenWorkflow::pesanAjukan('agenda', $agenda, auth()->user()));
    }

    public function revokeToDraft(Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id() || $agenda->status !== 'Review') {
            abort(403);
        }

        $agenda->update(['status' => 'Draft']);
        return back()->with('success', 'Pengajuan agenda berhasil ditarik kembali.');
    }
}