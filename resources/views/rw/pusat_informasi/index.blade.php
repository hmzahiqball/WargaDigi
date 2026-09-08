@extends('layouts.global')

@section('title', 'Pusat Informasi')

@push('styles')
<style>
    /* Tabs Styling */
    .custom-tabs {
        display: flex;
        gap: 1.5rem;
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 5px;
    }
    .custom-tabs::-webkit-scrollbar {
        height: 4px;
    }
    .custom-tabs::-webkit-scrollbar-thumb {
        background: #E5E7EB;
        border-radius: 4px;
    }
    .custom-tab-btn {
        background: none;
        border: none;
        padding: 0.5rem 0.25rem;
        font-weight: 600;
        font-size: 15px;
        color: #6B7280;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }
    .custom-tab-btn:hover {
        color: #374151;
    }
    .custom-tab-btn.active {
        color: #198754;
        border-bottom-color: #198754;
    }

    /* List Item Styling */
    .list-item-card {
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 1.25rem;
        background: #fff;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        gap: 1rem;
        margin-bottom: 0.75rem;
        position: relative;
        overflow: hidden;
    }
    .list-item-card:hover {
        border-color: #BFCABA;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    .list-item-card.selected {
        border-color: #198754;
        background-color: #F0FDF4;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.1);
    }
    .list-item-card.selected::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #198754;
        border-radius: 4px 0 0 4px;
    }
    
    .item-icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: hidden;
        transition: all 0.2s;
    }
    .item-icon-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Detail Panel Styling */
    .detail-panel {
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 120px);
        position: sticky;
        top: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .detail-header {
        padding: 1.5rem 1.75rem;
        border-bottom: 1px solid #E5E7EB;
        background: #F9FAFB;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .detail-body {
        padding: 1.75rem;
        overflow-y: auto;
        flex-grow: 1;
    }
    .detail-footer {
        padding: 1.25rem 1.75rem;
        border-top: 1px solid #E5E7EB;
        background: #fff;
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    /* Badges & Buttons */
    .btn-gradient-green {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
        border: none !important;
        color: white !important;
        transition: opacity 0.2s, transform 0.1s;
    }
    .btn-gradient-green:hover {
        opacity: 0.95;
        transform: translateY(-1px);
    }
    .status-badge {
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.3px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .type-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        background: #E5E7EB;
        color: #4B5563;
        margin-right: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Quick Rejection Pills */
    .quick-reject-pill {
        font-size: 12px;
        padding: 4px 10px;
        background: #F3F4F6;
        color: #4B5563;
        border-radius: 16px;
        cursor: pointer;
        border: 1px solid #E5E7EB;
        transition: all 0.2s;
        display: inline-block;
        margin-right: 6px;
        margin-bottom: 6px;
    }
    .quick-reject-pill:hover {
        background: #E5E7EB;
        color: #1F2937;
    }

    /* Scrollbar */
    .list-container {
        height: calc(100vh - 180px);
        overflow-y: auto;
        padding-right: 6px;
    }
    .list-container::-webkit-scrollbar,
    .detail-body::-webkit-scrollbar {
        width: 5px;
    }
    .list-container::-webkit-scrollbar-thumb,
    .detail-body::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 10px;
    }
    
    .fade-in { animation: fadeIn 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
    @keyframes fadeIn { 
        from { opacity: 0; transform: translateY(8px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    
    /* Layout Transition Classes */
    #listCol, #detailCol {
        transition: all 0.3s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Pusat Informasi</h2>
        <p class="text-muted mb-0">Meninjau dan mengelola pengajuan Agenda, Berita, & Pengumuman.</p>
    </div>
</div>

<div class="row g-4" id="mainRow">
    {{-- List Column --}}
    <div class="col-12" id="listCol">
        
        {{-- Header Controls: Tabs + Search/Filter --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 border-bottom pb-2 gap-3">
            {{-- Tabs --}}
            <div class="custom-tabs mb-0">
                <button class="custom-tab-btn active" id="tabSemua" onclick="switchTab('Semua')">Lihat Semua</button>
                <button class="custom-tab-btn" id="tabBerita" onclick="switchTab('Berita')">Berita Komunitas</button>
                <button class="custom-tab-btn" id="tabAgenda" onclick="switchTab('Agenda')">Agenda Kegiatan</button>
                <button class="custom-tab-btn" id="tabPengumuman" onclick="switchTab('Pengumuman')">Pengumuman</button>
            </div>

            {{-- Search & Filter --}}
            <div class="d-flex gap-2">
                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Cari..." autocomplete="off">
                </div>
                <select id="statusFilter" class="form-select form-select-sm text-muted fw-medium" style="width: 140px;">
                    <option value="all">Semua Status</option>
                    <option value="Review">Ditinjau</option>
                    <option value="Publish">Terbit</option>
                    <option value="Revisi">Revisi</option>
                </select>
            </div>
        </div>

        {{-- Items List --}}
        <div class="list-container" id="itemsList">
            <!-- Items will be rendered here by JS -->
        </div>
    </div>

    {{-- Details Column (Hidden by default) --}}
    <div class="col-12 d-none fade-in" id="detailCol">
        
        {{-- Breadcrumb Navigation for Detail --}}
        <div class="d-flex align-items-center mb-3">
            <button class="btn btn-light border btn-sm me-3 text-muted rounded-circle d-flex justify-content-center align-items-center" style="width: 36px; height: 36px;" onclick="closeDetail()" title="Kembali ke daftar">
                <i class="bi bi-arrow-left fs-5"></i>
            </button>
            <nav aria-label="breadcrumb" class="mb-0">
                <ol class="breadcrumb mb-0" style="font-size: 14px; font-weight: 500;">
                    <li class="breadcrumb-item"><a href="#" onclick="closeDetail(); return false;" class="text-decoration-none text-success">Pusat Informasi</a></li>
                    <li class="breadcrumb-item text-secondary" id="breadcrumbType">Kategori</li>
                    <li class="breadcrumb-item active text-dark text-truncate" id="breadcrumbTitle" aria-current="page" style="max-width: 250px;">Judul Konten</li>
                </ol>
            </nav>
        </div>

        <div class="detail-panel border-0 shadow-sm" id="detailPanel" style="height: auto; min-height: calc(100vh - 200px);">
            {{-- Content State --}}
            <div id="detailContent" class="h-100 d-flex flex-column">
                <div class="detail-header" style="border-radius: 16px 16px 0 0;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="type-badge" id="detailTypeBadge"></span>
                            <div id="detailStatusBadgeContainer">
                                <!-- Status Badge -->
                            </div>
                        </div>
                        <div class="small text-muted mt-2" id="detailId" style="font-family: monospace; font-size: 11px;">ID: -</div>
                    </div>
                </div>

                <div class="detail-body px-4 px-md-5 py-4">
                    <h3 class="fw-bold text-dark mb-3" id="detailTitle" style="line-height: 1.4; font-size: 1.8rem;"></h3>
                    <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center border" style="width: 45px; height: 45px;">
                            <i class="bi bi-person fs-5 text-secondary"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Diajukan oleh <span class="fw-bold text-dark" id="detailAuthor"></span></p>
                            <p class="text-muted small mb-0"><i class="bi bi-clock"></i> Diperbarui <span id="detailTimeAgo"></span></p>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 p-3 rounded-4" style="background: #F9FAFB; border: 1px solid #E5E7EB;">
                        <div class="col-md-4">
                            <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Waktu & Tanggal</span>
                            <div class="fw-bold text-dark mt-1 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                <i class="bi bi-calendar-event text-success"></i> <span id="detailDate"></span>
                            </div>
                        </div>
                        <div class="col-md-4" id="detailLocationBox">
                            <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Lokasi</span>
                            <div class="fw-bold text-dark mt-1 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                                <i class="bi bi-geo-alt text-danger"></i> <span id="detailLocation"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Kategori</span>
                            <div class="mt-1">
                                <span class="badge rounded-pill px-3 py-1 fw-semibold" style="background: white; color: #374151; font-size: 12px; border: 1px solid #D1D5DB;" id="detailKategori"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4 d-none" id="detailExtraInfoBox">
                        <div class="col-12 mb-3">
                            <div class="p-3 bg-light rounded-3 border" style="border-color: #E5E7EB;">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bi bi-people-fill text-success me-2 fs-5"></i>
                                    <span class="fw-bold text-dark" style="font-size: 14px;">Konfirmasi Kehadiran (RSVP)</span>
                                </div>
                                <span id="detailRsvpStatus" class="d-block text-muted" style="font-size: 13px;">Aktif</span>
                            </div>
                        </div>
                        <div class="col-12" id="detailMapContainer" style="display: none;">
                            <span class="d-block text-muted mb-2" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Titik Lokasi Peta</span>
                            <div class="rounded-3 overflow-hidden border" style="border-color: #E5E7EB; height: 250px;" id="mapDetailAgenda"></div>
                        </div>
                    </div>

                    <span class="d-block text-muted mb-2 fw-semibold" style="font-size: 13px;">Lampiran Flyer/Poster/Gambar</span>
                    <div class="mb-4" id="detailImageBox">
                        <div class="rounded-4 overflow-hidden border bg-light text-center p-3">
                            <img id="detailImage" src="" alt="Poster" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: contain; display: none; cursor: pointer;" onclick="if(this.src) window.open(this.src, '_blank')">
                            <span id="detailImageEmpty" class="text-muted small">Tidak ada lampiran</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h6 class="fw-bold text-dark mb-3" style="font-size: 14px; letter-spacing: 0.5px; text-transform: uppercase;">Isi Konten</h6>
                        <div class="p-4 rounded-4 text-dark bg-white shadow-sm" style="border: 1px solid #E5E7EB; min-height: 200px; font-size: 15px; line-height: 1.8;" id="detailRawContent">
                        </div>
                    </div>

                    {{-- Form Penolakan (Hanya muncul jika butuh review) --}}
                    <div id="rejectionBox" class="mb-4 d-none">
                        <div class="p-4 rounded-4 border" style="background-color: #FAFAFA;">
                            <h6 class="fw-bold text-danger mb-2" style="font-size: 14px;"><i class="bi bi-x-circle me-1"></i> Catatan Revisi</h6>
                            <p class="small text-muted mb-3">Pilih atau ketik alasan penolakan jika Anda meminta revisi untuk pengajuan ini.</p>
                            
                            {{-- Quick Pills --}}
                            <div class="mb-3" id="quickPillsBox">
                                <!-- Injected by JS -->
                            </div>

                            <textarea id="catatanRevisi" class="form-control bg-white" rows="4" placeholder="Ketik catatan perbaikan secara detail..." style="border-radius: 12px; font-size: 14.5px; padding: 14px;"></textarea>
                            <div class="invalid-feedback fw-medium small mt-2">Catatan perbaikan wajib diisi jika Anda menolak.</div>
                        </div>
                    </div>
                    
                    {{-- Catatan Penolakan Lama (Jika status = Revisi) --}}
                    <div id="pastRejectionBox" class="mb-4 d-none">
                        <div class="alert alert-danger mb-0 border-0 shadow-sm p-4" style="background-color: #FEF2F2; color: #991B1B; border-radius: 16px;">
                            <div class="d-flex gap-3">
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-2" style="font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan Revisi Terakhir</h6>
                                    <p id="pastCatatanRevisi" class="mb-0" style="font-size: 14.5px; line-height: 1.6;"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="detail-footer" id="detailFooterActions" style="border-radius: 0 0 16px 16px; background: #F9FAFB;">
                    <button type="button" class="btn fw-bold px-4 rounded-pill text-danger" style="border: 1px solid #FCA5A5; background: white; font-size: 14.5px;" id="btnReject" onclick="handleReject()">
                        Tolak & Revisi
                    </button>
                    <button type="button" class="btn fw-bold px-5 rounded-pill btn-gradient-green shadow-sm" style="font-size: 14.5px;" id="btnApprove" onclick="handleApprove()">
                        <i class="bi bi-check-lg me-1"></i> Setujui Pengajuan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const semuaData = @json($semua);
    const agendaData = @json($agenda);
    const beritaData = @json($berita);
    const pengumumanData = @json($pengumuman);
    
    let currentTab = 'Semua';
    let currentData = semuaData;
    let selectedItem = null;

    const statusConfig = {
        'Draft': { bg: '#F3F4F6', color: '#6B7280', label: 'Draf', icon: 'bi-file-earmark' },
        'Review': { bg: '#FEF3C7', color: '#D97706', label: 'Ditinjau', icon: 'bi-hourglass-split' },
        'Revisi': { bg: '#FEE2E2', color: '#991B1B', label: 'Revisi', icon: 'bi-exclamation-triangle' },
        'Publish': { bg: '#D1FAE5', color: '#065F46', label: 'Terbit', icon: 'bi-check-circle' },
        'Archive': { bg: '#E5E7EB', color: '#4B5563', label: 'Diarsipkan', icon: 'bi-archive' }
    };

    const typeConfig = {
        'Agenda': { color: '#0284c7', bg: '#e0f2fe', icon: 'bi-calendar-event' },
        'Berita': { color: '#059669', bg: '#d1fae5', icon: 'bi-newspaper' },
        'Pengumuman': { color: '#d97706', bg: '#fef3c7', icon: 'bi-megaphone' }
    };

    const quickReasons = {
        'Agenda': ['Waktu tidak sesuai', 'Lokasi belum jelas', 'Deskripsi kurang lengkap', 'Perbaiki poster/flyer'],
        'Berita': ['Perbaiki tata bahasa', 'Konten kurang relevan', 'Ganti gambar resolusi tinggi', 'Sumber tidak valid'],
        'Pengumuman': ['Informasi kurang jelas', 'Kurangi teks yang terlalu panjang', 'Bukan prioritas saat ini'],
        'Semua': ['Informasi kurang jelas', 'Perbaiki tata bahasa']
    };

    function switchTab(tabName) {
        currentTab = tabName;
        if(tabName === 'Agenda') currentData = agendaData;
        else if(tabName === 'Berita') currentData = beritaData;
        else if(tabName === 'Pengumuman') currentData = pengumumanData;
        else currentData = semuaData;
        
        document.getElementById('tabSemua').classList.toggle('active', tabName === 'Semua');
        document.getElementById('tabAgenda').classList.toggle('active', tabName === 'Agenda');
        document.getElementById('tabBerita').classList.toggle('active', tabName === 'Berita');
        document.getElementById('tabPengumuman').classList.toggle('active', tabName === 'Pengumuman');
        
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = 'all';
        
        closeDetail();
        renderList();
        renderQuickReasons();
    }

    function generateStatusBadge(status) {
        const conf = statusConfig[status] || statusConfig['Draft'];
        return `
            <span class="status-badge" style="background: ${conf.bg}; color: ${conf.color};">
                <span class="rounded-circle" style="width: 7px; height: 7px; background: ${conf.color};"></span>
                ${conf.label}
            </span>
        `;
    }

    function renderList() {
        const listContainer = document.getElementById('itemsList');
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        
        let filteredData = currentData.filter(item => {
            const matchesSearch = item.title.toLowerCase().includes(searchTerm) || 
                                  item.author.toLowerCase().includes(searchTerm);
            const matchesStatus = statusFilter === 'all' || item.status === statusFilter;
            return matchesSearch && matchesStatus;
        });

        if (filteredData.length === 0) {
            listContainer.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center py-5 mt-5">
                    <i class="bi bi-search fs-1 mb-3 text-secondary opacity-25"></i>
                    <h6 class="fw-bold text-dark">Tidak ada data ditemukan</h6>
                    <p class="small text-muted mb-0">Coba sesuaikan kata kunci pencarian atau filter status.</p>
                </div>
            `;
            return;
        }

        let html = '';
        filteredData.forEach(item => {
            const isSelected = selectedItem && selectedItem.id === item.id && selectedItem.type === item.type;
            const descPreview = item.description ? (item.description.length > 55 ? item.description.substring(0, 55) + '...' : item.description) : 'Tidak ada deskripsi.';
            
            // Icon or Image Wrapper logic
            const conf = typeConfig[item.type] || { color: '#6B7280', bg: '#F3F4F6', icon: 'bi-file-earmark' };
            let iconHtml = '';
            if (item.image) {
                iconHtml = `<img src="${item.image}" alt="Thumb">`;
            } else {
                iconHtml = `<i class="bi ${conf.icon} fs-4"></i>`;
            }
            
            let wrapperStyle = `background: ${conf.bg}; color: ${conf.color};`;
            if(isSelected) wrapperStyle = `background: rgba(25, 135, 84, 0.15); color: #198754;`;

            html += `
                <div class="list-item-card ${isSelected ? 'selected fade-in' : ''}" onclick="selectItemById('${item.id}', '${item.type}')">
                    <div class="item-icon-wrapper" style="${wrapperStyle}">
                        ${iconHtml}
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center">
                                ${currentTab === 'Semua' ? `<span class="type-badge">${item.type}</span>` : ''}
                                <h6 class="fw-bold text-dark mb-0 text-truncate pe-2" style="font-size: 14.5px; padding-top: 2px;">${item.title}</h6>
                            </div>
                            <div>${generateStatusBadge(item.status)}</div>
                        </div>
                        <p class="text-muted small mb-2 text-truncate" style="font-size: 12.5px;">${descPreview}</p>
                        <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 11px; font-weight: 600;">
                            <span><i class="bi bi-clock me-1"></i> ${item.date}</span>
                            ${item.location ? `<span class="text-truncate" style="max-width: 150px;"><i class="bi bi-geo-alt me-1"></i> ${item.location}</span>` : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        
        listContainer.innerHTML = html;
    }

    function renderQuickReasons() {
        const box = document.getElementById('quickPillsBox');
        let reasons = quickReasons[currentTab] || quickReasons['Semua'];
        if (selectedItem) {
            reasons = quickReasons[selectedItem.type] || quickReasons['Semua'];
        }
        let html = '';
        reasons.forEach(r => {
            html += `<span class="quick-reject-pill" onclick="addReason('${r}')">${r}</span>`;
        });
        box.innerHTML = html;
    }

    function addReason(text) {
        const ta = document.getElementById('catatanRevisi');
        if(ta.value.length > 0) {
            ta.value += ', ' + text;
        } else {
            ta.value = text;
        }
        ta.classList.remove('is-invalid');
    }

    function closeDetail() {
        selectedItem = null;
        const listCol = document.getElementById('listCol');
        const detailCol = document.getElementById('detailCol');
        
        listCol.classList.remove('d-none');
        detailCol.classList.add('d-none');
        
        renderList();
    }

    function selectItemById(id, type) {
        try {
            const item = semuaData.find(x => x.id == id && x.type == type);
            if (item) {
                selectItem(item);
            } else {
                console.error("Item not found:", id, type);
            }
        } catch (e) {
            console.error("Error in selectItemById:", e);
        }
    }

    function selectItem(item) {
        try {
            selectedItem = item;
            
            if (!item) {
                closeDetail();
                return;
            }

            const listCol = document.getElementById('listCol');
            const detailCol = document.getElementById('detailCol');
            const detailContent = document.getElementById('detailContent');

            // Change layout to full page detail
            listCol.classList.add('d-none');
            detailCol.classList.remove('d-none');
            
            detailContent.classList.remove('fade-in');
            void detailContent.offsetWidth; 
            detailContent.classList.add('fade-in');

            // Update Breadcrumb
            document.getElementById('breadcrumbType').textContent = item.type || '-';
            document.getElementById('breadcrumbTitle').textContent = item.title || '-';

            const itemIdText = item.id ? item.id.toString().padStart(4, '0') : '0000';
            document.getElementById('detailId').textContent = `ID: ${item.type.toUpperCase()}-${new Date().getFullYear()}-${itemIdText}`;
            document.getElementById('detailTypeBadge').textContent = item.type || '';
            document.getElementById('detailStatusBadgeContainer').innerHTML = generateStatusBadge(item.status);
            document.getElementById('detailTitle').textContent = item.title || '';
            document.getElementById('detailAuthor').textContent = item.author || '';
            document.getElementById('detailTimeAgo').textContent = item.time_ago || '';
            document.getElementById('detailDate').textContent = item.date || '';
            
            const detailKategori = document.getElementById('detailKategori');
            if(detailKategori) detailKategori.textContent = item.kategori || '-';
            
            const detailRawContent = document.getElementById('detailRawContent');
            if(detailRawContent) detailRawContent.innerHTML = item.raw_content || '<i class="text-muted">Tidak ada konten</i>';

            // Extra Info Box (RSVP + Map)
            const extraInfoBox = document.getElementById('detailExtraInfoBox');
            if (item.type === 'Agenda') {
                extraInfoBox.classList.remove('d-none');
                
                // RSVP
                const rsvpStatus = document.getElementById('detailRsvpStatus');
                if (item.is_rsvp_enabled == 1) {
                    rsvpStatus.textContent = 'Aktif - Warga dapat mengonfirmasi kehadiran';
                    rsvpStatus.className = 'd-block text-success fw-medium';
                } else {
                    rsvpStatus.textContent = 'Tidak Aktif';
                    rsvpStatus.className = 'd-block text-muted';
                }
                
                // Map
                const mapContainer = document.getElementById('detailMapContainer');
                if (item.latitude && item.longitude) {
                    mapContainer.style.display = 'block';
                    setTimeout(() => {
                        if (!window.detailMap) {
                            window.detailMap = L.map('mapDetailAgenda').setView([item.latitude, item.longitude], 15);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(window.detailMap);
                            window.detailMarker = L.marker([item.latitude, item.longitude]).addTo(window.detailMap);
                        } else {
                            window.detailMap.setView([item.latitude, item.longitude], 15);
                            window.detailMarker.setLatLng([item.latitude, item.longitude]);
                            window.detailMap.invalidateSize();
                        }
                    }, 300);
                } else {
                    mapContainer.style.display = 'none';
                }
            } else {
                extraInfoBox.classList.add('d-none');
            }

            const detailLocationBox = document.getElementById('detailLocationBox');
            if (detailLocationBox) {
                if (item.location) {
                    detailLocationBox.classList.remove('d-none');
                    document.getElementById('detailLocation').textContent = item.location;
                } else {
                    detailLocationBox.classList.add('d-none');
                }
            }

            const detailImg = document.getElementById('detailImage');
            const detailImgEmpty = document.getElementById('detailImageEmpty');
            if (item.image) {
                detailImg.src = item.image;
                detailImg.style.display = 'inline-block';
                detailImgEmpty.style.display = 'none';
            } else {
                detailImg.src = '';
                detailImg.style.display = 'none';
                detailImgEmpty.style.display = 'inline-block';
            }

            const footerActions = document.getElementById('detailFooterActions');
            const rejectionBox = document.getElementById('rejectionBox');
            const pastRejectionBox = document.getElementById('pastRejectionBox');
            const inputCatatan = document.getElementById('catatanRevisi');
            
            if (inputCatatan) {
                inputCatatan.value = '';
                inputCatatan.classList.remove('is-invalid');
            }

            if (item.status === 'Review') {
                if(footerActions) footerActions.classList.remove('d-none');
                if(rejectionBox) rejectionBox.classList.remove('d-none');
                if(pastRejectionBox) pastRejectionBox.classList.add('d-none');
                renderQuickReasons();
            } else {
                if(footerActions) footerActions.classList.add('d-none');
                if(rejectionBox) rejectionBox.classList.add('d-none');
                
                if (item.status === 'Revisi' && item.reject_note) {
                    if(pastRejectionBox) pastRejectionBox.classList.remove('d-none');
                    const pastCatatan = document.getElementById('pastCatatanRevisi');
                    if(pastCatatan) pastCatatan.textContent = item.reject_note;
                } else {
                    if(pastRejectionBox) pastRejectionBox.classList.add('d-none');
                }
            }
            
            const detailBody = document.querySelector('.detail-body');
            if(detailBody) detailBody.scrollTop = 0;
            
        } catch (e) {
            console.error("Error in selectItem:", e);
        }
    }

    function handleApprove() {
        if (!selectedItem) return;

        Swal.fire({
            title: 'Setujui Pengajuan?',
            html: `Anda akan menyetujui dan mempublikasikan <b>${selectedItem.title}</b>.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn text-white btn-gradient-green fw-bold rounded-pill px-4 m-0 mb-2',
                cancelButton: 'btn fw-bold px-4 m-0',
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('approve');
            }
        });
    }

    function handleReject() {
        if (!selectedItem) return;
        
        const catatan = document.getElementById('catatanRevisi');
        if (!catatan.value.trim()) {
            catatan.classList.add('is-invalid');
            catatan.focus();
            return;
        }
        catatan.classList.remove('is-invalid');

        Swal.fire({
            title: 'Tolak & Revisi?',
            html: `Pengajuan ini akan dikembalikan ke operator dengan catatan revisi Anda.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-danger text-white fw-bold rounded-pill px-4 m-0 mb-2',
                cancelButton: 'btn fw-bold px-4 m-0',
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                submitAction('reject', catatan.value.trim());
            }
        });
    }

    function submitAction(actionType, catatan = null) {
        const typePath = selectedItem.type.toLowerCase();
        const url = `/rw/pusat-informasi/${typePath}/${selectedItem.id}/${actionType}`;
        
        const payload = {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            _method: 'PUT'
        };
        
        if (actionType === 'reject') {
            payload.catatan_revisi = catatan;
        }

        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.success,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(data.error || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message
            });
        });
    }

    document.getElementById('searchInput').addEventListener('input', renderList);
    document.getElementById('statusFilter').addEventListener('change', renderList);

    document.addEventListener('DOMContentLoaded', () => {
        renderList();
        renderQuickReasons();
    });
</script>
@endpush
