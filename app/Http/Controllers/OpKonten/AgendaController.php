<?php

namespace App\Http\Controllers\OpKonten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agenda;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $query = Agenda::with('operator')->where('operator_id', auth()->id());

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
            'draft' => Agenda::where('operator_id', auth()->id())->where('status', 'Draft')->count(),
            'pending_rw' => Agenda::where('operator_id', auth()->id())->where('status', 'Review')->count(),
            'revisi' => Agenda::where('operator_id', auth()->id())->where('status', 'Revisi')->count(),
            'published' => Agenda::where('operator_id', auth()->id())->where('status', 'Publish')->count(),
        ];

        return view('opKonten.agenda.index', compact('stats', 'agenda'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_agenda' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'detail_pengumuman' => 'required|string',
            'banner_flyer' => 'nullable|image|max:5120',
            'action' => 'required|in:draft,review'
        ]);

        $data = $request->only(['judul_agenda', 'kategori', 'lokasi', 'latitude', 'longitude', 'detail_pengumuman']);
        $data['is_rsvp_enabled'] = $request->has('is_rsvp_enabled') ? 1 : 0;
        $data['tanggal_mulai'] = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        $data['tanggal_selesai'] = $request->tanggal . ' ' . $request->waktu_selesai . ':00';
        $data['status'] = $request->action === 'review' ? 'Review' : 'Draft';
        $data['operator_id'] = auth()->id();

        if ($request->hasFile('banner_flyer')) {
            $file = $request->file('banner_flyer');
            $filename = 'agenda_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/agenda'), $filename);
            $data['banner_flyer'] = 'assets/images/agenda/' . $filename;
        }

        Agenda::create($data);

        $msg = $request->action === 'review' ? 'Agenda berhasil diajukan untuk ditinjau.' : 'Agenda berhasil disimpan sebagai draf.';
        return redirect()->route('opkonten.agenda.index')->with('success', $msg);
    }

    public function update(Request $request, Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'judul_agenda' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i',
            'lokasi' => 'required|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'detail_pengumuman' => 'required|string',
            'banner_flyer' => 'nullable|image|max:5120',
            'action' => 'required|in:draft,review'
        ]);

        $data = $request->only(['judul_agenda', 'kategori', 'lokasi', 'latitude', 'longitude', 'detail_pengumuman']);
        $data['is_rsvp_enabled'] = $request->has('is_rsvp_enabled') ? 1 : 0;
        $data['tanggal_mulai'] = $request->tanggal . ' ' . $request->waktu_mulai . ':00';
        $data['tanggal_selesai'] = $request->tanggal . ' ' . $request->waktu_selesai . ':00';
        
        if (in_array($agenda->status, ['Draft', 'Revisi'])) {
            $data['status'] = $request->action === 'review' ? 'Review' : 'Draft';
        }

        if ($request->hasFile('banner_flyer')) {
            if ($agenda->banner_flyer && file_exists(public_path($agenda->banner_flyer))) {
                unlink(public_path($agenda->banner_flyer));
            }
            $file = $request->file('banner_flyer');
            $filename = 'agenda_' . time() . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images/agenda'), $filename);
            $data['banner_flyer'] = 'assets/images/agenda/' . $filename;
        } elseif ($request->boolean('remove_banner')) {
            if ($agenda->banner_flyer && file_exists(public_path($agenda->banner_flyer))) {
                unlink(public_path($agenda->banner_flyer));
            }
            $data['banner_flyer'] = null;
        }

        $agenda->update($data);

        return redirect()->route('opkonten.agenda.index')->with('success', 'Agenda berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda)
    {
        if ($agenda->operator_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($agenda->status, ['Draft', 'Revisi'])) {
            return back()->with('error', 'Hanya agenda berstatus Draf atau Revisi yang dapat dihapus.');
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

        $agenda->update(['status' => 'Review']);
        return back()->with('success', 'Agenda berhasil diajukan untuk ditinjau.');
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
