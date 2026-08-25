@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'WargaDigi 21 — Digitalisasi Gotong Royong RW 21 Tanimulya. Mewujudkan lingkungan yang modern, transparan, dan saling mendukung.')

@section('content')
{{-- Hero Section --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="hero-badge">
                    <i class="bi bi-geo-alt-fill"></i> Halo Warga RW 21
                </div>
                <h1>Digitalisasi Gotong Royong RW 21 Tanimulya</h1>
                <p>Mewujudkan lingkungan RW 21 yang modern, transparan, dan saling mendukung melalui platform digital terpadu untuk pelayanan warga dan penyelenggaraan kegiatan.</p>
                <a href="#" class="btn btn-hero mt-2">
                    <i class="bi bi-grid-fill"></i> Layanan Warga
                </a>
            </div>
            <div class="col-lg-7">
                <div class="hero-images">
                    <div class="hero-img-main">
                        <img src="{{ asset('images/hero-main.jpg') }}" alt="Kegiatan Warga RW 21">
                    </div>
                    <div class="hero-img-side">
                        <div class="side-card">
                            <img src="{{ asset('images/hero-side-1.jpg') }}" alt="Harmoni Warga">
                            <div class="side-label">
                                Harmoni Warga
                                <small>Kebersamaan Kita</small>
                            </div>
                        </div>
                        <div class="side-card">
                            <img src="{{ asset('images/hero-side-2.jpg') }}" alt="Dasar & Aman">
                            <div class="side-label">
                                Dasar & Aman
                                <small>Untuk Kita</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats Bar --}}
<section class="container">
    <div class="stats-bar">
        <div class="row">
            @foreach($stats as $stat)
            <div class="col-4">
                <div class="stat-item">
                    <div class="stat-number" data-count-up data-target="{{ $stat['number'] }}" data-suffix="{{ $stat['suffix'] }}">0</div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

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
        @foreach($berita as $item)
        <div class="col-md-4 fade-on-scroll">
            <div class="card-warga">
                <img src="{{ asset($item['image']) }}" class="card-img-top" alt="{{ $item['title'] }}">
                <div class="card-body">
                    <div class="card-date">{{ $item['date'] }}</div>
                    <h5 class="card-title">{{ $item['title'] }}</h5>
                    <p class="card-text">{{ $item['excerpt'] }}</p>
                    <a href="#" class="link-green" style="font-size: 0.9rem;">Baca Selengkapnya <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>

{{-- Kalender Interaktif --}}
<section class="py-5" style="background: #f0f5f0;">
    <div class="container">
        <div class="text-center mb-4">
            <div class="section-badge"><i class="bi bi-calendar3"></i> Kegiatan & Rencana RW 21</div>
            <h2>Kalender Interaktif</h2>
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
                        @foreach($agenda as $item)
                        <div class="agenda-item">
                            <div class="agenda-date-box">
                                <div class="agenda-day">{{ $item['day'] }}</div>
                                <div class="agenda-month">{{ $item['month'] }}</div>
                            </div>
                            <div class="agenda-info">
                                <h6>{{ $item['title'] }}</h6>
                                <small>{{ $item['time'] }}</small>
                            </div>
                            <a href="#" class="agenda-link">Detail</a>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-warga">Lihat Semua Agenda</a>
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
                    <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}">
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
            initCalendar('calendarWidget', [
                { day: 17, month: 7 },
                { day: 25, month: 7 },
                { day: 1, month: 8 },
            ]);
        }
    });
</script>
@endpush
