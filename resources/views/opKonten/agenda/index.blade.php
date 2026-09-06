@extends('layouts.global')

@section('title', 'Manajemen Agenda')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
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
    
    /* Styling for Agenda Modal & Table */
    .agenda-table th,
    .agenda-table td {
        text-align: center;
        vertical-align: middle;
    }
    .agenda-table td:nth-child(1),
    .agenda-table th:nth-child(1) {
        text-align: left;
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

    /* Drag and Drop Image Box */
    .drop-zone {
        border: 2px dashed #BFCABA;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .drop-zone:hover, .drop-zone.dragover {
        background: #f8f9fa;
        border-color: #0D631B;
    }
    
    .image-preview-container {
        border: 1px solid #BFCABA;
        border-radius: 8px;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8f9fa;
    }
    .image-preview-container img {
        height: 50px;
        width: 50px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 12px;
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
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Agenda</h2>
        <p class="text-muted mb-0">Kelola jadwal kegiatan warga secara terpusat.</p>
    </div>
    <button type="button" onclick="openBuatAgendaModal()" class="btn btn-gradient-green fw-semibold px-4 py-2 rounded-3 text-white shadow-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Buat Agenda Baru
    </button>
</div>

{{-- Status Workflow Bento --}}
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
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['revisi'] ?? 0 }}</div>
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

{{-- Filters --}}
<div class="card card-custom p-4 shadow-sm border-0 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <form id="filterForm" action="{{ route('opkonten.agenda.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
            <div style="max-width: 300px; flex-grow: 1;">
                <label class="form-label text-muted small fw-medium mb-1">Cari Judul</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" name="search" class="form-control border-start-0 ps-0 py-2 fs-6" placeholder="Cari judul agenda..." value="{{ request('search') }}" autocomplete="off">
                </div>
            </div>
            <div>
                <label class="form-label text-muted small fw-medium mb-1">Tanggal</label>
                <input type="date" name="tanggal" class="form-control rounded-3 py-2 fs-6" style="width: auto; min-width: 150px;" value="{{ request('tanggal') }}" onchange="document.getElementById('filterForm').submit()">
            </div>
            <div>
                <label class="form-label text-muted small fw-medium mb-1">Kategori</label>
                <select name="kategori" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 160px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua Kategori</option>
                    <option value="Sosial" {{ request('kategori') == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                    <option value="Infrastruktur" {{ request('kategori') == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                    <option value="Hiburan" {{ request('kategori') == 'Hiburan' ? 'selected' : '' }}>Hiburan</option>
                    <option value="Kesehatan" {{ request('kategori') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="Keamanan" {{ request('kategori') == 'Keamanan' ? 'selected' : '' }}>Keamanan</option>
                    <option value="Organisasi" {{ request('kategori') == 'Organisasi' ? 'selected' : '' }}>Organisasi</option>
                </select>
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
        </form>
    </div>
</div>

{{-- Agenda Table --}}
<div class="card card-custom shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 agenda-table">
            <colgroup>
                <col style="width: 25%;">
                <col style="width: 15%;">
                <col style="width: 20%;">
                <col style="width: 15%;">
                <col style="width: 10%;">
                <col style="width: 15%;">
            </colgroup>
            <thead class="thead-green-gradient">
                <tr class="text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Judul Agenda</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Kategori</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Tanggal & Waktu</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Lokasi</th>
                    <th scope="col" class="py-3 px-3 fw-semibold border-0 text-white text-center">Status</th>
                    <th scope="col" class="py-3 px-4 fw-semibold border-0 text-white text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agenda as $item)
                    <tr class="border-bottom" style="border-color: #BFCABA !important;">
                        <td class="py-4 px-3 text-center">
                            <div class="fw-bold text-dark mb-1">{{ $item->judul_agenda }}</div>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <span class="badge rounded-1 px-3 py-2 fw-medium" style="font-size: 12px; background: #E5E7EB; color: #374151;">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="py-4 px-3 text-center">
                            <div class="fw-semibold" style="color: #1B1C1C;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}</div>
                            <div class="text-muted" style="font-size: 13px;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('H:i') }} WIB</div>
                        </td>
                        <td class="py-4 px-3 text-center" style="color: #1B1C1C; font-size: 14px;">
                            <i class="bi bi-geo-alt me-1 text-muted"></i> {{ $item->lokasi }}
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
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada agenda yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($agenda->total() > 0)
    <div class="d-flex justify-content-between align-items-center p-3 border-top" style="background: #FBF9F8; border-color: #E5E7EB !important;">
        <div class="text-muted" style="font-size: 12px; color: #40493D !important;">
            Menampilkan {{ $agenda->firstItem() ?? 0 }}-{{ $agenda->lastItem() ?? 0 }} dari {{ $agenda->total() }} agenda
        </div>
        <div class="d-flex gap-1">
            @if (!$agenda->onFirstPage())
                <a href="{{ $agenda->previousPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Sebelumnya</a>
            @endif

            @php
                $start = max(1, $agenda->currentPage() - 2);
                $end = min($agenda->lastPage(), $agenda->currentPage() + 2);
            @endphp
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $agenda->currentPage())
                    <button class="btn btn-sm rounded-1 px-3 py-1 text-white btn-gradient-green" style="font-size: 12px; cursor: default;">{{ $page }}</button>
                @else
                    <a href="{{ $agenda->url($page) }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">{{ $page }}</a>
                @endif
            @endfor

            @if ($agenda->hasMorePages())
                <a href="{{ $agenda->nextPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Selanjutnya</a>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal Buat/Edit Agenda --}}
<div class="modal fade" id="modalBuatAgenda" tabindex="-1" data-bs-backdrop="true" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formAgenda" action="{{ route('opkonten.agenda.store') }}" method="POST" enctype="multipart/form-data" class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="action" id="formAction" value="draft">
                <input type="hidden" name="remove_banner" id="removeBannerInput" value="0">
                
                <div class="modal-header border-bottom px-4 py-3">
                    <h5 class="modal-title fw-bold" id="modalTitle">Buat Agenda Baru</h5>
                    <button type="button" class="btn-close" aria-label="Close" onclick="confirmCloseModal()"></button>
                </div>
                
                <div class="modal-body px-4 py-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Judul Agenda</label>
                            <input type="text" name="judul_agenda" id="judulAgenda" class="form-control" placeholder="Masukkan judul agenda..." required>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Kategori Agenda</label>
                            <select name="kategori" id="kategoriAgenda" class="form-select" required>
                                <option value="">Pilih kategori...</option>
                                <option value="Sosial">Sosial</option>
                                <option value="Infrastruktur">Infrastruktur</option>
                                <option value="Hiburan">Hiburan</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Keamanan">Keamanan</option>
                                <option value="Organisasi">Organisasi</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggalAgenda" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Waktu</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="time" name="waktu_mulai" id="waktuMulai" class="form-control" required>
                                <span>-</span>
                                <input type="time" name="waktu_selesai" id="waktuSelesai" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark" style="font-size: 14px;">Lokasi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-geo-alt"></i></span>
                                <input type="text" name="lokasi" id="lokasiAgenda" class="form-control border-start-0 ps-0" placeholder="Masukkan detail lokasi..." required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Aktifkan Peta Lokasi</h6>
                                    <p class="text-muted small mb-0">Tampilkan peta interaktif untuk menentukan titik koordinat presisi.</p>
                                </div>
                                <div class="form-check form-switch fs-4 m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="mapToggle" style="cursor: pointer;">
                                </div>
                            </div>

                            <div id="mapContainerWrapper" style="display: none;">
                                <label class="form-label fw-medium text-dark mb-1" style="font-size: 14px;">Tentukan Titik Peta</label>
                                <p class="text-muted small mb-2">Cari alamat di ikon pencarian peta, klik pada peta, atau geser pin.</p>
                                <input type="hidden" name="latitude" id="latitudeInput">
                                <input type="hidden" name="longitude" id="longitudeInput">
                                <div id="mapAgenda" class="rounded-3 border" style="height: 250px; width: 100%; z-index: 1;"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 rounded-3 border bg-white d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Konfirmasi Kehadiran</h6>
                                    <p class="text-muted small mb-0">Aktifkan agar warga dapat mengonfirmasi kehadiran mereka sebelum acara dimulai.</p>
                                </div>
                                <div class="form-check form-switch fs-4 m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="is_rsvp_enabled" id="rsvpToggle" style="cursor: pointer;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="fw-semibold text-dark mb-2">Deskripsi Agenda <span class="text-danger">*</span></label>
                            <input type="hidden" name="detail_pengumuman" id="deskripsiAgendaInput">
                            <div class="quill-wrapper bg-light rounded-3">
                                <div id="editor-container" style="height: 250px; background-color: white;"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-medium text-dark mb-1" style="font-size: 14px;">Lampiran Flyer/Poster (Opsional)</label>
                            
                            <!-- Existing File Preview (For Edit) -->
                            <div id="existingFilePreview" class="image-preview-container mb-3 d-none">
                                <div class="d-flex align-items-center">
                                    <img id="previewImg" src="" alt="Lampiran" style="cursor: pointer;" onclick="openLightbox(this.src)">
                                    <span id="previewFilename" class="text-muted small">filename.jpg</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-link text-danger" onclick="removeExistingFile()">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </div>

                            <!-- Upload Box -->
                            <input type="file" name="banner_flyer" id="bannerFlyer" class="d-none" accept="image/*">
                            <div id="dropZone" class="p-4 text-center rounded-3 bg-white" style="border: 2px dashed #BFCABA; cursor: pointer; transition: all 0.2s;">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; background: rgba(13, 99, 27, 0.1);">
                                    <i class="bi bi-cloud-arrow-up fs-4 text-success"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Tarik dan lepas gambar di sini, atau klik untuk mencari</h6>
                                <p class="text-muted small mb-0" style="font-size: 12px;">Format didukung: JPG, PNG, WEBP (Maks 5MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end" style="border-color: #BFCABA !important;">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-dark fw-semibold px-4 btn-hover-gray" style="border-color: #D1D5DB; background: white;" onclick="confirmCloseModal()">Batal</button>
                        <button type="submit" class="btn btn-draft fw-semibold px-4" style="border: 1px solid #707A6C; color: #1B1C1C;" onclick="document.getElementById('formAction').value='draft'">Simpan ke Draf</button>
                        <button type="submit" class="btn fw-semibold px-4 text-white btn-gradient-green" onclick="document.getElementById('formAction').value='review'">Ajukan Agenda</button>
                    </div>
                </div>
            </form>

    </div>
</div>

{{-- Modal Konfirmasi Keluar --}}
<div class="modal fade" id="confirmCloseModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-2">Buang Perubahan?</h5>
                <p class="text-muted small mb-4">Ada perubahan yang belum disimpan. Apakah Anda yakin ingin membuang perubahan ini?</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-draft fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #707A6C; color: #1B1C1C;" onclick="hideConfirmPopup()">Lanjutkan Mengedit</button>
                    <button type="button" class="btn btn-danger text-white" onclick="forceCloseModal()">Buang Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Custom Toast Notification --}}
<div id="customToast" class="position-fixed top-50 start-50 translate-middle p-3 rounded-3 shadow-lg" style="background: rgba(13, 99, 27, 0.95); color: white; z-index: 9999; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5" id="customToastIcon"></i>
        <span id="customToastMessage" class="fw-semibold">Berhasil!</span>
    </div>
</div>

{{-- Modal Detail Agenda --}}
<div class="modal fade" id="modalDetailAgenda" tabindex="-1" aria-labelledby="modalDetailAgendaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold" id="modalDetailAgendaLabel">Detail Agenda</h5>
                <div class="d-flex align-items-center gap-3">
                    <span id="detailStatus" class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                        <span class="rounded-circle status-dot" style="width: 6px; height: 6px; display: inline-block;"></span>
                        <span class="status-text">Publish</span>
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
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Judul Agenda</label>
                        <input type="text" id="detailJudul" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Kategori Agenda</label>
                        <input type="text" id="detailKategori" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Tanggal</label>
                        <input type="text" id="detailTanggal" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Waktu</label>
                        <input type="text" id="detailWaktu" class="form-control bg-white" readonly disabled>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Lokasi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" id="detailLokasi" class="form-control border-start-0 ps-0 bg-white" readonly disabled>
                        </div>
                    </div>

                    <div class="col-12" id="detailMapContainer" style="display: none;">
                        <label class="form-label fw-medium text-dark mb-2" style="font-size: 14px;">Titik Lokasi Peta</label>
                        <div id="mapDetailAgenda" class="rounded-3 border" style="height: 200px; width: 100%; z-index: 1;"></div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light border">
                            <i class="bi bi-person-check fs-5 text-success"></i>
                            <div>
                                <span class="d-block fw-semibold" style="font-size: 13px;">Konfirmasi Kehadiran</span>
                                <span id="detailRsvpStatus" class="d-block text-muted" style="font-size: 12px;">Aktif</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <span class="d-block text-muted small mb-2 fw-semibold">Deskripsi Agenda</span>
                        <div id="detailKonten" class="p-4 rounded-3 text-dark bg-white" style="border: 1px solid #E5E7EB; min-height: 150px; font-size: 15px; line-height: 1.6;"></div>
                    </div>
                    
                    <div class="col-12 d-none" id="detailImageContainer">
                        <label class="form-label fw-medium text-dark mb-2" style="font-size: 14px;">Lampiran Flyer/Poster</label>
                        <div class="rounded-3 overflow-hidden border bg-white text-center p-2">
                            <img id="detailImage" src="" alt="Lampiran" class="img-fluid rounded" style="max-height: 300px; object-fit: contain; cursor: pointer;" onclick="openLightbox(this.src)">
                        </div>
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

{{-- Lightbox Modal --}}
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center position-relative">
                <button type="button" class="btn-close position-absolute bg-white rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close" style="top: 8px; right: 8px; padding: 8px; font-size: 12px; z-index: 1055; opacity: 0.9;"></button>
                <img id="lightboxImage" src="" class="img-fluid rounded shadow-lg" alt="Enlarged view" style="cursor: pointer;" data-bs-dismiss="modal" title="Klik untuk menutup">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    let formDirty = false;
    let allowModalClose = false;
    
    // Map variables
    let formMap = null;
    let formMarker = null;
    let detailMap = null;
    let detailMarker = null;
    
    // Initialize Quill
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Tuliskan detail agenda di sini...',
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
    let agendaModal = null;
    let confirmModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Hapus manual backdrop static agar hide.bs.modal terpanggil saat backdrop di-klik
        document.getElementById('modalBuatAgenda').setAttribute('data-bs-backdrop', 'true');

        agendaModal = new bootstrap.Modal(document.getElementById('modalBuatAgenda'));
        confirmModal = new bootstrap.Modal(document.getElementById('confirmCloseModal'));

        // Intercept modal close event (X button, backdrop, or ESC)
        document.getElementById('modalBuatAgenda').addEventListener('hide.bs.modal', function (event) {
            if (allowModalClose) {
                allowModalClose = false; // Reset for next time
                return; // Allow closing
            }

            // Hanya tampilkan pop-up peringatan jika ada perubahan yang belum disimpan
            if (formDirty) {
                event.preventDefault(); // Stop modal from closing
                confirmModal.show();
            }
        });

        // Efek blur pada form utama saat pop-up konfirmasi muncul
        const confirmModalEl = document.getElementById('confirmCloseModal');
        if (confirmModalEl) {
            confirmModalEl.addEventListener('show.bs.modal', function () {
                const formContent = document.getElementById('formAgenda');
                formContent.style.filter = 'blur(5px)';
                
                // Sembunyikan backdrop lapis kedua agar tidak terlalu gelap (tumpang tindih)
                setTimeout(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    if (backdrops.length > 1) {
                        backdrops[backdrops.length - 1].style.opacity = '0';
                    }
                }, 10);
            });

            confirmModalEl.addEventListener('hidden.bs.modal', function () {
                const formContent = document.getElementById('formAgenda');
                formContent.style.filter = 'none';
            });
        }

        // Watch for form changes
        const formInputs = document.querySelectorAll('#formAgenda input:not([type="hidden"]), #formAgenda select, #formAgenda textarea');
        formInputs.forEach(input => {
            input.addEventListener('input', () => formDirty = true);
            input.addEventListener('change', () => formDirty = true);
        });

        // Map Toggle Logic
        document.getElementById('mapToggle').addEventListener('change', function() {
            const wrapper = document.getElementById('mapContainerWrapper');
            if (this.checked) {
                wrapper.style.display = 'block';
                // Trigger resize after display block to fix Leaflet rendering issues
                setTimeout(() => {
                    initFormMap(document.getElementById('latitudeInput').value, document.getElementById('longitudeInput').value);
                }, 100);
            } else {
                wrapper.style.display = 'none';
                document.getElementById('latitudeInput').value = '';
                document.getElementById('longitudeInput').value = '';
            }
            formDirty = true;
        });

        // Sync Quill content before submit
        document.getElementById('formAgenda').addEventListener('submit', function(e) {
            document.getElementById('deskripsiAgendaInput').value = quill.root.innerHTML;
            const action = document.getElementById('formAction').value;
            if (action === 'review') {
                e.preventDefault();
                Swal.fire({
                    title: 'Ajukan agenda ke Ketua RW?',
                    html: 'Agenda akan masuk tahap review dan menunggu persetujuan Ketua RW.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, ajukan!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'custom-swal-popup',
                        confirmButton: 'btn text-white btn-gradient-green fw-semibold w-100 m-0 mb-2',
                        cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        allowModalClose = true;
                        e.target.submit();
                    }
                });
            } else {
                allowModalClose = true;
            }
        });

        // Drag and Drop Logic
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('bannerFlyer');
        const existingPreview = document.getElementById('existingFilePreview');
        const MAX_SIZE = 5 * 1024 * 1024;

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFile(e.dataTransfer.files[0]);
                formDirty = true;
            }
        });
        fileInput.addEventListener('change', function() {
            if (this.files[0]) {
                handleFile(this.files[0]);
                formDirty = true;
            }
        });

        function handleFile(file) {
            if (!file.type.startsWith('image/')) {
                showNotification('Harap unggah gambar yang valid.', true);
                return;
            }
            if (file.size > MAX_SIZE) {
                showNotification('Ukuran file maksimal 5MB.', true);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('previewFilename').textContent = file.name;
                dropZone.classList.add('d-none');
                existingPreview.classList.remove('d-none');
            }
            reader.readAsDataURL(file);
        }

        // Search debounce
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            if (searchInput.value) {
                searchInput.focus();
                const val = searchInput.value;
                searchInput.value = '';
                searchInput.value = val;
            }
            let searchTimeout;
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
        // Menutup modal hanya jika bukan error
        if (!isError) {
            closeBuatAgendaModal();
        }

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

    function closeBuatAgendaModal() {
        allowModalClose = true;
        const modalElement = document.getElementById('modalBuatAgenda');
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modalInstance.hide();
        }
    }

    // Lightbox Logic
    function openLightbox(src) {
        if (!src || src === window.location.href) return;
        const lightboxImg = document.getElementById('lightboxImage');
        lightboxImg.src = src;
        const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        modal.show();
    }

    function openBuatAgendaModal() {
        formDirty = false;
        allowModalClose = false;
        document.getElementById('formAgenda').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('formAgenda').action = "{{ route('opkonten.agenda.store') }}";
        document.getElementById('modalTitle').textContent = 'Buat Agenda Baru';
        
        document.getElementById('dropZone').classList.remove('d-none');
        document.getElementById('existingFilePreview').classList.add('d-none');
        document.getElementById('removeBannerInput').value = '0';
        
        // Reset Map
        document.getElementById('latitudeInput').value = '';
        document.getElementById('longitudeInput').value = '';
        document.getElementById('rsvpToggle').checked = false;
        document.getElementById('mapToggle').checked = false;
        document.getElementById('mapContainerWrapper').style.display = 'none';
        
        agendaModal.show();
    }

    function editAgenda(agenda) {
        formDirty = false;
        allowModalClose = false;
        document.getElementById('formAgenda').reset();
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('formAgenda').action = `/op-konten/agenda/${agenda.id}`;
        document.getElementById('modalTitle').textContent = 'Edit Agenda';
        
        document.getElementById('judulAgenda').value = agenda.judul_agenda;
        document.getElementById('kategoriAgenda').value = agenda.kategori;
        document.getElementById('lokasiAgenda').value = agenda.lokasi;
        quill.root.innerHTML = agenda.detail_pengumuman;
        
        const dateObj = new Date(agenda.tanggal_mulai);
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        document.getElementById('tanggalAgenda').value = `${yyyy}-${mm}-${dd}`;
        
        const hhMulai = String(dateObj.getHours()).padStart(2, '0');
        const minMulai = String(dateObj.getMinutes()).padStart(2, '0');
        document.getElementById('waktuMulai').value = `${hhMulai}:${minMulai}`;
        
        const endObj = new Date(agenda.tanggal_selesai);
        const hhSelesai = String(endObj.getHours()).padStart(2, '0');
        const minSelesai = String(endObj.getMinutes()).padStart(2, '0');
        document.getElementById('waktuSelesai').value = `${hhSelesai}:${minSelesai}`;

        // Handle Image
        const dropZone = document.getElementById('dropZone');
        const previewBox = document.getElementById('existingFilePreview');
        document.getElementById('removeBannerInput').value = '0';
        
        if (agenda.banner_flyer) {
            document.getElementById('previewImg').src = agenda.banner_flyer.startsWith('http') ? agenda.banner_flyer : '/' + agenda.banner_flyer;
            document.getElementById('previewFilename').textContent = agenda.banner_flyer.split('/').pop();
            dropZone.classList.add('d-none');
            previewBox.classList.remove('d-none');
        } else {
            dropZone.classList.remove('d-none');
            previewBox.classList.add('d-none');
        }
        
        document.getElementById('latitudeInput').value = agenda.latitude || '';
        document.getElementById('longitudeInput').value = agenda.longitude || '';
        document.getElementById('rsvpToggle').checked = agenda.is_rsvp_enabled == 1;

        if (agenda.latitude && agenda.longitude) {
            document.getElementById('mapToggle').checked = true;
            document.getElementById('mapContainerWrapper').style.display = 'block';
        } else {
            document.getElementById('mapToggle').checked = false;
            document.getElementById('mapContainerWrapper').style.display = 'none';
        }

        agendaModal.show();
        
        if (agenda.latitude && agenda.longitude) {
            setTimeout(() => {
                initFormMap(agenda.latitude, agenda.longitude);
            }, 300);
        }
    }

    function removeExistingFile() {
        document.getElementById('removeBannerInput').value = '1';
        document.getElementById('existingFilePreview').classList.add('d-none');
        document.getElementById('dropZone').classList.remove('d-none');
        document.getElementById('bannerFlyer').value = '';
        formDirty = true;
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

    // Modal Detail Logic
    const dummyAgendaData = @json($agenda->items());

    function openDetailModal(id) {
        const item = dummyAgendaData.find(a => a.id == id);
        if (!item) return;

        // Populate Image
        const detailImgContainer = document.getElementById('detailImageContainer');
        const detailImg = document.getElementById('detailImage');
        if (item.banner_flyer) {
            detailImg.src = item.banner_flyer.startsWith('http') ? item.banner_flyer : '/' + item.banner_flyer;
            detailImgContainer.classList.remove('d-none');
        } else {
            detailImg.src = '';
            detailImgContainer.classList.add('d-none');
        }

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

        // Fields
        document.getElementById('detailKategori').value = item.kategori;
        document.getElementById('detailJudul').value = item.judul_agenda;
        document.getElementById('detailKonten').innerHTML = item.detail_pengumuman || '<p class="text-muted italic">Tidak ada konten</p>';
        document.getElementById('detailLokasi').value = item.lokasi || '-';

        // Map Detail
        const mapContainer = document.getElementById('detailMapContainer');
        if (item.latitude && item.longitude) {
            mapContainer.style.display = 'block';
            setTimeout(() => {
                if (!detailMap) {
                    detailMap = L.map('mapDetailAgenda').setView([item.latitude, item.longitude], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(detailMap);
                    detailMarker = L.marker([item.latitude, item.longitude]).addTo(detailMap);
                } else {
                    detailMap.setView([item.latitude, item.longitude], 15);
                    detailMarker.setLatLng([item.latitude, item.longitude]);
                    detailMap.invalidateSize();
                }
            }, 300);
        } else {
            mapContainer.style.display = 'none';
        }

        // RSVP Status
        const rsvpStatusText = document.getElementById('detailRsvpStatus');
        if (item.is_rsvp_enabled == 1) {
            rsvpStatusText.textContent = 'Aktif';
            rsvpStatusText.className = 'd-block text-success fw-medium';
        } else {
            rsvpStatusText.textContent = 'Tidak Aktif';
            rsvpStatusText.className = 'd-block text-muted fw-medium';
        }

        // Date and Time
        if (item.tanggal_mulai && item.tanggal_selesai) {
            const dateStr = new Date(item.tanggal_mulai).toLocaleDateString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit' });
            // Format as YYYY-MM-DD for input date, or just display DD/MM/YYYY
            document.getElementById('detailTanggal').value = dateStr;
            const timeStart = new Date(item.tanggal_mulai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            const timeEnd = new Date(item.tanggal_selesai).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('detailWaktu').value = `${timeStart} - ${timeEnd} WIB`;
        }

        // Revisi Notes
        const alertRevisi = document.getElementById('alertDetailCatatanRevisi');
        if (item.status === 'Revisi' && item.catatan_revisi) {
            alertRevisi.classList.remove('d-none');
            document.getElementById('teksDetailCatatanRevisi').textContent = item.catatan_revisi;
        } else {
            alertRevisi.classList.add('d-none');
        }

        // Actions Footer
        const detailFooter = document.getElementById('detailFooterActions');
        let actionsHtml = '';
        
        if (item.status === 'Draft' || item.status === 'Revisi') {
            actionsHtml += `
                <button type="button" class="btn btn-outline-dark fw-semibold px-4 d-flex align-items-center gap-2 btn-hover-gray" style="border-color: #D1D5DB; background: white;" onclick="editAgendaFromJson('${item.id}')">
                    <i class="bi bi-pencil"></i> Edit Agenda
                </button>
                <form action="/op-konten/agenda/${item.id}/submit" method="POST" class="d-inline" id="formSubmit_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn fw-semibold px-4 text-white btn-gradient-green" onclick="confirmSubmit('${item.id}')">
                        Ajukan Agenda
                    </button>
                </form>
            `;
            if (item.status === 'Draft') {
                actionsHtml += `
                    <form action="/op-konten/agenda/${item.id}" method="POST" class="d-inline ms-auto" id="formDelete_${item.id}">
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
                <form action="/op-konten/agenda/${item.id}/revoke" method="POST" class="d-inline" id="formRevoke_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-outline-warning fw-semibold px-4" onclick="confirmRevoke('${item.id}')">
                        <i class="bi bi-arrow-return-left"></i> Batalkan Pengajuan
                    </button>
                </form>
            `;
        }

        detailFooter.innerHTML = actionsHtml;

        const detailModal = new bootstrap.Modal(document.getElementById('modalDetailAgenda'));
        detailModal.show();
    }

    function editAgendaFromJson(id) {
        const item = dummyAgendaData.find(a => a.id == id);
        if(item) {
            const detailModal = bootstrap.Modal.getInstance(document.getElementById('modalDetailAgenda'));
            if(detailModal) detailModal.hide();
            editAgenda(item);
        }
    }

    // Sweet Alerts
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Agenda Ini?',
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
            title: 'Ajukan agenda ke Ketua RW?',
            text: "Agenda akan masuk tahap review dan menunggu persetujuan Ketua RW.",
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

    function confirmRevoke(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Agenda akan ditarik kembali menjadi status Draf.",
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
            if (result.isConfirmed) document.getElementById('formRevoke_' + id).submit();
        });
    }

    function forceCloseModal() {
        allowModalClose = true;
        formDirty = false;
        if (confirmModal) confirmModal.hide();
        if (agendaModal) agendaModal.hide();
    }

    document.getElementById('modalBuatAgenda').addEventListener('hide.bs.modal', function (e) {
        if (formDirty && !allowModalClose) {
            e.preventDefault();
            confirmModal.show();
        }
    });

    function initFormMap(lat = null, lng = null) {
        const defaultLat = -6.8398; // Tanimulya approx
        const defaultLng = 107.5147;
        const initialLat = lat || defaultLat;
        const initialLng = lng || defaultLng;

        if (!formMap) {
            formMap = L.map('mapAgenda').setView([initialLat, initialLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(formMap);

            // Add Search Geocoder
            L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: "Cari alamat/lokasi..."
            })
            .on('markgeocode', function(e) {
                const bbox = e.geocode.bbox;
                const poly = L.polygon([
                    bbox.getSouthEast(),
                    bbox.getNorthEast(),
                    bbox.getNorthWest(),
                    bbox.getSouthWest()
                ]).addTo(formMap);
                formMap.fitBounds(poly.getBounds());
                
                // Set marker
                formMarker.setLatLng(e.geocode.center);
                document.getElementById('latitudeInput').value = e.geocode.center.lat;
                document.getElementById('longitudeInput').value = e.geocode.center.lng;
                formDirty = true;
            })
            .addTo(formMap);

            if (lat && lng) {
                formMarker = L.marker([lat, lng], { draggable: true }).addTo(formMap);
            } else {
                formMarker = L.marker([initialLat, initialLng], { draggable: true }).addTo(formMap);
                document.getElementById('latitudeInput').value = initialLat;
                document.getElementById('longitudeInput').value = initialLng;
            }

            formMap.on('click', function (e) {
                formMarker.setLatLng(e.latlng);
                document.getElementById('latitudeInput').value = e.latlng.lat;
                document.getElementById('longitudeInput').value = e.latlng.lng;
                formDirty = true;
            });

            formMarker.on('dragend', function (e) {
                const position = formMarker.getLatLng();
                document.getElementById('latitudeInput').value = position.lat;
                document.getElementById('longitudeInput').value = position.lng;
                formDirty = true;
            });
        } else {
            formMap.setView([initialLat, initialLng], 15);
            formMarker.setLatLng([initialLat, initialLng]);
            formMap.invalidateSize();
        }
    }
</script>
@endpush
