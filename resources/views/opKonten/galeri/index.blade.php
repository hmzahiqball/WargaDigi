@extends('layouts.global')

@section('title', 'Galeri Dokumentasi')

@push('styles')
<style>
    /* Bento Cards */
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

    /* Agenda Card */
    .galeri-card {
        border-radius: 12px;
        border: 1px solid #BFCABA;
        background: #fff;
        overflow: hidden;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .galeri-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }
    .galeri-card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background: #F3F4F6;
    }
    .galeri-card-body {
        padding: 16px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .galeri-card-body .card-title {
        font-size: 15px;
        font-weight: 700;
        color: #1B1C1C;
        margin-bottom: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .galeri-card-body .card-desc {
        font-size: 13px;
        color: #6B7280;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }
    .galeri-card-footer {
        padding: 12px 16px;
        border-top: 1px solid #E5E7EB;
    }

    /* Btn */
    .btn-gradient-green {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
        border: none !important;
        transition: opacity 0.2s;
    }
    .btn-gradient-green:hover {
        opacity: 0.9;
        color: white !important;
    }

    /* Custom SweetAlert */
    .custom-swal-popup {
        border-radius: 1rem !important;
        border: none !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        padding-bottom: 0 !important;
    }
    .custom-swal-popup .swal2-title { font-size: 1.25rem !important; font-weight: 700 !important; margin-top: 1rem !important; }
    .custom-swal-popup .swal2-html-container { font-size: 0.875rem !important; color: #6B7280 !important; margin-top: 0.5rem !important; margin-bottom: 1.5rem !important; }
    .custom-swal-popup .swal2-actions { display: flex !important; flex-direction: column !important; gap: 0.5rem !important; width: 100% !important; padding: 0 1.5rem 1.5rem 1.5rem !important; margin-top: 0 !important; }
    .btn-cancel-swal { border: 1px solid #707A6C !important; color: #1B1C1C !important; background-color: white !important; }
    .btn-cancel-swal:hover { background-color: #F3F4F6 !important; }

    /* Drop Zone */
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

    /* Photo Preview Grid */
    .photo-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 8px;
    }
    .photo-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1;
    }
    .photo-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .photo-preview-item .remove-btn {
        position: absolute; top: 4px; right: 4px;
        background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%;
        width: 22px; height: 22px; font-size: 11px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
    }

    /* Modal Backdrop */
    .modal-backdrop { backdrop-filter: blur(5px) !important; background-color: rgba(0, 0, 0, 0.4) !important; }
    .modal-backdrop.show { opacity: 1 !important; }

    /* Default Image Placeholder */
    .default-img-placeholder {
        width: 100%;
        height: 180px;
        background: linear-gradient(135deg, #e0e7d8 0%, #c8d6bc 50%, #a8b89c 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6B7280;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Galeri Dokumentasi Kegiatan</h2>
        <p class="text-muted mb-0">Kelola dan unggah foto bukti kegiatan untuk agenda warga yang telah selesai.</p>
    </div>
</div>

{{-- Status Bento --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: rgba(46, 125, 50, 0.10); color: #007232;">
                <i class="bi bi-calendar-check fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">AGENDA SELESAI</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['total_selesai'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #D1FAE5; color: #065F46;">
                <i class="bi bi-images fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">SUDAH DOKUMENTASI</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['sudah_dokumentasi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #FEF3C7; color: #D97706;">
                <i class="bi bi-camera fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">BELUM DOKUMENTASI</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['belum_dokumentasi'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="bento-card">
            <div class="bento-icon" style="background: #EDE9FE; color: #6D28D9;">
                <i class="bi bi-file-image fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">TOTAL FOTO</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ $stats['total_foto'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card card-custom p-4 shadow-sm border-0 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-3">
        <form id="filterForm" action="{{ route('opkonten.galeri.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-3 flex-grow-1">
            <div style="max-width: 300px; flex-grow: 1;">
                <label class="form-label text-muted small fw-medium mb-1">Cari Judul Agenda</label>
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
                <label class="form-label text-muted small fw-medium mb-1">Dokumentasi</label>
                <select name="dokumentasi" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 180px;" onchange="document.getElementById('filterForm').submit()">
                    <option value="">Semua</option>
                    <option value="sudah" {{ request('dokumentasi') == 'sudah' ? 'selected' : '' }}>Sudah Terdokumentasi</option>
                    <option value="belum" {{ request('dokumentasi') == 'belum' ? 'selected' : '' }}>Belum Terdokumentasi</option>
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Card Grid --}}
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
    @forelse($agendaSelesai as $item)
        @php
            $hasGaleri = $item->galeriDokumentasi !== null;
            $fotos = $hasGaleri ? ($item->galeriDokumentasi->foto ?? []) : [];
            $thumbnail = count($fotos) > 0 ? asset($fotos[0]) : null;
            $bannerFallback = $item->banner_flyer ? asset($item->banner_flyer) : null;
        @endphp
        <div class="col">
            <div class="galeri-card">
                {{-- Card Image --}}
                @if($thumbnail)
                    <img src="{{ $thumbnail }}" alt="{{ $item->judul_agenda }}" class="galeri-card-img">
                @elseif($bannerFallback)
                    <img src="{{ $bannerFallback }}" alt="{{ $item->judul_agenda }}" class="galeri-card-img">
                @else
                    <div class="default-img-placeholder">
                        <i class="bi bi-image" style="font-size: 48px; opacity: 0.4;"></i>
                    </div>
                @endif

                {{-- Card Body --}}
                <div class="galeri-card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill px-2 py-1 fw-semibold" style="font-size: 10px; background: #D1FAE5; color: #065F46;">SELESAI</span>
                        <span class="badge rounded-pill px-2 py-1" style="font-size: 10px; background: #E5E7EB; color: #374151;">{{ $item->kategori }}</span>
                        <span class="text-muted" style="font-size: 12px;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="card-title">{{ $item->judul_agenda }}</div>
                    <div class="card-desc">{!! strip_tags($item->detail_pengumuman) !!}</div>

                    @if($hasGaleri)
                        <div class="mt-2">
                            <span class="badge rounded-pill px-2 py-1" style="font-size: 11px; background: #EDE9FE; color: #6D28D9;">
                                <i class="bi bi-images me-1"></i>{{ count($fotos) }} Foto
                            </span>
                            @if($item->galeriDokumentasi->jumlah_peserta)
                                <span class="badge rounded-pill px-2 py-1 ms-1" style="font-size: 11px; background: #F0FDF4; color: #065F46;">
                                    <i class="bi bi-people me-1"></i>{{ $item->galeriDokumentasi->jumlah_peserta }} Warga
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Card Footer --}}
                <div class="galeri-card-footer">
                    @if($hasGaleri)
                        <a href="{{ route('opkonten.galeri.show', $item->id) }}" class="btn btn-sm w-100 rounded-3 fw-semibold" style="font-size: 13px; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
                            <i class="bi bi-eye me-1"></i> Lihat Detail
                        </a>
                    @else
                        <button type="button" class="btn btn-sm w-100 rounded-3 fw-semibold text-white btn-gradient-green" style="font-size: 13px;" onclick="openUploadModal('{{ $item->id }}')">
                            <i class="bi bi-cloud-arrow-up me-1"></i> Upload Bukti
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 d-flex flex-column align-items-center justify-content-center py-5 text-muted w-100">
            <i class="bi bi-images fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <p class="fw-medium">Belum ada agenda yang telah selesai.</p>
            <p class="small text-muted">Agenda yang berstatus "Terbit" dan jadwalnya sudah lewat akan muncul di sini.</p>
        </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($agendaSelesai->total() > 0)
<div class="d-flex justify-content-between align-items-center p-3 rounded-3" style="background: #FBF9F8; border: 1px solid #E5E7EB;">
    <div class="text-muted" style="font-size: 12px; color: #40493D !important;">
        Menampilkan {{ $agendaSelesai->firstItem() ?? 0 }}-{{ $agendaSelesai->lastItem() ?? 0 }} dari {{ $agendaSelesai->total() }} agenda
    </div>
    <div class="d-flex gap-1">
        @if (!$agendaSelesai->onFirstPage())
            <a href="{{ $agendaSelesai->previousPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Sebelumnya</a>
        @endif
        @php
            $start = max(1, $agendaSelesai->currentPage() - 2);
            $end = min($agendaSelesai->lastPage(), $agendaSelesai->currentPage() + 2);
        @endphp
        @for ($page = $start; $page <= $end; $page++)
            @if ($page == $agendaSelesai->currentPage())
                <button class="btn btn-sm rounded-1 px-3 py-1 text-white btn-gradient-green" style="font-size: 12px; cursor: default;">{{ $page }}</button>
            @else
                <a href="{{ $agendaSelesai->url($page) }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">{{ $page }}</a>
            @endif
        @endfor
        @if ($agendaSelesai->hasMorePages())
            <a href="{{ $agendaSelesai->nextPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Selanjutnya</a>
        @endif
    </div>
</div>
@endif

{{-- Toast Notification --}}
<div id="customToast" class="position-fixed top-50 start-50 translate-middle p-3 rounded-3 shadow-lg" style="background: rgba(13, 99, 27, 0.95); color: white; z-index: 9999; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5" id="customToastIcon"></i>
        <span id="customToastMessage" class="fw-semibold">Berhasil!</span>
    </div>
</div>

{{-- Modal Upload Dokumentasi (Pertama Kali) --}}
<div class="modal fade" id="modalUpload" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form id="formUpload" action="{{ route('opkonten.galeri.store') }}" method="POST" enctype="multipart/form-data" class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            @csrf
            <input type="hidden" name="agenda_id" id="uploadAgendaId">

            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-cloud-arrow-up me-2 text-success"></i>Unggah Dokumentasi
                </h5>
                <button type="button" class="btn-close" onclick="tryCloseUploadModal()"></button>
            </div>

            <div class="modal-body px-4 py-4">
                {{-- Info Agenda --}}
                <div class="mb-4 p-3 rounded-3" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                    <div class="d-flex align-items-center gap-2 text-success">
                        <i class="bi bi-calendar-check"></i>
                        <span class="fw-semibold small">Agenda: <span id="uploadAgendaName" class="text-dark">-</span></span>
                    </div>
                    <div class="d-flex gap-3 mt-1 text-muted" style="font-size: 12px;">
                        <span><i class="bi bi-geo-alt me-1"></i><span id="uploadLokasi">-</span></span>
                        <span><i class="bi bi-clock me-1"></i><span id="uploadWaktu">-</span></span>
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Jumlah Peserta --}}
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Jumlah Warga Hadir</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-people text-muted"></i></span>
                            <input type="number" name="jumlah_peserta" id="uploadJumlahPeserta" class="form-control" min="0" placeholder="Contoh: 45">
                        </div>
                    </div>
                    <div class="col-md-6"></div>

                    {{-- Upload Foto --}}
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Upload Foto <span class="text-danger">*</span></label>
                        <div class="drop-zone" id="dropZone" onclick="document.getElementById('fotoInput').click()">
                            <i class="bi bi-cloud-arrow-up" style="font-size: 32px; color: #2E7D32;"></i>
                            <p class="fw-medium mb-1 mt-2" style="color: #374151;">Tarik & Lepas foto di sini atau <span class="text-success text-decoration-underline">Klik untuk Telusuri</span></p>
                            <p class="text-muted small mb-0">Mendukung JPG, PNG, WEBP (Maks 5MB) — Maks 10 foto</p>
                        </div>
                        <input type="file" name="foto[]" id="fotoInput" class="d-none" accept="image/jpeg,image/jpg,image/png,image/webp" multiple>

                        {{-- Foto Terpilih --}}
                        <div class="mt-3">
                            <label class="form-label fw-medium text-dark" style="font-size: 13px;">Foto Terpilih</label>
                            <div id="photoPreviewContainer" class="photo-preview-grid"></div>
                            <div id="photoEmpty" class="p-3 rounded-3 text-center text-muted small" style="background: #F9FAFB; border: 1px solid #E5E7EB;">
                                Belum ada foto yang dipilih.
                            </div>
                            <div id="photoCount" class="form-text text-muted mt-2 d-none"><span id="photoCountText">0</span>/10 foto dipilih</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end gap-2" style="border-color: #BFCABA !important;">
                <button type="button" class="btn fw-semibold" style="border: 1px solid #707A6C; color: #1B1C1C;" onclick="tryCloseUploadModal()">Batal</button>
                <button type="submit" class="btn btn-gradient-green fw-semibold px-4 text-white d-flex align-items-center gap-2" id="btnSubmitUpload">
                    <i class="bi bi-cloud-arrow-up"></i> Unggah Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const agendaData = @json($agendaSelesai->items());
    let selectedFiles = [];
    let formDirty = false;
    let uploadModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        uploadModalInstance = new bootstrap.Modal(document.getElementById('modalUpload'));

        // Debounce search
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => document.getElementById('filterForm').submit(), 600);
            });
        }

        // Drop zone
        const dropZone = document.getElementById('dropZone');
        const fotoInput = document.getElementById('fotoInput');

        dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('dragover'); });
        dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });
        fotoInput.addEventListener('change', (e) => { handleFiles(e.target.files); });

        // Toast
        @if(session('success'))
            showNotification("{!! addslashes(session('success')) !!}");
        @endif
        @if(session('error'))
            showNotification("{!! addslashes(session('error')) !!}", true);
        @endif
        @if($errors->any())
            showNotification("{!! addslashes($errors->first()) !!}", true);
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
            setTimeout(() => { toast.style.opacity = '0'; }, 3000);
        }
    }

    function handleFiles(files) {
        const maxFiles = 10;
        const maxSize = 5 * 1024 * 1024;
        const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];

        for (let file of files) {
            if (selectedFiles.length >= maxFiles) { showNotification('Maksimal 10 foto.', true); break; }
            if (!allowed.includes(file.type)) { showNotification(`"${file.name}" bukan format yang didukung.`, true); continue; }
            if (file.size > maxSize) { showNotification(`"${file.name}" melebihi 5MB.`, true); continue; }
            selectedFiles.push(file);
            formDirty = true;
        }
        renderPhotoPreview();
        syncFileInput();
    }

    function renderPhotoPreview() {
        const container = document.getElementById('photoPreviewContainer');
        const emptyEl = document.getElementById('photoEmpty');
        const countEl = document.getElementById('photoCount');
        const countText = document.getElementById('photoCountText');
        container.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item';
                div.innerHTML = `<img src="${e.target.result}" alt="Preview"><button type="button" class="remove-btn" onclick="removePhoto(${index})"><i class="bi bi-x"></i></button>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        countText.textContent = selectedFiles.length;
        emptyEl.style.display = selectedFiles.length > 0 ? 'none' : 'block';
        countEl.classList.toggle('d-none', selectedFiles.length === 0);
    }

    function removePhoto(index) {
        selectedFiles.splice(index, 1);
        renderPhotoPreview();
        syncFileInput();
    }

    function syncFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        document.getElementById('fotoInput').files = dt.files;
    }

    function openUploadModal(agendaId) {
        formDirty = false;
        selectedFiles = [];
        renderPhotoPreview();

        const item = agendaData.find(a => a.id == agendaId);
        if (!item) return;

        document.getElementById('uploadAgendaId').value = item.id;
        document.getElementById('uploadAgendaName').textContent = item.judul_agenda;
        document.getElementById('uploadLokasi').textContent = item.lokasi || '-';

        const mulai = new Date(item.tanggal_mulai);
        const selesai = new Date(item.tanggal_selesai);
        const timeStr = mulai.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' • ' +
            mulai.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' - ' +
            selesai.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WIB';
        document.getElementById('uploadWaktu').textContent = timeStr;

        document.getElementById('uploadJumlahPeserta').value = '';
        document.getElementById('fotoInput').value = '';

        uploadModalInstance.show();
    }

    function tryCloseUploadModal() {
        if (formDirty || selectedFiles.length > 0) {
            Swal.fire({
                title: 'Buang Perubahan?',
                text: 'Data yang sudah diisi dan foto terpilih akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Ya, buang',
                cancelButtonText: 'Lanjutkan Mengedit',
                customClass: {
                    popup: 'custom-swal-popup',
                    confirmButton: 'btn btn-danger text-white fw-semibold w-100 m-0',
                    cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formDirty = false;
                    selectedFiles = [];
                    uploadModalInstance.hide();
                }
            });
        } else {
            uploadModalInstance.hide();
        }
    }
</script>
@endpush
