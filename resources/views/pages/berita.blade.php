@extends('layouts.app')

@section('title', 'Arsip Berita')
@section('meta_description', 'Arsip Berita RW 21 — Kumpulan informasi, pengumuman, dan dokumentasi kegiatan warga. Tetap terhubung dan berpartisipasi dalam lingkungan kita.')

@section('content')
<section class="container py-5">
    {{-- Header --}}
    <div class="mb-4">
        <h1 style="font-size: 2rem;">Arsip Berita RW 21</h1>
        <p class="text-muted">Kumpulan informasi, pengumuman, dan dokumentasi kegiatan warga. Tetap terhubung<br>dan berpartisipasi dalam lingkungan kita.</p>
    </div>

    {{-- Search & Filter --}}
    <div class="row align-items-center g-3 mb-5">
        <div class="col-lg-6">
            <div class="search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" class="form-control" placeholder="Cari berita...">
            </div>
        </div>
        <div class="col-lg-6">
            <div class="filter-pills justify-content-lg-end">
                @foreach($categories as $index => $cat)
                <a href="#" class="filter-pill {{ $index === 0 ? 'active' : '' }}">{{ $cat }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bento Grid --}}
    <div class="bento-grid">
        {{-- Item 1: Left column --}}
        <div class="bento-item">
            @if($articles[0]['image'])
            <img src="{{ asset($articles[0]['image']) }}" class="bento-img" alt="{{ $articles[0]['title'] }}">
            @endif
            <div class="bento-body">
                <div class="mb-2">
                    <span class="card-category cat-{{ strtolower($articles[0]['category']) }}">{{ strtoupper($articles[0]['category']) }}</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">{{ $articles[0]['date'] }}</span>
                </div>
                <h5 class="card-title">{{ $articles[0]['title'] }}</h5>
                <p class="card-text">{{ $articles[0]['excerpt'] }}</p>
            </div>
        </div>

        {{-- Item 2: Center tall --}}
        <div class="bento-item bento-tall">
            @if($articles[1]['image'])
            <img src="{{ asset($articles[1]['image']) }}" class="bento-img" style="height: 260px;" alt="{{ $articles[1]['title'] }}">
            @endif
            <div class="bento-body">
                <div class="mb-2">
                    <span class="card-category cat-{{ strtolower($articles[1]['category']) }}">{{ strtoupper($articles[1]['category']) }}</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">{{ $articles[1]['date'] }}</span>
                </div>
                <h5 class="card-title">{{ $articles[1]['title'] }}</h5>
                <p class="card-text">{{ $articles[1]['excerpt'] }}</p>
            </div>
        </div>

        {{-- Item 3: Right column (no image) --}}
        <div class="bento-item" style="background: #f8faf8;">
            <div class="bento-body">
                <div class="mb-2">
                    <span class="card-category cat-{{ strtolower($articles[2]['category']) }}">{{ strtoupper($articles[2]['category']) }}</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">{{ $articles[2]['date'] }}</span>
                </div>
                <h5 class="card-title">{{ $articles[2]['title'] }}</h5>
                <p class="card-text">{{ $articles[2]['excerpt'] }}</p>
            </div>
        </div>

        {{-- Item 4: Bottom left (no image) --}}
        <div class="bento-item" style="background: #fff8e1;">
            <div class="bento-body">
                <div class="mb-2">
                    <span class="card-category cat-{{ strtolower($articles[3]['category']) }}">{{ strtoupper($articles[3]['category']) }}</span>
                    <span class="text-muted ms-2" style="font-size: 0.8rem;">{{ $articles[3]['date'] }}</span>
                </div>
                <h5 class="card-title">{{ $articles[3]['title'] }}</h5>
                <p class="card-text">{{ $articles[3]['excerpt'] }}</p>
            </div>
        </div>
    </div>
</section>
@endsection
