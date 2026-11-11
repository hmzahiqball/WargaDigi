@extends('layouts.global')

@section('title', 'Transparansi Keuangan')

@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $unitLabel = ['RT' => ($kodeRt ? 'RT ' . $kodeRt : 'RT Saya'), 'RW' => 'RW', 'DKM' => 'DKM'];
    $adaData = ($data['jumlah_laporan'] ?? 0) > 0;
@endphp

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">Transparansi Keuangan</h2>
    <p class="text-muted mb-0">Ringkasan uang masuk dan uang keluar yang sudah dipublikasikan pengurus.</p>
</div>

{{-- Pemilih unit dan tahun --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 d-flex flex-column flex-sm-row gap-3 justify-content-between">
        <ul class="nav nav-pills gap-1 flex-nowrap flex-grow-1" data-testid="pemilih-unit">
            @foreach($unitLabel as $kode => $label)
                <li class="nav-item flex-fill text-center">
                    <a class="nav-link text-nowrap px-2 {{ $unit === $kode ? 'active' : '' }}"
                       href="{{ route('laporan-keuangan.publik', ['unit' => $kode]) }}"
                       @if(!($tersedia[$kode] ?? false)) title="Belum ada laporan terbit" @endif>
                        {{ $label }}
                        @if(!($tersedia[$kode] ?? false))<span class="d-block opacity-75" style="font-size:.65rem;line-height:1">belum ada</span>@endif
                    </a>
                </li>
            @endforeach
        </ul>

        @if(!empty($daftarTahun))
        <form method="GET" action="{{ route('laporan-keuangan.publik') }}" class="d-flex align-items-center gap-2 flex-shrink-0">
            <input type="hidden" name="unit" value="{{ $unit }}">
            <label for="tahun" class="small text-muted mb-0">Tahun</label>
            <select id="tahun" name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($daftarTahun as $t)
                    <option value="{{ $t }}" @selected($t == $tahun)>{{ $t }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>
</div>

@if(!$adaData)
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-wallet2 fs-1 text-muted"></i>
            <h5 class="fw-bold mt-3">Belum ada laporan terbit</h5>
            <p class="text-muted mb-0">Laporan keuangan tahun {{ $tahun }} untuk unit ini belum dipublikasikan.</p>
        </div>
    </div>
@else
    {{-- Kartu ringkasan --}}
    <div class="row g-2 g-md-3 mb-3 mb-md-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="small text-muted">Saldo Kas Terkini</div>
                    <div class="fs-3 fw-bold text-break {{ ($data['saldo_terkini'] ?? 0) < 0 ? 'text-danger' : 'text-dark' }}">{{ $rp($data['saldo_terkini'] ?? 0) }}</div>
                    @if(!empty($data['periode_terkini']))
                        <div class="small text-muted">per {{ $data['periode_terkini'] }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="small text-muted">Pemasukan {{ $tahun }}</div>
                    <div class="fs-6 fw-bold text-success text-nowrap">{{ $rp($data['total_pemasukan']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="small text-muted">Pengeluaran {{ $tahun }}</div>
                    <div class="fs-6 fw-bold text-danger text-nowrap">{{ $rp($data['total_pengeluaran']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status iuran saya --}}
    @if($iuranSaya)
        @php
            $lunas = (int) ($iuranSaya['lunas'] ?? 0);
            $persen = min(100, (int) round($lunas / 12 * 100));
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4" data-testid="status-iuran-saya">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="fw-semibold">Iuran saya: {{ $iuranSaya['nama'] ?? 'Iuran Bulanan' }}</div>
                    <span class="small text-muted">{{ $lunas }} dari 12 bulan lunas</span>
                </div>
                <div class="progress" style="height: 8px;" role="progressbar" aria-valuenow="{{ $lunas }}" aria-valuemin="0" aria-valuemax="12">
                    <div class="progress-bar bg-success" style="width: {{ $persen }}%"></div>
                </div>
                @if(!empty($iuranSaya['menunggu']))
                    <div class="small text-muted mt-2">{{ $iuranSaya['menunggu'] }} bulan menunggu verifikasi.</div>
                @endif
            </div>
        </div>
    @endif

    {{-- Grafik tren --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Tren Bulanan {{ $tahun }}</h6>
            <div style="position: relative; height: 260px;">
                <canvas id="chartTren" aria-label="Grafik pemasukan dan pengeluaran per bulan" role="img"></canvas>
            </div>
        </div>
    </div>

    {{-- Rekap kategori --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Uang masuk dari mana</h6>
                    @forelse($data['kategori_masuk'] as $nama => $jumlah)
                        <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                            <span class="text-break">{{ $nama }}</span>
                            <span class="fw-semibold text-success text-nowrap">{{ $rp($jumlah) }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Uang dipakai untuk apa</h6>
                    @forelse($data['kategori_keluar'] as $nama => $jumlah)
                        <div class="d-flex justify-content-between gap-3 py-2 border-bottom">
                            <span class="text-break">{{ $nama }}</span>
                            <span class="fw-semibold text-danger text-nowrap">{{ $rp($jumlah) }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar bulanan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Rincian per Bulan</h6>
            <div class="d-md-none">
                @foreach($data['per_bulan'] as $b)
                    <div class="py-2 border-bottom">
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">{{ $b['nama'] }}</span>
                            @if(!$b['ada'])<span class="small text-muted">belum terbit</span>@endif
                        </div>
                        @if($b['ada'])
                            <div class="d-flex justify-content-between small"><span class="text-muted">Pemasukan</span><span class="text-success">{{ $rp($b['pemasukan']) }}</span></div>
                            <div class="d-flex justify-content-between small"><span class="text-muted">Pengeluaran</span><span class="text-danger">{{ $rp($b['pengeluaran']) }}</span></div>
                            <div class="d-flex justify-content-between small"><span class="text-muted">Saldo akhir</span><span class="fw-semibold {{ ($b['saldo_akhir'] ?? 0) < 0 ? 'text-danger' : '' }}">{{ $rp($b['saldo_akhir']) }}</span></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="table-responsive d-none d-md-block">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="small text-muted">
                            <th>Bulan</th>
                            <th class="text-end">Pemasukan</th>
                            <th class="text-end">Pengeluaran</th>
                            <th class="text-end">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['per_bulan'] as $b)
                            <tr>
                                <td>{{ $b['nama'] }}</td>
                                @if($b['ada'])
                                    <td class="text-end text-success">{{ $rp($b['pemasukan']) }}</td>
                                    <td class="text-end text-danger">{{ $rp($b['pengeluaran']) }}</td>
                                    <td class="text-end fw-semibold {{ ($b['saldo_akhir'] ?? 0) < 0 ? 'text-danger' : '' }}">{{ $rp($b['saldo_akhir']) }}</td>
                                @else
                                    <td colspan="3" class="text-end text-muted small">belum terbit</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection

@if($adaData)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('chartTren');
    if (!el || !window.Chart) return;
    const bulan = @json(array_map(fn ($b) => $b['nama'], array_values($data['per_bulan'])));
    const masuk = @json(array_map(fn ($b) => $b['ada'] ? (int) $b['pemasukan'] : null, array_values($data['per_bulan'])));
    const keluar = @json(array_map(fn ($b) => $b['ada'] ? (int) $b['pengeluaran'] : null, array_values($data['per_bulan'])));
    new Chart(el, {
        type: 'bar',
        data: {
            labels: bulan.map(n => n.substring(0, 3)),
            datasets: [
                { label: 'Pemasukan', data: masuk, backgroundColor: '#198754' },
                { label: 'Pengeluaran', data: keluar, backgroundColor: '#dc3545' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') } } },
            plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': Rp ' + Number(c.parsed.y).toLocaleString('id-ID') } } }
        }
    });
});
</script>
@endpush
@endif

