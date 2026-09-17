@extends('layouts.app')

@section('title', $article['title'])

@section('content')
<section class="container py-5 max-w-lg mx-auto" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('berita') }}" class="text-decoration-none text-muted mb-3 d-inline-block">
            <i class="bi bi-arrow-left"></i> Kembali ke Arsip Berita
        </a>
        <div class="mb-2">
            <span class="badge bg-success rounded-pill px-3 py-2 text-white">{{ strtoupper($article['category']) }}</span>
            <span class="text-muted ms-2">{{ $article['date'] }}</span>
        </div>
        <h1 class="fw-bold text-dark" style="font-size: 2.5rem;">{{ $article['title'] }}</h1>
    </div>

    @if($article['image'])
    <div class="mb-4 rounded overflow-hidden shadow-sm">
        <img src="{{ asset($article['image']) }}" class="img-fluid w-100" alt="{{ $article['title'] }}" style="object-fit: cover; max-height: 400px;">
    </div>
    @endif

    <div class="content text-secondary" style="font-size: 1.1rem; line-height: 1.8;">
        <p>{{ $article['content'] }}</p>
    </div>
</section>
@endsection
