@extends('layouts.app')

@section('title', 'Pojok UMKM')
@section('meta_description', 'Pojok UMKM WargaDigi 21 — Dukung usaha tetangga. Temukan beragam produk dan jasa unggulan dari warga RW 21 Tanimulya.')

@section('content')
{{-- Page Hero --}}
<section class="page-hero">
    <div class="container">
        <h1>Pojok UMKM</h1>
        <p>Dukung usaha tetangga. Temukan beragam produk dan jasa unggulan dari warga RW 21 Tanimulya.</p>
    </div>
</section>

{{-- Filter & Search --}}
<section class="container mb-4">
    <div class="row align-items-center g-3">
        <div class="col-lg-8">
            <div class="filter-pills">
                @foreach($categories as $index => $cat)
                <a href="#" class="filter-pill {{ $index === 0 ? 'active' : '' }}">{{ $cat }}</a>
                @endforeach
            </div>
        </div>
        <div class="col-lg-4">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control" placeholder="Cari produk atau toko...">
            </div>
        </div>
    </div>
</section>

{{-- Products Grid --}}
<section class="container pb-5">
    <div class="row g-4">
        @foreach($products as $product)
        <div class="col-lg-3 col-md-6 fade-on-scroll">
            <div class="card-umkm">
                <div class="umkm-img-wrapper">
                    <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}">
                    <span class="price-badge">{{ $product['price'] }}</span>
                </div>
                <div class="card-body">
                    <div class="umkm-rt"><i class="bi bi-geo-alt"></i> {{ $product['rt'] }}</div>
                    <h5 class="card-title">{{ $product['name'] }}</h5>
                    <p class="card-text">{{ $product['desc'] }}</p>
                    <a href="#" class="btn btn-hubungi mt-2">
                        <i class="bi bi-chat-dots"></i> Hubungi Penjual
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="text-center mt-5">
        <a href="#" class="btn btn-outline-warga">Muat Lebih Banyak</a>
    </div>
</section>
@endsection
