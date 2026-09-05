@extends('layouts.global')

@section('title', $produk->nama_produk)

@section('content')
<div class="mb-4">
    <a href="{{ route('warga.umkm.galeri') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
        <i class="bi bi-arrow-left"></i> Kembali ke Galeri
    </a>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="rounded overflow-hidden bg-light shadow-sm d-flex align-items-center justify-content-center" style="height: 400px;">
            <img src="{{ $produk->foto_produk ? asset('storage/' . $produk->foto_produk) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&auto=format&fit=crop' }}" alt="{{ $produk->nama_produk }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 d-flex flex-column">
                <span class="badge bg-success bg-opacity-10 text-success mb-2 align-self-start">{{ $produk->usaha->kategori ?? 'UMKM' }}</span>
                <h2 class="fw-bold text-dark mb-2">{{ $produk->nama_produk }}</h2>
                <h4 class="fw-bold text-success mb-4">Rp {{ number_format($produk->harga, 0, ',', '.') }}</h4>

                <div class="mb-4">
                    <h6 class="fw-bold">Deskripsi Produk</h6>
                    <p class="text-secondary" style="line-height: 1.6;">
                        {{ $produk->deskripsi ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>

                <div class="mb-4 bg-light p-3 rounded">
                    <h6 class="fw-bold mb-2">Informasi Toko</h6>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shop text-muted"></i>
                        <span class="fw-semibold">{{ $produk->usaha->nama_usaha ?? 'Warga RW 21' }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person text-muted"></i>
                        <span class="text-muted">{{ $produk->usaha->user->name ?? 'Pemilik' }}</span>
                    </div>
                </div>

                <div class="mt-auto">
                    @php
                        $waLink = $produk->link_wa ?? 'https://wa.me/' . ($produk->usaha->no_wa ?? '');
                    @endphp
                    <a href="{{ $waLink }}" target="_blank" class="btn btn-success w-100 py-3 fw-bold fs-5 shadow-sm rounded-3">
                        <i class="bi bi-whatsapp me-2"></i> Hubungi Penjual via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
