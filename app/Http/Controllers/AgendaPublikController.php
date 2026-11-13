<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Support\KontenScope;
use App\Support\SlugKonten;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Halaman Agenda publik: kalender kegiatan, agenda mendatang, dan arsip agenda.
 * Cakupan mengikuti KontenScope::untukWarga (tamu: agenda RW; warga login: RW + RT-nya).
 */
class AgendaPublikController extends Controller
{
    private function terbit()
    {
        return KontenScope::untukWarga(Agenda::with('rt'), auth()->user())->where('status', 'Publish');
    }

    public function index(Request $request)
    {
        $mendatang = $this->terbit()
            ->where(fn ($q) => $q->where('tanggal_selesai', '>=', now())
                ->orWhere(fn ($w) => $w->whereNull('tanggal_selesai')->where('tanggal_mulai', '>=', today())))
            ->orderBy('tanggal_mulai')
            ->get()
            ->map(fn (Agenda $a) => self::kartu($a));

        $selesai = $this->terbit()
            ->where(fn ($q) => $q->where('tanggal_selesai', '<', now())
                ->orWhere(fn ($w) => $w->whereNull('tanggal_selesai')->where('tanggal_mulai', '<', today())))
            ->orderByDesc('tanggal_mulai')
            ->paginate(6, ['*'], 'arsip_page')
            ->withQueryString()
            ->through(fn (Agenda $a) => self::kartu($a));

        $tandaKalender = self::tandaKalender($this->terbit());

        return view('pages.agenda', compact('mendatang', 'selesai', 'tandaKalender'));
    }

    public function show(string $agenda)
    {
        $item = SlugKonten::cari($this->terbit(), $agenda);
        abort_if(! $item, 404);

        if ($item->slug && $agenda !== $item->slug) {
            return redirect()->route('agenda.show', $item->slug, 301);
        }

        $lainnya = $this->terbit()
            ->where('id', '!=', $item->id)
            ->where('tanggal_selesai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->take(3)
            ->get()
            ->map(fn (Agenda $a) => self::kartu($a));

        return view('pages.agenda_detail', [
            'agenda'  => self::kartu($item) + [
                'isi'        => \App\Support\HtmlAman::bersihkan($item->detail_pengumuman),
                'link_gmaps' => filter_var($item->link_gmaps, FILTER_VALIDATE_URL) && Str::startsWith($item->link_gmaps, ['http://', 'https://']) ? $item->link_gmaps : null,
                'latitude'   => is_numeric($item->latitude) ? (float) $item->latitude : null,
                'longitude'  => is_numeric($item->longitude) ? (float) $item->longitude : null,
            ],
            'lainnya' => $lainnya,
        ]);
    }

    /** Data tampilan satu agenda (dipakai halaman daftar, detail, dan beranda). */
    public static function kartu(Agenda $a): array
    {
        $mulai = $a->tanggal_mulai;
        $selesai = $a->tanggal_selesai;
        $berlangsung = $mulai && $mulai->isPast() && (! $selesai || $selesai->isFuture());

        $jam = $mulai ? $mulai->format('H:i') : '';
        if ($selesai && $mulai) {
            $jam .= $selesai->isSameDay($mulai)
                ? ' - ' . $selesai->format('H:i')
                : ' - ' . $selesai->translatedFormat('d M, H:i');
        }

        return [
            'judul'       => $a->judul_agenda,
            'slug'        => $a->kunci_url,
            'kategori'    => $a->kategori,
            'asal'        => $a->asal_label,
            'hari'        => $mulai ? $mulai->format('d') : '--',
            'bulan'       => $mulai ? $mulai->translatedFormat('M') : '',
            'tanggal'     => $mulai ? $mulai->translatedFormat('l, d F Y') : '',
            'jam'         => trim($jam) . ($jam ? ' WIB' : ''),
            'lokasi'      => $a->lokasi,
            'ringkasan'   => Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $a->detail_pengumuman)))), 140),
            'banner'      => $a->banner_flyer && is_file(public_path($a->banner_flyer)) ? asset($a->banner_flyer) : null,
            'berlangsung' => $berlangsung,
            'url'         => route('agenda.show', $a->kunci_url),
        ];
    }

    /** Tanda tanggal di kalender (bulan 0-11), 3 bulan lalu sampai 6 bulan ke depan. */
    public static function tandaKalender($query): array
    {
        return $query
            ->whereBetween('tanggal_mulai', [now()->subMonths(3)->startOfMonth(), now()->addMonths(6)->endOfMonth()])
            ->pluck('tanggal_mulai')
            ->filter()
            ->map(fn ($t) => ['day' => (int) $t->format('j'), 'month' => (int) $t->format('n') - 1, 'year' => (int) $t->format('Y')])
            ->unique(fn ($e) => $e['year'] . '-' . $e['month'] . '-' . $e['day'])
            ->values()
            ->all();
    }
}
