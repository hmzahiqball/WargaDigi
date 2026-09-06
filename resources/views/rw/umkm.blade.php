@extends('layouts.global')

@section('title', 'Pusat Manajemen UMKM - RW')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section & Navigation Tabs -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1 fs-2">Pusat Manajemen UMKM</h2>
            <p class="text-muted mb-0 small fs-6">Tinjau dan setujui kiriman komunitas.</p>
        </div>

        <!-- Navigation Tabs Top Right -->
        <div class="d-flex align-items-center gap-4 border-bottom pb-2">
            <a href="#" class="text-decoration-none fw-bold text-success pb-2 border-bottom border-3 border-success px-1" style="color: #2E7D32 !important; border-color: #2E7D32 !important;">
                Persetujuan UMKM
            </a>
            <a href="#" class="text-decoration-none fw-semibold text-muted pb-2 px-1 hover-success">
                Review Content
            </a>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="row g-4">
        <!-- Left Column: Pending UMKM Cards -->
        <div class="col-lg-8">
            @if(isset($pendingUsaha) && count($pendingUsaha) > 0)
                @foreach($pendingUsaha as $item)
                    <div class="card border shadow-sm rounded-4 overflow-hidden mb-4 p-3 p-md-4 bg-white">
                        <div class="row g-3 g-md-4 align-items-center">
                            <!-- Image -->
                            <div class="col-md-5 col-lg-4">
                                <img src="{{ $item->foto_usaha ? asset('storage/' . $item->foto_usaha) : 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop' }}" class="img-fluid rounded-3 object-fit-cover w-100" style="height: 180px;" alt="{{ $item->nama_usaha }}">
                            </div>
                            <!-- Details -->
                            <div class="col-md-7 col-lg-8 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-light text-secondary border px-3 py-1 rounded-pill small fw-semibold text-uppercase" style="letter-spacing: 0.5px;">
                                            {{ $item->kategori_umkm->nama_kategori ?? $item->kategori ?? 'UMKM' }}
                                        </span>
                                        <span class="badge rounded-pill px-3 py-1 small fw-semibold" style="background-color: #fde8e8; color: #e02424;">
                                            <i class="bi bi-journal-text me-1"></i> Pending Review
                                        </span>
                                    </div>
                                    <h4 class="fw-bold text-dark mb-2">{{ $item->nama_usaha }}</h4>
                                    <p class="text-muted small mb-3" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $item->deskripsi ?? 'Pendaftaran usaha baru dari warga RW.' }}
                                    </p>
                                </div>

                                <div>
                                    <hr class="my-3 text-muted opacity-25">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                                        <div class="d-flex align-items-center gap-2 text-muted small">
                                            <span><i class="bi bi-person me-1"></i> {{ $item->pemilik->penduduk->nama_lengkap ?? $item->pemilik->username ?? 'Warga' }} ({{ $item->alamat_usaha ?? 'Alamat Usaha' }})</span>
                                            <span class="opacity-50">|</span>
                                            <span><i class="bi bi-telephone me-1"></i> {{ $item->no_wa ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('rw.umkm.reject', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary rounded-3 px-4 py-2 small fw-semibold text-dark">Reject</button>
                                        </form>
                                        <form action="{{ route('rw.umkm.approve', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn text-white rounded-3 px-4 py-2 small fw-semibold shadow-sm" style="background-color: #5b9b76; border-color: #5b9b76;">Approve Profile</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-3 mb-3 mx-auto">
                        <i class="bi bi-check2-circle text-success fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Semua UMKM Telah Diverifikasi</h5>
                    <p class="text-muted small mb-0">Saat ini tidak ada pendaftaran UMKM baru yang menunggu peninjauan RW.</p>
                </div>
            @endif
        </div>

        <!-- Right Column: Info Box & Workflow Guidelines -->
        <div class="col-lg-4">
            <div class="card border shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-info-circle fs-4 text-success" style="color: #2E7D32 !important;"></i>
                    <h5 class="fw-bold text-dark mb-0">Proses Persetujuan UMKM</h5>
                </div>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    Tinjau pendaftaran bisnis baru yang diajukan oleh warga. Pastikan nama dan kategori bisnis mematuhi pedoman komunitas.
                </p>

                <!-- Inset Box: Post-Approval Workflow -->
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 class="fw-bold text-dark small mb-2">Post-Approval Workflow</h6>
                    <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">
                        Setelah disetujui, menu <strong>Kelola Produk</strong> otomatis diaktifkan untuk akun penghuni, yang memungkinkan mereka mengelola inventaris dan daftar mereka.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
