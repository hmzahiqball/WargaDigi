@extends('layouts.global')

@section('title', 'Manajemen Agenda')

@push('styles')
<style>
    .agenda-table th,
    .agenda-table td {
        text-align: center;
        vertical-align: middle;
    }
    .agenda-table td:nth-child(1),
    .agenda-table th:nth-child(1) {
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
        <h2 class="fw-bold text-dark mb-1">Manajemen Agenda</h2>
        <p class="text-muted mb-0">Kelola jadwal kegiatan warga secara terpusat.</p>
    </div>
    <a href="#" onclick="alert('Formulir Buat Agenda segera hadir!')" class="btn btn-success fw-semibold px-4 py-2 rounded-3 text-white shadow-sm d-flex align-items-center gap-2" style="background: rgba(46, 125, 50, 0.9);">
        <i class="bi bi-plus-lg"></i> Buat Agenda Baru
    </a>
</div>

{{-- Status Workflow Bento --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="bento-card">
            <div class="bento-icon" style="background: #EFEDED; color: #40493D;">
                <i class="bi bi-calendar-minus fs-5"></i>
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
                <i class="bi bi-calendar-check fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 12px; letter-spacing: 0.6px;">PUBLISHED</div>
                <div class="fw-bold text-dark" style="font-size: 24px; line-height: 1;">{{ $stats['published'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Agenda Table --}}
<div class="card card-custom shadow-sm border-0" style="border: 1px solid #BFCABA !important;">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom" style="background: #FBF9F8;">
        <h5 class="fw-bold text-dark mb-0">Daftar Agenda</h5>
        <button class="btn btn-sm btn-white border rounded-2 px-3 py-2 shadow-sm text-dark d-flex align-items-center gap-2">
            <i class="bi bi-funnel"></i> Filter
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 agenda-table">
            <colgroup>
                <col style="width: 25%;">
                <col style="width: 20%;">
                <col style="width: 25%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
            </colgroup>
            <thead class="border-bottom" style="background: #F5F3F3; border-color: #BFCABA !important;">
                <tr class="text-muted fw-bold" style="color: #40493D !important; font-size: 14px;">
                    <th scope="col" class="py-3 ps-4 pe-3">Judul Agenda</th>
                    <th scope="col" class="py-3 px-3">Tanggal & Waktu</th>
                    <th scope="col" class="py-3 px-3">Lokasi</th>
                    <th scope="col" class="py-3 px-3">Status</th>
                    <th scope="col" class="py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agenda as $item)
                    <tr class="border-bottom" style="border-color: #BFCABA !important;">
                        <td class="py-4 ps-4 pe-3">
                            <div class="fw-semibold text-dark mb-1" style="font-size: 14px;">{{ $item['judul'] }}</div>
                            <div class="text-muted" style="font-size: 12px; color: #40493D !important;">{{ $item['deskripsi'] }}</div>
                        </td>
                        <td class="py-4 px-3" style="color: #1B1C1C; font-size: 14px;">
                            {{ $item['tanggal_waktu'] }}
                        </td>
                        <td class="py-4 px-3" style="color: #1B1C1C; font-size: 14px;">
                            {{ $item['lokasi'] }}
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada agenda yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Placeholder --}}
    <div class="d-flex justify-content-between align-items-center p-3 border-top" style="background: #FBF9F8; border-color: #BFCABA !important;">
        <div class="text-muted" style="font-size: 12px; color: #40493D !important;">
            Menampilkan 1-3 dari 65 agenda
        </div>
        <div class="d-flex gap-1">
            <button class="btn btn-sm btn-white border rounded-1 px-3 py-1" style="font-size: 12px; color: #40493D; opacity: 0.5;" disabled>Sebelumnya</button>
            <button class="btn btn-sm rounded-1 px-3 py-1 text-white" style="background: #0D631B; font-size: 12px;">1</button>
            <button class="btn btn-sm btn-white border rounded-1 px-3 py-1" style="font-size: 12px; color: #40493D;">2</button>
            <button class="btn btn-sm btn-white border rounded-1 px-3 py-1" style="font-size: 12px; color: #40493D;">Selanjutnya</button>
        </div>
    </div>
</div>
@endsection
