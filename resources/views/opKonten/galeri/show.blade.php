@extends('layouts.global')

@section('title', $agenda->judul_agenda . ' — Galeri Dokumentasi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .breadcrumb-custom a {
        color: #6B7280;
        text-decoration: none;
        font-size: 14px;
    }
    .breadcrumb-custom a:hover { color: #2E7D32; }
    .breadcrumb-custom span { color: #374151; font-size: 14px; font-weight: 600; }

    .detail-sidebar {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        padding: 24px;
    }
    .detail-sidebar .sidebar-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9CA3AF;
        margin-bottom: 4px;
    }
    .detail-sidebar .sidebar-value {
        font-size: 14px;
        color: #1B1C1C;
        font-weight: 600;
        margin-bottom: 16px;
    }

    /* Photo Gallery Grid */
    .photo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }
    .photo-gallery-item {
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #E5E7EB;
        background: white;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .photo-gallery-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .photo-gallery-item img {
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        cursor: pointer;
    }
    .photo-gallery-item .photo-info {
        padding: 8px 12px;
        font-size: 12px;
        color: #6B7280;
    }

    .btn-gradient-green {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
        border: none !important;
        transition: opacity 0.2s;
    }
    .btn-gradient-green:hover { opacity: 0.9; color: white !important; }

    /* Custom SweetAlert */
    .custom-swal-popup { border-radius: 1rem !important; border: none !important; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; padding-bottom: 0 !important; }
    .custom-swal-popup .swal2-title { font-size: 1.25rem !important; font-weight: 700 !important; margin-top: 1rem !important; }
    .custom-swal-popup .swal2-html-container { font-size: 0.875rem !important; color: #6B7280 !important; margin-top: 0.5rem !important; margin-bottom: 1.5rem !important; }
    .custom-swal-popup .swal2-actions { display: flex !important; flex-direction: column !important; gap: 0.5rem !important; width: 100% !important; padding: 0 1.5rem 1.5rem 1.5rem !important; margin-top: 0 !important; }
    .btn-cancel-swal { border: 1px solid #707A6C !important; color: #1B1C1C !important; background-color: white !important; }
    .btn-cancel-swal:hover { background-color: #F3F4F6 !important; }

    /* Drop Zone */
    .drop-zone { border: 2px dashed #BFCABA; border-radius: 8px; padding: 30px; text-align: center; background: white; cursor: pointer; transition: all 0.2s ease; }
    .drop-zone:hover, .drop-zone.dragover { background: #f8f9fa; border-color: #0D631B; }

    .photo-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 8px; }
    .photo-preview-item { position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; }
    .photo-preview-item img { width: 100%; height: 100%; object-fit: cover; }
    .photo-preview-item .remove-btn { position: absolute; top: 4px; right: 4px; background: rgba(220, 53, 69, 0.9); color: white; border: none; border-radius: 50%; width: 22px; height: 22px; font-size: 11px; display: flex; align-items: center; justify-content: center; cursor: pointer; }

    .modal-backdrop { backdrop-filter: blur(5px) !important; background-color: rgba(0, 0, 0, 0.4) !important; }
    .modal-backdrop.show { opacity: 1 !important; }

    /* Empty state for photos */
    .empty-photos {
        border: 2px dashed #D1D5DB;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        background: #FAFAFA;
    }
</style>
@endpush

@section('content')
{{-- Breadcrumbs --}}
<div class="breadcrumb-custom mb-3">
    <a href="{{ route('opkonten.galeri.index') }}">Galeri Dokumentasi</a>
    <span class="mx-2 text-muted">›</span>
    <span>{{ Str::limit($agenda->judul_agenda, 50) }}</span>
</div>

@php
    $galeri = $agenda->galeriDokumentasi;
    $fotos = $galeri ? ($galeri->foto ?? []) : [];
@endphp

<div class="row g-4">
    {{-- Main Content --}}
    <div class="col-lg-8">
        <div class="bg-white rounded-3 p-4 shadow-sm border" style="border-color: #E5E7EB !important;">
            {{-- Title & Actions --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h2 class="fw-bold text-dark mb-0" style="font-size: 24px; line-height: 1.3;">{{ $agenda->judul_agenda }}</h2>
                <div class="d-flex gap-2 flex-shrink-0">
                    @if($galeri)
                        <button type="button" class="btn btn-sm fw-semibold rounded-3 px-3" style="border: 1px solid #D1D5DB; color: #374151; font-size: 13px;" onclick="openEditModal()">
                            <i class="bi bi-pencil me-1"></i> Edit Detail
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-gradient-green fw-semibold rounded-3 px-3 text-white" style="font-size: 13px;" data-bs-toggle="modal" data-bs-target="#modalUploadFoto">
                        <i class="bi bi-plus-lg me-1"></i> Upload Foto Baru
                    </button>
                </div>
            </div>

            {{-- Date & Status --}}
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-calendar3 text-muted"></i>
                <span class="text-muted" style="font-size: 14px;">{{ \Carbon\Carbon::parse($agenda->tanggal_mulai)->translatedFormat('d F Y') }}</span>
                <span class="badge rounded-pill px-2 py-1 fw-semibold" style="font-size: 11px; background: #D1FAE5; color: #065F46;">Selesai</span>
            </div>

            {{-- Description --}}
            <div class="mb-4 text-dark" style="font-size: 14px; line-height: 1.7;">
                @if($agenda->detail_pengumuman)
                    {!! $agenda->detail_pengumuman !!}
                @else
                    <span class="text-muted fst-italic">Belum ada deskripsi agenda.</span>
                @endif
            </div>

            {{-- Poster/Flyer Section --}}
            @if($agenda->banner_flyer)
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;"><i class="bi bi-file-image me-2 text-success"></i>Poster / Flyer Agenda</h6>
                    <img src="{{ asset($agenda->banner_flyer) }}" alt="Poster Agenda" class="img-fluid rounded-3 border" style="max-height: 400px; object-fit: contain; cursor: pointer;" onclick="openLightbox('{{ asset($agenda->banner_flyer) }}')">
                </div>
            @endif

            <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;"><i class="bi bi-images me-2 text-success"></i>Foto Bukti Kegiatan</h6>
            {{-- Photo Grid --}}
            @if(count($fotos) > 0)
                <div class="photo-gallery-grid">
                    @foreach($fotos as $index => $foto)
                        <div class="photo-gallery-item">
                            <img src="{{ asset($foto) }}" alt="Dokumentasi {{ $index + 1 }}" onclick="openLightbox('{{ asset($foto) }}')">
                            <div class="photo-info d-flex justify-content-between align-items-center">
                                <span>{{ basename($foto) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-photos">
                    <i class="bi bi-camera text-muted" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="fw-medium text-muted mt-2 mb-1">Belum ada foto dokumentasi</p>
                    <p class="text-muted small mb-3">Klik tombol "Upload Foto Baru" untuk mengunggah foto kegiatan.</p>
                    <button type="button" class="btn btn-sm btn-gradient-green text-white fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalUploadFoto">
                        <i class="bi bi-plus-lg me-1"></i> Upload Foto
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        <div class="detail-sidebar">
            <h6 class="fw-bold text-dark mb-3" style="font-size: 15px;">Detail Kegiatan</h6>

            <div class="sidebar-label">LOKASI</div>
            <div class="sidebar-value mb-2">
                <i class="bi bi-geo-alt-fill text-success me-1"></i>{{ $agenda->lokasi ?? '-' }}
            </div>
            @if($agenda->latitude && $agenda->longitude)
                <div class="mb-3 rounded-3 border overflow-hidden" id="mapDetailGaleri" style="height: 150px; width: 100%; z-index: 1;"></div>
            @endif

            <div class="sidebar-label">WAKTU PELAKSANAAN</div>
            <div class="sidebar-value">
                <i class="bi bi-clock text-success me-1"></i>
                {{ \Carbon\Carbon::parse($agenda->tanggal_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($agenda->tanggal_selesai)->format('H:i') }} WIB
            </div>

            <div class="sidebar-label">KATEGORI</div>
            <div class="sidebar-value">
                <span class="badge rounded-1 px-2 py-1" style="background: #E5E7EB; color: #374151; font-size: 12px;">{{ $agenda->kategori }}</span>
            </div>

            <div class="sidebar-label">JUMLAH PESERTA</div>
            <div class="sidebar-value">
                <i class="bi bi-people-fill text-success me-1"></i>
                @if($galeri && $galeri->jumlah_peserta)
                    {{ $galeri->jumlah_peserta }} Warga
                @else
                    <span class="text-muted fst-italic">Belum diisi</span>
                @endif
            </div>

            <div class="sidebar-label">OPERATOR ENTRY</div>
            <div class="sidebar-value">
                <i class="bi bi-person-circle text-success me-1"></i>
                @if($galeri && $galeri->operator)
                    {{ $galeri->operator->name ?? '-' }}
                @else
                    <span class="text-muted fst-italic">-</span>
                @endif
            </div>

            @if($galeri)
            <div class="sidebar-label">TANGGAL UNGGAH</div>
            <div class="sidebar-value mb-0">
                <i class="bi bi-calendar-check text-success me-1"></i>
                {{ $galeri->created_at->translatedFormat('d F Y, H:i') }}
            </div>
            @endif
        </div>

        {{-- Danger Zone --}}
        @if($galeri)
        <div class="mt-3 p-3 rounded-3" style="background: #FEF2F2; border: 1px solid #FECACA;">
            <p class="text-danger small fw-medium mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i>Zona Berbahaya</p>
            <form action="{{ route('opkonten.galeri.destroy', $galeri->id) }}" method="POST" id="formDeleteGaleri">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-outline-danger fw-semibold w-100" onclick="confirmDeleteGaleri()">
                    <i class="bi bi-trash me-1"></i> Hapus Semua Dokumentasi
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Toast --}}
<div id="customToast" class="position-fixed top-50 start-50 translate-middle p-3 rounded-3 shadow-lg" style="background: rgba(13, 99, 27, 0.95); color: white; z-index: 9999; opacity: 0; transition: opacity 0.3s ease-in-out; pointer-events: none;">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill fs-5" id="customToastIcon"></i>
        <span id="customToastMessage" class="fw-semibold">Berhasil!</span>
    </div>
</div>

{{-- Lightbox Modal --}}
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center position-relative p-0">
                <button type="button" class="btn-close position-absolute bg-white rounded-circle shadow-sm" data-bs-dismiss="modal" aria-label="Close" style="top: 12px; right: 12px; padding: 10px; font-size: 12px; z-index: 1070; opacity: 0.9;"></button>
                <img id="lightboxImage" src="" class="img-fluid rounded shadow-lg" alt="Preview" style="max-height: 85vh; cursor: pointer;" data-bs-dismiss="modal">
            </div>
        </div>
    </div>
</div>

{{-- Modal Upload Foto Baru --}}
<div class="modal fade" id="modalUploadFoto" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('opkonten.galeri.' . ($galeri ? 'update' : 'store'), $galeri ? $galeri->id : '') }}" method="POST" enctype="multipart/form-data" class="modal-content" id="formUploadFoto" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            @csrf
            @if($galeri)
                @method('PUT')
                <input type="hidden" name="jumlah_peserta" value="{{ $galeri->jumlah_peserta }}">
            @else
                <input type="hidden" name="agenda_id" value="{{ $agenda->id }}">
            @endif

            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold">Upload Foto Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">
                <div class="drop-zone" id="dropZoneShow" onclick="document.getElementById('fotoInputShow').click()">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 32px; color: #2E7D32;"></i>
                    <p class="fw-medium mb-1 mt-2" style="color: #374151;">Tarik & Lepas foto di sini atau <span class="text-success text-decoration-underline">Klik untuk Telusuri</span></p>
                    <p class="text-muted small mb-0">Mendukung JPG, PNG, WEBP (Maks 5MB)</p>
                </div>
                <input type="file" name="{{ $galeri ? 'foto_baru[]' : 'foto[]' }}" id="fotoInputShow" class="d-none" accept="image/jpeg,image/jpg,image/png,image/webp" multiple>

                <div class="mt-3">
                    <label class="form-label fw-medium text-dark" style="font-size: 13px;">Foto Terpilih</label>
                    <div id="photoPreviewShow" class="photo-preview-grid"></div>
                    <div id="photoEmptyShow" class="p-3 rounded-3 text-center text-muted small" style="background: #F9FAFB; border: 1px solid #E5E7EB;">
                        Belum ada foto yang dipilih.
                    </div>
                </div>

            </div>

            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end gap-2" style="border-color: #BFCABA !important;">
                <button type="button" class="btn fw-semibold" style="border: 1px solid #707A6C; color: #1B1C1C;" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-gradient-green fw-semibold px-4 text-white">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Unggah Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Detail --}}
@if($galeri)
<div class="modal fade" id="modalEditDetail" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('opkonten.galeri.update', $galeri->id) }}" method="POST" class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            @csrf
            @method('PUT')

            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold">Edit Detail Kegiatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Judul Kegiatan</label>
                        <input type="text" class="form-control bg-light" value="{{ $agenda->judul_agenda }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Tanggal</label>
                        <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($agenda->tanggal_mulai)->translatedFormat('d M Y') }}" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Jumlah Peserta</label>
                        <input type="number" name="jumlah_peserta" class="form-control" min="0" value="{{ $galeri->jumlah_peserta }}" placeholder="Contoh: 45">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Lokasi</label>
                        <input type="text" class="form-control bg-light" value="{{ $agenda->lokasi }}" readonly disabled>
                    </div>

                    {{-- Manage Photos --}}
                    <div class="col-12 mt-3">
                        <label class="form-label fw-medium text-dark" style="font-size: 14px;">Kelola Foto Saat Ini</label>
                        <div class="photo-preview-grid" id="editPhotoGrid">
                            @foreach($fotos as $index => $foto)
                                <div class="photo-preview-item" id="edit-photo-{{ $index }}">
                                    <img src="{{ asset($foto) }}" alt="Preview">
                                    <button type="button" class="remove-btn" onclick="confirmDeleteSinglePhoto('{{ $index }}', '{{ $foto }}')" title="Hapus Foto">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                            @if(count($fotos) == 0)
                                <div class="p-3 rounded-3 text-center text-muted small w-100" style="background: #F9FAFB; border: 1px solid #E5E7EB; grid-column: 1 / -1;">
                                    Belum ada foto yang dipilih.
                                </div>
                            @endif
                        </div>
                        <div id="hiddenInputsForDelete"></div>
                        <div class="form-text text-muted mt-1"><i class="bi bi-info-circle me-1"></i>Foto baru benar-benar terhapus saat Anda menekan tombol "Simpan Perubahan".</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end gap-2" style="border-color: #BFCABA !important;">
                <button type="button" class="btn fw-semibold" style="border: 1px solid #707A6C; color: #1B1C1C;" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-gradient-green fw-semibold px-4 text-white">
                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let showSelectedFiles = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Drop zone for show page
        const dropZone = document.getElementById('dropZoneShow');
        const fotoInput = document.getElementById('fotoInputShow');

        if (dropZone && fotoInput) {
            dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('dragover'); });
            dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('dragover'); });
            dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('dragover'); handleShowFiles(e.dataTransfer.files); });
            fotoInput.addEventListener('change', (e) => { handleShowFiles(e.target.files); });
        }

        @if(session('success'))
            showToast("{!! addslashes(session('success')) !!}");
        @endif
        @if(session('error'))
            showToast("{!! addslashes(session('error')) !!}", true);
        @endif
    });

    function showToast(message, isError = false) {
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

    function handleShowFiles(files) {
        const maxSize = 5 * 1024 * 1024;
        const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        const currentCount = {{ count($fotos) }};

        for (let file of files) {
            if ((showSelectedFiles.length + currentCount) >= 10) { showToast('Maksimal 10 foto per album.', true); break; }
            if (!allowed.includes(file.type)) { showToast(`"${file.name}" bukan format yang didukung.`, true); continue; }
            if (file.size > maxSize) { showToast(`"${file.name}" melebihi 5MB.`, true); continue; }
            showSelectedFiles.push(file);
        }
        renderShowPreview();
        syncShowInput();
    }

    function renderShowPreview() {
        const container = document.getElementById('photoPreviewShow');
        const emptyEl = document.getElementById('photoEmptyShow');
        container.innerHTML = '';

        showSelectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item';
                div.innerHTML = `<img src="${e.target.result}" alt="Preview"><button type="button" class="remove-btn" onclick="removeShowPhoto(${index})"><i class="bi bi-x"></i></button>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });

        emptyEl.style.display = showSelectedFiles.length > 0 ? 'none' : 'block';
    }

    function removeShowPhoto(index) {
        showSelectedFiles.splice(index, 1);
        renderShowPreview();
        syncShowInput();
    }

    function syncShowInput() {
        const dt = new DataTransfer();
        showSelectedFiles.forEach(file => dt.items.add(file));
        document.getElementById('fotoInputShow').files = dt.files;
    }

    function openLightbox(src) {
        document.getElementById('lightboxImage').src = src;
        new bootstrap.Modal(document.getElementById('lightboxModal')).show();
    }

    function openEditModal() {
        new bootstrap.Modal(document.getElementById('modalEditDetail')).show();
    }

    function confirmDeleteGaleri() {
        Swal.fire({
            title: 'Hapus Semua Dokumentasi?',
            text: 'Semua foto dan data dokumentasi akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, hapus semua!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'btn btn-danger text-white fw-semibold w-100 m-0',
                cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
            }
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('formDeleteGaleri').submit();
        });
    }

    function confirmDeleteSinglePhoto(index, path) {
        Swal.fire({
            title: 'Hapus Foto Ini?',
            text: 'Foto akan ditandai untuk dihapus dan benar-benar hilang setelah Anda menyimpan perubahan.',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'btn btn-danger text-white fw-semibold w-100 m-0',
                cancelButton: 'btn btn-cancel-swal fw-semibold w-100 m-0'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Hide photo element
                document.getElementById('edit-photo-' + index).style.display = 'none';
                
                // Add hidden input to tell controller to delete it
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'foto_hapus[]';
                input.value = path;
                document.getElementById('hiddenInputsForDelete').appendChild(input);
            }
        });
    }

    @if($agenda->latitude && $agenda->longitude)
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('mapDetailGaleri', {
            zoomControl: false,
            dragging: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            boxZoom: false,
            keyboard: false
        }).setView([{{ $agenda->latitude }}, {{ $agenda->longitude }}], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([{{ $agenda->latitude }}, {{ $agenda->longitude }}]).addTo(map);
    });
    @endif
</script>
@endpush
