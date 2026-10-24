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
    @if(count($articles) > 0)
    <div class="bento-grid">
        @foreach($articles as $index => $item)
            @php
                // Pertahankan gaya bento: item ke-2 dibuat "tall", item tanpa gambar diberi latar lembut.
                $itemClass = 'bento-item';
                $itemStyle = '';
                if ($index === 1) {
                    $itemClass .= ' bento-tall';
                } elseif (!$item['image']) {
                    $itemStyle = ($index % 2 === 0) ? 'background: #f8faf8;' : 'background: #fff8e1;';
                }
                $imgUrl = $item['image']
                    ? (\Illuminate\Support\Str::startsWith($item['image'], ['http://','https://']) ? $item['image'] : asset('storage/'.$item['image']))
                    : null;
            @endphp
            <div class="{{ $itemClass }}" @if($itemStyle) style="{{ $itemStyle }}" @endif>
                <a href="{{ route('berita.show', $item['id']) }}" class="text-decoration-none text-dark d-block h-100">
                    @if($imgUrl)
                    <img src="{{ $imgUrl }}" class="bento-img" @if($index === 1) style="height: 260px;" @endif alt="{{ $item['title'] }}">
                    @endif
                    <div class="bento-body">
                        <div class="mb-2">
                            <span class="card-category cat-{{ strtolower($item['category'] ?? '') }}">{{ strtoupper($item['category'] ?? '') }}</span>
                            <span class="text-muted ms-2" style="font-size: 0.8rem;">{{ $item['date'] }}</span>
                        </div>
                        <h5 class="card-title">{{ $item['title'] }}</h5>
                        <p class="card-text">{{ $item['excerpt'] }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    @else
    <div class="text-center text-muted py-5">
        <i class="bi bi-newspaper fs-1 d-block mb-2"></i>
        Belum ada berita yang dipublikasikan.
    </div>
    @endif
</section>
@endsection
