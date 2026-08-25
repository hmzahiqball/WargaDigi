@extends('layouts.app')

@section('title', 'Pusat Informasi Data')
@section('meta_description', 'Pusat Informasi Data RW 21 — Statistik dan demografi terkini warga RW 21 Desa Tanimulya. Transparansi data untuk membangun lingkungan yang lebih baik.')

@section('content')
<section class="container py-5">
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
        <div>
            <h1 style="font-size: 2rem;">Pusat Informasi Data RW 21</h1>
            <p class="text-muted">Statistik dan demografi terkini warga RW 21 Desa Tanimulya. Transparansi data<br>untuk membangun lingkungan yang lebih baik.</p>
        </div>
        <div class="badge-update mt-2">
            <i class="bi bi-arrow-clockwise"></i> Pembaruan Terakhir: 7 Agustus 2026
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-4 mb-4">
        @foreach($stats as $stat)
        <div class="col-lg-3 col-md-6 fade-on-scroll">
            <div class="stat-info-card {{ $stat['highlight'] ?? false ? 'highlight' : '' }}">
                <div class="stat-info-header">
                    <div class="stat-info-icon"><i class="bi {{ $stat['icon'] }}"></i></div>
                    <span class="stat-info-label">{{ $stat['label'] }}</span>
                </div>
                <div class="stat-info-value">{{ $stat['value'] }}</div>
                <div class="stat-info-sub">{{ $stat['sub'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Charts Row --}}
    <div class="row g-4 mb-4">
        {{-- Gender Pie Chart --}}
        <div class="col-lg-5">
            <div class="chart-container">
                <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Komposisi Gender</h5>
                <div style="max-width: 250px; margin: 0 auto;">
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="gender-box">
                    <div class="gender-item">
                        <div class="gender-count">713</div>
                        <div class="gender-label">Laki-laki</div>
                    </div>
                    <div class="gender-item">
                        <div class="gender-count">733</div>
                        <div class="gender-label">Perempuan</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Age Distribution Bar Chart --}}
        <div class="col-lg-7">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="font-weight: 700; margin: 0;">Distribusi Kelompok Umur</h5>
                    <a href="#" class="link-green" style="font-size: 0.85rem;">Unduh Laporan <i class="bi bi-download"></i></a>
                </div>
                <canvas id="ageChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Table & Facilities --}}
    <div class="row g-4">
        {{-- RT Table --}}
        <div class="col-lg-7">
            <div class="chart-container">
                <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Sebaran Warga per RT</h5>
                <div class="table-responsive">
                    <table class="table table-warga mb-0">
                        <thead>
                            <tr>
                                <th>Wilayah</th>
                                <th>Kepala Keluarga</th>
                                <th>Total Jiwa</th>
                                <th>Keterwakilan UMKM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rtData as $rt)
                            <tr>
                                <td><span class="rt-badge">{{ $rt['rt'] }}</span></td>
                                <td>{{ $rt['kk'] }}</td>
                                <td>{{ $rt['jiwa'] }}</td>
                                <td><span class="umkm-badge">{{ $rt['umkm'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-muted" style="font-size: 0.85rem;">
                    Menampilkan 4 dari 6 RT... <a href="#" class="link-green">Lihat Selengkapnya</a>
                </div>
            </div>
        </div>

        {{-- Facilities --}}
        <div class="col-lg-5">
            <div class="chart-container">
                <h5 style="font-weight: 700; margin-bottom: 1.25rem;">Fasilitas Umum</h5>
                @foreach($facilities as $facility)
                <div class="facility-item">
                    <div class="facility-icon"><i class="bi {{ $facility['icon'] }}"></i></div>
                    <div class="facility-info">
                        <h6>{{ $facility['name'] }}</h6>
                        <small>{{ $facility['location'] }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gender Donut Chart
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($genderData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($genderData['data']) !!},
                    backgroundColor: {!! json_encode($genderData['colors']) !!},
                    borderWidth: 0,
                    cutout: '70%',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Age Distribution Bar Chart
    const ageCtx = document.getElementById('ageChart');
    if (ageCtx) {
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($ageData['labels']) !!},
                datasets: [{
                    label: 'Jumlah',
                    data: {!! json_encode($ageData['data']) !!},
                    backgroundColor: '#2E7D32',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                    },
                    x: {
                        grid: { display: false },
                    }
                }
            }
        });
    }
});
</script>
@endpush
