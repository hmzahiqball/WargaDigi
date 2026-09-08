<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function index()
    {
        $agendas = Agenda::where('status', 'Publish')
            ->orderBy('tanggal_mulai', 'asc')
            ->get()
            ->map(function ($item) {
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
                    $colors = ['bg' => '#f3f4f6', 'border' => '#d1d5db', 'text' => '#6b7280']; // Abu-abu
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
                        'tanggal_mulai_formatted' => $start->translatedFormat('l, d F Y'),
                        'jam_mulai'    => $start->format('H:i'),
                        'jam_selesai'  => $end->format('H:i'),
                    ],
                ];
            });

        return view('warga.agenda.index', compact('agendas'));
    }
}
