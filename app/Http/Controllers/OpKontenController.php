<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OpKontenController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();

        // 1. Top Stats
        $beritaAktif = \App\Models\Berita::where('operator_id', $userId)
            ->where('status', 'Publish')
            ->count();

        $pengumumanAktif = \App\Models\Pengumuman::where('operator_id', $userId)
            ->where('status', 'Publish')
            ->count();

        $agendaMendatang = \App\Models\Agenda::where('operator_id', $userId)
            ->where('status', 'Publish')
            ->whereDate('tanggal_mulai', '>=', now()->toDateString())
            ->count();

        $butuhTindakan = \App\Models\Berita::where('operator_id', $userId)->where('status', 'Revisi')->count() +
                         \App\Models\Agenda::where('operator_id', $userId)->where('status', 'Revisi')->count() +
                         \App\Models\Pengumuman::where('operator_id', $userId)->where('status', 'Revisi')->count();

        $stats = [
            'berita_aktif' => $beritaAktif,
            'pengumuman_aktif' => $pengumumanAktif,
            'agenda_mendatang' => $agendaMendatang,
            'butuh_tindakan' => $butuhTindakan,
        ];

        // 2. Kolom Kiri: Antrean Revisi
        $revisiBerita = \App\Models\Berita::where('operator_id', $userId)->where('status', 'Revisi')->get()->map(function($item) {
            return [
                'type' => 'Berita',
                'title' => $item->judul_berita,
                'note' => $item->catatan_revisi,
                'updated_at' => $item->updated_at,
                'url' => route('opkonten.berita.index')
            ];
        });
        $revisiAgenda = \App\Models\Agenda::where('operator_id', $userId)->where('status', 'Revisi')->get()->map(function($item) {
            return [
                'type' => 'Agenda',
                'title' => $item->judul_agenda,
                'note' => $item->catatan_revisi,
                'updated_at' => $item->updated_at,
                'url' => route('opkonten.agenda.index')
            ];
        });
        $revisiPengumuman = \App\Models\Pengumuman::where('operator_id', $userId)->where('status', 'Revisi')->get()->map(function($item) {
            return [
                'type' => 'Pengumuman',
                'title' => $item->judul_pengumuman,
                'note' => $item->catatan_revisi,
                'updated_at' => $item->updated_at,
                'url' => route('opkonten.pengumuman.index')
            ];
        });
        
        $antreanRevisi = $revisiBerita->concat($revisiAgenda)->concat($revisiPengumuman)
            ->sortByDesc('updated_at'); // Remove take(5) to allow scrolling

        // 3. Kolom Kiri: Aktivitas Terkini
        $recentBerita = \App\Models\Berita::where('operator_id', $userId)->latest('updated_at')->take(20)->get()->map(function($item) {
            return ['type' => 'Berita', 'title' => $item->judul_berita, 'status' => $item->status, 'updated_at' => $item->updated_at];
        });
        $recentAgenda = \App\Models\Agenda::where('operator_id', $userId)->latest('updated_at')->take(20)->get()->map(function($item) {
            return ['type' => 'Agenda', 'title' => $item->judul_agenda, 'status' => $item->status, 'updated_at' => $item->updated_at];
        });
        $recentPengumuman = \App\Models\Pengumuman::where('operator_id', $userId)->latest('updated_at')->take(20)->get()->map(function($item) {
            return ['type' => 'Pengumuman', 'title' => $item->judul_pengumuman, 'status' => $item->status, 'updated_at' => $item->updated_at];
        });

        $aktivitasTerkini = $recentBerita->concat($recentAgenda)->concat($recentPengumuman)
            ->sortByDesc('updated_at')->take(30);

        // 4. Kolom Kanan: Pengumuman Penting (Pinned)
        $pengumumanPin = \App\Models\Pengumuman::where('operator_id', $userId)
            ->where('status', 'Publish')
            ->where('is_priority', true)
            ->latest('updated_at')
            ->take(5)
            ->get();

        // 5 & 6. Data Agenda
        // We will pass all active agendas to JS to handle both the calendar and the "Agenda Terdekat" list dynamically.
        $semuaAgenda = \App\Models\Agenda::where('operator_id', $userId)
            ->where('status', 'Publish')
            ->orderBy('tanggal_mulai', 'asc')
            ->get(['id', 'judul_agenda', 'tanggal_mulai', 'lokasi'])
            ->map(function($agenda) {
                $agenda->date_str = \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('Y-m-d');
                $agenda->time_str = \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('H:i');
                $agenda->month_short = \Carbon\Carbon::parse($agenda->tanggal_mulai)->translatedFormat('M');
                $agenda->day_num = \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('d');
                return $agenda;
            });

        return view('opKonten.dashboard', compact('stats', 'antreanRevisi', 'aktivitasTerkini', 'pengumumanPin', 'semuaAgenda'));
    }
}
