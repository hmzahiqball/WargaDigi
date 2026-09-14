<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use App\Models\AgendaKehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $agendas = Agenda::with('operator')->where('status', 'Publish')
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->map(function ($item) use ($userId) {
                $start = Carbon::parse($item->tanggal_mulai)->setTimezone('Asia/Jakarta');
                $end = $item->tanggal_selesai
                    ? Carbon::parse($item->tanggal_selesai)->setTimezone('Asia/Jakarta')
                    : (clone $start)->addHours(1);

                // Assign colors based on kategori
                $colorMap = [
                    'Rapat'         => ['bg' => '#dcfce7', 'border' => '#86efac', 'text' => '#166534'],
                    'Keagamaan'     => ['bg' => '#fef9c3', 'border' => '#fde047', 'text' => '#854d0e'],
                    'Sosial'        => ['bg' => '#dbeafe', 'border' => '#93c5fd', 'text' => '#1e40af'],
                    'Gotong Royong' => ['bg' => '#f3e8ff', 'border' => '#d8b4fe', 'text' => '#6b21a8'],
                    'Kesehatan'     => ['bg' => '#ccfbf1', 'border' => '#5eead4', 'text' => '#115e59'],
                    'Olahraga'      => ['bg' => '#fce7f3', 'border' => '#f9a8d4', 'text' => '#9d174d'],
                    'Pendidikan'    => ['bg' => '#e0f2fe', 'border' => '#7dd3fc', 'text' => '#075985'],
                    'Lainnya'       => ['bg' => '#f3f4f6', 'border' => '#d1d5db', 'text' => '#374151'],
                ];
                $colors = $colorMap[$item->kategori] ?? $colorMap['Lainnya'];
                
                // Past event logic
                $now = Carbon::now('Asia/Jakarta');
                if ($end < $now) {
                    $colors = ['bg' => '#f3f4f6', 'border' => '#d1d5db', 'text' => '#6b7280'];
                }

                // RSVP data
                $userRsvp = null;
                $totalHadir = 0;
                if ($item->is_rsvp_enabled) {
                    $rsvp = AgendaKehadiran::where('agenda_id', $item->id)
                        ->where('user_id', $userId)
                        ->first();
                    $userRsvp = $rsvp ? $rsvp->status_kehadiran : null;
                    $totalHadir = AgendaKehadiran::where('agenda_id', $item->id)
                        ->where('status_kehadiran', 'Hadir')
                        ->count();
                }

                return [
                    'id'              => $item->id,
                    'title'           => $item->judul_agenda,
                    'start'           => $start->toIso8601String(),
                    'end'             => $end->toIso8601String(),
                    'backgroundColor' => $colors['bg'],
                    'borderColor'     => $colors['border'],
                    'textColor'       => $colors['text'],
                    'extendedProps'   => [
                        'lokasi'       => $item->lokasi,
                        'kategori'     => $item->kategori,
                        'deskripsi'    => $item->detail_pengumuman,
                        'link_gmaps'   => $item->link_gmaps,
                        'latitude'     => $item->latitude,
                        'longitude'    => $item->longitude,
                        'banner_flyer' => $item->banner_flyer ? asset($item->banner_flyer) : null,
                        'tanggal_mulai_formatted' => $start->translatedFormat('l, d F Y'),
                        'tanggal_selesai_formatted' => $end->translatedFormat('l, d F Y'),
                        'is_multi_day' => $start->format('Y-m-d') !== $end->format('Y-m-d') ? 1 : 0,
                        'jam_mulai'    => $start->format('H:i'),
                        'jam_selesai'  => $end->format('H:i'),
                        'is_rsvp_enabled' => $item->is_rsvp_enabled ? 1 : 0,
                        'user_rsvp_status' => $userRsvp,
                        'total_hadir'  => $totalHadir,
                    ],
                ];
            });

        return view('warga.agenda.index', compact('agendas'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $agenda = Agenda::with('operator')->where('status', 'Publish')->findOrFail($id);

        $start = Carbon::parse($agenda->tanggal_mulai)->setTimezone('Asia/Jakarta');
        $end = $agenda->tanggal_selesai
            ? Carbon::parse($agenda->tanggal_selesai)->setTimezone('Asia/Jakarta')
            : (clone $start)->addHours(1);

        $isMultiDay = $start->format('Y-m-d') !== $end->format('Y-m-d');

        // RSVP data
        $userRsvp = null;
        $totalHadir = 0;
        $totalTidakHadir = 0;
        $totalRagu = 0;
        if ($agenda->is_rsvp_enabled) {
            $rsvp = AgendaKehadiran::where('agenda_id', $agenda->id)
                ->where('user_id', $userId)
                ->first();
            $userRsvp = $rsvp ? $rsvp->status_kehadiran : null;
            $totalHadir = AgendaKehadiran::where('agenda_id', $agenda->id)->where('status_kehadiran', 'Hadir')->count();
            $totalTidakHadir = AgendaKehadiran::where('agenda_id', $agenda->id)->where('status_kehadiran', 'Tidak Hadir')->count();
            $totalRagu = AgendaKehadiran::where('agenda_id', $agenda->id)->where('status_kehadiran', 'Ragu-ragu')->count();
        }

        return view('warga.agenda.show', compact(
            'agenda', 'start', 'end', 'isMultiDay',
            'userRsvp', 'totalHadir', 'totalTidakHadir', 'totalRagu'
        ));
    }

    public function rsvp(Request $request)
    {
        $request->validate([
            'agenda_id' => 'required|uuid|exists:agenda,id',
            'status_kehadiran' => 'required|in:Hadir,Tidak Hadir,Ragu-ragu',
        ]);

        $agenda = Agenda::findOrFail($request->agenda_id);

        if (!$agenda->is_rsvp_enabled) {
            return response()->json(['success' => false, 'message' => 'RSVP tidak aktif untuk agenda ini.'], 422);
        }

        $kehadiran = AgendaKehadiran::updateOrCreate(
            [
                'agenda_id' => $request->agenda_id,
                'user_id'   => Auth::id(),
            ],
            [
                'status_kehadiran' => $request->status_kehadiran,
            ]
        );

        $totalHadir = AgendaKehadiran::where('agenda_id', $request->agenda_id)
            ->where('status_kehadiran', 'Hadir')
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Respons kehadiran berhasil disimpan.',
            'status_kehadiran' => $kehadiran->status_kehadiran,
            'total_hadir' => $totalHadir,
        ]);
    }
}
