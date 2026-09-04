@extends('layouts.global')

@section('title', 'Manajemen Pengumuman')

@push('styles')
<style>
    .pengumuman-table th,
    .pengumuman-table td {
        text-align: center;
        vertical-align: middle;
    }
    .pengumuman-table td:nth-child(1) {
        text-align: left;
    }
    
    .bento-card {
        border-radius: 12px;
        border: 1px solid #BFCABA;
        background: #fff;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease-in-out;
    }
    .bento-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .bento-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Pengumuman</h2>
        <p class="text-muted mb-0">Publikasikan informasi penting dan pengumuman resmi bagi seluruh warga.</p>
    </div>
    <a href="#" onclick="alert('Formulir Buat Pengumuman segera hadir!')" class="btn btn-success fw-semibold px-4 py-2 rounded-3 text-white shadow-sm d-flex align-items-center gap-2" style="background: rgba(46, 125, 50, 0.9);">
        <i class="bi bi-plus-lg"></i> Buat Pengumuman Baru
    </a>
</div>

{{-- Status Workflow Bento --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="bento-card">
            <div class="bento-icon" style="background: #EFEDED; color: #40493D;">
                <i class="bi bi-file-earmark-text fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 12px; letter-spacing: 0.6px;">DRAFT</div>
                <div class="fw-bold text-dark" style="font-size: 24px; line-height: 1;">{{ $stats['draft'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bento-card">
            <div class="bento-icon" style="background: #FFDDB5; color: #2A1800;">
                <i class="bi bi-hourglass-split fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 12px; letter-spacing: 0.6px;">PENDING RW</div>
                <div class="fw-bold text-dark" style="font-size: 24px; line-height: 1;">{{ $stats['pending_rw'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bento-card">
            <div class="bento-icon" style="background: rgba(46, 125, 50, 0.10); color: #007232;">
                <i class="bi bi-check-circle-fill fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 12px; letter-spacing: 0.6px;">PUBLISHED</div>
                <div class="fw-bold text-dark" style="font-size: 24px; line-height: 1;">{{ $stats['published'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Announcements Table --}}
<div class="card card-custom shadow-sm border-0" style="border: 1px solid #BFCABA !important;">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom" style="background: #FBF9F8;">
        <h5 class="fw-bold text-dark mb-0">Daftar Pengumuman</h5>
        <button class="btn btn-sm btn-white border rounded-2 px-3 py-2 shadow-sm text-dark d-flex align-items-center gap-2">
            <i class="bi bi-funnel"></i> Filter
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 pengumuman-table">
            <colgroup>
                <col style="width: 40%;">
                <col style="width: 25%;">
                <col style="width: 15%;">
                <col style="width: 20%;">
            </colgroup>
            <thead class="border-bottom" style="background: #FBF9F8; border-color: #BFCABA !important;">
                <tr class="text-muted fw-bold" style="color: #40493D !important; font-size: 14px;">
                    <th scope="col" class="py-3 ps-4 pe-3 text-start">Judul Pengumuman</th>
                    <th scope="col" class="py-3 px-3">Tanggal Publikasi</th>
                    <th scope="col" class="py-3 px-3">Status</th>
                    <th scope="col" class="py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengumuman as $item)
                    <tr class="border-bottom" style="border-color: #BFCABA !important;">
                        <td class="py-4 ps-4 pe-3">
                            <div class="fw-semibold text-dark" style="font-size: 14px;">{{ $item['judul'] }}</div>
                        </td>
                        <td class="py-4 px-3" style="color: #40493D; font-size: 14px;">
                            {{ $item['tanggal'] }}
                        </td>
                        <td class="py-4 px-3">
                            <span class="badge rounded-pill px-3 py-1 fw-medium" style="background: {{ $item['status_bg'] }}; color: {{ $item['status_color'] }}; font-size: 12px;">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="py-4 px-3">
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-white border border-0 text-dark d-flex align-items-center justify-content-center p-2" title="Edit">
                                    <i class="bi bi-pencil fs-6"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-white border border-0 text-dark d-flex align-items-center justify-content-center p-2" title="Hapus">
                                    <i class="bi bi-trash fs-6"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-megaphone fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada pengumuman yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
