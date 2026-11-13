<?php

namespace App\Http\Controllers\Rw\Concerns;

use App\Models\Agenda;
use App\Models\AgendaKehadiran;
use App\Models\Berita;
use App\Models\Pengumuman;
use Carbon\Carbon;

/** Pemetaan berita, agenda, dan pengumuman menjadi data daftar untuk halaman Persetujuan Konten dan Pusat Informasi. */
trait MemetakanKonten
{
    /**
     * @param callable(string):\Illuminate\Database\Eloquent\Builder $dasar  pembangun query terbatas cakupan, menerima nama kelas model
     * @param string[] $status  status konten yang dimuat
     * @return array{berita:\Illuminate\Support\Collection,agenda:\Illuminate\Support\Collection,pengumuman:\Illuminate\Support\Collection,semua:array}
     */
    protected function daftarKonten(callable $dasar, array $status): array
    {
        $berita = $dasar(Berita::class)->with('operator')
            ->whereIn('status', $status)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Berita',
                    'asal' => $item->asal_label,
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

        $agenda = $dasar(Agenda::class)->with('operator')
            ->whereIn('status', $status)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Agenda',
                    'asal' => $item->asal_label,
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

        $pengumuman = $dasar(Pengumuman::class)->with('operator')
            ->whereIn('status', $status)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Pengumuman',
                    'asal' => $item->asal_label,
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
                    'kategori' => $item->kategori ?: 'Pengumuman Warga',
                    'updated_at' => $item->updated_at
                ];
            });


        $semua = collect($berita)->merge($agenda)->merge($pengumuman)->sortByDesc('updated_at')->values()->all();

        return compact('berita', 'agenda', 'pengumuman', 'semua');
    }
}