@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'WargaDigi 21 — Digitalisasi Gotong Royong RW 21 Tanimulya. Mewujudkan lingkungan yang modern, transparan, dan saling mendukung.')

@section('content')
{{-- Hero Section: teks dan gambar diatur Admin Aplikasi di Pengaturan Halaman Utama. --}}
@php
    $hb = \App\Services\HalamanUtama::data()['banner'];
    $hg = $hb['gambar'];
    $urlHero = fn ($slot) => \App\Services\HalamanUtama::urlGambar($slot, $hg[$slot]);
@endphp
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-5">
                @if($hb['badge'])
                    <div class="hero-badge">
                        <i class="bi bi-geo-alt-fill"></i> {{ $hb['badge'] }}
                    </div>
                @endif
                <h1>{{ $hb['judul'] }}</h1>
                @if($hb['deskripsi'])
                    <p>{{ $hb['deskripsi'] }}</p>
                @endif
                @if($hb['tombol_teks'] && $hb['tombol_url'])
                    <a href="{{ $hb['tombol_url'] }}" class="btn btn-hero mt-2"
                       @if(\Illuminate\Support\Str::startsWith($hb['tombol_url'], ['http://', 'https://'])) target="_blank" rel="noopener" @endif>
                        <i class="bi bi-grid-fill"></i> {{ $hb['tombol_teks'] }}
                    </a>
                @endif
            </div>
            <div class="col-lg-7">
                <div class="hero-images">
                    <div class="hero-img-main">
                        <img src="{{ $urlHero('utama') }}" alt="{{ $hb['judul'] }}" fetchpriority="high">
                    </div>
                    <div class="hero-img-side">
                        @foreach(['samping1', 'samping2'] as $slot)
                            <div class="side-card">
                                <img src="{{ $urlHero($slot) }}" alt="{{ $hg[$slot]['label'] ?: 'Kegiatan warga' }}" loading="lazy">
                                @if($hg[$slot]['label'] || $hg[$slot]['sublabel'])
                                    <div class="side-label">
                                        {{ $hg[$slot]['label'] }}
                                        @if($hg[$slot]['sublabel'])<small>{{ $hg[$slot]['sublabel'] }}</small>@endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Statistik jumlah KK, jiwa, dan layanan digital dihapus dari halaman publik (terlalu membuka data warga). --}}

{{-- Berita Terkini --}}
<section class="container py-5">
    <div class="section-header d-flex justify-content-between align-items-start">
        <div>
            <h2>Berita Terkini</h2>
            <p>Informasi dan pengumuman terbaru dari pengurus RW 21.</p>
        </div>
        <a href="/berita" class="link-green d-none d-md-inline">Lihat Semua <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4">
        @forelse($berita as $item)
        <div class="col-md-4 fade-on-scroll">
            <div class="card-warga">
                @php($beritaImg = \App\Models\Berita::urlGambar($item['image']) ?? asset('images/berita-1.jpg'))
                <img src="{{ $beritaImg }}" class="card-img-top" alt="{{ $item['title'] }}">
                <div class="card-body">
                    <div class="card-date">{{ $item['date'] }} <span class="berita-asal ms-1"><i class="bi bi-geo-alt-fill"></i> {{ $item['origin'] }}</span></div>
                    <h5 class="card-title">{{ $item['title'] }}</h5>
                    <p class="card-text">{{ $item['excerpt'] }}</p>
                    <a href="{{ route('berita.show', $item['slug']) }}" class="link-green" style="font-size: 0.9rem;">Baca Selengkapnya <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <p class="text-muted text-center mb-0">Belum ada berita yang dipublikasikan.</p>
        </div>
        @endforelse
    </div>
</section>

{{-- Kalender Kegiatan --}}
<section class="py-5" style="background: #f0f5f0;">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-badge"><i class="bi bi-calendar3"></i> Kegiatan & Rencana RW 21</div>
            <h2>Kalender Kegiatan</h2>
            <p class="text-muted">Jangan lewatkan informasi penting terkait jadwal kegiatan atau acara di lingkungan kita.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="calendar-widget" id="calendarWidget"></div>
            </div>
            <div class="col-lg-6">
                <div class="card-warga p-4">
                    <h5 class="mb-3" style="font-weight: 700;">Agenda Mendatang</h5>
                    <div class="agenda-list">
                        @forelse($agenda as $item)
                        <div class="agenda-item">
                            <div class="agenda-date-box">
                                <div class="agenda-day">{{ $item['day'] }}</div>
                                <div class="agenda-month">{{ $item['month'] }}</div>
                            </div>
                            <div class="agenda-info">
                                <h6>{{ $item['title'] }}</h6>
                                <small>{{ $item['time'] }}</small>
                            </div>
                            <a href="{{ $item['url'] }}" class="agenda-link">Detail</a>
                        </div>
                        @empty
                        <p class="text-muted mb-0">Belum ada agenda mendatang.</p>
                        @endforelse
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('agenda') }}" class="btn btn-outline-warga">Lihat Semua Agenda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pojok UMKM --}}
<section class="container py-5">
    <div class="text-center mb-4">
        <div class="section-badge"><i class="bi bi-shop"></i> Dukung Ekonomi Lokal</div>
        <h2>Pojok UMKM Warga</h2>
        <p class="text-muted">Temukan dan dukung berbagai produk dan jasa, unggulan dari keluarga di warga kita! RW 21.<br>Beli dari mereka, dukung sesama.</p>
    </div>

    <div class="row g-4">
        @foreach($umkm as $item)
        <div class="col-md-4 fade-on-scroll">
            <div class="card-umkm">
                <div class="umkm-img-wrapper">
                    @php($umkmImg = $item['image'] ? (\Illuminate\Support\Str::startsWith($item['image'], ['http://','https://']) ? $item['image'] : asset('storage/'.$item['image'])) : asset('images/umkm-1.jpg'))
                    <img src="{{ $umkmImg }}" alt="{{ $item['name'] }}">
                    <span class="price-badge">{{ $item['price'] }}</span>
                </div>
                <div class="card-body">
                    <div class="umkm-rt"><i class="bi bi-geo-alt"></i> {{ $item['rt'] }}</div>
                    <h5 class="card-title">{{ $item['name'] }}</h5>
                    <p class="card-text">{{ $item['desc'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
        <div class="col-md-4 d-flex align-items-center justify-content-center fade-on-scroll">
            <div class="text-center">
                <h5 class="mb-2" style="font-weight: 700;">Lihat Semua UMKM</h5>
                <p class="text-muted mb-3" style="font-size: 0.9rem;">Temukan lebih banyak produk dan jasa dari warga RW 21.</p>
                <a href="/pojok-umkm" class="btn btn-outline-warga">
                    <i class="bi bi-arrow-right"></i> Jelajah Sekarang
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar
        if (typeof initCalendar === 'function') {
            // Tanda tanggal agenda terbit dari database (bulan 0-11, sesuai kalender JS).
            initCalendar('calendarWidget', @json($tandaKalender));
        }
    });
</script>
@endpush
