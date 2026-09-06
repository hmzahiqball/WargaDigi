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


@if(isset($pendingUmkmCount) && $pendingUmkmCount > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 44px; height: 44px;">
                <i class="bi bi-shop text-warning fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Pemberitahuan UMKM Baru Menunggu Verifikasi</h6>
                <p class="text-muted small mb-0">Terdapat <strong>{{ $pendingUmkmCount }}</strong> usaha warga baru yang telah didaftarkan dan menunggu peninjauan Anda.</p>
            </div>
        </div>
        <a href="{{ route('rw.umkm.index') }}" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
            <i class="bi bi-clipboard-check me-1"></i> Tinjau UMKM Sekarang
        </a>
    </div>
@endif


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <small class="text-secondary fw-bold">JUMLAH PENDUDUK</small>
                <i class="bi bi-people fs-4 text-info"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $stats['penduduk']['total'] }}</h3>
            <small class="text-success fw-semibold"><i class="bi bi-arrow-up-short"></i> {{ $stats['penduduk']['change'] }}</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <small class="text-danger fw-bold">DOKUMEN PENDING</small>
                <i class="bi bi-file-earmark-medical fs-4 text-danger"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $stats['dokumen_pending']['total'] }}</h3>
            <small class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> {{ $stats['dokumen_pending']['need_review'] }} perlu review</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('rw.umkm.index') }}" class="text-decoration-none">
            <div class="card card-custom p-3 h-100 shadow-sm border-0 position-relative">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <small class="text-muted fw-bold">UMKM BARU</small>
                    <i class="bi bi-shop fs-4 text-success"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">{{ $stats['umkm_baru']['total'] }}</h3>
                <small class="{{ ($stats['umkm_baru']['total'] > 0) ? 'text-warning fw-bold' : 'text-muted' }}">
                    @if($stats['umkm_baru']['total'] > 0)
                        <i class="bi bi-exclamation-circle-fill me-1"></i>
                    @endif
                    {{ $stats['umkm_baru']['status'] }}
                </small>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <div class="card card-custom p-3 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <small class="text-muted fw-bold">KONTEN DITINJAU</small>
                <i class="bi bi-pencil-square fs-4 text-success"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $stats['konten_ditinjau']['total'] }}</h3>
            <small class="text-muted">{{ $stats['konten_ditinjau']['status'] }}</small>
        </div>
    </div>
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
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="bi {{ $activity['icon'] }}"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="fw-bold text-dark mb-1 timeline-title">
                                {{ $activity['title'] }}
                            </div>
                            <div class="text-muted text-xs mb-2">{{ $activity['time'] }}</div>
                            @if($activity['badge'])
                                <span class="badge uppercase-badge {{ $activity['badge_class'] }}">
                                    {{ $activity['badge'] }}
                                </span>
                            @endif
                            @if($activity['quote'])
                                <div class="quote-box p-3 mt-2 rounded">
                                    &ldquo;{{ $activity['quote'] }}&rdquo;
                                </div>
                            @endif
                        </div>
                    </div>
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
                    <div class="col-6">
                        <a href="{{ $action['link'] }}" class="text-decoration-none text-dark">
                            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                                <i class="bi {{ $action['icon'] }} mb-2 fs-3 text-success"></i>
                                <span class="fw-semibold small">{{ $action['title'] }}</span>
                            </div>
                        </a>
                    </div>
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
                    <div class="col-12 col-sm-6">
                        <div class="card card-custom p-3 shadow-sm border-0 h-100">
                            <div class="bg-light rounded p-3 text-center mb-3">
                                <i class="bi {{ $doc['icon'] }} text-secondary fs-1"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold doc-title text-truncate mb-0">{{ $doc['title'] }}</h6>
                                <button class="btn btn-link text-muted p-0 border-0 ms-1" type="button">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                            </div>
                            <p class="text-muted small doc-desc mb-3">{{ $doc['desc'] }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <span class="badge uppercase-badge {{ $doc['status_class'] }}">{{ $doc['status'] }}</span>
                                <span class="text-muted text-xs">{{ $doc['date'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
