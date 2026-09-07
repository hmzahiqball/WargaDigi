@extends('layouts.global')

@section('title', $pageTitle ?? 'Koleksi Produk UMKM')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h2 class="fw-bold text-success mb-0 fs-2">{{ $pageTitle }}</h2>
                @if($tipe === 'unggulan')
                    <span class="badge rounded-pill px-3 py-1 small fw-bold text-white shadow-sm" style="background-color: #f97316;">
                        <i class="bi bi-fire me-1"></i> Terpopuler
                    </span>
                @else
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-bold">
                        <i class="bi bi-stars me-1"></i> Rilisan Terbaru
                    </span>
                @endif
            </div>
            <p class="text-muted mb-0" style="max-width: 680px;">
                {{ $pageSubtitle }}
            </p>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center p-1 bg-white rounded-pill border shadow-sm">
                <a href="{{ route('warga.umkm.koleksi', 'unggulan') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-decoration-none transition {{ $tipe === 'unggulan' ? 'btn-success text-white shadow-sm' : 'text-muted' }}" 
                   style="{{ $tipe === 'unggulan' ? 'background-color: #2E7D32;' : '' }}">
                    <i class="bi bi-fire me-1"></i> Unggulan
                </a>
                <a href="{{ route('warga.umkm.koleksi', 'terbaru') }}" 
                   class="btn btn-sm rounded-pill px-3 py-1 fw-semibold text-decoration-none transition {{ $tipe === 'terbaru' ? 'btn-success text-white shadow-sm' : 'text-muted' }}" 
                   style="{{ $tipe === 'terbaru' ? 'background-color: #2E7D32;' : '' }}">
                    <i class="bi bi-stars me-1"></i> Terbaru
                </a>
            </div>

            <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Galeri
            </a>
        </div>
    </div>

    @include('warga.umkm.components.filterSearchBar', [
        'actionUrl' => route('warga.umkm.koleksi', $tipe),
        'placeholder' => 'Cari ' . strtolower($pageTitle) . '...',
        'produk' => $produk ?? null,
        'showStatus' => false
    ])

    <div class="row g-4 mb-4">
        @forelse($produk as $item)
            @include('warga.umkm.components.cardProduct', ['item' => $item, 'showOwner' => true])
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
                    <i class="bi bi-search fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-dark mb-1">Tidak ada produk ditemukan</h5>
                    <p class="text-muted small mb-3">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('warga.umkm.koleksi', $tipe) }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                        </a>
                        <a href="{{ route('warga.umkm.galeri') }}" class="btn btn-success btn-sm rounded-pill px-3 text-white">
                            <i class="bi bi-grid me-1"></i> Galeri Utama
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @include('components.pagination', ['paginator' => $produk, 'label' => 'produk'])

    @include('warga.umkm.components.footerUmkm')
</div>
@endsection
