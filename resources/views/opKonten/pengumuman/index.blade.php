@extends('layouts.global')

@section('title', 'Manajemen Pengumuman')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    /* Quill Styling */
    .ql-toolbar.ql-snow {
        border-color: #BFCABA;
        background-color: white;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    .ql-container.ql-snow {
        border-color: #BFCABA;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }
    .ql-editor.ql-blank::before {
        color: rgba(64, 73, 61, 0.50);
        font-style: normal;
    }
    .quill-wrapper:focus-within .ql-container.ql-snow,
    .quill-wrapper:focus-within .ql-toolbar.ql-snow {
        border-color: #0D631B;
    }
    
    /* Custom Blockquote (Highlight Hijau) */
    .ql-editor blockquote,
    #detailKonten blockquote {
        padding: 1rem !important;
        margin-top: 1.5rem !important;
        margin-bottom: 1.5rem !important;
        border-radius: 0 0.5rem 0.5rem 0 !important;
        border-left: 4px solid #198754 !important;
        background-color: #F0FDF4 !important;
        color: #166534 !important;
        font-size: 14px !important;
        font-style: normal !important;
    }
    
    /* Styling for Pengumuman Table */
    .pengumuman-table th,
    .pengumuman-table td {
        text-align: center;
        vertical-align: middle;
    }
    
    /* Custom SweetAlert Styling */
    .custom-swal-popup {
        border-radius: 1rem !important;
        border: none !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        padding-bottom: 0 !important;
    }
    .custom-swal-popup .swal2-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        margin-top: 1rem !important;
    }
    .custom-swal-popup .swal2-html-container {
        font-size: 0.875rem !important;
        color: #6B7280 !important;
        margin-top: 0.5rem !important;
        margin-bottom: 1.5rem !important;
    }
    .custom-swal-popup .swal2-actions {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
        width: 100% !important;
        padding: 0 1.5rem 1.5rem 1.5rem !important;
        margin-top: 0 !important;
    }
    .btn-cancel-swal {
        border: 1px solid #707A6C !important;
        color: #1B1C1C !important;
        background-color: white !important;
    }
    .btn-cancel-swal:hover {
        background-color: #F3F4F6 !important;
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

    /* Modal Backdrop Blur */
    .modal-backdrop {
        backdrop-filter: blur(5px) !important;
        background-color: rgba(0, 0, 0, 0.4) !important;
    }
    .modal-backdrop.show {
        opacity: 1 !important;
    }

    /* Custom Gradients */
    .thead-green-gradient {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
    }
    .thead-green-gradient th {
        background-color: transparent !important;
        color: white !important;
        border: none !important;
    }
    .btn-gradient-green {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
        border: none !important;
        transition: opacity 0.2s;
    }
    .btn-gradient-green:hover {
        opacity: 0.9;
        color: white !important;
    }

    .btn-draft:hover {
        background-color: #E5E7EB !important;
        color: #1B1C1C !important;
    }
    .btn-hover-gray {
        color: #374151 !important;
        transition: all 0.2s;
    }
    .btn-hover-gray:hover {
        background-color: #F3F4F6 !important;
        border-color: #D1D5DB !important;
        color: #111827 !important;
    }

    /* Toggle Switch Priority */
    .form-check-input.priority-toggle {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    .form-check-input.priority-toggle:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Pengumuman</h2>
        <p class="text-muted mb-0">Kelola dan terbitkan pengumuman penting untuk warga.</p>
    </div>
    <button type="button" onclick="openCreateModal()" class="btn btn-gradient-green fw-semibold px-4 py-2 rounded-3 text-white shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Pengumuman Baru
    </button>
</div>

{{-- Status Workflow Bento (Synced with Agenda) --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #EFEDED; color: #40493D;">
                <i class="bi bi-file-earmark-text fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">DRAF</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['draft'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #FFDDB5; color: #2A1800;">
                <i class="bi bi-hourglass-split fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">DITINJAU</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['pending_rw'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #FEE2E2; color: #991B1B;">
                <i class="bi bi-exclamation-triangle fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #991B1B; font-size: 11px; letter-spacing: 0.6px;">REVISI</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['revisi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: rgba(46, 125, 50, 0.10); color: #007232;">
                <i class="bi bi-megaphone fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">TERBIT</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['published'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters (Auto-submit, no Terapkan button) --}}
<div class="card card-custom p-4 shadow-sm border-0 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <form id="filterForm" action="{{ route('opkonten.pengumuman.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
            <div style="max-width: 300px; flex-grow: 1;">
                <label class="form-label text-muted small fw-medium mb-1">Cari Judul</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" name="search" class="form-control border-start-0 ps-0 py-2 fs-6" placeholder="Cari judul pengumuman..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div>
                <label class="form-label text-muted small fw-medium mb-1">Status</label>
                <select name="status" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 150px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Status</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draf</option>
                    <option value="Review" {{ request('status') == 'Review' ? 'selected' : '' }}>Ditinjau</option>
                    <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                    <option value="Publish" {{ request('status') == 'Publish' ? 'selected' : '' }}>Terbit</option>
                </select>
            </div>
            <div>
                <label class="form-label text-muted small fw-medium mb-1">Prioritas</label>
                <select name="priority" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 160px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Prioritas</option>
                    <option value="Penting" {{ request('priority') == 'Penting' ? 'selected' : '' }}>Penting</option>
                    <option value="Biasa" {{ request('priority') == 'Biasa' ? 'selected' : '' }}>Biasa</option>
                </select>
            </div>
            <div>
                <label class="form-label text-muted small fw-medium mb-1">Tanggal</label>
                <input type="date" name="tanggal" class="form-control rounded-3 py-2 fs-6" style="width: auto; min-width: 150px;" value="{{ request('tanggal') }}" onchange="document.getElementById('filterForm').submit()">
            </div>
        </form>
    </div>
</div>

{{-- Pengumuman Table --}}
<div class="card card-custom shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 pengumuman-table">
            <colgroup>
                <col style="width: 35%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 15%;">
                <col style="width: 20%;">
            </colgroup>
            <thead class="thead-green-gradient">
                <tr class="text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Pengumuman</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Prioritas</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Tanggal</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Status</th>
                    <th scope="col" class="py-3 px-4 fw-semibold border-0 text-white text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengumuman as $item)
                    <tr class="border-bottom" style="border-color: #BFCABA !important;">
                        <td class="py-4 px-3 text-center">
                            <div class="fw-bold text-dark mb-1">{{ $item->judul_pengumuman }}</div>
                            <div class="text-muted" style="font-size: 13px; max-width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin: 0 auto;">
                                {{ Str::limit(strip_tags($item->isi_pengumuman), 60) }}
                            </div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            @if($item->is_priority)
                                <span class="badge bg-danger rounded-pill px-3 py-2 fw-medium d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                    <i class="bi bi-pin-angle-fill"></i> PENTING
                                </span>
                            @else
                                <span class="badge rounded-1 px-3 py-2 fw-medium" style="font-size: 12px; background: #E5E7EB; color: #374151;">
                                    Biasa
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-3 text-center">
                            <div class="fw-semibold" style="color: #1B1C1C;">{{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y') }}</div>
                            <div class="text-muted" style="font-size: 13px;">{{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB</div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            @php
                                $statusColors = [
                                    'Draft' => ['bg' => '#F3F4F6', 'color' => '#6B7280', 'label' => 'Draf'],
                                    'Review' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Ditinjau'],
                                    'Revisi' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'label' => 'Revisi'],
                                    'Publish' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'label' => 'Terbit'],
                                ];
                                $s_bg = $statusColors[$item->status]['bg'] ?? '#F3F4F6';
                                $s_color = $statusColors[$item->status]['color'] ?? '#6B7280';
                                $s_label = $statusColors[$item->status]['label'] ?? $item->status;
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-1" style="background: {{ $s_bg }}; color: {{ $s_color }};">
                                <span class="rounded-circle" style="width: 6px; height: 6px; display: inline-block; background: {{ $s_color }};"></span>
                                {{ $s_label }}
                            </span>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <button type="button" class="btn btn-sm rounded-3 fw-semibold px-3" style="font-size: 12px; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;" onclick="openDetailModal('{{ $item->id }}')">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-megaphone fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada pengumuman yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination (Synced with Agenda style) --}}
    @if($pengumuman->total() > 0)
    <div class="d-flex justify-content-between align-items-center p-3 border-top" style="background: #FBF9F8; border-color: #E5E7EB !important;">
        <div class="text-muted" style="font-size: 12px; color: #40493D !important;">
            Menampilkan {{ $pengumuman->firstItem() ?? 0 }}-{{ $pengumuman->lastItem() ?? 0 }} dari {{ $pengumuman->total() }} pengumuman
        </div>
        <div class="d-flex gap-1">
            @if (!$pengumuman->onFirstPage())
                <a href="{{ $pengumuman->previousPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Sebelumnya</a>
            @endif

            @php
                $start = max(1, $pengumuman->currentPage() - 2);
                $end = min($pengumuman->lastPage(), $pengumuman->currentPage() + 2);
            @endphp
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $pengumuman->currentPage())
                    <button class="btn btn-sm rounded-1 px-3 py-1 text-white btn-gradient-green" style="font-size: 12px; cursor: default;">{{ $page }}</button>
                @else
                    <a href="{{ $pengumuman->url($page) }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">{{ $page }}</a>
                @endif
            @endfor

            @if ($pengumuman->hasMorePages())
                <a href="{{ $pengumuman->nextPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Selanjutnya</a>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal Buat/Edit Pengumuman --}}
<div class="modal fade" id="modalPengumuman" tabindex="-1" data-bs-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formPengumuman" action="{{ route('opkonten.pengumuman.store') }}" method="POST" class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" id="pengumumanIdInput">
                <input type="hidden" name="action" id="formAction" value="draft">
                
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold" id="modalTitle">Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close" aria-label="Close" onclick="confirmCloseModal()"></button>
                </div>
                
                <div class="modal-body px-4 py-4">
                    {{-- Alert Catatan Revisi --}}
                    <div id="alertCatatanRevisi" class="alert alert-danger mb-4 d-none border-0 shadow-sm" style="background-color: #FEF2F2; color: #991B1B; border-radius: 8px;">
                        <div class="d-flex gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-4 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Catatan Revisi dari RW:</h6>
                                <p id="teksCatatanRevisi" class="mb-0 small" style="font-size: 14px;"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Judul Pengumuman</label>
                            <input type="text" name="judul_pengumuman" id="judulPengumuman" class="form-control" placeholder="Masukkan judul pengumuman..." required>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background: #FDF4F5; border: 1px solid #F8D7DA;">
                                <div>
                                    <h6 class="fw-bold text-danger mb-1 d-flex align-items-center gap-2">
                                        <i class="bi bi-pin-angle-fill"></i> Tandai Sebagai Pengumuman Penting
                                    </h6>
                                    <p class="text-muted small mb-0">Pengumuman penting akan selalu ditampilkan di urutan teratas pada aplikasi warga.</p>
                                </div>
                                <div class="form-check form-switch fs-4 mb-0">
                                    <input class="form-check-input priority-toggle shadow-none" type="checkbox" role="switch" id="isPriorityToggle" name="is_priority" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Isi Pengumuman</label>
                            <input type="hidden" name="isi_pengumuman" id="isiPengumumanInput">
                            <div class="quill-wrapper bg-light rounded-3">
                                <div id="editor-container" style="height: 200px; background-color: white;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end gap-2" style="border-color: #BFCABA !important;">
                    <button type="button" class="btn btn-draft fw-semibold" style="border: 1px solid #707A6C; color: #1B1C1C;" onclick="confirmCloseModal()">Batal</button>
                    <button type="submit" class="btn btn-gradient-green fw-semibold px-4 text-white d-flex align-items-center gap-2">
                        <i class="bi bi-save"></i> Simpan ke Draf
                    </button>
                </div>
        </form>
    </div>
</div>

{{-- Modal Konfirmasi Batal / Close (Synced with Agenda) --}}
<div class="modal fade" id="confirmCloseModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Buang Perubahan?</h5>
                <p class="text-muted small mb-4">Anda memiliki perubahan yang belum disimpan. Yakin ingin menutup form ini?</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-draft fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #707A6C; color: #1B1C1C;" onclick="hideConfirmPopup()">Lanjutkan Mengedit</button>
                    <button type="button" class="btn btn-danger text-white" onclick="forceCloseModal()">Buang Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Toast Notification (Centered, synced with Agenda) --}}
<div id="customToast" class="position-fixed top-50 start-50 translate-middle p-3 rounded-3 shadow-lg" style="background: rgba(13, 99, 27, 0.95); color: white; z-index: 9999; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5" id="customToastIcon"></i>
        <span id="customToastMessage" class="fw-semibold">Berhasil!</span>
    </div>
</div>

{{-- Modal Detail Pengumuman (Synced with Agenda) --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold" id="modalDetailLabel">Detail Pengumuman</h5>
                <div class="d-flex align-items-center gap-3">
                    <span id="detailStatus" class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle status-dot" style="width: 6px; height: 6px; display: inline-block;"></span>
                        <span class="status-text">Draf</span>
                    </span>
                    <span id="detailPriority" class="badge bg-danger rounded-pill px-2 py-1 d-none align-items-center gap-1" style="font-size: 11px;">
                        <i class="bi bi-pin-angle-fill"></i> PENTING
                    </span>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            
            <div class="modal-body px-4 py-4">
                {{-- Notifikasi Catatan Revisi --}}
                <div id="alertDetailCatatanRevisi" class="alert alert-danger mb-4 d-none border-0 shadow-sm" style="background-color: #FEF2F2; color: #991B1B; border-radius: 8px;">
                    <div class="d-flex gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4 mt-1"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Catatan Revisi dari RW:</h6>
                            <p id="teksDetailCatatanRevisi" class="mb-0 small" style="font-size: 14px;"></p>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Judul Pengumuman</label>
                        <input type="text" id="detailJudul" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Tanggal Dibuat</label>
                        <input type="text" id="detailTanggal" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Tingkat Prioritas</label>
                        <input type="text" id="detailPrioritas" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-12">
                        <span class="d-block text-muted small mb-2 fw-semibold">Isi Pengumuman</span>
                        <div id="detailKonten" class="p-4 rounded-3 text-dark bg-white" style="border: 1px solid #E5E7EB; min-height: 150px; font-size: 15px; line-height: 1.6;"></div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end" style="border-color: #BFCABA !important;">
                <div id="detailFooterActions" class="w-100 d-flex justify-content-end gap-2">
                    <!-- Actions will be injected via JS -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    // Data from Laravel to JS
    const dummyPengumumanData = @json($pengumuman->items());
    
    let formDirty = false;
    let allowModalClose = false;

    // Initialize Quill
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Tuliskan isi pengumuman di sini...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['blockquote', 'link'],
                ['clean']
            ]
        }
    });

    // Track quill changes
    quill.on('text-change', function() {
        formDirty = true;
    });
    let pengumumanModal = null;
    let confirmModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Remove manual backdrop static so hide.bs.modal fires on backdrop click
        document.getElementById('modalPengumuman').setAttribute('data-bs-backdrop', 'true');

        pengumumanModal = new bootstrap.Modal(document.getElementById('modalPengumuman'));
        confirmModal = new bootstrap.Modal(document.getElementById('confirmCloseModal'));

        // Intercept modal close event (X button, backdrop, or ESC)
        document.getElementById('modalPengumuman').addEventListener('hide.bs.modal', function (event) {
            if (allowModalClose) {
                allowModalClose = false;
                return;
            }
            if (formDirty) {
                event.preventDefault();
                confirmModal.show();
            }
        });

        // Dirty form listeners
        const form = document.getElementById('formPengumuman');
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', () => formDirty = true);
        });

        // Sync Quill on submit
        form.addEventListener('submit', function(e) {
            document.getElementById('isiPengumumanInput').value = quill.root.innerHTML;
        });

        // Debounce search input
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 600);
            });
        }

        // Toast
        @if(session('success'))
            showNotification("{!! addslashes(session('success')) !!}");
        @endif
        @if($errors->any())
            showNotification("{!! addslashes($errors->first()) !!}", true);
        @endif
        @if(session('error'))
            showNotification("{!! addslashes(session('error')) !!}", true);
        @endif
    });

    function showNotification(message, isError = false) {
        const toast = document.getElementById('customToast');
        const toastMsg = document.getElementById('customToastMessage');
        if (toast && toastMsg) {
            toastMsg.textContent = message;
            toast.style.background = isError ? 'rgba(220, 53, 69, 0.95)' : 'rgba(13, 99, 27, 0.95)';
            document.getElementById('customToastIcon').className = isError ? 'bi bi-exclamation-triangle-fill fs-5' : 'bi bi-check-circle-fill fs-5';
            
            toast.style.opacity = '1';

            // Sembunyikan setelah 3 detik
            setTimeout(() => {
                toast.style.opacity = '0';
            }, 3000);
        }
    }

    function openCreateModal() {
        formDirty = false;
        allowModalClose = false;
        const form = document.getElementById('formPengumuman');
        form.reset();
        quill.setText('');
        
        document.getElementById('formMethod').value = 'POST';
        form.action = "{{ route('opkonten.pengumuman.store') }}";
        document.getElementById('pengumumanIdInput').value = '';
        document.getElementById('alertCatatanRevisi').classList.add('d-none');
        
        document.getElementById('modalTitle').textContent = 'Buat Pengumuman Baru';
        pengumumanModal.show();
    }

    function editPengumuman(item) {
        formDirty = false;
        allowModalClose = false;
        const form = document.getElementById('formPengumuman');
        form.reset();
        
        document.getElementById('formMethod').value = 'PUT';
        form.action = `/op-konten/pengumuman/${item.id}`;
        document.getElementById('pengumumanIdInput').value = item.id;
        document.getElementById('modalTitle').textContent = 'Edit Pengumuman';
        
        document.getElementById('judulPengumuman').value = item.judul_pengumuman;
        document.getElementById('isPriorityToggle').checked = item.is_priority;
        quill.root.innerHTML = item.isi_pengumuman;
        
        // Catatan Revisi
        const alertRevisi = document.getElementById('alertCatatanRevisi');
        if (item.status === 'Revisi' && item.catatan_revisi) {
            alertRevisi.classList.remove('d-none');
            document.getElementById('teksCatatanRevisi').textContent = item.catatan_revisi;
        } else {
            alertRevisi.classList.add('d-none');
        }
        
        pengumumanModal.show();
    }

    function editPengumumanFromJson(id) {
        const item = dummyPengumumanData.find(a => a.id == id);
        if(item) {
            const detailModalEl = bootstrap.Modal.getInstance(document.getElementById('modalDetail'));
            if(detailModalEl) detailModalEl.hide();
            editPengumuman(item);
        }
    }

    function openDetailModal(id) {
        const item = dummyPengumumanData.find(a => a.id == id);
        if (!item) return;

        // Status
        const statusBadge = document.getElementById('detailStatus');
        const statusColors = {
            'Draft': { bg: '#F3F4F6', color: '#6B7280', label: 'Draf' },
            'Review': { bg: '#FEF3C7', color: '#D97706', label: 'Ditinjau' },
            'Revisi': { bg: '#FEE2E2', color: '#991B1B', label: 'Revisi' },
            'Publish': { bg: '#D1FAE5', color: '#065F46', label: 'Terbit' }
        };
        const s_bg = statusColors[item.status]?.bg || '#F3F4F6';
        const s_color = statusColors[item.status]?.color || '#6B7280';
        const s_label = statusColors[item.status]?.label || item.status;
        
        statusBadge.style.background = s_bg;
        statusBadge.style.color = s_color;
        statusBadge.querySelector('.status-dot').style.background = s_color;
        statusBadge.querySelector('.status-text').textContent = s_label;

        // Priority badge in header
        const priorityBadge = document.getElementById('detailPriority');
        if (item.is_priority) {
            priorityBadge.classList.remove('d-none');
            priorityBadge.classList.add('d-inline-flex');
        } else {
            priorityBadge.classList.add('d-none');
            priorityBadge.classList.remove('d-inline-flex');
        }

        // Fields
        document.getElementById('detailJudul').value = item.judul_pengumuman;
        document.getElementById('detailKonten').innerHTML = item.isi_pengumuman || '<p class="text-muted italic">Tidak ada konten</p>';
        document.getElementById('detailPrioritas').value = item.is_priority ? '📌 Penting (Pin to Top)' : 'Biasa';

        // Date
        const dateStr = new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
        document.getElementById('detailTanggal').value = dateStr;

        // Revisi Notes
        const alertRevisi = document.getElementById('alertDetailCatatanRevisi');
        if (item.status === 'Revisi' && item.catatan_revisi) {
            alertRevisi.classList.remove('d-none');
            document.getElementById('teksDetailCatatanRevisi').textContent = item.catatan_revisi;
        } else {
            alertRevisi.classList.add('d-none');
        }

        // Actions Footer (Synced with Agenda pattern)
        const detailFooter = document.getElementById('detailFooterActions');
        let actionsHtml = '';
        
        if (item.status === 'Draft' || item.status === 'Revisi') {
            actionsHtml += `
                <button type="button" class="btn btn-outline-dark fw-semibold px-4 d-flex align-items-center gap-2 btn-hover-gray" style="border-color: #D1D5DB; background: white;" onclick="editPengumumanFromJson('${item.id}')">
                    <i class="bi bi-pencil"></i> Edit Pengumuman
                </button>
                <form action="/op-konten/pengumuman/${item.id}/submit" method="POST" class="d-inline" id="formSubmit_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn fw-semibold px-4 text-white btn-gradient-green" onclick="confirmSubmit('${item.id}')">
                        Ajukan Pengumuman
                    </button>
                </form>
            `;
            if (item.status === 'Draft') {
                actionsHtml += `
                    <form action="/op-konten/pengumuman/${item.id}" method="POST" class="d-inline ms-auto" id="formDelete_${item.id}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="button" class="btn btn-outline-danger fw-semibold px-4" onclick="confirmDelete('${item.id}')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                `;
            }
        } else if (item.status === 'Review') {
            actionsHtml += `
                <form action="/op-konten/pengumuman/${item.id}/cancel-submit" method="POST" class="d-inline" id="formCancelSubmit_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-outline-warning fw-semibold px-4" onclick="confirmCancelSubmit('${item.id}')">
                        <i class="bi bi-arrow-return-left"></i> Batalkan Pengajuan
                    </button>
                </form>
            `;
        }

        detailFooter.innerHTML = actionsHtml;

        const detailModal = new bootstrap.Modal(document.getElementById('modalDetail'));
        detailModal.show();
    }

    function confirmCloseModal() {
        if (formDirty) {
            confirmModal.show();
        } else {
            forceCloseModal();
        }
    }

    function hideConfirmPopup() {
        confirmModal.hide();
    }

    // Sweet Alerts (Synced with Agenda)
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Pengumuman Ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'btn btn-danger text-white fw-semibold w-100 m-0',
                cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDelete_' + id).submit();
        });
    }

    function confirmSubmit(id) {
        Swal.fire({
            title: 'Ajukan pengumuman ke Ketua RW?',
            text: "Pengumuman akan masuk tahap review dan menunggu persetujuan Ketua RW.",
            icon: 'question',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, ajukan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'btn text-white btn-gradient-green fw-semibold w-100 m-0',
                cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formSubmit_' + id).submit();
        });
    }

    function confirmCancelSubmit(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Pengumuman akan ditarik kembali menjadi status Draf.",
            icon: 'info',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'btn btn-warning text-dark fw-semibold w-100 m-0',
                cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formCancelSubmit_' + id).submit();
        });
    }

    function forceCloseModal() {
        allowModalClose = true;
        formDirty = false;
        if (confirmModal) confirmModal.hide();
        if (pengumumanModal) pengumumanModal.hide();
    }

    document.getElementById('modalPengumuman').addEventListener('hide.bs.modal', function (e) {
        if (formDirty && !allowModalClose) {
            e.preventDefault();
            confirmModal.show();
        }
    });
</script>
@endpush
