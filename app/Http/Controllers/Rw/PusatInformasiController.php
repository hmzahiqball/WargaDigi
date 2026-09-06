<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Agenda;
use App\Models\Pengumuman;
use Carbon\Carbon;

class PusatInformasiController extends Controller
{
    public function index()
    {
        // Get Berita
        $berita = Berita::with('operator')
            ->whereIn('status', ['Review', 'Publish', 'Revisi'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Berita',
                    'title' => $item->judul_berita,
                    'description' => strip_tags($item->isi_berita),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->format('d M Y, H:i'),
                    'location' => null,
                    'image' => $item->featured_image ? asset($item->featured_image) : null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->isi_berita,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Agenda
        $agenda = Agenda::with('operator')
            ->whereIn('status', ['Review', 'Publish', 'Revisi'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Agenda',
                    'title' => $item->judul_agenda,
                    'description' => strip_tags($item->detail_pengumuman ?? ''),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_mulai)->format('d M Y, H:i') . ' WIB',
                    'location' => $item->lokasi,
                    'image' => $item->banner_flyer ? asset($item->banner_flyer) : null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->detail_pengumuman,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Pengumuman
        $pengumuman = Pengumuman::with('operator')
            ->whereIn('status', ['Review', 'Publish', 'Revisi'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Pengumuman',
                    'title' => $item->judul_pengumuman,
                    'description' => strip_tags($item->isi_pengumuman ?? ''),
                    'author' => $item->operator->username ?? 'Unknown',
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->format('d M Y, H:i'),
                    'location' => null,
                    'image' => null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->isi_pengumuman,
                    'kategori' => 'Pengumuman Warga',
                    'updated_at' => $item->updated_at
                ];
            });

        $semua = collect($berita)->merge($agenda)->merge($pengumuman)->sortByDesc('updated_at')->values()->all();

        return view('rw.pusat_informasi.index', compact('berita', 'agenda', 'pengumuman', 'semua'));
    }

    public function approveBerita(Request $request, $id)
    {
        $item = Berita::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        return response()->json(['success' => 'Berita berhasil disetujui!']);
    }

    public function rejectBerita(Request $request, $id)
    {
        $item = Berita::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        return response()->json(['success' => 'Berita ditolak dan dikembalikan untuk revisi.']);
    }

    public function approveAgenda(Request $request, $id)
    {
        $item = Agenda::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        return response()->json(['success' => 'Agenda berhasil disetujui!']);
    }

    public function rejectAgenda(Request $request, $id)
    {
        $item = Agenda::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        return response()->json(['success' => 'Agenda ditolak dan dikembalikan untuk revisi.']);
    }

    public function approvePengumuman(Request $request, $id)
    {
        $item = Pengumuman::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        return response()->json(['success' => 'Pengumuman berhasil disetujui!']);
    }

    public function rejectPengumuman(Request $request, $id)
    {
        $item = Pengumuman::findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        return response()->json(['success' => 'Pengumuman ditolak dan dikembalikan untuk revisi.']);
    }
}
