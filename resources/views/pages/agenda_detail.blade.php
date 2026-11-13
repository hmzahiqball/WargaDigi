@extends('layouts.app')

@section('title', $agenda['judul'])
@section('meta_description', $agenda['ringkasan'])

@section('content')
<section class="container py-4 py-lg-5">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="link-green">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('agenda') }}" class="link-green">Agenda</a></li>
            <li class="breadcrumb-item active text-truncate" aria-current="page" style="max-width: 50vw;">{{ $agenda['judul'] }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="agenda-detail">
                @if($agenda['banner'])
                    <div class="agenda-detail-banner">
                        <img src="{{ $agenda['banner'] }}" alt="{{ $agenda['judul'] }}">
                    </div>
                @endif
                <div class="agenda-detail-body">
                    <div class="berita-meta mb-2">
                        @if($agenda['berlangsung'])
                            <span class="agenda-status-berlangsung"><i class="bi bi-broadcast"></i> Sedang berlangsung</span>
                        @endif
                        @if($agenda['kategori'])<span class="berita-kat">{{ $agenda['kategori'] }}</span>@endif
                        <span class="berita-asal"><i class="bi bi-geo-alt-fill"></i> {{ $agenda['asal'] }}</span>
                    </div>
                    <h1>{{ $agenda['judul'] }}</h1>
                    <div class="agenda-detail-isi">
                        {!! $agenda['isi'] !!}
                    </div>
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <aside class="agenda-detail-info">
                <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Informasi Kegiatan</h2>
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-calendar3"></i><div><small>Tanggal</small>{{ $agenda['tanggal'] }}</div></li>
                    @if($agenda['jam'])<li><i class="bi bi-clock"></i><div><small>Waktu</small>{{ $agenda['jam'] }}</div></li>@endif
                    @if($agenda['lokasi'])<li><i class="bi bi-geo-alt"></i><div><small>Lokasi</small>{{ $agenda['lokasi'] }}</div></li>@endif
                </ul>
                @if($agenda['link_gmaps'])
                    <a href="{{ $agenda['link_gmaps'] }}" target="_blank" rel="noopener" class="btn btn-outline-warga w-100 mt-3">
                        <i class="bi bi-map me-1"></i> Buka di Google Maps
                    </a>
                @elseif($agenda['latitude'] && $agenda['longitude'])
                    <a href="https://www.google.com/maps?q={{ $agenda['latitude'] }},{{ $agenda['longitude'] }}" target="_blank" rel="noopener" class="btn btn-outline-warga w-100 mt-3">
                        <i class="bi bi-map me-1"></i> Lihat Lokasi di Peta
                    </a>
                @endif
                @guest
                    <p class="text-muted small mt-3 mb-0">
                        Warga dapat mengonfirmasi kehadiran setelah <a href="{{ route('login') }}" class="link-green">masuk</a>.
                    </p>
                @endguest
            </aside>

            @if($lainnya->isNotEmpty())
                <h2 class="berita-subjudul mt-4 mb-3" style="font-size: 1.1rem;">Agenda Lainnya</h2>
                @foreach($lainnya as $a)
                    <div class="mb-2">@include('pages.partials.kartu-agenda', ['a' => $a])</div>
                @endforeach
            @endif

            <a href="{{ route('agenda') }}" class="link-green d-inline-block mt-3"><i class="bi bi-arrow-left"></i> Kembali ke Agenda</a>
        </div>
    </div>
</section>
@endsection
