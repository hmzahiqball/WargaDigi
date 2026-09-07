@extends('layouts.opKeuangan')

@section('title', 'Laporan Keuangan')

@section('content')
@php
    $role = Auth::user()?->role ?? '';
    $isOpKeuanganRT = $role === 'Op. Keuangan RT';
    $isOpKeuanganRW = $role === 'Op. Keuangan RW';
    $isKetuaRT = $role === 'Ketua RT';
    $isPimpinanRW = $role === 'Pimpinan RW';
    $isDKM = $role === 'DKM';

    // Op. Keuangan RT: bisa ajukan laporan RT
    // Ketua RT: bisa setujui/tolak laporan RT
    // Op. Keuangan RW & Pimpinan RW: read-only semua RT
    $canSubmitLaporan = $isOpKeuanganRT;
    $canApproveLaporan = $isKetuaRT;
    $canManageDonasi = $isOpKeuanganRW || $isPimpinanRW;
@endphp

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Laporan Keuangan</h2>
        <p class="text-muted mb-0">Rekapitulasi keuangan, status pembayaran, dan distribusi donasi warga.</p>
    </div>
    <div class="d-flex gap-2">
        @if($canManageDonasi)
            <button type="button" class="btn btn-outline-success px-3 rounded-3 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#modalBroadcastNotifikasi">
                <i class="bi bi-megaphone me-1"></i> Broadcast Tagihan
            </button>
            <button type="button" class="btn btn-success px-3 rounded-3 fw-semibold"
                    onclick="showSuccessDialog('Fitur Donasi', 'Halaman distribusi donasi akan segera tersedia.')">
                <i class="bi bi-heart me-1"></i> Kelola Donasi
            </button>
        @endif
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-arrow-down-circle text-success fs-5"></i>
                </div>
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Total Pemasukan</span>
            </div>
            <h3 class="fw-bold text-success mb-0">Rp {{ number_format($ringkasan['total_pemasukan'], 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-danger bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-arrow-up-circle text-danger fs-5"></i>
                </div>
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Total Pengeluaran</span>
            </div>
            <h3 class="fw-bold text-danger mb-0">Rp {{ number_format($ringkasan['total_pengeluaran'], 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="bg-primary bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                </div>
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Saldo Bersih</span>
            </div>
            <h3 class="fw-bold text-dark mb-0">Rp {{ number_format($ringkasan['saldo_bersih'], 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

{{-- Chart: KK Sudah/Belum Bayar per RT --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">
                    STATUS PEMBAYARAN IURAN PER RT
                </span>
                <div class="d-flex align-items-center gap-3" style="font-size: 0.75rem;">
                    <span class="d-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #198754;"></span>
                        Sudah Bayar
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #dc3545;"></span>
                        Belum Bayar
                    </span>
                </div>
            </div>
            <div class="chart-container" style="position: relative; height: 250px;">
                <canvas id="kkPembayaranChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Donasi Terbaru --}}
    <div class="col-lg-4">
        <div class="card card-custom p-4 shadow-sm border-0 h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">Donasi Terbaru</span>
                @if($canManageDonasi)
                    <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
                @endif
            </div>

            <div class="d-flex flex-column gap-3 mb-auto">
                @foreach($donasiTerbaru as $donasi)
                    <div class="bg-light rounded-3 p-3 border border-light-subtle">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold small mb-0">{{ $donasi['judul'] }}</h6>
                            @php
                                $donasiStatusClass = match($donasi['status']) {
                                    'Disalurkan' => 'bg-success bg-opacity-10 text-success',
                                    'Selesai' => 'bg-primary bg-opacity-10 text-primary',
                                    'Direncanakan' => 'bg-warning bg-opacity-10 text-warning',
                                    default => 'bg-secondary bg-opacity-10 text-secondary',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $donasiStatusClass }} px-2 py-1" style="font-size: 0.65rem;">
                                {{ $donasi['status'] }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 0.7rem;">{{ $donasi['tanggal'] }}</span>
                            <span class="fw-bold small text-success">Rp {{ number_format($donasi['jumlah'], 0, ',', '.') }}</span>
                        </div>
                        <span class="text-muted d-block mt-1" style="font-size: 0.65rem;">
                            <i class="bi bi-people me-1"></i>{{ $donasi['jenis_penerima'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Laporan per RT Table --}}
<div class="card card-custom shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-center p-4 pb-3">
        <span class="text-muted fw-bold text-uppercase small" style="letter-spacing: 0.5px;">
            Laporan Rekapitulasi per RT
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted small text-uppercase" style="letter-spacing: 0.5px;">
                    <th class="py-3 px-4 fw-bold border-0">RT</th>
                    <th class="py-3 px-4 fw-bold border-0">Periode</th>
                    <th class="py-3 px-4 fw-bold border-0 text-end">Pemasukan</th>
                    <th class="py-3 px-4 fw-bold border-0 text-end">Pengeluaran</th>
                    <th class="py-3 px-4 fw-bold border-0 text-end">Saldo</th>
                    <th class="py-3 px-4 fw-bold border-0 text-center">Status</th>
                    <th class="py-3 px-4 fw-bold border-0 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanRt as $lap)
                    <tr class="border-bottom">
                        <td class="py-3 px-4 fw-bold small">{{ $lap['rt'] }}</td>
                        <td class="py-3 px-4 text-muted small">{{ $lap['periode'] }}</td>
                        <td class="py-3 px-4 text-end text-success small fw-semibold">
                            Rp {{ number_format($lap['pemasukan'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-end text-danger small fw-semibold">
                            Rp {{ number_format($lap['pengeluaran'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-end fw-bold small">
                            Rp {{ number_format($lap['saldo'], 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $lapStatusClass = match($lap['status']) {
                                    'Draft' => 'bg-warning bg-opacity-10 text-warning',
                                    'Diajukan' => 'bg-info bg-opacity-10 text-info',
                                    'Disetujui' => 'bg-success bg-opacity-10 text-success',
                                    'Ditolak' => 'bg-danger bg-opacity-10 text-danger',
                                    default => 'bg-secondary bg-opacity-10 text-secondary',
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $lapStatusClass }} px-2 py-1 fw-semibold" style="font-size: 0.7rem;">
                                {{ $lap['status'] }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            {{-- Op. Keuangan RT: ajukan laporan Draft --}}
                            @if($canSubmitLaporan && $lap['status'] === 'Draft')
                                <button type="button" class="btn btn-success btn-sm rounded-3 px-3"
                                        onclick="showSuccessDialog('Pengajuan Berhasil Dikirim', 'Laporan {{ $lap['rt'] }} periode {{ $lap['periode'] }} telah diajukan ke Ketua RT untuk disetujui.')">
                                    <i class="bi bi-send me-1"></i> Ajukan
                                </button>
                            @elseif($canSubmitLaporan && $lap['status'] === 'Ditolak')
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-3 px-3"
                                        onclick="showSuccessDialog('Pengajuan Ulang Berhasil', 'Laporan {{ $lap['rt'] }} telah diajukan ulang.')">
                                    <i class="bi bi-arrow-repeat me-1"></i> Ajukan Ulang
                                </button>

                            {{-- Ketua RT: setujui/tolak laporan Diajukan --}}
                            @elseif($canApproveLaporan && $lap['status'] === 'Diajukan')
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-2"
                                            onclick="showSuccessDialog('Laporan Ditolak', 'Laporan {{ $lap['rt'] }} telah ditolak. Operator akan menerima notifikasi.')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-sm rounded-3 px-2"
                                            onclick="showSuccessDialog('Laporan Disetujui', 'Laporan {{ $lap['rt'] }} periode {{ $lap['periode'] }} telah disetujui.')">
                                        <i class="bi bi-check-circle me-1"></i> Setujui
                                    </button>
                                </div>

                            {{-- Semua role: lihat detail --}}
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                    Lihat Detail
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Include Modals --}}
@if($canManageDonasi)
    @include('components.modals.broadcast-notifikasi')
@endif
@include('components.modals.success-dialog')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart: KK Pembayaran per RT
    const ctx = document.getElementById('kkPembayaranChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($kkPembayaran['labels']) !!},
                datasets: [
                    {
                        label: 'Sudah Bayar (%)',
                        data: {!! json_encode($kkPembayaran['sudah_bayar']) !!},
                        backgroundColor: '#198754',
                        borderRadius: 4,
                        barPercentage: 0.5,
                        categoryPercentage: 0.6
                    },
                    {
                        label: 'Belum Bayar (%)',
                        data: {!! json_encode($kkPembayaran['belum_bayar']) !!},
                        backgroundColor: '#dc3545',
                        borderRadius: 4,
                        barPercentage: 0.5,
                        categoryPercentage: 0.6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' } }
                    },
                    y: {
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function(value) { return value + '%'; },
                            font: { size: 10 }
                        },
                        max: 100
                    }
                }
            }
        });
    }
});
</script>
@endpush
