@extends('layouts.global')

@section('title', 'Manajemen Berita')

@push('styles')
<!-- Quill CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .berita-table {
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 900px;
    }
    .berita-table th,
    .berita-table td {
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #E5E7EB;
    }
    .berita-table thead th {
        text-align: center;
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
    
    /* Blur background when modal is open */
    .modal-backdrop {
        backdrop-filter: blur(5px) !important;
        background-color: rgba(0, 0, 0, 0.4) !important;
    }
    .modal-backdrop.show {
        opacity: 1 !important;
    }

    /* Custom Gradient Classes */
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
    .btn-hover-gray {
        color: #374151 !important;
        transition: all 0.2s;
    }
    .btn-hover-gray:hover {
        background-color: #F3F4F6 !important;
        border-color: #D1D5DB !important;
        color: #111827 !important;
    }
    /* Content Typography & Editor Alignment */
    #detailKonten, .ql-editor {
        font-family: inherit !important;
    }
    #detailKonten p, .ql-editor p {
        color: #4B5563;
        line-height: 1.7;
        margin-bottom: 1.25rem;
    }
    #detailKonten h1, #detailKonten h2, #detailKonten h3, 
    #detailKonten h4, #detailKonten h5, #detailKonten h6,
    .ql-editor h1, .ql-editor h2, .ql-editor h3, 
    .ql-editor h4, .ql-editor h5, .ql-editor h6 {
        font-family: inherit !important;
        color: #1F2937 !important;
        font-weight: 700 !important;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }
    
    /* Make sure content headings are strictly smaller than the main modal title */
    #detailKonten h1, .ql-editor h1 { font-size: 1.35rem !important; }
    #detailKonten h2, .ql-editor h2 { font-size: 1.25rem !important; }
    #detailKonten h3, .ql-editor h3 { font-size: 1.15rem !important; }
    #detailKonten h4, .ql-editor h4 { font-size: 1.1rem !important; }
    #detailKonten h5, .ql-editor h5 { font-size: 1.05rem !important; }
    #detailKonten h6, .ql-editor h6 { font-size: 1rem !important; }

    #detailKonten ul, #detailKonten ol,
    .ql-editor ul, .ql-editor ol {
        color: #4B5563;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    #detailKonten li, .ql-editor li {
        margin-bottom: 0.5rem;
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
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Manajemen Berita</h2>
        <p class="text-muted mb-0">Kelola artikel berita, persetujuan, dan tinjauan draf.</p>
    </div>
    <button type="button" class="btn fw-semibold px-3 py-2 rounded-3 text-white shadow-sm btn-gradient-green" data-bs-toggle="modal" data-bs-target="#modalBuatBerita" onclick="resetToCreate()">
        <i class="bi bi-plus-lg me-1"></i> Buat Berita Baru
    </button>
</div>

{{-- Filters & Export --}}
<div class="card card-custom p-4 shadow-sm border-0 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <form action="" method="GET" class="d-flex flex-wrap align-items-center gap-3 flex-grow-1" id="filterForm">
            <div class="input-group" style="max-width: 300px; flex-grow: 1;">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="searchInput" name="search" class="form-control border-start-0 ps-0 py-2 fs-6" placeholder="Cari judul berita..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <select name="kategori" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 160px;" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="Sosial" {{ request('kategori') == 'Sosial' ? 'selected' : '' }}>Sosial</option>
                <option value="Infrastruktur" {{ request('kategori') == 'Infrastruktur' ? 'selected' : '' }}>Infrastruktur</option>
                <option value="Hiburan" {{ request('kategori') == 'Hiburan' ? 'selected' : '' }}>Hiburan</option>
                <option value="Kesehatan" {{ request('kategori') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                <option value="Keamanan" {{ request('kategori') == 'Keamanan' ? 'selected' : '' }}>Keamanan</option>
            </select>
            <select name="status" class="form-select rounded-3 py-2 fs-6" style="width: auto; min-width: 150px;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="Publish" {{ request('status') == 'Publish' ? 'selected' : '' }}>Terbit</option>
                <option value="Review" {{ request('status') == 'Review' ? 'selected' : '' }}>Ditinjau</option>
                <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draf</option>
                <option value="Revisi" {{ request('status') == 'Revisi' ? 'selected' : '' }}>Revisi</option>
                <option value="Archive" {{ request('status') == 'Archive' ? 'selected' : '' }}>Diarsipkan</option>
            </select>
        </form>
        <button type="button" class="btn btn-sm rounded-2 px-3 py-2 fw-semibold shadow-sm" style="background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;">
            <i class="bi bi-download me-1"></i> Ekspor
        </button>
    </div>
</div>

{{-- Data Table --}}
<div class="card card-custom shadow-sm border-0 overflow-hidden" style="border-radius: 12px;">
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
            <thead class="thead-green-gradient">
                <tr class="text-uppercase text-xs fw-bold" style="letter-spacing: 0.5px;">
                    <th scope="col" class="py-3 px-3">Sampul</th>
                    <th scope="col" class="py-3 px-3">Judul</th>
                    <th scope="col" class="py-3 px-3">Kategori</th>
                    <th scope="col" class="py-3 px-3">Penulis</th>
                    <th scope="col" class="py-3 px-3">Status</th>
                    <th scope="col" class="py-3 px-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($berita as $item)
                    @php
                        // Mapping status colors and labels
                        $statusColors = [
                            'Draft' => ['bg' => '#F3F4F6', 'color' => '#6B7280', 'label' => 'Draf'],
                            'Review' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Ditinjau'], 
                            'Revisi' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'label' => 'Revisi'], 
                            'Publish' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'label' => 'Terbit'], 
                            'Archive' => ['bg' => '#E5E7EB', 'color' => '#4B5563', 'label' => 'Diarsipkan'],
                        ];
                        $s_bg = $statusColors[$item->status]['bg'] ?? '#F3F4F6';
                        $s_color = $statusColors[$item->status]['color'] ?? '#6B7280';
                        $s_label = $statusColors[$item->status]['label'] ?? $item->status;
                        $imageUrl = $item->featured_image ? asset($item->featured_image) : 'https://placehold.co/400x300?text=No+Image';
                    @endphp
                    <tr>
                        <td class="py-3 px-3">
                            <div class="rounded-1 overflow-hidden bg-light mx-auto position-relative" style="width: 150px; height: 95px; border: 1px solid #E5E7EB;">
                                @if($item->featured_image)
                                    <img src="{{ asset($item->featured_image) }}" alt="{{ $item->judul_berita }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background-color: #F3F4F6; color: #9CA3AF;">
                                        <i class="bi bi-image" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 px-3">
                            <div class="fw-bold text-dark small">{{ $item->judul_berita }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-1 px-2 py-1 fw-medium" style="font-size: 10px; background: #E5E7EB; color: #374151;">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="text-muted small py-3 px-3">{{ $item->operator->username ?? 'Unknown' }}</td>
                        <td class="py-3 px-3">
                            <span class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-1" style="background: {{ $s_bg }}; color: {{ $s_color }};">
                                <span class="rounded-circle" style="width: 6px; height: 6px; display: inline-block; background: {{ $s_color }};"></span>
                                {{ $s_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <button type="button" class="btn btn-sm rounded-3 fw-semibold px-3" style="font-size: 12px; background: #F3F4F6; color: #374151; border: 1px solid #D1D5DB;" onclick="openDetailModal('{{ $item->id }}')">
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
    @if($berita->total() > 0)
    <div class="d-flex justify-content-between align-items-center p-3 border-top" style="background: #FBF9F8; border-color: #E5E7EB !important;">
        <div class="text-muted" style="font-size: 12px; color: #40493D !important;">
            Menampilkan {{ $berita->firstItem() ?? 0 }}-{{ $berita->lastItem() ?? 0 }} dari {{ $berita->total() }} berita
        </div>
        <div class="d-flex gap-1">
            @if (!$berita->onFirstPage())
                <a href="{{ $berita->previousPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Sebelumnya</a>
            @endif

            @php
                $start = max(1, $berita->currentPage() - 2);
                $end = min($berita->lastPage(), $berita->currentPage() + 2);
            @endphp
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $berita->currentPage())
                    <button class="btn btn-sm rounded-1 px-3 py-1 text-white btn-gradient-green" style="font-size: 12px; cursor: default;">{{ $page }}</button>
                @else
                    <a href="{{ $berita->url($page) }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">{{ $page }}</a>
                @endif
            @endfor

            @if ($berita->hasMorePages())
                <a href="{{ $berita->nextPageUrl() }}" class="btn btn-sm btn-white border rounded-1 px-3 py-1 text-decoration-none" style="font-size: 12px; color: #40493D; background: white;">Selanjutnya</a>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal Detail Berita --}}
<div class="modal fade" id="modalDetailBerita" tabindex="-1" aria-labelledby="modalDetailBeritaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header px-4 py-3 border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-5" id="modalDetailBeritaLabel">Detail Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    {{-- Left Column: Image & Info --}}
                    <div class="col-lg-4">
                        <div class="rounded-3 overflow-hidden mb-3 border" style="height: 200px; background-color: #f8f9fa;">
                            <img id="detailImage" src="" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        
                        <div class="p-4 rounded-3" style="background-color: #F3F4F6; border: 1px solid #E5E7EB;">
                            <h6 class="fw-bold mb-4" style="font-size: 13px; letter-spacing: 0.5px; color: #6B7280;">INFORMASI PUBLIKASI</h6>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Status</span>
                                <span id="detailStatus" class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                                    <span class="rounded-circle status-dot" style="width: 6px; height: 6px; display: inline-block;"></span>
                                    <span class="status-text">Publish</span>
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Kategori</span>
                                <span id="detailKategori" class="badge rounded-1 px-2 py-1 fw-medium" style="background: #E5E7EB; color: #374151; font-size: 12px; border: 1px solid #D1D5DB;">
                                    SOSIAL
                                </span>
                            </div>
                            
                            <div class="mb-3">
                                <span class="d-block text-muted small mb-1">Penulis</span>
                                <div class="text-dark fw-medium fs-6 d-flex align-items-center gap-2">
                                    <i class="bi bi-person" style="color: #6B7280;"></i>
                                    <span id="detailPenulis">Budi Santoso</span>
                                </div>
                            </div>
                            
                            <div>
                                <span class="d-block text-muted small mb-1">Tanggal Publikasi</span>
                                <div class="text-dark fw-medium fs-6 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event" style="color: #6B7280;"></i>
                                    <span id="detailTanggal">15 Agustus 2023, 09:00 WIB</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Right Column: Content --}}
                    <div class="col-lg-8">
                        <h3 id="detailJudul" class="fw-bold text-dark mb-4" style="line-height: 1.4;">Vaksinasi Massal Warga 04</h3>
                        
                        <div id="detailKonten" class="text-dark" style="line-height: 1.7; font-size: 15px;">
                            <p>Dalam upaya mempercepat program pemerintah dan memastikan kekebalan kelompok (herd immunity) di lingkungan kita, Pengurus RW 04 bersama dengan Puskesmas Kecamatan dan relawan kesehatan setempat telah sukses menyelenggarakan acara Vaksinasi Massal. Acara ini ditujukan khusus bagi seluruh warga RW 04 yang belum mendapatkan dosis vaksin lengkap atau vaksin booster.</p>
                            
                            <h6 class="fw-bold mt-4 mb-2">Detail Pelaksanaan</h6>
                            <ul class="mb-4 text-dark">
                                <li class="mb-2"><strong>Lokasi:</strong> Balai Warga RW 04, Jl. Merdeka Bersama No. 45.</li>
                                <li class="mb-2"><strong>Waktu Pelaksanaan:</strong> Sabtu, 12 Agustus 2023, mulai pukul 08.00 WIB hingga selesai.</li>
                                <li class="mb-2"><strong>Jenis Vaksin Tersedia:</strong> Sinovac, AstraZeneca, dan Pfizer (khusus booster).</li>
                            </ul>
                            
                            <p>Antusiasme warga sangat luar biasa. Sejak pagi hari, antrean sudah terlihat memanjang namun tetap tertib berkat pengaturan dari panitia lokal dan petugas keamanan lingkungan. Tercatat lebih dari 350 warga dari berbagai rentang usia, mulai dari lansia hingga remaja, turut berpartisipasi dalam kegiatan ini.</p>
                            
                            <p>"Kami sangat berterima kasih atas partisipasi aktif warga. Kesehatan lingkungan adalah tanggung jawab bersama. Dengan cakupan vaksinasi yang tinggi, kita berharap RW 04 bisa menjadi kawasan yang lebih aman dan tangguh menghadapi ancaman penyakit," ujar Ketua RW 04 dalam sambutannya.</p>
                            
                            <div class="p-3 mt-4 rounded-end-2 border-start border-4 border-success" style="background-color: #F0FDF4; color: #166534; font-size: 14px;">
                                Bagi warga yang berhalangan hadir pada acara ini, Puskesmas Kecamatan tetap melayani vaksinasi reguler setiap hari Senin-Jumat pukul 08.00 - 12.00 WIB. Harap membawa KTP dan bukti vaksinasi sebelumnya.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light border-top" id="detailFooterActions">
                <!-- Action buttons will be injected dynamically based on status -->
            </div>
        </div>
    </div>
</div>

{{-- Modal Buat Konten Berita --}}
<div class="modal fade" id="modalBuatBerita" tabindex="-1" aria-labelledby="modalBuatBeritaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('opkonten.berita.store') }}" method="POST" enctype="multipart/form-data" id="formBuatBerita" class="modal-content" style="background: #FBF9F8; border-radius: 12px; border: none; box-shadow: 0px 25px 50px -12px rgba(0, 0, 0, 0.25);">
            @csrf
            <input type="hidden" name="isi_berita" id="isiBeritaInput">
            <input type="hidden" name="action" id="actionInput">
            <input type="hidden" name="berita_id" id="beritaIdInput">
            
            <div class="modal-header px-4 py-3 border-bottom" style="border-color: #BFCABA !important;">
                <h5 class="modal-title fw-bold text-dark" id="modalBuatBeritaLabel" style="font-size: 20px;">Buat Konten Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background-color: #FBF9F8;">
                {{-- Alert Catatan Revisi (Hidden by default) --}}
                <div id="alertCatatanRevisi" class="alert alert-danger d-none d-flex gap-3 align-items-start mb-4" role="alert" style="border-radius: 10px; border-left: 4px solid #dc3545;">
                    <i class="bi bi-exclamation-octagon-fill fs-4 text-danger mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-danger">Catatan Perbaikan dari RW</h6>
                        <p id="teksCatatanRevisi" class="mb-0 small text-dark"></p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark" style="font-size: 14px;">Judul Berita</label>
                        <input type="text" name="judul_berita" id="judulBeritaInput" class="form-control bg-light" style="font-size: 14px; border: 1px solid #BFCABA;" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark" style="font-size: 14px;">Kategori</label>
                        <select name="kategori" id="kategoriSelect" class="form-select bg-light" style="font-size: 14px; border: 1px solid #BFCABA;" required>
                            <option selected disabled value="">Pilih kategori</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Infrastruktur">Infrastruktur</option>
                            <option value="Hiburan">Hiburan</option>
                            <option value="Kesehatan">Kesehatan</option>
                            <option value="Keamanan">Keamanan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Isi Konten</label>
                    <div class="quill-wrapper bg-light rounded-3">
                        <div id="editor-container" style="height: 200px; font-size: 14px; background-color: #FBF9F8;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark" style="font-size: 14px;">Foto Utama</label>
                    <input type="file" name="foto_utama" id="fotoUtama" class="d-none" accept="image/*">
                    <div id="dropZone" class="p-4 text-center rounded-3 bg-white" style="border: 2px dashed #BFCABA; cursor: pointer; transition: all 0.2s;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 50px; height: 50px; background: rgba(13, 99, 27, 0.1);">
                            <i class="bi bi-cloud-arrow-up fs-4 text-success"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Tarik dan lepas gambar di sini, atau klik untuk mencari</h6>
                        <p class="text-muted small mb-0" style="font-size: 12px;">Format didukung: JPG, PNG, WEBP (Maks 5MB)</p>
                    </div>
                    
                    {{-- Thumbnail Preview (muncul setelah diunggah) --}}
                    <div id="previewContainer" class="d-none mt-3">
                        <div class="position-relative d-inline-block rounded border shadow-sm p-1 bg-white">
                            <img id="imagePreview" src="" alt="Preview" class="rounded" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="openLightbox()">
                            <button type="button" class="btn-close position-absolute bg-white rounded-circle shadow-sm" style="top: -8px; right: -8px; padding: 4px; font-size: 10px;" onclick="removeImage()" aria-label="Remove"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-white border-top d-flex justify-content-end" style="border-color: #BFCABA !important;">
                <div id="footerCreateActions" class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-dark fw-semibold px-4 btn-hover-gray" style="border-color: #D1D5DB; background: white;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="draft" class="btn btn-draft fw-semibold px-4" style="border: 1px solid #707A6C; color: #1B1C1C;">
                        Simpan sebagai Draft
                    </button>
                    <button type="submit" name="action" value="review" class="btn fw-semibold px-4 text-white btn-gradient-green">
                        Ajukan Berita
                    </button>
                </div>

                <div id="footerEditActions" class="d-flex gap-2 d-none">
                    <button type="button" class="btn btn-outline-dark fw-semibold px-4 btn-hover-gray" style="border-color: #D1D5DB; background: white;" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="action" value="draft" class="btn fw-semibold px-4 text-white btn-gradient-green">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
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
                ['bold', 'italic', 'underline', 'strike', 'blockquote'],
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
            'ql-blockquote': 'Highlight Hijau (Kutipan)',
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
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');
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
            fileInput.files = e.dataTransfer.files; // sync to input
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
        }
        reader.readAsDataURL(file);
    }

    function removeImage() {
        fileInput.value = '';
        imagePreview.src = '';
        previewContainer.classList.add('d-none');
    }

    // Lightbox Logic
    function openLightbox() {
        const lightboxImg = document.getElementById('lightboxImage');
        lightboxImg.src = imagePreview.src;
        const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
        modal.show();
    }

    let allowModalClose = false;
    let formDirty = false;

    // Track unsaved changes
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = document.querySelectorAll('#formBuatBerita input, #formBuatBerita select, #fotoUtama');
        formInputs.forEach(el => {
            el.addEventListener('input', () => formDirty = true);
            el.addEventListener('change', () => formDirty = true);
        });
        
        // Track quill changes
        quill.on('text-change', function() {
            formDirty = true;
        });
    });

    // Remove static backdrop so hide.bs.modal CAN trigger on outside click
    document.getElementById('modalBuatBerita').setAttribute('data-bs-backdrop', 'true');

    // Intercept modal close event (X button, backdrop, or ESC)
    document.getElementById('modalBuatBerita').addEventListener('hide.bs.modal', function (event) {
        if (allowModalClose) {
            allowModalClose = false; // Reset for next time
            return; // Allow closing
        }

        // Hanya tampilkan pop-up peringatan jika ada perubahan yang belum disimpan
        if (formDirty) {
            event.preventDefault(); // Stop modal from closing
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmCloseModal'));
            confirmModal.show();
        }
    });

    // Efek blur pada form utama saat pop-up konfirmasi muncul
    document.addEventListener('DOMContentLoaded', function() {
        const confirmModalEl = document.getElementById('confirmCloseModal');
        if (confirmModalEl) {
            confirmModalEl.addEventListener('show.bs.modal', function () {
                const formContent = document.getElementById('formBuatBerita');
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
                const formContent = document.getElementById('formBuatBerita');
                formContent.style.filter = 'none';
            });
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

    function cleanupForm() {
        const form = document.getElementById('formBuatBerita');
        form.reset();
        form.action = "{{ route('opkonten.berita.store') }}";
        document.getElementById('beritaIdInput').value = '';
        document.getElementById('alertCatatanRevisi').classList.add('d-none');
        
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        
        quill.setText('');
        removeImage();
    }

    function forceCloseModal() {
        allowModalClose = true;
        
        const confirmModalEl = document.getElementById('confirmCloseModal');
        const confirmModal = bootstrap.Modal.getInstance(confirmModalEl);
        
        if (confirmModal) {
            // Hide main modal after confirm modal is completely hidden to avoid backdrop freeze
            confirmModalEl.addEventListener('hidden.bs.modal', function onHidden() {
                confirmModalEl.removeEventListener('hidden.bs.modal', onHidden);
                
                const buatBeritaModal = bootstrap.Modal.getInstance(document.getElementById('modalBuatBerita'));
                if (buatBeritaModal) buatBeritaModal.hide();
                
                cleanupForm();
            });
            confirmModal.hide();
        } else {
            const buatBeritaModal = bootstrap.Modal.getInstance(document.getElementById('modalBuatBerita'));
            if (buatBeritaModal) buatBeritaModal.hide();
            cleanupForm();
        }
    }
    
    function saveDraftFromConfirm() {
        // removed as no longer needed
    }

    // Sync Quill content before submit
    document.getElementById('formBuatBerita').addEventListener('submit', function(e) {
        document.getElementById('isiBeritaInput').value = quill.root.innerHTML;
        allowModalClose = true; // allow modal to close since we are submitting
    });

    function resetToCreate() {
        document.getElementById('modalBuatBeritaLabel').textContent = 'Buat Konten Berita';
        document.getElementById('footerCreateActions').classList.remove('d-none');
        document.getElementById('footerEditActions').classList.add('d-none');
        cleanupForm(); // Clean up form without hiding the modal (Bootstrap handles showing it)
        
        // Reset form dirty state after cleaning up
        setTimeout(() => { formDirty = false; }, 100);
    }

    // Modal Detail Berita Logic
    const dummyBeritaData = @json($berita->items());

    function openDetailModal(id) {
        const item = dummyBeritaData.find(b => b.id == id);
        if (!item) return;

        // Populate elements
        if (item.featured_image) {
            document.getElementById('detailImage').src = item.featured_image;
            document.getElementById('detailImage').alt = item.judul_berita;
            document.getElementById('detailImage').classList.remove('d-none');
            const iconPlaceholder = document.getElementById('detailImage').nextElementSibling;
            if (iconPlaceholder && iconPlaceholder.classList.contains('bi-image')) {
                iconPlaceholder.parentElement.remove();
            }
        } else {
            document.getElementById('detailImage').classList.add('d-none');
            // Insert placeholder icon if not exists
            if (!document.getElementById('detailImage').nextElementSibling) {
                const placeholder = document.createElement('div');
                placeholder.className = "d-flex align-items-center justify-content-center w-100 h-100";
                placeholder.style.backgroundColor = "#E5E7EB";
                placeholder.style.color = "#9CA3AF";
                placeholder.innerHTML = '<i class="bi bi-image" style="font-size: 4rem;"></i>';
                document.getElementById('detailImage').parentNode.appendChild(placeholder);
            }
        }
        
        const statusBadge = document.getElementById('detailStatus');
        
        const statusColors = {
            'Draft': { bg: '#F3F4F6', color: '#6B7280', label: 'Draf' },
            'Review': { bg: '#FEF3C7', color: '#D97706', label: 'Ditinjau' },
            'Revisi': { bg: '#FEE2E2', color: '#991B1B', label: 'Revisi' },
            'Publish': { bg: '#D1FAE5', color: '#065F46', label: 'Terbit' },
            'Archive': { bg: '#E5E7EB', color: '#4B5563', label: 'Diarsipkan' }
        };
        const s_bg = statusColors[item.status]?.bg || '#F3F4F6';
        const s_color = statusColors[item.status]?.color || '#6B7280';
        const s_label = statusColors[item.status]?.label || item.status;
        
        statusBadge.style.background = s_bg;
        statusBadge.style.color = s_color;
        statusBadge.querySelector('.status-dot').style.background = s_color;
        statusBadge.querySelector('.status-text').textContent = s_label;

        document.getElementById('detailKategori').textContent = item.kategori;
        document.getElementById('detailPenulis').textContent = item.operator ? item.operator.username : 'Unknown';
        document.getElementById('detailJudul').textContent = item.judul_berita;
        document.getElementById('detailKonten').innerHTML = item.isi_berita || '<p class="text-muted italic">Tidak ada konten</p>';
        
        const dateStr = item.tanggal_publish ? new Date(item.tanggal_publish).toLocaleString('id-ID') : '-';
        document.getElementById('detailTanggal').textContent = dateStr;

        // Dynamic Footer Buttons
        const detailFooter = document.getElementById('detailFooterActions');
        let actionsHtml = '';
        
        if (item.status === 'Draft' || item.status === 'Revisi') {
            actionsHtml += `
                <button type="button" class="btn btn-outline-dark fw-semibold px-4 d-flex align-items-center gap-2 btn-hover-gray" style="border-color: #D1D5DB; background: white;" onclick="editBerita()">
                    <i class="bi bi-pencil"></i> Edit Berita
                </button>
                <form action="/op-konten/berita/${item.id}/submit" method="POST" class="d-inline" id="formSubmit_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn fw-semibold px-4 text-white btn-gradient-green" onclick="confirmSubmit('${item.id}')">
                        Ajukan Berita
                    </button>
                </form>
            `;
            if (item.status === 'Draft') {
                actionsHtml += `
                    <form action="/op-konten/berita/${item.id}" method="POST" class="d-inline ms-auto" id="formDelete_${item.id}">
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
                <form action="/op-konten/berita/${item.id}/revoke" method="POST" class="d-inline" id="formRevoke_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn btn-outline-warning fw-semibold px-4" onclick="confirmRevoke('${item.id}')">
                        <i class="bi bi-arrow-return-left"></i> Batalkan Pengajuan
                    </button>
                </form>
            `;
        } else if (item.status === 'Publish') {
            actionsHtml += `
                <form action="/op-konten/berita/${item.id}/archive" method="POST" class="d-inline" id="formArchive_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn btn-outline-secondary fw-semibold px-4" onclick="document.getElementById('formArchive_${item.id}').submit()">
                        <i class="bi bi-archive"></i> Arsipkan
                    </button>
                </form>
            `;
        } else if (item.status === 'Archive') {
            actionsHtml += `
                <form action="/op-konten/berita/${item.id}/unarchive" method="POST" class="d-inline" id="formUnarchive_${item.id}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <button type="button" class="btn btn-outline-primary fw-semibold px-4" onclick="document.getElementById('formUnarchive_${item.id}').submit()">
                        <i class="bi bi-box-arrow-up"></i> Buka Arsip
                    </button>
                </form>
            `;
        }
        
        detailFooter.innerHTML = actionsHtml;

        // Save ID for edit
        document.getElementById('beritaIdInput').value = item.id;

        // Show modal
        const detailModal = new bootstrap.Modal(document.getElementById('modalDetailBerita'));
        detailModal.show();
    }

    function editBerita() {
        document.getElementById('modalBuatBeritaLabel').textContent = 'Edit Berita';
        document.getElementById('footerCreateActions').classList.add('d-none');
        document.getElementById('footerEditActions').classList.remove('d-none');

        const detailModal = bootstrap.Modal.getInstance(document.getElementById('modalDetailBerita'));
        if (detailModal) detailModal.hide();
        
        const id = document.getElementById('beritaIdInput').value;
        const item = dummyBeritaData.find(b => b.id == id);
        
        if (item) {
            document.getElementById('judulBeritaInput').value = item.judul_berita;
            document.getElementById('kategoriSelect').value = item.kategori;
            quill.root.innerHTML = item.isi_berita;
            
            if (item.featured_image) {
                document.getElementById('imagePreview').src = item.featured_image;
                document.getElementById('previewContainer').classList.remove('d-none');
            }

            // Catatan Revisi
            const alertRevisi = document.getElementById('alertCatatanRevisi');
            if (item.status === 'Revisi' && item.catatan_revisi) {
                alertRevisi.classList.remove('d-none');
                document.getElementById('teksCatatanRevisi').textContent = item.catatan_revisi;
            } else {
                alertRevisi.classList.add('d-none');
            }
            
            // Set form to update
            const form = document.getElementById('formBuatBerita');
            form.action = `/op-konten/berita/${item.id}`;
            
            // Inject PUT method securely
            if (!form.querySelector('input[name="_method"]')) {
                const putMethod = document.createElement('input');
                putMethod.type = 'hidden';
                putMethod.name = '_method';
                putMethod.value = 'PUT';
                form.appendChild(putMethod);
            }
        }
        
        // Reset form dirty state after loading data
        setTimeout(() => { formDirty = false; }, 100);
        
        const buatBeritaModal = new bootstrap.Modal(document.getElementById('modalBuatBerita'));
        buatBeritaModal.show();
    }
</script>

{{-- Modal Konfirmasi Keluar --}}
<div class="modal fade" id="confirmCloseModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <i class="bi bi-exclamation-circle text-danger mb-3" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mb-2">Buang Perubahan?</h5>
                <p class="text-muted small mb-4">Ada perubahan yang belum disimpan. Apakah Anda yakin ingin membuang perubahan ini?</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-draft fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #707A6C; color: #1B1C1C;">Lanjutkan Mengedit</button>
                    <button type="button" class="btn btn-danger text-white" onclick="forceCloseModal()">Buang Perubahan</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Logic & Confirmations -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Berita Ini?',
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
            if (result.isConfirmed) {
                document.getElementById('formDelete_' + id).submit();
            }
        })
    }

    function confirmSubmit(id) {
        Swal.fire({
            title: 'Ajukan berita ke Ketua RW?',
            text: "Berita akan masuk tahap review dan menunggu persetujuan Ketua RW.",
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
            if (result.isConfirmed) {
                document.getElementById('formSubmit_' + id).submit();
            }
        })
    }

    function confirmRevoke(id) {
        Swal.fire({
            title: 'Batalkan Pengajuan?',
            text: "Berita akan ditarik kembali menjadi status Draf.",
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
            if (result.isConfirmed) {
                document.getElementById('formRevoke_' + id).submit();
            }
        })
    }

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            showNotification("{!! addslashes(session('success')) !!}");
        });
    @endif
    
    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            showNotification("{!! addslashes(session('error')) !!}", true);
        });
    @endif

    // Live search with debounce
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            // Auto-focus and move cursor to end if there's a search value
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
    });
</script>
@endpush
@endsection