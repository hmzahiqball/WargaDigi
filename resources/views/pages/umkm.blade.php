@extends('layouts.app')

@section('title', 'Pojok UMKM')
@section('meta_description', 'Pojok UMKM WargaDigi 21 — Dukung usaha tetangga. Temukan beragam toko, produk, dan jasa unggulan dari warga RW 21 Tanimulya.')

@section('content')
{{-- Page Hero --}}
<section class="page-hero mb-4">
    <div class="container">
        <h1>Pojok UMKM</h1>
        <p>Dukung usaha tetangga. Temukan beragam toko, produk, dan jasa unggulan dari warga RW 21 Tanimulya.</p>
    </div>
</section>

{{-- Filter & Search Component --}}
<section class="container mb-4">
    @include('warga.umkm.components.filterSearchBar', [
        'actionUrl' => route('pojok-umkm'),
        'placeholder' => 'Cari Toko / Usaha UMKM Berdasarkan Nama...',
        'daftarKategoriUmkm' => $daftarKategoriUmkm ?? null,
        'showStatus' => false
    ])
</section>

{{-- Usaha / Toko Grid --}}
<section class="container pb-5">
    <div class="row g-4">
        @forelse($daftarUsaha as $usaha)
            @include('warga.umkm.components.cardUsaha', [
                'usaha' => $usaha, 
                'colClass' => 'col-lg-4 col-md-6 fade-on-scroll'
            ])
        @empty
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
                    <i class="bi bi-shop fs-1 text-muted d-block mb-3"></i>
                    <h5 class="fw-bold text-dark mb-1">Tidak ada usaha atau toko ditemukan</h5>
                    <p class="text-muted small mb-3">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    <div>
                        <a href="{{ route('pojok-umkm') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Pencarian
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination Component --}}
    @include('components.pagination', ['paginator' => $daftarUsaha, 'label' => 'toko'])
</section>
@endsection
