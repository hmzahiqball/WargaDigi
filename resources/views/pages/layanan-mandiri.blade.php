@extends('layouts.app')

@section('title', 'Layanan Mandiri')
@section('meta_description', 'Layanan Mandiri WargaDigi 21 — Ajukan surat pengantar dan kelola data warga Anda secara online.')

@section('content')
<section class="container py-5">
    {{-- Header --}}
    <div class="mb-5 pb-3 border-bottom">
        <h1 style="font-size: 2rem; color: #1a1a2e; font-weight: 700;">Layanan Mandiri</h1>
        <p class="text-muted" style="font-size: 1.1rem;">Ajukan surat pengantar dan kelola data warga Anda secara online.</p>
    </div>

    {{-- Services Grid --}}
    <div class="row g-4">
        @foreach($services as $service)
        <div class="col-lg-3 col-md-6 fade-on-scroll">
            <div class="service-card p-4 h-100" style="background: #ffffff; border-radius: 1rem; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 15px rgba(0,0,0,0.03); transition: all 0.3s ease;">
                <div class="service-icon mb-3" style="width: 48px; height: 48px; border-radius: 0.5rem; background: #e8f5e9; color: #2E7D32; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="bi {{ $service['icon'] }}"></i>
                </div>
                <h5 style="font-weight: 600; color: #1a1a2e; margin-bottom: 0.75rem;">{{ $service['title'] }}</h5>
                <p class="text-muted" style="font-size: 0.9rem; line-height: 1.5; margin-bottom: 1.5rem;">{{ $service['desc'] }}</p>
                <a href="#" class="service-link" style="color: #2E7D32; font-weight: 600; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                    Buat Pengajuan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endsection
