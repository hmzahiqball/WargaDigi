<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Agenda;
use App\Models\AgendaKehadiran;
use App\Models\Pengumuman;
use App\Support\KontenScope;
use Carbon\Carbon;

class PusatInformasiController extends Controller
{
    /**
     * Konten dalam cakupan penyetuju: Ketua RT hanya konten RT-nya, pimpinan RW hanya konten RW.
     * Konten di luar cakupan dianggap tidak ada (404).
     */
    private function dasar(string $kelas)
    {
        return KontenScope::untukKelola($kelas::query(), auth()->user());
    }

    public function index()
    {
        // Get Berita
        $berita = $this->dasar(Berita::class)->with('operator')
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
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'location' => null,
                    'image' => $item->featured_image ? asset($item->featured_image) : null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->setTimezone('Asia/Jakarta')->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->isi_berita,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Agenda
        $agenda = $this->dasar(Agenda::class)->with('operator')
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
                    'date' => Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('Y-m-d') !== Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('Y-m-d')
                        ? Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' - ' . Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB'
                        : Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') . ' - ' . Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB',
                    'location' => $item->lokasi,
                    'latitude' => $item->latitude,
                    'longitude' => $item->longitude,
                    'is_rsvp_enabled' => $item->is_rsvp_enabled,
                    'total_hadir' => $item->is_rsvp_enabled ? AgendaKehadiran::where('agenda_id', $item->id)->where('status_kehadiran', 'Hadir')->count() : 0,
                    'image' => $item->banner_flyer ? asset($item->banner_flyer) : null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->setTimezone('Asia/Jakarta')->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->detail_pengumuman,
                    'kategori' => $item->kategori,
                    'updated_at' => $item->updated_at
                ];
            });

        // Get Pengumuman
        $pengumuman = $this->dasar(Pengumuman::class)->with('operator')
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
                    'date' => Carbon::parse($item->tanggal_publish ?? $item->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i'),
                    'location' => null,
                    'image' => null,
                    'status' => $item->status,
                    'time_ago' => Carbon::parse($item->updated_at)->setTimezone('Asia/Jakarta')->diffForHumans(),
                    'reject_note' => $item->catatan_revisi,
                    'raw_content' => $item->isi_pengumuman,
                    'kategori' => 'Pengumuman Warga',
                    'updated_at' => $item->updated_at
                ];
            });

        $semua = collect($berita)->merge($agenda)->merge($pengumuman)->sortByDesc('updated_at')->values()->all();

        $basePath = request()->is('rt/*') ? '/rt/pusat-informasi' : '/rw/pusat-informasi';
        $tingkat = KontenScope::cakupanKelola(auth()->user()) ? 'RT' : 'RW';

        return view('rw.pusat_informasi.index', compact('berita', 'agenda', 'pengumuman', 'semua', 'basePath', 'tingkat'));
    }

    public function approveBerita(Request $request, $id)
    {
        $item = $this->dasar(Berita::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        // Berita: TIDAK memberi notifikasi warga (kebijakan), hanya tampil di dashboard.
        \App\Services\NotificationService::notifikasiKontenPublish('Berita', $item->judul_berita, null, $item->rt_id);

        return response()->json(['success' => 'Berita berhasil disetujui!']);
    }

    public function rejectBerita(Request $request, $id)
    {
        $item = $this->dasar(Berita::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        \App\Services\NotificationService::notifikasiKontenRevisi(
            \App\Services\NotificationService::userDariId($item->operator_id),
            'Berita', $item->judul_berita, $request->catatan_revisi
        );

        return response()->json(['success' => 'Berita ditolak dan dikembalikan untuk revisi.']);
    }

    public function approveAgenda(Request $request, $id)
    {
        $item = $this->dasar(Agenda::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        // Agenda: beri tahu semua warga (arahkan ke detail agenda).
        \App\Services\NotificationService::notifikasiKontenPublish(
            'Agenda',
            $item->judul_agenda,
            \Illuminate\Support\Facades\Route::has('warga.agenda.show') ? route('warga.agenda.show', $item->id) : null,
            $item->rt_id
        );

        return response()->json(['success' => 'Agenda berhasil disetujui!']);
    }

    public function rejectAgenda(Request $request, $id)
    {
        $item = $this->dasar(Agenda::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        \App\Services\NotificationService::notifikasiKontenRevisi(
            \App\Services\NotificationService::userDariId($item->operator_id),
            'Agenda', $item->judul_agenda, $request->catatan_revisi
        );

        return response()->json(['success' => 'Agenda ditolak dan dikembalikan untuk revisi.']);
    }

    public function approvePengumuman(Request $request, $id)
    {
        $item = $this->dasar(Pengumuman::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $item->update([
            'status' => 'Publish',
            'approval_id' => auth()->id(),
            'tanggal_publish' => now(),
            'catatan_revisi' => null
        ]);

        // Pengumuman: beri tahu semua warga (tampil di dashboard warga).
        \App\Services\NotificationService::notifikasiKontenPublish(
            'Pengumuman',
            $item->judul_pengumuman,
            \Illuminate\Support\Facades\Route::has('warga.dashboard') ? route('warga.dashboard') : null,
            $item->rt_id
        );

        return response()->json(['success' => 'Pengumuman berhasil disetujui!']);
    }

    public function rejectPengumuman(Request $request, $id)
    {
        $item = $this->dasar(Pengumuman::class)->findOrFail($id);
        if ($item->status !== 'Review') return response()->json(['error' => 'Status tidak valid'], 400);

        $request->validate(['catatan_revisi' => 'required|string']);

        $item->update([
            'status' => 'Revisi',
            'approval_id' => auth()->id(),
            'catatan_revisi' => $request->catatan_revisi
        ]);

        \App\Services\NotificationService::notifikasiKontenRevisi(
            \App\Services\NotificationService::userDariId($item->operator_id),
            'Pengumuman', $item->judul_pengumuman, $request->catatan_revisi
        );

        return response()->json(['success' => 'Pengumuman ditolak dan dikembalikan untuk revisi.']);
    }
}
