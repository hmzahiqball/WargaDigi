@extends('layouts.warga')

@section('title', 'Dashboard Warga')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-success mb-1">Selamat Datang, {{ Auth::user()->name ?? 'Bapak/Ibu' }}</h2>
        <p class="text-muted mb-0">Kepala Keluarga - RW 21 Desa Tanimulya</p>
    </div>
    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
        <i class="bi bi-check-circle me-1"></i> STATUS: AKTIF
    </span>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.surat.index') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-file-earmark-plus mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Ajukan Surat<br>Keterangan</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.surat.index') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-search mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Cek Status Surat<br>Keterangan</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('transparansi') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-cash-coin mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Laporan Kas &<br>Iuran Warga</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.umkm.produk.index') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-shop mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Kelola Produk<br>Saya (UMKM)</span>
            </div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Bagian Kiri -->
    <div class="col-lg-8">
        
        <!-- Data Anggota Keluarga -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Data Anggota Keluarga</h5>
            <a href="{{ route('warga.keluarga.index') }}" class="text-success text-decoration-none small fw-semibold">+ Kelola Keluarga</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-custom p-3 h-100 d-flex flex-row align-items-center gap-3 shadow-sm border-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Kepala Keluarga') }}&background=random" class="rounded-circle" width="48" height="48">
                    <div>
                        <h6 class="mb-0 fw-bold">{{ Auth::user()->name ?? 'Kepala Keluarga' }}</h6>
                        <small class="text-muted d-block">Kepala Keluarga</small>
                        <small class="text-muted" style="font-size: 0.75rem;">Status: Terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom p-3 h-100 d-flex flex-row align-items-center gap-3 bg-light shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people text-secondary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Daftar Tanggungan</h6>
                        <small class="text-muted d-block">Lihat anggota keluarga lainnya</small>
                        <a href="{{ route('warga.keluarga.index') }}" class="text-success text-decoration-none" style="font-size: 0.75rem;">Kelola Data &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Transparansi Keuangan -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Transparansi Keuangan RW 21</h5>
            <a href="{{ route('transparansi') }}" class="text-success text-decoration-none small fw-semibold">Lihat Detail</a>
        </div>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">DANA KEMATIAN</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Aktif / Transparan</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">KAS RT & RW</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Terbuka</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">LAPORAN</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Unduh PDF</h4>
                </div>
            </div>
        </div>

        <!-- Update Terkini -->
        <div class="d-flex align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0">Update Terkini</h5>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('berita') }}" class="badge bg-success rounded-pill px-3 py-2 text-decoration-none text-white">Semua Berita</a>
            </div>
        </div>
        
        <div class="card card-custom mb-3 overflow-hidden shadow-sm border-0">
            <div class="row g-0">
                <div class="col-md-3 bg-light d-flex align-items-center justify-content-center">
                    <i class="bi bi-image text-muted fs-1"></i>
                </div>
                <div class="col-md-9">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-light text-secondary border">GOTONG ROYONG</span>
                            <small class="text-muted">RW 21 Tanimulya</small>
                        </div>
                        <h6 class="fw-bold">Pembersihan Saluran Air Lingkungan RW 21</h6>
                        <p class="card-text text-muted small mb-0">Kegiatan gotong royong warga bersama pengurus RT dan RW untuk mengantisipasi musim hujan...</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bagian Kanan -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4 shadow-sm border-0">
            <h6 class="fw-bold mb-4"><i class="bi bi-megaphone text-success me-2"></i> Agenda Terkini</h6>
            
            <div class="d-flex gap-3 mb-4 align-items-center">
                <div class="bg-light border rounded text-center p-2" style="min-width: 60px;">
                    <span class="fs-5 fw-bold text-success"><i class="bi bi-calendar-check"></i></span>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Rembug Warga & Pengurus</h6>
                    <small class="text-muted"><i class="bi bi-geo-alt"></i> Balai Pertemuan RW 21</small>
                </div>
            </div>

            <a href="{{ route('informasi') }}" class="btn btn-outline-success w-100">Lihat Semua Agenda</a>
        </div>
    </div>
</div>

<!-- Pojok UMKM -->
<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <div>
        <h5 class="fw-bold mb-1">Pojok UMKM</h5>
        <p class="text-muted small mb-0">Dukung potensi usaha mandiri warga RW 21 Tanimulya</p>
    </div>
    <a href="{{ route('warga.umkm.produk.index') }}" class="text-success text-decoration-none small fw-semibold">Lihat Semua <i class="bi bi-arrow-right"></i></a>
</div>

<div class="row g-4">
    <div class="col-sm-6 col-md-3">
        <div class="card card-custom p-4 text-center h-100 d-flex flex-column justify-content-center align-items-center shadow-sm border-dashed" style="border: 2px dashed #ced4da;">
            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                <i class="bi bi-shop fs-4"></i>
            </div>
            <h6 class="fw-bold">Daftarkan UMKM Anda</h6>
            <p class="small text-muted mb-4">Promosikan produk kuliner, kriya, atau jasa Anda ke seluruh warga.</p>
            <a href="{{ route('warga.umkm.produk.index') }}" class="btn btn-success px-4 py-2 small fw-semibold rounded-pill text-decoration-none text-white">Daftar Sekarang</a>
        </div>
    </div>
</div>
@endsection