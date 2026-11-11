@extends('layouts.global')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1 fs-4">Transparansi Keuangan</h2>
        <p class="text-muted mb-0">Laporan keuangan bulanan RT, RW, dan DKM yang telah dipublikasikan.</p>
    </div>
</div>


@if($bolehStatistik ?? false)
<ul class="nav nav-pills gap-1 mb-4" data-testid="tab-laporan-keuangan">
    <li class="nav-item"><a class="nav-link {{ ($tab ?? 'daftar') === 'daftar' ? 'active' : '' }}" href="{{ route('laporan-keuangan.publik') }}"><i class="bi bi-table me-1"></i>Daftar Laporan</a></li>
    <li class="nav-item"><a class="nav-link {{ ($tab ?? 'daftar') === 'statistik' ? 'active' : '' }}" href="{{ route('laporan-keuangan.publik', ['tab' => 'statistik']) }}" data-testid="tab-statistik"><i class="bi bi-graph-up-arrow me-1"></i>Statistik</a></li>
</ul>
@endif

@if(($tab ?? 'daftar') === 'statistik')
    @include('laporanKeuangan._statistik')
@else

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3 p-md-4">
        <form action="{{ route('laporan-keuangan.publik') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-semibold">Tahun</label>
                <select name="tahun" class="form-select bg-light border-0">
                    <option value="">Semua Tahun</option>
                    @foreach($filterOptions['tahun'] as $t)
                        <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted small fw-semibold">Bulan</label>
                <select name="bulan" class="form-select bg-light border-0">
                    <option value="">Semua Bulan</option>
                    @foreach($filterOptions['bulan'] as $val => $name)
                        <option value="{{ $val }}" {{ request('bulan') == $val ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small fw-semibold">Unit</label>
                <select name="unit" class="form-select bg-light border-0">
                    <option value="all">Semua Unit (RT, RW, DKM)</option>
                    <option value="RT" {{ request('unit') == 'RT' ? 'selected' : '' }}>Hanya RT</option>
                    <option value="RW" {{ request('unit') == 'RW' ? 'selected' : '' }}>Hanya RW</option>
                    <option value="DKM" {{ request('unit') == 'DKM' ? 'selected' : '' }}>Hanya DKM</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

@php
    $namaBulan = \App\Services\IuranService::BULAN;
    $bolehUnduh = Auth::user()?->activeRole() !== 'Warga'; // Warga hanya melihat angka ringkasan
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
@endphp

@if($laporanList->isEmpty())
    <div class="text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class="bi bi-journal-x text-muted fs-1"></i>
        </div>
        <h5 class="fw-bold text-dark">Belum ada laporan yang dipublikasikan</h5>
        <p class="text-muted">Laporan keuangan akan muncul di sini setelah dipublikasikan oleh Bendahara.</p>
    </div>
@else
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">

        {{-- Tabel (tablet dan desktop) --}}
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover align-middle mb-0" data-testid="tabel-laporan-keuangan">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Periode</th>
                        <th>Unit</th>
                        <th class="text-end">Pemasukan</th>
                        <th class="text-end">Pengeluaran</th>
                        <th class="text-end">Saldo Akhir</th>
                        @if($bolehUnduh)<th class="text-end pe-4">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                @foreach($laporanList as $lap)
                    <tr>
                        <td class="ps-4 fw-semibold text-dark text-nowrap">{{ $namaBulan[$lap->periode_bulan] ?? $lap->periode_bulan }} {{ $lap->periode_tahun }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">{{ $lap->unit }}{{ $lap->unit === 'RT' && $lap->rt ? ' ' . ($lap->rt->kode_rt ?? '') : '' }}</span></td>
                        <td class="text-end text-success text-nowrap">{{ $rp($lap->total_pemasukan) }}</td>
                        <td class="text-end text-danger text-nowrap">{{ $rp($lap->total_pengeluaran) }}</td>
                        <td class="text-end fw-bold text-nowrap {{ $lap->saldo_akhir < 0 ? 'text-danger' : 'text-primary' }}">{{ $rp($lap->saldo_akhir) }}</td>
                        @if($bolehUnduh)
                        <td class="text-end pe-4">
                            <a href="{{ route('laporan-keuangan.pdf', $lap->id) }}" class="btn btn-sm btn-light text-danger" style="width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                               data-bs-toggle="tooltip" data-bs-title="Unduh PDF" aria-label="Unduh PDF" target="_blank"><i class="bi bi-file-pdf"></i></a>
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- Daftar (mobile) --}}
        <div class="d-md-none" data-testid="daftar-laporan-keuangan">
            @foreach($laporanList as $lap)
                <div class="p-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="fw-bold text-dark">{{ $namaBulan[$lap->periode_bulan] ?? $lap->periode_bulan }} {{ $lap->periode_tahun }}</div>
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $lap->unit }}{{ $lap->unit === 'RT' && $lap->rt ? ' ' . ($lap->rt->kode_rt ?? '') : '' }}</span>
                        </div>
                        @if($bolehUnduh)
                            <a href="{{ route('laporan-keuangan.pdf', $lap->id) }}" class="btn btn-sm btn-light text-danger flex-shrink-0" style="width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center;"
                               aria-label="Unduh PDF" target="_blank"><i class="bi bi-file-pdf"></i></a>
                        @endif
                    </div>
                    <div class="small mt-2">
                        <div class="d-flex justify-content-between"><span class="text-muted">Pemasukan</span><span class="text-success">{{ $rp($lap->total_pemasukan) }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Pengeluaran</span><span class="text-danger">{{ $rp($lap->total_pengeluaran) }}</span></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Saldo akhir</span><span class="fw-bold {{ $lap->saldo_akhir < 0 ? 'text-danger' : 'text-primary' }}">{{ $rp($lap->saldo_akhir) }}</span></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($laporanList->hasPages())
        <div class="card-footer bg-white border-0 py-3">{{ $laporanList->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endif
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (!window.bootstrap) return;
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover focus' }));
});
</script>
@endpush