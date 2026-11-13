@extends('layouts.global')

@section('title', 'Persetujuan UMKM - RW')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-3">
        <h2 class="fw-bold text-dark mb-1 fs-2">Persetujuan UMKM</h2>
        <p class="text-muted mb-0 small fs-6">
            {{ $isPimpinan ? 'Pantau riwayat pengajuan UMKM warga RW.' : 'Tinjau pendaftaran usaha baru dan arsip pengajuan yang ditolak.' }}
        </p>
    </div>

    <div class="d-flex align-items-center gap-2 mb-4 flex-nowrap overflow-auto border-bottom" role="tablist">
        <a href="{{ route('rw.persetujuan-umkm', ['tab' => 'persetujuan']) }}" role="tab" aria-selected="{{ $tab === 'persetujuan' ? 'true' : 'false' }}"
           class="text-decoration-none fw-bold pb-2 px-3 border-bottom border-3 d-flex align-items-center gap-2 text-nowrap flex-shrink-0 {{ $tab === 'persetujuan' ? 'text-success border-success' : 'text-muted border-transparent hover-success' }}"
           style="{{ $tab === 'persetujuan' ? 'color: #2E7D32 !important; border-color: #2E7D32 !important;' : '' }}">
            <i class="bi bi-shield-check"></i>
            <span>{{ $isPimpinan ? 'Riwayat Persetujuan UMKM' : 'Persetujuan UMKM' }}</span>
            @if($pendingCount > 0)
                <span class="badge bg-danger rounded-pill px-2 py-1 small">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('rw.persetujuan-umkm', ['tab' => 'ditolak']) }}" role="tab" aria-selected="{{ $tab === 'ditolak' ? 'true' : 'false' }}"
           class="text-decoration-none fw-bold pb-2 px-3 border-bottom border-3 d-flex align-items-center gap-2 text-nowrap flex-shrink-0 {{ $tab === 'ditolak' ? 'text-danger border-danger' : 'text-muted border-transparent hover-danger' }}"
           style="{{ $tab === 'ditolak' ? 'color: #dc3545 !important; border-color: #dc3545 !important;' : '' }}">
            <i class="bi bi-x-circle"></i>
            <span>Pengajuan Ditolak</span>
            @if($rejectedUsaha->total() > 0)
                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 small">{{ $rejectedUsaha->total() }}</span>
            @endif
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @if($tab === 'persetujuan')
                @if($isPimpinan)
                    <div class="alert shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3" style="background-color: #ffffff;">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(34, 197, 94, 0.2); width: 40px; height: 40px;">
                            <i class="bi bi-info-circle text-success fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Mode Monitoring Ketua RW</h6>
                            <p class="text-muted small mb-0">Menampilkan seluruh riwayat pengajuan UMKM (Pending, Approved, dan Rejected). Klik kartu untuk melihat rincian pemohon.</p>
                        </div>
                    </div>
                @endif

                @include('rw.umkm.partials.daftar-pengajuan', [
                    'daftar' => $pengajuan,
                    'label' => 'pengajuan',
                    'kosongJudul' => $isPimpinan ? 'Belum Ada Pengajuan UMKM' : 'Semua UMKM Telah Diverifikasi',
                    'kosongPesan' => $isPimpinan ? 'Saat ini belum terdapat catatan riwayat pengajuan UMKM.' : 'Saat ini tidak ada pendaftaran UMKM baru yang menunggu peninjauan RW.',
                ])
            @else
                <div class="alert shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3" style="background-color: #fff5f5; border: 1px solid #fed7d7;">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(239, 68, 68, 0.2); width: 40px; height: 40px;">
                        <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Daftar Pengajuan UMKM yang Ditolak</h6>
                        <p class="text-muted small mb-0">Pemilik dapat memperbaiki data lalu mengajukan ulang; pengajuan ulang akan kembali ke tab Persetujuan UMKM.</p>
                    </div>
                </div>

                @include('rw.umkm.partials.daftar-pengajuan', [
                    'daftar' => $rejectedUsaha,
                    'label' => 'pengajuan ditolak',
                    'kosongJudul' => 'Tidak Ada Pengajuan Ditolak',
                    'kosongPesan' => 'Saat ini belum terdapat pengajuan UMKM yang berstatus ditolak oleh RW.',
                ])
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border shadow-sm rounded-4 p-4 bg-white mb-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-info-circle fs-4 text-success" style="color: #2E7D32 !important;"></i>
                    <h5 class="fw-bold text-dark mb-0">{{ $isPimpinan ? 'Monitoring Persetujuan' : 'Proses Persetujuan UMKM' }}</h5>
                </div>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    {{ $isPimpinan
                        ? 'Pantau seluruh aktivitas pendaftaran UMKM di wilayah RW. Persetujuan dilakukan oleh Admin RW.'
                        : 'Tinjau pendaftaran bisnis baru yang diajukan oleh warga. Pastikan nama dan kategori bisnis mematuhi pedoman komunitas.' }}
                </p>
                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <h6 class="fw-bold text-dark small mb-2">Alur Persetujuan</h6>
                    <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">
                        Setelah disetujui, usaha tampil di <strong>Pojok UMKM</strong> dan warga dapat mengelola produknya secara mandiri.
                        Pengajuan yang ditolak dapat diperbaiki dan diajukan ulang oleh pemiliknya.
                    </p>
                </div>
            </div>

            <div class="card border shadow-sm rounded-4 p-4 bg-white text-center">
                <div class="d-inline-flex p-3 mb-1 mx-auto text-success">
                    <i class="bi bi-shop fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Pojok UMKM</h6>
                <p class="text-muted small mb-3">Lihat usaha dan katalog produk UMKM yang sudah disetujui.</p>
                <a href="{{ route('rw.umkm.index') }}" class="btn btn-outline-success rounded-3 px-3 py-2 small fw-semibold">Buka Pojok UMKM</a>
            </div>
        </div>
    </div>
</div>
@endsection
