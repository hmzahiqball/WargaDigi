<?php

namespace App\Http\Controllers\Rw;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Rw\Concerns\MemetakanKonten;
use App\Models\Agenda;
use App\Models\Berita;
use App\Models\Pengumuman;
use App\Support\KontenScope;
use Illuminate\Http\Request;

/**
 * Persetujuan konten (berita, agenda, pengumuman) di menu Persetujuan.
 * Semua persetujuan (berita, agenda, pengumuman; konten RT maupun RW) hanya oleh Admin RW (403 bagi peran lain).
 */
class PersetujuanKontenController extends Controller
{
    use MemetakanKonten;

    private function dasar(string $kelas)
    {
        return KontenScope::untukSetuju($kelas::query(), strtolower(class_basename($kelas)), auth()->user());
    }

    /** Antrean dan keputusan persetujuan konten hanya untuk Admin RW. */
    private function pastikanAdminRw(): void
    {
        abort_unless(KontenScope::adminRw(auth()->user()), 403, 'Persetujuan konten hanya dapat dilakukan oleh Admin RW.');
    }

    public function index()
    {
        $this->pastikanAdminRw();

        // Antrean kerja: yang menunggu keputusan (Review) dan yang dikembalikan untuk revisi.
        $data = $this->daftarKonten(fn (string $kelas) => $this->dasar($kelas), ['Review', 'Revisi']);

        return view('rw.pusat_informasi.index', $data + [
            'mode'        => 'persetujuan',
            'basePath'    => '/rw/persetujuan-konten',
            'tingkat'     => 'RW',
            'bolehBerita' => true,
            'keterangan'  => 'Anda menyetujui semua berita, agenda, dan pengumuman, baik dari RT maupun RW.',
        ]);
    }
    public function approveBerita(Request $request, $id)
    {
        $this->pastikanAdminRw();
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
        $this->pastikanAdminRw();
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
        $this->pastikanAdminRw();
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
            \Illuminate\Support\Facades\Route::has('warga.agenda.show') ? route('warga.agenda.show', $item->kunci_url) : null,
            $item->rt_id
        );

        return response()->json(['success' => 'Agenda berhasil disetujui!']);
    }

    public function rejectAgenda(Request $request, $id)
    {
        $this->pastikanAdminRw();
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
        $this->pastikanAdminRw();
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
        $this->pastikanAdminRw();
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
