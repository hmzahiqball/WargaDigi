@extends('layouts.global')

@section('title', 'Galeri Dokumentasi Kegiatan')

@push('styles')
<style>
    .filter-bar {
        background: #F8F9FA;
        box-shadow: 0px 8px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid #C3C5D7;
        border-radius: 4px;
        padding: 8px;
    }
    .galeri-card {
        background: #F8F9FA;
        box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #C3C5D7;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease-in-out;
    }
    .galeri-card:hover {
        transform: translateY(-4px);
        box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.08);
    }
    .galeri-image-placeholder {
        height: 128px;
        background: #F3F4F5;
        border-bottom: 1px solid #C3C5D7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #737686;
    }
    .galeri-card-body {
        padding: 16px;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .galeri-title {
        color: #191C1D;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.4;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .galeri-desc {
        color: #434654;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 16px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="mb-4">
    <h2 class="fw-bold mb-2" style="color: #191C1D; font-size: 36px;">Galeri Dokumentasi Kegiatan</h2>
    <p class="mb-0" style="color: #434654; font-size: 16px;">
        Kelola dan unggah foto-foto bukti kegiatan untuk agenda warga yang telah selesai. Pilih agenda di<br>bawah ini untuk memulai.
    </p>
</div>

{{-- Filter Bar --}}
<div class="filter-bar d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" style="min-width: 150px; background-color: #F8F9FA; border-color: #C3C5D7; color: #191C1D;">
            <option selected>Semua Kategori</option>
            <option value="1">Sosial</option>
            <option value="2">Keamanan</option>
            <option value="3">Infrastruktur</option>
        </select>
        <input type="date" class="form-control form-control-sm" style="background-color: #F8F9FA; border-color: #C3C5D7; color: #191C1D;" placeholder="mm/dd/yyyy">
    </div>
    <button class="btn btn-sm btn-white d-flex align-items-center gap-2" style="background: #F8F9FA; border: 1px solid #C3C5D7; color: #434654; font-weight: 600;">
        <i class="bi bi-funnel"></i> Filter Lanjutan
    </button>
</div>

{{-- Content Grid --}}
<div class="row g-4">
    @forelse($galeri as $item)
        <div class="col-md-4">
            <div class="galeri-card">
                <div class="galeri-image-placeholder">
                    <i class="bi bi-image fs-1 opacity-50"></i>
                </div>
                <div class="galeri-card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge" style="background: #E8F5E9; color: #1B5E20; font-size: 10px; padding: 4px 8px; letter-spacing: 0.5px;">
                                {{ $item['status'] }}
                            </span>
                            <span style="color: #434654; font-size: 12px;">{{ $item['tanggal'] }}</span>
                        </div>
                        <h3 class="galeri-title">{{ $item['judul'] }}</h3>
                        <p class="galeri-desc">{{ $item['deskripsi'] }}</p>
                    </div>
                    <button class="btn w-100 mt-auto d-flex align-items-center justify-content-center gap-2" style="background: rgba(46, 125, 50, 0.70); color: white; font-size: 12px; font-weight: 600; padding: 8px;">
                        <i class="bi bi-cloud-arrow-up"></i> Upload Bukti
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Belum ada agenda yang selesai.</p>
        </div>
    @endforelse
</div>
@endsection
