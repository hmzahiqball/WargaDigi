@extends('layouts.app')

@section('title', 'Transparansi Keuangan')
@section('meta_description', 'Transparansi Keuangan RW 21 — Mewujudkan lingkungan yang jujur dan terbuka. Pantau aliran dana kas warga secara real-time.')

@section('content')
<section class="container py-5">
    {{-- Header --}}
    <div class="mb-4">
        <h1 style="font-size: 2rem; color: #2E7D32;">Transparansi Keuangan RW 21</h1>
        <p class="text-muted">Mewujudkan lingkungan yang jujur dan terbuka. Pantau aliran dana kas warga<br>secara real-time untuk kepercayaan bersama.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        {{-- Total Saldo --}}
        <div class="col-lg-4">
            <div class="finance-card">
                <div class="finance-label"><i class="bi bi-wallet2"></i> Total Saldo Kas</div>
                <div class="finance-value">{{ $summary['saldo'] }}</div>
                <div class="updated-badge"><i class="bi bi-check-circle"></i> Diperbarui: {{ $summary['updated'] }}</div>
            </div>
        </div>

        {{-- Pemasukan --}}
        <div class="col-lg-4">
            <div class="finance-card">
                <div class="finance-label" style="color: #43A047;"><i class="bi bi-graph-up-arrow"></i> Pemasukan ({{ $summary['pemasukan']['bulan'] }})</div>
                <div class="finance-value" style="color: #2E7D32;">{{ $summary['pemasukan']['total'] }}</div>
                @foreach($summary['pemasukan']['items'] as $item)
                <div class="finance-detail">
                    <span>{{ $item['label'] }}</span>
                    <span>{{ $item['amount'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pengeluaran --}}
        <div class="col-lg-4">
            <div class="finance-card">
                <div class="finance-label" style="color: #E53935;"><i class="bi bi-graph-down-arrow"></i> Pengeluaran ({{ $summary['pengeluaran']['bulan'] }})</div>
                <div class="finance-value" style="color: #E53935;">{{ $summary['pengeluaran']['total'] }}</div>
                @foreach($summary['pengeluaran']['items'] as $item)
                <div class="finance-detail">
                    <span>{{ $item['label'] }}</span>
                    <span>{{ $item['amount'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Chart & Transactions --}}
    <div class="row g-4 mb-5">
        {{-- Cash Flow Chart --}}
        <div class="col-lg-7">
            <div class="chart-container">
                <h5 style="font-weight: 700; color: #2E7D32; margin-bottom: 1.25rem;">Grafik Arus Kas 6 Bulan Terakhir</h5>
                <canvas id="cashFlowChart" height="200"></canvas>
            </div>
        </div>

        {{-- Recent Transactions --}}
        <div class="col-lg-5">
            <div class="chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 style="font-weight: 700; margin: 0;">Transaksi Terakhir</h5>
                    <a href="#" class="link-green" style="font-size: 0.85rem;">Lihat Semua</a>
                </div>
                <div class="transaction-list">
                    @foreach($transactions as $tx)
                    <div class="transaction-item">
                        <div class="transaction-icon {{ $tx['icon'] }}">
                            <i class="bi {{ $tx['icon'] === 'income' ? 'bi-arrow-down-left' : 'bi-arrow-up-right' }}"></i>
                        </div>
                        <div class="transaction-info">
                            <h6>{{ $tx['title'] }}</h6>
                            <small>{{ $tx['date'] }}</small>
                        </div>
                        <span class="transaction-amount {{ $tx['type'] }}">{{ $tx['amount'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Official Reports --}}
    <div class="mb-4">
        <h4 style="color: #2E7D32; font-weight: 700;">Laporan Keuangan Resmi</h4>
    </div>
    <div class="row g-4">
        @foreach($reports as $report)
        <div class="col-lg-4 col-md-6 fade-on-scroll">
            <div class="report-card">
                <div class="report-icon {{ $report['color'] }}">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h6>{{ $report['title'] }}</h6>
                <div class="report-period">{{ $report['period'] }}</div>
                <a href="{{ route('transparansi.download-pdf', $loop->index) }}" class="report-download">
                    <i class="bi bi-download"></i> Unduh PDF ({{ $report['size'] }})
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cashFlowCtx = document.getElementById('cashFlowChart');
    if (cashFlowCtx) {
        new Chart(cashFlowCtx, {
            type: 'line',
            data: {
                labels: ['Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt'],
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: [3800000, 4100000, 3900000, 4500000, 4000000, 4200000],
                        borderColor: '#2E7D32',
                        backgroundColor: 'rgba(46, 125, 50, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#2E7D32',
                    },
                    {
                        label: 'Pengeluaran',
                        data: [2100000, 1900000, 2500000, 2000000, 2200000, 1850000],
                        borderColor: '#E53935',
                        backgroundColor: 'rgba(229, 57, 53, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: '#E53935',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        }
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
