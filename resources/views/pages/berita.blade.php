@extends('layouts.app')

@section('title', 'Berita')
@section('meta_description', 'Berita RW 21: kabar terbaru, pengumuman, dan cerita kegiatan warga. Tetap terhubung dan berpartisipasi dalam lingkungan kita.')

@php
    $utama = $articles[0] ?? null;
    $lainnya = array_slice($articles, 1);
    // Gambar cadangan bila berita tanpa foto atau foto gagal dimuat.
    $cadangan = [asset('images/berita-1.jpg'), asset('images/berita-2.jpg'), asset('images/berita-3.jpg')];
    $gambar = fn ($item, $i) => $item['image'] ?: $cadangan[$i % 3];
@endphp

@section('content')
{{-- Header --}}
<section class="berita-hero">
    <div class="container">
        <span class="section-badge mb-2"><i class="bi bi-newspaper"></i> Kabar Warga</span>
        <h1>Berita RW 21</h1>
        <p>Kabar terbaru, pengumuman, dan cerita kegiatan dari lingkungan kita. Baca, bagikan, dan ikut berpartisipasi.</p>

        <div class="berita-toolbar">
            <div class="search-box flex-grow-1">
                <i class="bi bi-search search-icon" aria-hidden="true"></i>
                <label for="cariBerita" class="visually-hidden">Cari berita</label>
                <input type="search" id="cariBerita" class="form-control" placeholder="Cari judul atau isi berita..." autocomplete="off">
            </div>
        </div>
        <div class="filter-pills berita-kategori" role="group" aria-label="Filter kategori berita">
            @foreach($categories as $index => $cat)
                <button type="button" class="filter-pill {{ $index === 0 ? 'active' : '' }}" data-kategori="{{ $index === 0 ? '' : $cat }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}">{{ $cat }}</button>
            @endforeach
        </div>
    </div>
</section>

<section class="container pb-5">
    @if($utama)
        {{-- Berita utama (terbaru) --}}
        <article class="berita-utama item-berita" data-judul="{{ \Illuminate\Support\Str::lower($utama['title'] . ' ' . $utama['excerpt']) }}" data-kategori="{{ $utama['category'] }}">
            <a href="{{ route('berita.show', $utama['slug']) }}" class="berita-utama-link">
                <div class="berita-utama-img">
                    <img src="{{ $gambar($utama, 0) }}" alt="{{ $utama['title'] }}" fetchpriority="high"
                         onerror="this.onerror=null;this.src='{{ $cadangan[0] }}';">
                    <span class="berita-label-terbaru"><i class="bi bi-lightning-charge-fill"></i> Terbaru</span>
                </div>
                <div class="berita-utama-body">
                    <div class="berita-meta">
                        @if($utama['category'])<span class="berita-kat">{{ $utama['category'] }}</span>@endif
                        <span class="berita-asal"><i class="bi bi-geo-alt-fill"></i> {{ $utama['origin'] }}</span>
                        <span><i class="bi bi-calendar3"></i> {{ $utama['date'] }}</span>
                        <span><i class="bi bi-clock"></i> {{ $utama['minutes'] }} menit baca</span>
                    </div>
                    <h2>{{ $utama['title'] }}</h2>
                    <p>{{ $utama['excerpt'] }}</p>
                    <span class="berita-baca">Baca selengkapnya <i class="bi bi-arrow-right"></i></span>
                </div>
            </a>
        </article>

        {{-- Berita lainnya --}}
        @if(count($lainnya))
            <div class="d-flex align-items-center justify-content-between mt-5 mb-3">
                <h3 class="berita-subjudul mb-0">Berita Lainnya</h3>
            </div>
            <div class="row g-4" id="gridBerita">
                @foreach($lainnya as $i => $item)
                    <div class="col-sm-6 col-lg-4 item-berita" data-judul="{{ \Illuminate\Support\Str::lower($item['title'] . ' ' . $item['excerpt']) }}" data-kategori="{{ $item['category'] }}">
                        <article class="kartu-berita">
                            <a href="{{ route('berita.show', $item['slug']) }}" class="kartu-berita-link">
                                <div class="kartu-berita-img">
                                    <img src="{{ $gambar($item, $i + 1) }}" alt="{{ $item['title'] }}" loading="lazy"
                                         onerror="this.onerror=null;this.src='{{ $cadangan[($i + 1) % 3] }}';">
                                    @if($item['category'])<span class="berita-kat berita-kat-img">{{ $item['category'] }}</span>@endif
                                </div>
                                <div class="kartu-berita-body">
                                    <div class="berita-meta">
                                        <span class="berita-asal"><i class="bi bi-geo-alt-fill"></i> {{ $item['origin'] }}</span>
                                        <span><i class="bi bi-calendar3"></i> {{ $item['date'] }}</span>
                                        <span><i class="bi bi-clock"></i> {{ $item['minutes'] }} menit</span>
                                    </div>
                                    <h4>{{ $item['title'] }}</h4>
                                    <p>{{ $item['excerpt'] }}</p>
                                    <span class="berita-baca mt-auto">Baca <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </a>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center text-muted py-5 d-none" id="beritaTidakDitemukan" role="status">
            <i class="bi bi-search fs-1 d-block mb-2"></i>
            Tidak ada berita yang cocok. Coba kata kunci atau kategori lain.
        </div>
    @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-newspaper fs-1 d-block mb-2"></i>
            Belum ada berita yang dipublikasikan.
        </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
(function () {
    // Pencarian dan filter kategori di sisi browser (seluruh berita terbit sudah dimuat).
    const input = document.getElementById('cariBerita');
    const tombol = document.querySelectorAll('.berita-kategori .filter-pill');
    const item = document.querySelectorAll('.item-berita');
    const kosong = document.getElementById('beritaTidakDitemukan');
    const subjudul = document.querySelector('.berita-subjudul');
    let kategori = '';

    function saring() {
        const kata = (input?.value || '').trim().toLowerCase();
        let tampil = 0;
        item.forEach(function (el) {
            const cocok = (!kategori || el.dataset.kategori === kategori) && (!kata || el.dataset.judul.includes(kata));
            el.classList.toggle('d-none', !cocok);
            if (cocok) tampil++;
        });
        kosong?.classList.toggle('d-none', tampil > 0);
        if (subjudul) subjudul.textContent = (kata || kategori) ? 'Hasil (' + tampil + ' berita)' : 'Berita Lainnya';
    }

    tombol.forEach(function (b) {
        b.addEventListener('click', function () {
            tombol.forEach(x => { x.classList.remove('active'); x.setAttribute('aria-pressed', 'false'); });
            b.classList.add('active');
            b.setAttribute('aria-pressed', 'true');
            kategori = b.dataset.kategori;
            saring();
        });
    });
    input?.addEventListener('input', saring);
})();
</script>
@endpush
