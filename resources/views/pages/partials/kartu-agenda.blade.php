{{-- Kartu satu agenda. Variabel: $a (array dari AgendaPublikController::kartu), $redup (bool, untuk arsip). --}}
<article class="kartu-agenda {{ ($redup ?? false) ? 'kartu-agenda-arsip' : '' }}">
    <a href="{{ $a['url'] }}" class="kartu-agenda-link">
        <div class="kartu-agenda-tanggal" aria-hidden="true">
            <span class="hari">{{ $a['hari'] }}</span>
            <span class="bulan">{{ $a['bulan'] }}</span>
        </div>
        <div class="kartu-agenda-isi">
            <div class="berita-meta mb-1">
                @if($a['berlangsung'])
                    <span class="agenda-status-berlangsung"><i class="bi bi-broadcast"></i> Sedang berlangsung</span>
                @endif
                @if($a['kategori'])<span class="berita-kat">{{ $a['kategori'] }}</span>@endif
                <span class="berita-asal"><i class="bi bi-geo-alt-fill"></i> {{ $a['asal'] }}</span>
            </div>
            <h3>{{ $a['judul'] }}</h3>
            <ul class="kartu-agenda-info list-unstyled mb-0">
                <li><i class="bi bi-calendar3"></i> <span class="visually-hidden">Tanggal:</span> {{ $a['tanggal'] }}</li>
                @if($a['jam'])<li><i class="bi bi-clock"></i> <span class="visually-hidden">Waktu:</span> {{ $a['jam'] }}</li>@endif
                @if($a['lokasi'])<li><i class="bi bi-geo-alt"></i> <span class="visually-hidden">Lokasi:</span> {{ $a['lokasi'] }}</li>@endif
            </ul>
        </div>
        <i class="bi bi-chevron-right kartu-agenda-panah" aria-hidden="true"></i>
    </a>
</article>
