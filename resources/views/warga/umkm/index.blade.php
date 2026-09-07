@extends('layouts.global')

@section('title', 'Galeri UMKM')

@push('styles')
<style>
    .umkm-hero-card {
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        background-size: cover;
        background-position: center;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .umkm-hero-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
    }
    .umkm-hero-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.85) 100%);
        z-index: 1;
    }
    .umkm-hero-content {
        position: relative;
        z-index: 2;
    }
    .badge-kuliner { background-color: #ff9800 !important; color: #fff; }
    .badge-fashion { background-color: #3b82f6 !important; color: #fff; }
    .badge-jasa { background-color: #06b6d4 !important; color: #fff; }
    .badge-kerajinan { background-color: #198754 !important; color: #fff; }
    .badge-koperasi { background-color: #8b5cf6 !important; color: #fff; }

    .product-card {
        border-radius: 14px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fff;
        overflow: hidden;
        transition: all 0.25s ease-in-out;
    }
    .product-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        transform: translateY(-4px);
    }
    .product-img-wrapper {
        position: relative;
        height: 180px;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .product-card:hover .product-img-wrapper img {
        transform: scale(1.06);
    }
    .btn-whatsapp {
        background-color: #25D366;
        color: #fff;
        border: none;
    }
    .btn-whatsapp:hover {
        background-color: #20ba59;
        color: #fff;
    }
    .btn-outline-whatsapp {
        border: 1px solid #198754;
        color: #198754;
        background-color: transparent;
    }
    .btn-outline-whatsapp:hover {
        background-color: #198754;
        color: #fff;
    }
    .filter-pill {
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    .filter-pill.active, .filter-pill:hover {
        background: #5b9b76;
        border-color: #5b9b76;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Galeri UMKM -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-7">
            <h2 class="fw-bold text-success mb-2">Galeri UMKM</h2>
            <p class="text-muted mb-0" style="max-width: 680px;">
                Dukung usaha tetangga, majukan ekonomi lokal RW 21 Tanimulya. Temukan berbagai produk dan jasa terbaik dari komunitas kita.
            </p>
        </div>
        <div class="col-md-5 text-md-end d-flex gap-2 justify-content-md-end flex-wrap">
            <a href="{{ route('warga.umkm.daftar') }}" class="btn btn-success fw-semibold px-3 py-2 rounded-3 text-white shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Daftarkan Usaha Anda
            </a>
            <a href="{{ route('warga.umkm.kelola') }}" class="btn text-white fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background-color: #5b9b76;">
                <i class="bi bi-shop me-1"></i> Kelola UMKM Anda
            </a>
        </div>
    </div>

    <!-- Section Produk Unggulan RW 21 -->
    <div class="mb-5">
        <h5 class="fw-bold text-dark mb-3">Produk Unggulan RW 21</h5>
        <div class="row g-3">
            <!-- Left Large Hero Card -->
            <div class="col-lg-7">
                <div class="umkm-hero-card h-100" style="min-height: 380px; background-image: url('https://images.unsplash.com/photo-1544816155-12df9643f363?w=800&auto=format&fit=crop');">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge badge-kerajinan px-3 py-2 mb-2 fw-semibold rounded-2">Kerajinan</span>
                        <h3 class="fw-bold mb-2 text-white">Pahatan Kayu Jati Custom</h3>
                        <p class="text-white-50 mb-3 small">
                            Karya tangan Pak Budi, RT 03. Cocok untuk hiasan rumah atau hadiah eksklusif.
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="fs-4 fw-bold text-white">Rp 350.000</span>
                            <a href="https://wa.me/628123456789?text=Halo%20Pak%20Budi,%20saya%20tertarik%20dengan%20Pahatan%20Kayu%20Jati" target="_blank" class="btn btn-whatsapp rounded-pill px-3 py-2 fw-semibold text-decoration-none">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Stacked Cards -->
            <div class="col-lg-5 d-flex flex-column gap-3">
                <!-- Top Card: Kue Tampah -->
                <div class="umkm-hero-card flex-grow-1" style="min-height: 180px; background-image: url('https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=600&auto=format&fit=crop');">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge badge-kuliner px-2 py-1 mb-1 fw-bold rounded-2">Kuliner</span>
                        <h5 class="fw-bold mb-1 text-white">Kue Tampah Ibu Sari</h5>
                        <span class="fw-semibold text-white">Rp 150.000</span>
                    </div>
                </div>

                <!-- Bottom Card: Batik Tulis -->
                <div class="umkm-hero-card flex-grow-1" style="min-height: 180px; background-image: url('https://images.unsplash.com/photo-1601924994987-69e26d50dc26?w=600&auto=format&fit=crop');">
                    <div class="umkm-hero-overlay"></div>
                    <div class="umkm-hero-content">
                        <span class="badge badge-fashion px-2 py-1 mb-1 fw-bold rounded-2">Fashion</span>
                        <h5 class="fw-bold mb-1 text-white">Batik Tulis Tanimulya</h5>
                        <span class="fw-semibold text-white">Rp 250.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar & Sorting -->
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <!-- Category Filter Pills -->
            <div class="d-flex align-items-center gap-2 overflow-auto pb-1 pb-md-0" style="white-space: nowrap;">
                <a href="{{ route('warga.umkm.galeri') }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ empty($kategori) || $kategori == 'Semua' ? 'active' : '' }}">Semua Kategori</a>
                <a href="{{ route('warga.umkm.galeri', ['kategori' => 'Kuliner']) }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ $kategori == 'Kuliner' ? 'active' : '' }}">Kuliner</a>
                <a href="{{ route('warga.umkm.galeri', ['kategori' => 'Fashion']) }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ $kategori == 'Fashion' ? 'active' : '' }}">Fashion</a>
                <a href="{{ route('warga.umkm.galeri', ['kategori' => 'Jasa']) }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ $kategori == 'Jasa' ? 'active' : '' }}">Jasa</a>
                <a href="{{ route('warga.umkm.galeri', ['kategori' => 'Kerajinan']) }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ $kategori == 'Kerajinan' ? 'active' : '' }}">Kerajinan</a>
                <a href="{{ route('warga.umkm.galeri', ['kategori' => 'Koperasi']) }}" class="btn filter-pill rounded-pill px-3 py-2 text-decoration-none {{ $kategori == 'Koperasi' ? 'active' : '' }}">Koperasi</a>
            </div>

            <!-- Sorting Select -->
            <div class="d-flex align-items-center gap-2 ms-md-auto" style="min-width: 200px;">
                <form action="{{ route('warga.umkm.galeri') }}" method="GET" class="w-100">
                    @if($kategori)
                        <input type="hidden" name="kategori" value="{{ $kategori }}">
                    @endif
                    <select name="sort" class="form-select border-1 rounded-3 shadow-none small" onchange="this.form.submit()">
                        <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Urutkan: Terbaru</option>
                        <option value="termurah" {{ $sort == 'termurah' ? 'selected' : '' }}>Urutkan: Termurah</option>
                        <option value="termahal" {{ $sort == 'termahal' ? 'selected' : '' }}>Urutkan: Termahal</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="row g-4 mb-4">
        @forelse($produk as $item)
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="product-img-wrapper">
                        <a href="{{ route('warga.umkm.produk.show', $item->id) }}">
                            <img src="{{ $item->foto_produk ? asset('storage/' . $item->foto_produk) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&auto=format&fit=crop' }}" alt="{{ $item->nama_produk }}">
                        </a>
                        <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">
                            {{ $item->usaha->kategori ?? 'UMKM' }}
                        </span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <a href="{{ route('warga.umkm.produk.show', $item->id) }}" class="text-decoration-none">
                            <h6 class="fw-bold text-dark mb-1">{{ $item->nama_produk }}</h6>
                        </a>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-person me-1"></i> {{ $item->usaha->nama_usaha ?? 'Warga RW 21' }}
                        </small>
                        
                        <div class="mt-auto">
                            <div class="fw-bold text-success fs-6 mb-3">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </div>
                            <a href="{{ $item->link_wa ?? 'https://wa.me/' . ($item->usaha->no_wa ?? '628123456789') }}" target="_blank" class="btn btn-outline-whatsapp w-100 rounded-3 fw-semibold small text-decoration-none">
                                <i class="bi bi-chat-dots me-1"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- Default Sample Product Cards (matching mockup) --}}
            <!-- Product 1: Mie Ayam -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="product-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=500&auto=format&fit=crop" alt="Mie Ayam Bang Jago">
                        <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">Kuliner</span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Mie Ayam Bang Jago</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-person me-1"></i> Bpk. Jago (RT 01)
                        </small>
                        <div class="mt-auto">
                            <div class="fw-bold text-success fs-6 mb-3">Rp 15.000</div>
                            <a href="https://wa.me/628123456789?text=Halo%20Bpk.%20Jago,%20saya%20mau%20pesan%20Mie%20Ayam" target="_blank" class="btn btn-outline-whatsapp w-100 rounded-3 fw-semibold small text-decoration-none">
                                <i class="bi bi-chat-dots me-1"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 2: Jasa Cukur -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="product-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=500&auto=format&fit=crop" alt="Jasa Cukur Panggilan">
                        <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">Jasa</span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Jasa Cukur Panggilan</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-person me-1"></i> Mas Andi (RT 04)
                        </small>
                        <div class="mt-auto">
                            <div class="fw-bold text-success fs-6 mb-3">Mulai Rp 25.000</div>
                            <a href="https://wa.me/628123456789?text=Halo%20Mas%20Andi,%20saya%20mau%20booking%20cukur" target="_blank" class="btn btn-outline-whatsapp w-100 rounded-3 fw-semibold small text-decoration-none">
                                <i class="bi bi-chat-dots me-1"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 3: Sabun Organik -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="product-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1607006482682-1d54238e8f85?w=500&auto=format&fit=crop" alt="Sabun Organik Herbal">
                        <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">Kerajinan</span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Sabun Organik Herbal</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-person me-1"></i> Ibu Ningsih (RT 02)
                        </small>
                        <div class="mt-auto">
                            <div class="fw-bold text-success fs-6 mb-3">Rp 35.000</div>
                            <a href="https://wa.me/628123456789?text=Halo%20Ibu%20Ningsih,%20saya%20mau%20pesan%20sabun" target="_blank" class="btn btn-outline-whatsapp w-100 rounded-3 fw-semibold small text-decoration-none">
                                <i class="bi bi-chat-dots me-1"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product 4: Roti Sourdough -->
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="product-card h-100 d-flex flex-column">
                    <div class="product-img-wrapper">
                        <img src="https://images.unsplash.com/photo-1589367920969-ab8e050bbb04?w=500&auto=format&fit=crop" alt="Roti Sourdough Rumahan">
                        <span class="badge bg-light text-dark border px-2 py-1 position-absolute top-0 end-0 m-3 shadow-sm small fw-semibold">Kuliner</span>
                    </div>
                    <div class="p-3 d-flex flex-column flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Roti Sourdough Rumahan</h6>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-person me-1"></i> Dapur Rina (RT 05)
                        </small>
                        <div class="mt-auto">
                            <div class="fw-bold text-success fs-6 mb-3">Rp 45.000</div>
                            <a href="https://wa.me/628123456789?text=Halo%20Dapur%20Rina,%20saya%20mau%20pesan%20roti" target="_blank" class="btn btn-outline-whatsapp w-100 rounded-3 fw-semibold small text-decoration-none">
                                <i class="bi bi-chat-dots me-1"></i> Hubungi via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Load More Button -->
    <div class="text-center my-4">
        <button type="button" class="btn btn-light text-secondary border rounded-pill px-4 py-2 fw-semibold shadow-sm">
            Muat Lebih Banyak
        </button>
    </div>
</div>
@endsection
