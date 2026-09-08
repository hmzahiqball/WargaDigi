@extends('layouts.global')

@section('title', 'Pusat Manajemen UMKM - RW')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1 fs-2">Pusat Manajemen UMKM</h2>
            <p class="text-muted mb-0 small fs-6">
                {{ $isPimpinan ? 'Monitoring riwayat persetujuan dan katalog UMKM warga RW.' : 'Tinjau pendaftaran baru dan pantau seluruh UMKM warga RW.' }}
            </p>
        </div>

        @php
            $pendingCount = ($pendingUsaha instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $pendingUsaha->total() : count($pendingUsaha ?? []);
            $terdaftarCount = ($daftarUsahaTerdaftar instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $daftarUsahaTerdaftar->total() : count($daftarUsahaTerdaftar ?? []);
        @endphp
        <div class="d-flex align-items-center gap-2 border-bottom pb-2 flex-wrap">
            <a href="{{ route('rw.umkm.index', ['tab' => 'persetujuan']) }}" 
               class="text-decoration-none fw-bold pb-2 px-3 border-bottom border-3 d-flex align-items-center gap-2 {{ $tab !== 'terdaftar' ? 'text-success border-success' : 'text-muted border-transparent hover-success' }}"
               style="{{ $tab !== 'terdaftar' ? 'color: #2E7D32 !important; border-color: #2E7D32 !important;' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span>{{ $isPimpinan ? 'Riwayat Persetujuan UMKM' : 'Persetujuan UMKM' }}</span>
                @if(!$isPimpinan && $pendingCount > 0)
                    <span class="badge bg-danger rounded-pill px-2 py-1 small">{{ $pendingCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('rw.umkm.index', ['tab' => 'terdaftar']) }}" 
               class="text-decoration-none fw-bold pb-2 px-3 border-bottom border-3 d-flex align-items-center gap-2 {{ $tab === 'terdaftar' ? 'text-success border-success' : 'text-muted border-transparent hover-success' }}"
               style="{{ $tab === 'terdaftar' ? 'color: #2E7D32 !important; border-color: #2E7D32 !important;' : '' }}">
                <i class="bi bi-shop"></i>
                <span>Daftar Usaha UMKM yang Terdaftar</span>
                @if($terdaftarCount > 0)
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small">{{ $terdaftarCount }}</span>
                @endif
            </a>
        </div>
    </div>

    @if($tab !== 'terdaftar')
        <div class="row g-4">
            <div class="col-lg-8">
                @if($isPimpinan)
                    <div class="alert shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center gap-3" style="background-color: #ffffff;">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(34, 197, 94, 0.2); width: 40px; height: 40px;">
                            <i class="bi bi-info-circle text-success fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Mode Monitoring Pimpinan RW</h6>
                            <p class="text-muted small mb-0">Menampilkan seluruh history pengajuan UMKM (Pending & Approved). Klik kartu untuk melihat rincian pemohon.</p>
                        </div>
                    </div>
                @endif

                @php
                    $listCards = $isPimpinan ? $historyUsaha : $pendingUsaha;
                @endphp

                @if(isset($listCards) && $listCards->count() > 0)
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($listCards as $item)
                            @include('rw.umkm.components.cardApprovalUmkm', ['item' => $item, 'isPimpinan' => $isPimpinan])

                            @include('rw.umkm.components.modalDetailPengaju', ['item' => $item, 'isPimpinan' => $isPimpinan])
                        @endforeach
                    </div>

                    @if(isset($listCards) && method_exists($listCards, 'total') && $listCards->total() > 0)
                        @include('components.pagination', ['paginator' => $listCards, 'label' => 'pengajuan'])
                    @endif
                @else
                    <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
                        <div class="p-3 mb-3 mx-auto">
                            <i class="bi bi-check2-circle text-success fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $isPimpinan ? 'Belum Ada Pengajuan UMKM' : 'Semua UMKM Telah Diverifikasi' }}
                        </h5>
                        <p class="text-muted small mb-0">
                            {{ $isPimpinan ? 'Saat ini belum terdapat catatan riwayat pengajuan UMKM.' : 'Saat ini tidak ada pendaftaran UMKM baru yang menunggu peninjauan RW.' }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card border shadow-sm rounded-4 p-4 bg-white mb-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-info-circle fs-4 text-success" style="color: #2E7D32 !important;"></i>
                        <h5 class="fw-bold text-dark mb-0">
                            {{ $isPimpinan ? 'Monitoring Persetujuan' : 'Proses Persetujuan UMKM' }}
                        </h5>
                    </div>
                    <p class="text-muted small mb-4" style="line-height: 1.6;">
                        {{ $isPimpinan 
                            ? 'Pantau seluruh aktivitas pendaftaran UMKM di wilayah RW. Klik kartu usaha mana saja untuk melihat data pemohon pendaftaran.' 
                            : 'Tinjau pendaftaran bisnis baru yang diajukan oleh warga. Pastikan nama dan kategori bisnis mematuhi pedoman komunitas.' }}
                    </p>

                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                        <h6 class="fw-bold text-dark small mb-2">Workflow Terintegrasi</h6>
                        <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">
                            Setelah profil disetujui, UMKM otomatis masuk ke <strong>Daftar Usaha Terdaftar</strong> dan warga dapat mengelola produknya secara mandiri.
                        </p>
                    </div>
                </div>

                <div class="card border shadow-sm rounded-4 p-4 bg-white text-center">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-3 mx-auto text-success">
                        <i class="bi bi-shop fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Daftar Usaha Terdaftar</h6>
                    <p class="text-muted small mb-3">Ingin melihat toko dan katalog etalase UMKM yang sudah disetujui?</p>
                    <a href="{{ route('rw.umkm.index', ['tab' => 'terdaftar']) }}" class="btn btn-outline-success rounded-3 px-3 py-2 small fw-semibold">
                        Buka Daftar Usaha Terdaftar
                    </a>
                </div>
            </div>
        </div>

    @else
        <div class="mb-4">
            @include('components.filterSearchBar', [
                'placeholder' => 'Cari nama usaha, pemilik, atau kategori UMKM...'
            ])

            @if(isset($daftarUsahaTerdaftar) && count($daftarUsahaTerdaftar) > 0)
                <div class="row g-4 mb-4">
                    @foreach($daftarUsahaTerdaftar as $item)
                        <div class="col-md-6 col-lg-4">
                            @include('rw.umkm.components.cardUsahaTerdaftar', ['item' => $item])
                        </div>
                    @endforeach
                </div>

                @if(isset($daftarUsahaTerdaftar) && method_exists($daftarUsahaTerdaftar, 'total') && $daftarUsahaTerdaftar->total() > 0)
                    @include('components.pagination', ['paginator' => $daftarUsahaTerdaftar, 'label' => 'usaha'])
                @endif
            @else
                <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-3 mx-auto text-muted">
                        <i class="bi bi-shop fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada UMKM Terdaftar</h5>
                    <p class="text-muted small mb-0">Belum ada usaha warga yang berstatus disetujui (Approved) saat ini.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
