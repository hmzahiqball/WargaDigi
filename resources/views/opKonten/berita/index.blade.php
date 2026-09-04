@extends('layouts.global')

@section('title', 'Manajemen Berita')

@push('styles')
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .berita-table {
        table-layout: fixed;
        border-collapse: collapse;
        min-width: 900px;
    }
    .berita-table th,
    .berita-table td {
        text-align: center;
        vertical-align: middle;
        border: 1px solid #E5E7EB;
    }
    .berita-table thead th {
        text-align: center;
        border-bottom: 2px solid #D1D5DB;
    }
    .berita-table td:nth-child(1),
    .berita-table td:nth-child(3),
    .berita-table td:nth-child(4),
    .berita-table td:nth-child(5),
    .berita-table td:nth-child(6),
    .berita-table th:nth-child(1),
    .berita-table th:nth-child(3),
    .berita-table th:nth-child(4),
    .berita-table th:nth-child(5),
    .berita-table th:nth-child(6) {
        white-space: nowrap;
    }
    .btn-draft:hover {
        background-color: #E5E7EB !important;
        color: #1B1C1C !important;
    }
    .editor-konten:empty:before {
        content: attr(placeholder);
        color: rgba(64, 73, 61, 0.50);
        pointer-events: none;
        display: block;
    }
    
    /* Quill Customization */
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
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Berita</h2>
        <p class="text-muted mb-0">Kelola artikel berita, persetujuan, dan tinjauan draf.</p>
    </div>
    <button type="button" class="btn btn-success fw-semibold px-3 py-2 rounded-3 text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBuatBerita">
        <i class="bi bi-plus-lg me-1"></i> Buat Berita Baru
    </button>
</div>

{{-- Filters & Export --}}
<div class="card card-custom p-4 shadow-sm border-0 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted fw-bold small text-uppercase" style="letter-spacing: 0.5px;">Filter By:</span>
            <select class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 160px;">
                <option value="">Semua Kategori</option>
                <option value="Sosial">Sosial</option>
                <option value="Infrastruktur">Infrastruktur</option>
                <option value="Hiburan">Hiburan</option>
                <option value="Kesehatan">Kesehatan</option>
                <option value="Keamanan">Keamanan</option>
            </select>
            <select class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 150px;">
                <option value="">Semua Status</option>
                <option value="Publish">Publish</option>
                <option value="Review">Review</option>
                <option value="Draft">Draft</option>
            </select>
        </div>
        <button type="button" class="btn btn-sm rounded-2 px-3 py-2 fw-semibold shadow-sm" style="background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
            <i class="bi bi-download me-1"></i> Export
        </button>
    </div>
</div>

{{-- Data Table --}}
<div class="card card-custom shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 berita-table">
            <colgroup>
                <col style="width: 14%;">
                <col style="width: 32%;">
                <col style="width: 12%;">
                <col style="width: 15%;">
                <col style="width: 12%;">
                <col style="width: 15%;">
            </colgroup>
            <thead class="table-light">
                <tr class="text-muted text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                    <th scope="col" class="py-3 px-3">Thumbnail</th>
                    <th scope="col" class="py-3 px-3">Judul</th>
                    <th scope="col" class="py-3 px-3">Kategori</th>
                    <th scope="col" class="py-3 px-3">Penulis</th>
                    <th scope="col" class="py-3 px-3">Status</th>
                    <th scope="col" class="py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($berita as $item)
                    <tr>
                        <td class="py-3 px-3">
                            <div class="rounded-1 overflow-hidden bg-light mx-auto" style="width: 150px; height: 95px;">
                                <img src="{{ $item['image'] }}" alt="{{ $item['judul'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="fw-bold text-dark small">{{ $item['judul'] }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-1 px-2 py-1 fw-medium" style="font-size: 10px; background: #E5E7EB; color: #374151;">
                                {{ $item['kategori'] }}
                            </span>
                        </td>
                        <td class="text-muted small py-3 px-3">{{ $item['penulis'] }}</td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-1" style="background: {{ $item['status_bg'] }}; color: {{ $item['status_color'] }};">
                                <span class="rounded-circle" style="width: 6px; height: 6px; display: inline-block; background: {{ $item['status_color'] }};"></span>
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <button type="button" class="btn btn-sm rounded-3 fw-semibold px-3" style="font-size: 12px; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-newspaper fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada berita yang dibuat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-4 py-3 border-top text-muted small">
        <div class="mb-2 mb-md-0">
            Menampilkan 1 ke {{ count($berita) }} dari 12 data
        </div>
        <div class="d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-white border rounded-2 text-muted px-3 py-1" disabled>Sebelumnya</button>
            <button class="btn btn-sm btn-success rounded-2 text-white px-3 py-1">1</button>
            <button class="btn btn-sm btn-white border rounded-2 text-dark px-3 py-1">2</button>
            <button class="btn btn-sm btn-white border rounded-2 text-dark px-3 py-1">3</button>
            <button class="btn btn-sm btn-white border rounded-2 text-dark px-3 py-1">Selanjutnya</button>
        </div>
    </div>
</div>

{{-- Modal Buat Konten Berita --}}
<div class="modal fade" id="modalBuatBerita" tabindex="-1" aria-labelledby="modalBuatBeritaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header px-4 py-3 border-bottom" style="border-color: #BFCABA !important;">
                <h5 class="modal-title fw-bold text-dark" id="modalBuatBeritaLabel" style="font-size: 20px;">Buat Konten Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #FBF9F8;">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Judul Berita</label>
                    <input type="text" id="judulBeritaInput" class="form-control form-control-lg bg-light" style="font-size: 14px; border: 1px solid #BFCABA;" placeholder="Masukkan judul berita yang menarik">
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Kategori</label>
                    <select id="kategoriSelect" class="form-select form-select-lg bg-light" style="font-size: 14px; border: 1px solid #BFCABA;">
                        <option selected disabled>Pilih kategori berita</option>
                        <option value="1">Sosial</option>
                        <option value="2">Infrastruktur</option>
                        <option value="3">Hiburan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Foto Utama</label>
                    <input type="file" id="fotoUtama" class="d-none" accept="image/*">
                    <div id="dropZone" class="p-4 text-center rounded-3 bg-white" style="border: 2px dashed #BFCABA; cursor: pointer; transition: all 0.2s;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; background: rgba(13, 99, 27, 0.1);">
                            <i class="bi bi-cloud-arrow-up fs-4 text-success"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Drag and drop image here, or click to browse</h6>
                        <p class="text-muted small mb-0" style="font-size: 12px;">Supported formats: JPG, PNG, WEBP (Max 5MB)</p>
                    </div>
                    
                    {{-- Thumbnail Preview (muncul setelah diunggah) --}}
                    <div id="previewContainer" class="d-none mt-3">
                        <div class="position-relative d-inline-block rounded border shadow-sm p-1 bg-white">
                            <img id="imagePreview" src="" alt="Preview" class="rounded" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="openLightbox()">
                            <button type="button" class="btn-close position-absolute bg-white rounded-circle shadow-sm" style="top: -8px; right: -8px; padding: 4px; font-size: 10px;" onclick="removeImage()" aria-label="Remove"></button>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Isi Konten</label>
                    <div class="quill-wrapper bg-light rounded-3">
                        <div id="editor-container" style="height: 200px; font-size: 14px; background-color: #FBF9F8;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-white border-top" style="border-color: #BFCABA !important;">
                <button type="button" class="btn btn-outline-secondary btn-draft fw-semibold px-4" onclick="showNotification('Draft berhasil disimpan!')" style="border-color: #707A6C; color: #1B1C1C; transition: all 0.2s;">Simpan Draft</button>
                <button type="button" class="btn fw-semibold px-4 text-white d-flex align-items-center gap-2" onclick="showNotification('Berita berhasil diajukan untuk review!')" style="background: rgba(13, 99, 27, 0.9);">
                    <i class="bi bi-send-fill"></i> Ajukan Review
                </button>
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

@push('scripts')
<!-- Quill JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
    function showNotification(message, isError = false) {
        // Menutup modal hanya jika bukan error
        if (!isError) {
            closeBuatBeritaModal();
        }

        // Tampilkan Toast
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

    // Initialize Quill Editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Tuliskan isi berita di sini...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Add Tooltips to Quill Toolbar
    setTimeout(() => {
        const tooltipMapping = {
            'ql-bold': 'Tebal (Bold)',
            'ql-italic': 'Miring (Italic)',
            'ql-underline': 'Garis Bawah (Underline)',
            'ql-strike': 'Coret (Strikethrough)',
            'ql-list[value="ordered"]': 'Daftar Angka',
            'ql-list[value="bullet"]': 'Daftar Titik',
            'ql-link': 'Sisipkan Tautan',
            'ql-image': 'Sisipkan Gambar',
            'ql-clean': 'Hapus Format',
            'ql-header': 'Ukuran Judul',
            'ql-color': 'Warna Teks',
            'ql-background': 'Warna Latar',
            'ql-align': 'Perataan Teks'
        };
        for (const [selector, text] of Object.entries(tooltipMapping)) {
            const el = document.querySelector('.ql-toolbar .' + selector);
            if (el) el.setAttribute('title', text);
        }
    }, 100);

    // Drag and Drop Image Logic
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fotoUtama');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = '#f8f9fa';
        dropZone.style.borderColor = '#0D631B';
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.style.backgroundColor = 'white';
        dropZone.style.borderColor = '#BFCABA';
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.style.backgroundColor = 'white';
        dropZone.style.borderColor = '#BFCABA';
        if (e.dataTransfer.files.length) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleFile(this.files[0]);
        }
    });

    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            showNotification('Harap unggah file gambar yang valid.', true);
            return;
        }

        if (file.size > MAX_FILE_SIZE) {
            showNotification('Ukuran file melebihi 5MB. Silakan unggah gambar lain.', true);
            fileInput.value = ''; // Reset input
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            previewContainer.classList.remove('d-none');
            // Jika ingin menyembunyikan kotak dropzone saat ada gambar:
            // dropZone.classList.add('d-none');
        }
        reader.readAsDataURL(file);
    }

    function removeImage() {
        fileInput.value = '';
        imagePreview.src = '';
        previewContainer.classList.add('d-none');
        // Tampilkan dropzone kembali jika sebelumnya disembunyikan
        // dropZone.classList.remove('d-none');
    }

    // Lightbox Logic
    function openLightbox() {
        const lightboxImg = document.getElementById('lightboxImage');
        lightboxImg.src = imagePreview.src;
        const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        modal.show();
    }

    let allowModalClose = false;

    // Intercept modal close event (X button, backdrop, or ESC)
    document.getElementById('modalBuatBerita').addEventListener('hide.bs.modal', function (event) {
        if (allowModalClose) {
            allowModalClose = false; // Reset for next time
            return; // Allow closing
        }

        const title = document.getElementById('judulBeritaInput').value.trim();
        const category = document.getElementById('kategoriSelect').value;
        const hasImage = fileInput.files.length > 0;
        const hasContent = quill.getText().trim().length > 0;

        if (title !== '' || category !== 'Pilih kategori berita' || hasImage || hasContent) {
            event.preventDefault(); // Stop modal from closing
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmCloseModal'));
            confirmModal.show();
        }
    });

    function closeBuatBeritaModal() {
        allowModalClose = true;
        const modalElement = document.getElementById('modalBuatBerita');
        if (modalElement) {
            const modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
            modalInstance.hide();
        }
    }

    function forceCloseModal() {
        // Reset form
        document.getElementById('judulBeritaInput').value = '';
        document.getElementById('kategoriSelect').selectedIndex = 0;
        removeImage();
        quill.setText('');
        
        const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmCloseModal'));
        if (confirmModal) confirmModal.hide();
        
        closeBuatBeritaModal();
    }
</script>

{{-- Modal Konfirmasi Keluar --}}
<div class="modal fade" id="confirmCloseModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-2">Simpan Perubahan?</h5>
                <p class="text-muted small mb-4">Ada perubahan yang belum disimpan. Apakah Anda ingin menyimpannya ke Draft atau membuangnya?</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-draft fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #707A6C; color: #1B1C1C;">Kembali</button>
                    <button type="button" class="btn btn-success text-white" onclick="bootstrap.Modal.getInstance(document.getElementById('confirmCloseModal')).hide(); showNotification('Draft berhasil disimpan!')">Simpan ke Draft</button>
                    <button type="button" class="btn btn-danger text-white" onclick="forceCloseModal()">Buang Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
@endsection