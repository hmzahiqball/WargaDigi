@extends('layouts.rt')

@section('title', 'Dashboard RT')

@section('content')
{{-- Header --}}
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Ringkasan</h2>
    <p class="text-muted mb-0">Selamat pagi, Ketua RT. Berikut adalah perkembangan terbaru di lingkungan Anda.</p>
</div>

{{-- Top 3 Metric Cards --}}
<div class="row g-3 mb-4">
    {{-- Card 1: TOTAL WARGA --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">TOTAL WARGA</span>
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h1 class="fw-bold text-primary mb-1 display-5">{{ $stats['total_warga']['total'] }}</h1>
            <div class="text-success small fw-semibold mb-3">
                <i class="bi bi-graph-up-arrow me-1"></i>{{ $stats['total_warga']['change'] }}
            </div>
            <hr class="my-2 text-muted opacity-25">
            <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                <span>Laki-laki <strong class="text-dark">{{ $stats['total_warga']['laki_laki'] }}</strong></span>
                <span>Perempuan <strong class="text-dark">{{ $stats['total_warga']['perempuan'] }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Card 2: DOKUMEN MENUNGGU --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">DOKUMEN MENUNGGU</span>
                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-journal-bookmark-fill fs-5"></i>
                </div>
            </div>
            <h1 class="fw-bold text-warning mb-1 display-5">{{ $stats['dokumen_menunggu']['total'] }}</h1>
            <div class="text-warning small fw-semibold mb-3">
                <i class="bi bi-clock me-1"></i>{{ $stats['dokumen_menunggu']['status'] }}
            </div>
            <hr class="my-2 text-muted opacity-25">
            <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                <span>Surat Domisili <strong class="text-dark">{{ $stats['dokumen_menunggu']['surat_domisili'] }}</strong></span>
                <span>Kartu Keluarga <strong class="text-dark">{{ $stats['dokumen_menunggu']['kartu_keluarga'] }}</strong></span>
            </div>
        </div>
    </div>

    {{-- Card 3: PERSETUJUAN KEUANGAN --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">PERSETUJUAN KEUANGAN</span>
                <div class="bg-danger bg-opacity-10 p-2 rounded-3 text-danger d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-cash-coin fs-5"></i>
                </div>
            </div>
            <h1 class="fw-bold text-danger mb-1 display-5">{{ $stats['persetujuan_keuangan']['total'] }}</h1>
            <div class="text-muted small fw-semibold mb-3">
                <i class="bi bi-info-circle me-1"></i>{{ $stats['persetujuan_keuangan']['status'] }}
            </div>
            <hr class="my-2 text-muted opacity-25">
            <div class="d-flex justify-content-between align-items-center text-muted small mt-2">
                <span>Iuran Bulanan <strong class="text-dark">{{ $stats['persetujuan_keuangan']['iuran_bulanan'] }}</strong></span>
                <span>Laporan Kas <strong class="text-dark">{{ $stats['persetujuan_keuangan']['laporan_kas'] }}</strong></span>
            </div>
        </div>
    </div>
</div>

{{-- Middle Section: Chart --}}
<div class="card card-custom p-4 mb-4 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Tren Populasi Warga (6 Bulan Terakhir)</h5>
        <select class="form-select form-select-sm w-auto rounded-pill border-light-subtle shadow-sm bg-light">
            <option>Tahun Ini</option>
            <option>Tahun Lalu</option>
        </select>
    </div>
    <div class="chart-container" style="position: relative; height: 260px;">
        <canvas id="populationChart"></canvas>
    </div>
</div>

{{-- Bottom 2-Column Section --}}
<div class="row g-4">
    {{-- Left: Aktivitas Laporan Keuangan --}}
    <div class="col-md-6">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Aktivitas Laporan Keuangan</h5>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($aktivitasKeuangan as $item)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="{{ $item['bg_icon'] }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi {{ $item['icon'] }} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark small" style="line-height: 1.3;">{{ $item['title'] }}</h6>
                                <span class="text-muted text-xs d-block">{{ $item['time'] }}</span>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 ms-2 {{ $item['badge_class'] }} fw-semibold small">
                            {{ $item['badge'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Aktivitas Laporan Dokumen --}}
    <div class="col-md-6">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Aktivitas Laporan Dokumen</h5>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($aktivitasDokumen as $item)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="{{ $item['bg_icon'] }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi {{ $item['icon'] }} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark small" style="line-height: 1.3;">{{ $item['title'] }}</h6>
                                <span class="text-muted text-xs d-block">{{ $item['time'] }}</span>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 ms-2 {{ $item['badge_class'] }} fw-semibold small">
                            {{ $item['badge'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('populationChart')?.getContext('2d');
    if (ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.25)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Jumlah Warga',
                    data: {!! json_encode($chartData['values']) !!},
                    borderColor: '#0d6efd',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6c757d', font: { family: 'Inter' } }
                    },
                    y: {
                        grid: { color: '#f0f0f0' },
                        ticks: { color: '#6c757d', font: { family: 'Inter' } },
                        min: 1180
                    }
                }
            }
        });
    }
});
</script>
@endpush
