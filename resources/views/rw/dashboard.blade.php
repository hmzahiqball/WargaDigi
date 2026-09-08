@extends('layouts.global')

@section('title', 'Dashboard RW')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-success mb-1">Ringkasan</h2>
        <p class="text-muted mb-0">Ringkasan Data Aplikasi - RW 12 Tanimulya</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 fw-semibold">
            <i class="bi bi-download me-1"></i> Export Data
        </button>
        <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
            <i class="bi bi-check-circle me-1"></i> STATUS: AKTIF
        </span>
    </div>
</div>

@if($pendingUmkmCount > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #ffffff;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 44px; height: 44px;">
                <i class="bi bi-shop text-warning fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Pemberitahuan UMKM Baru Menunggu Verifikasi</h6>
                <p class="text-muted small mb-0">Terdapat <strong>{{ $pendingUmkmCount }}</strong> usaha warga baru yang telah didaftarkan dan menunggu peninjauan Anda.</p>
            </div>
        </div>
        <a href="{{ route('rw.umkm.index') }}" class="btn btn-warning btn-sm text-light fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
            <i class="bi bi-clipboard-check me-1"></i> Tinjau UMKM Sekarang
        </a>
    </div>
@endif

<div class="row g-3 mb-4">
    @include('rw.components.cardStatDashboard', [
        'title' => 'JUMLAH PENDUDUK',
        'titleClass' => 'text-secondary',
        'icon' => 'bi-people',
        'iconColor' => 'text-info',
        'value' => $stats['penduduk']['total'],
        'subtitle' => $stats['penduduk']['change'],
        'subtitleClass' => 'text-success fw-semibold',
        'subtitleIcon' => 'bi-arrow-up-short',
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'DOKUMEN PENDING',
        'titleClass' => 'text-danger',
        'icon' => 'bi-file-earmark-medical',
        'iconColor' => 'text-danger',
        'value' => $stats['dokumen_pending']['total'],
        'subtitle' => $stats['dokumen_pending']['need_review'] . ' perlu review',
        'subtitleClass' => 'text-danger fw-semibold',
        'subtitleIcon' => 'bi-exclamation-triangle',
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'UMKM BARU',
        'icon' => 'bi-shop',
        'iconColor' => 'text-success',
        'value' => $stats['umkm_baru']['total'],
        'subtitle' => $stats['umkm_baru']['status'],
        'subtitleClass' => ($stats['umkm_baru']['total'] > 0) ? 'text-warning fw-bold' : 'text-muted',
        'subtitleIcon' => ($stats['umkm_baru']['total'] > 0) ? 'bi-exclamation-circle-fill' : null,
        'link' => route('rw.umkm.index'),
    ])

    @include('rw.components.cardStatDashboard', [
        'title' => 'KONTEN DITINJAU',
        'icon' => 'bi-pencil-square',
        'iconColor' => 'text-success',
        'value' => $stats['konten_ditinjau']['total'],
        'subtitle' => $stats['konten_ditinjau']['status'],
        'subtitleClass' => 'text-muted',
    ])
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">AKTIVITAS RT TERAKHIR</h5>
                <a href="#" class="text-success text-decoration-none small fw-semibold">Lihat Semua</a>
            </div>

            <div class="rw-timeline">
                @foreach($activities as $activity)
                    @include('rw.components.timelineActivity', ['activity' => $activity])
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-5 d-flex flex-column gap-4">
        <div class="card card-custom p-4 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Aksi Cepat</h5>
                <button class="btn btn-link text-muted p-0 border-0" type="button">
                    <i class="bi bi-three-dots"></i>
                </button>
            </div>

            <div class="row g-3">
                @foreach($quickActions as $action)
                    @include('rw.components.cardQuickAction', ['action' => $action])
                @endforeach
                <div class="col-6">
                    <a href="#" class="text-decoration-none text-dark">
                        <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0 border-dashed" style="border: 2px dashed #ced4da;">
                            <i class="bi bi-plus-lg mb-2 fs-3 text-muted"></i>
                            <span class="fw-semibold small text-muted">Customize</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div>
            <h5 class="fw-bold mb-3">Document Terakhir</h5>
            <div class="row g-3">
                @foreach($recentDocs as $doc)
                    @include('rw.components.cardRecentDoc', ['doc' => $doc])
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
