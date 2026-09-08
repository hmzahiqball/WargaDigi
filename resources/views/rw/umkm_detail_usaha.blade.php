@extends('layouts.global')

@section('title', 'Detail UMKM: ' . ($usaha->nama_usaha ?? 'Usaha Warga'))

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h2 class="fw-bold text-success mb-0">{{ $usaha->nama_usaha ?? 'Nama Usaha' }}</h2>
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 small fw-semibold">
                    <i class="bi bi-patch-check-fill me-1"></i> Terverifikasi RW 12
                </span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-1 small fw-semibold">
                    <i class="bi bi-eye-fill me-1"></i> Mode Peninjauan RW
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap text-muted small">
                <span>
                    <i class="bi bi-person-circle text-success me-1"></i> 
                    <strong>{{ $usaha->pemilik->penduduk->nama_lengkap ?? $usaha->user->username ?? 'Warga' }}</strong>
                </span>
                <span>•</span>
                <span>
                    <i class="bi bi-geo-alt text-muted me-1"></i>
                    {{ $usaha->pemilik->penduduk->keluarga->rt->nama_rt ? 'RT ' . $usaha->pemilik->penduduk->keluarga->rt->nama_rt : 'RW 12' }}
                </span>
                <span>•</span>
                <span>
                    <i class="bi bi-telephone text-muted me-1"></i>
                    {{ $usaha->no_wa ?? '-' }}
                </span>
            </div>
        </div>

        <div>
            <a href="{{ route('rw.umkm.index', ['tab' => 'terdaftar']) }}" class="btn btn-white bg-white border rounded-3 px-3 py-2 small fw-semibold text-dark shadow-sm text-decoration-none d-inline-flex align-items-center gap-2" style="height: 44px;">
                Kembali ke Daftar UMKM Terdaftar
            </a>
        </div>
    </div>

    @include('rw.components.bannerUsahaReadOnly', ['usaha' => $usaha])

    @include('rw.components.cardStatSummary', [
        'jumlahProdukAktif' => $jumlahProdukAktif,
        'jumlahProdukTidakAktif' => $jumlahProdukTidakAktif,
        'jumlahKategoriProduk' => $jumlahKategoriProduk,
        'jumlahProdukStokMenipis' => $jumlahProdukStokMenipis
    ])

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1">Katalog Produk & Etalase Usaha</h5>
            <p class="text-muted small mb-0">Daftar produk yang telah didaftarkan oleh pelaku UMKM dan tampil di Galeri Warga.</p>
        </div>
    </div>

    @include('components.filterSearchBar', [
        'actionUrl' => route('rw.umkm.usaha.detail', $usaha->id),
        'placeholder' => 'Cari produk berdasarkan nama...',
        'kategoriProdukList' => $kategoriProdukList,
        'usaha' => $usaha
    ])

    <div class="row g-4 mb-4">
        @if(isset($produk) && count($produk) > 0)
            @foreach($produk as $item)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    {{-- Reusable Component: Card Produk Read-Only --}}
                    @include('rw.components.cardProdukReadOnly', ['item' => $item, 'usaha' => $usaha])
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card border shadow-sm rounded-4 p-5 bg-white text-center">
                    <div class="rounded-circle bg-light d-inline-flex p-3 mb-3 mx-auto text-muted">
                        <i class="bi bi-box fs-1"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Produk yang Didaftarkan</h5>
                    <p class="text-muted small mb-0">Usaha ini belum menambahkan daftar produk ke katalog etalase.</p>
                </div>
            </div>
        @endif
    </div>

    @if(isset($produk) && method_exists($produk, 'total') && $produk->total() > 0)
        @include('components.pagination', ['paginator' => $produk, 'label' => 'produk'])
    @endif
</div>
@endsection
