@extends('layouts.opKeuangan')

@section('title', 'Dashboard Operator Keuangan')

@section('content')
{{-- Page Header --}}
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Dashboard Overview</h2>
    <p class="text-muted mb-0">Status keuangan dan alur kerja tertunda secara real-time.</p>
</div>

{{-- Top 3 Stat Cards --}}
<div class="row g-3 mb-4">
    {{-- Card 1: TOTAL KAS RW --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2 text-success fs-5"></i>
                    <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">TOTAL KAS RW</span>
                </div>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-2.5 py-1 text-xs">
                    <i class="bi bi-graph-up-arrow me-1"></i>{{ $stats['kas_rw']['change'] }}
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1 fs-2">{{ $stats['kas_rw']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['kas_rw']['subtext'] }}</span>
        </div>
    </div>

    {{-- Card 2: TOTAL KAS RT --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-building text-success fs-5"></i>
                    <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">TOTAL KAS RT</span>
                </div>
            </div>
            <h2 class="fw-bold text-dark mb-1 fs-2">{{ $stats['kas_rt']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['kas_rt']['subtext'] }}</span>
        </div>
    </div>

    {{-- Card 3: DANA KEMATIAN --}}
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-suit-heart-fill text-danger fs-5"></i>
                    <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">DANA KEMATIAN</span>
                </div>
            </div>
            <h2 class="fw-bold text-dark mb-1 fs-2">{{ $stats['dana_kematian']['total'] }}</h2>
            <span class="text-muted small">{{ $stats['dana_kematian']['subtext'] }}</span>
        </div>
    </div>
</div>

{{-- Middle Grid: Chart & Sheet Saldo --}}
<div class="row g-4 mb-4">
    {{-- Left: Chart Tren Kas --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">TREN KAS: RW, RT & KEMATIAN</span>
                <div class="d-flex align-items-center gap-3 text-xs fw-semibold">
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #198754;"></span> RW</span>
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #75b798;"></span> RT</span>
                    <span class="d-flex align-items-center gap-1"><span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #ced4da;"></span> Kematian</span>
                </div>
            </div>

            <div class="chart-container" style="position: relative; height: 230px;">
                <canvas id="kasChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Right: Sheet Saldo --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">SHEET SALDO</span>
                <a href="#" class="text-muted"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>

            <div class="d-flex flex-column gap-3 mb-auto">
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light-subtle">
                    <span class="text-muted small">Assets</span>
                    <span class="fw-bold text-dark">{{ $sheetSaldo['assets'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light-subtle">
                    <span class="text-muted small">Liabilitas</span>
                    <span class="fw-bold text-dark">{{ $sheetSaldo['liabilitas'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-light-subtle">
                    <span class="text-muted small">Ekuitas</span>
                    <span class="fw-bold text-dark">{{ $sheetSaldo['ekuitas'] }}</span>
                </div>
            </div>

            <div class="bg-light border border-light-subtle rounded-3 p-2.5 text-center mt-3">
                <span class="small fw-bold text-dark"><i class="bi bi-check-circle-fill me-1 text-dark"></i> {{ $sheetSaldo['status'] }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Grid: Transaksi Terbaru & Status Laporan Terbaru --}}
<div class="row g-4">
    {{-- Left: Transaksi Terbaru --}}
    <div class="col-lg-8">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">TRANSAKSI TERBARU</span>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="d-flex flex-column gap-3">
                @foreach($transaksiTerbaru as $tx)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light border border-light-subtle">
                        <div class="d-flex align-items-center gap-3">
                            <div class="{{ $tx['icon_bg'] }} rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi {{ $tx['icon'] }} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark small" style="line-height: 1.3;">{{ $tx['title'] }}</h6>
                                <span class="text-muted text-xs d-block">{{ $tx['time'] }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold small mb-1 {{ $tx['type'] == 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $tx['amount'] }}
                            </div>
                            <span class="badge rounded-pill px-2.5 py-1 {{ $tx['status_class'] }} fw-semibold text-xs">
                                {{ $tx['status'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Status Laporan Terbaru --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">STATUS LAPORAN TERBARU</span>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            {{-- Vertical Steps --}}
            <div class="d-flex flex-column gap-4 mb-4 position-relative ps-2">
                @foreach($statusLaporan as $step)
                    <div class="d-flex align-items-start gap-3 position-relative">
                        <div class="fs-5 flex-shrink-0">
                            <i class="bi {{ $step['icon'] }}"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 small">{{ $step['title'] }}</h6>
                            <span class="text-muted text-xs">{{ $step['subtitle'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <button class="btn btn-success w-100 py-2.5 fw-bold mt-auto rounded-3 shadow-sm" type="button">
                Ingatkan Verifikator
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('kasChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'RW',
                        data: {!! json_encode($chartData['rw']) !!},
                        backgroundColor: '#198754',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'RT',
                        data: {!! json_encode($chartData['rt']) !!},
                        backgroundColor: '#75b798',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'Kematian',
                        data: {!! json_encode($chartData['kematian']) !!},
                        backgroundColor: '#ced4da',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
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
                        ticks: { display: false }
                    },
                    y: {
                        grid: { color: '#f0f0f0' },
                        ticks: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
