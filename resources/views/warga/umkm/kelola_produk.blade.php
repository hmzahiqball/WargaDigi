@extends('layouts.global')

@section('title', 'Kelola Produk Saya')

@push('styles')
<style>
    .product-manage-card {
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.08);
        background: #fff;
        overflow: hidden;
        transition: all 0.25s ease-in-out;
    }
    .product-manage-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        transform: translateY(-3px);
    }
    .product-manage-card.out-of-stock {
        border: 1.5px solid #fca5a5;
    }
    .product-manage-img {
        position: relative;
        height: 190px;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .product-manage-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .add-product-card {
        border: 2px dashed #cbd5e1;
        border-radius: 14px;
        background: #fff;
        text-align: center;
        height: 100%;
        min-height: 380px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 32px 24px;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .add-product-card:hover {
        border-color: #198754;
        background-color: #f8fdf9;
        transform: translateY(-3px);
    }
    .add-product-icon {
        width: 56px;
        height: 56px;
        background-color: #d1e7dd;
        color: #0f5132;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0 small">
            <li class="breadcrumb-item"><a href="{{ route('warga.umkm.galeri') }}" class="text-decoration-none text-muted">Galeri UMKM</a></li>
            <li class="breadcrumb-item active fw-semibold text-dark" aria-current="page">Kelola Produk</li>
        </ol>
    </nav>

    <!-- Header Kelola Produk -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-7">
            <h2 class="fw-bold text-success mb-2">Kelola Produk Saya</h2>
            <p class="text-muted mb-0">
                Kelola persediaan di toko Anda, pantau tingkat stok, dan perbarui detail produk.
            </p>
        </div>
        <div class="col-md-5 text-md-end">
            <a href="{{ route('warga.umkm.produk.create') }}" class="btn btn-success fw-semibold px-3 py-2 rounded-3 text-white shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <form action="{{ route('warga.umkm.kelola') }}" method="GET" class="row g-3 align-items-center">
            <!-- Search Input -->
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-select-lg form-control border-start-0 rounded-end-3 fs-6" placeholder="Cari Produk Berdasarkan Nama" onchange="this.form.submit()">
                </div>
            </div>

            <!-- Filter Kategori -->
            <div class="col-md-3">
                <select name="kategori" class="form-select border-1 rounded-3 py-2 text-secondary fs-6" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    <option value="Kuliner" {{ request('kategori') == 'Kuliner' ? 'selected' : '' }}>Kuliner</option>
                    <option value="Fashion" {{ request('kategori') == 'Fashion' ? 'selected' : '' }}>Fashion</option>
                    <option value="Jasa" {{ request('kategori') == 'Jasa' ? 'selected' : '' }}>Jasa</option>
                    <option value="Kerajinan" {{ request('kategori') == 'Kerajinan' ? 'selected' : '' }}>Kerajinan</option>
                    <option value="Koperasi" {{ request('kategori') == 'Koperasi' ? 'selected' : '' }}>Koperasi</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="col-md-3">
                <select name="status" class="form-select border-1 rounded-3 py-2 text-secondary fs-6" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Stok Habis" {{ request('status') == 'Stok Habis' ? 'selected' : '' }}>Stok Habis</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    <div class="row g-4 mb-4">
        @forelse($produk as $item)
            @php
                $isHabis = ($item->stok <= 0);
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-manage-card h-100 d-flex flex-column {{ $isHabis ? 'out-of-stock' : '' }}">
                    <div class="product-manage-img">
                        <img src="{{ $item->foto_produk ? asset('storage/' . $item->foto_produk) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop' }}" alt="{{ $item->nama_produk }}">
                        @if($isHabis)
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Habis
                            </span>
                        @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                                <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Aktif
                            </span>
                        @endif
                    </div>

                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">{{ $item->nama_produk }}</h6>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $item->deskripsi ?? 'Tidak ada deskripsi produk.' }}
                        </p>

                        <div class="pt-2 border-top mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Harga</small>
                                    <span class="fw-bold text-success fs-6">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-end">
                                    @if($isHabis)
                                        <small class="text-danger fw-semibold d-block" style="font-size: 11px;">Perlu Restok</small>
                                        <span class="fw-bold text-danger">0 unit</span>
                                    @else
                                        <small class="text-muted d-block" style="font-size: 11px;">Stok</small>
                                        <span class="fw-bold text-dark">{{ $item->stok }} unit</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action Icons Footer -->
                        <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                            <a href="{{ $item->link_wa ?? '#' }}" target="_blank" class="text-success text-decoration-none" title="Lihat WA">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </a>
                            <a href="{{ route('warga.umkm.produk.edit', $item->id) }}" class="text-secondary text-decoration-none" title="Edit Produk">
                                <i class="bi bi-pencil fs-5"></i>
                            </a>
                            <form action="{{ route('warga.umkm.produk.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-secondary text-decoration-none p-0" title="Hapus Produk">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Default Sample Cards matching screenshot --}}
            <!-- Card 1: Keramik Buatan Pengrajin -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-manage-card h-100 d-flex flex-column">
                    <div class="product-manage-img">
                        <img src="https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=500&auto=format&fit=crop" alt="Keramik Buatan Pengrajin">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Aktif
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Keramik Buatan Pengrajin</h6>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4;">
                            Mug tanah liat bertekstur bintik-bintik buatan tangan, sempurna untuk pagi hari...
                        </p>
                        <div class="pt-2 border-top mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Harga</small>
                                    <span class="fw-bold text-success fs-6">Rp 120.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 11px;">Stok</small>
                                    <span class="fw-bold text-dark">24 unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                            <a href="#" class="text-success text-decoration-none"><i class="bi bi-whatsapp fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-pencil fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-trash fs-5"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Campuran Kopi Susu -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-manage-card h-100 d-flex flex-column">
                    <div class="product-manage-img">
                        <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop" alt="Campuran Kopi Susu">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                            <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i> Aktif
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Campuran Kopi Susu</h6>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4;">
                            Campuran khas rumah, 100% Arabica. Aroma cokelat.
                        </p>
                        <div class="pt-2 border-top mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Harga</small>
                                    <span class="fw-bold text-success fs-6">Rp 85.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 11px;">Stok</small>
                                    <span class="fw-bold text-dark">12 unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                            <a href="#" class="text-success text-decoration-none"><i class="bi bi-whatsapp fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-pencil fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-trash fs-5"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Syal Tenun Ikat (Stok Habis) -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-manage-card out-of-stock h-100 d-flex flex-column">
                    <div class="product-manage-img">
                        <img src="https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=500&auto=format&fit=crop" alt="Syal Tenun Ikat">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-semibold position-absolute top-0 end-0 m-3 small shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Stok Habis
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Syal Tenun Ikat</h6>
                        <p class="text-muted small mb-3 flex-grow-1" style="line-height: 1.4;">
                            Syal katun tenun tangan tradisional dengan... alami
                        </p>
                        <div class="pt-2 border-top mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px;">Harga</small>
                                    <span class="fw-bold text-success fs-6">Rp 250.000</span>
                                </div>
                                <div class="text-end">
                                    <small class="text-danger fw-semibold d-block" style="font-size: 11px;">Perlu Restok</small>
                                    <span class="fw-bold text-danger">0 unit</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center gap-3 pt-1">
                            <a href="#" class="text-success text-decoration-none"><i class="bi bi-whatsapp fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-pencil fs-5"></i></a>
                            <a href="#" class="text-secondary text-decoration-none"><i class="bi bi-trash fs-5"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse

        <!-- Card 4: Dotted Add Product Card -->
        <div class="col-sm-6 col-md-4 col-lg-3">
            <a href="{{ route('warga.umkm.produk.create') }}" class="add-product-card">
                <div class="add-product-icon">
                    <i class="bi bi-plus-lg fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Tambah Produk Baru</h6>
                <p class="text-muted small mb-0">
                    Perluas katalog UMKM Anda dengan menambahkan item baru ke galeri.
                </p>
            </a>
        </div>
    </div>

    <!-- Footer Pagination -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 border-top text-muted small">
        <div class="mb-2 mb-md-0">
            Menampilkan {{ count($produk) > 0 ? count($produk) : 3 }} dari {{ count($produk) > 0 ? count($produk) : 3 }} produk
        </div>
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none" disabled><i class="bi bi-chevron-left"></i></button>
            <span>Halaman 1 dari 1</span>
            <button class="btn btn-sm btn-link text-muted p-0 text-decoration-none" disabled><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>
@endsection
