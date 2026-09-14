@extends('layouts.global')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Transparansi Keuangan</h2>
        <p class="text-muted mb-0">Laporan keuangan bulanan RT, RW, dan DKM yang telah dipublikasikan.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
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

<div class="row g-4">
    @forelse($laporanList as $lap)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
            <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-start">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 mb-2">{{ $lap->unit }}{{ $lap->unit === 'RT' && $lap->rt ? ' ' . ($lap->rt->kode_rt ?? '') : '' }}</span>
                    <h5 class="fw-bold mb-0 text-dark">{{ date('F', mktime(0, 0, 0, $lap->periode_bulan, 10)) }} {{ $lap->periode_tahun }}</h5>
                </div>
                <a href="{{ route('laporan-keuangan.pdf', $lap->id) }}" class="btn btn-light text-danger rounded-circle p-2 shadow-sm" title="Download PDF" target="_blank">
                    <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                <p class="text-muted small mb-3">{{ $lap->judul }}</p>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Pemasukan</span>
                    <span class="text-success fw-medium">+ {{ $lap->formatted_total_pemasukan }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Pengeluaran</span>
                    <span class="text-danger fw-medium">- {{ $lap->formatted_total_pengeluaran }}</span>
                </div>
                <hr class="my-2 text-muted">
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="fw-semibold text-dark">Saldo Akhir</span>
                    <span class="fs-5 fw-bold text-primary">{{ $lap->formatted_saldo_akhir }}</span>
                </div>
            </div>
            <div class="card-footer bg-light border-top-0 px-4 py-3 rounded-bottom-4">
                <small class="text-muted"><i class="bi bi-person me-1"></i> {{ $lap->pembuat->name ?? 'Admin' }}</small>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-journal-x text-muted fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark">Belum ada laporan yang dipublikasikan</h5>
            <p class="text-muted">Laporan keuangan akan muncul di sini setelah dipublikasikan oleh Bendahara.</p>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $laporanList->withQueryString()->links('pagination::bootstrap-5') }}
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
