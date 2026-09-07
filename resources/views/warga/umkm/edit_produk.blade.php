@extends('layouts.global')

@section('title', 'Edit Produk UMKM')

@section('content')
<div class="mb-4">
    <a href="{{ route('warga.umkm.kelola') }}" class="text-decoration-none text-muted mb-2 d-inline-block">
        <i class="bi bi-arrow-left"></i> Kembali ke Kelola Produk
    </a>
    <h2 class="fw-bold text-success">Edit Produk</h2>
</div>

<div class="card shadow-sm border-0 max-w-lg mx-auto">
    <div class="card-body p-4">
        <form action="{{ route('warga.umkm.produk.update', $produk->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_produk" class="form-label fw-bold">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                @error('nama_produk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label fw-bold">Harga <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" value="{{ old('harga', $produk->harga) }}" required>
                </div>
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="stok" class="form-label fw-bold">Stok <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $produk->stok) }}" required>
                @error('stok')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="link_wa" class="form-label fw-bold">Link WhatsApp (Opsional)</label>
                <input type="url" class="form-control @error('link_wa') is-invalid @enderror" id="link_wa" name="link_wa" value="{{ old('link_wa', $produk->link_wa) }}" placeholder="https://wa.me/628...">
                @error('link_wa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success py-2 fw-semibold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
