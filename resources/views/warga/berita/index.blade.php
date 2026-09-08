@extends('layouts.global')

@section('title', 'Berita & Pusat Informasi')

@push('styles')
<style>
    .page-title-section {
        margin-bottom: 2rem;
    }
    .hero-card {
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        height: 380px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        margin-bottom: 2rem;
    }
    .hero-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0) 100%);
        padding: 2.5rem 2rem 1.5rem;
        color: white;
    }
    .hero-badge {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        background-color: rgba(25, 135, 84, 0.85);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        backdrop-filter: blur(4px);
    }
    .hero-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    .hero-meta {
        font-size: 0.9rem;
        opacity: 0.9;
        display: flex;
        gap: 1.5rem;
    }
    
    /* Tabs */
    .custom-tabs-container {
        border-bottom: 1px solid #E5E7EB;
        margin-bottom: 2rem;
        display: flex;
        gap: 2rem;
    }
    .custom-tab-warga {
        background: none;
        border: none;
        padding: 0.75rem 0;
        font-weight: 600;
        color: #6B7280;
        font-size: 1rem;
        position: relative;
        transition: all 0.3s ease;
    }
    .custom-tab-warga:hover {
        color: #198754;
    }
    .custom-tab-warga.active {
        color: #198754;
    }
    .custom-tab-warga.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #198754;
        border-radius: 3px 3px 0 0;
    }

    /* Content Cards */
    .content-card {
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .content-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.06);
    }
    .content-card-img-wrapper {
        position: relative;
        height: 180px;
        overflow: hidden;
    }
    .content-card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .content-card-img-wrapper .placeholder-img {
        width: 100%;
        height: 100%;
        background-color: #F3F4F6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9CA3AF;
        font-size: 2.5rem;
    }
    .card-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: white;
        color: #374151;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .content-card-body {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .content-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .content-card-text {
        font-size: 0.9rem;
        color: #6B7280;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .content-card-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
    }
    .content-card-footer .date {
        color: #6B7280;
        font-weight: 500;
    }
    .content-card-footer .read-more {
        color: #198754;
        font-weight: 600;
        text-decoration: none;
    }
    .content-card-footer .read-more:hover {
        text-decoration: underline;
    }

    /* Sidebar Widgets */
    .widget-box {
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        background: #fff;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .widget-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1F2937;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    /* Pengumuman Alerts */
    .pengumuman-alert {
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }
    .pengumuman-alert.danger {
        background-color: #FEF2F2;
        border: 1px solid #FEE2E2;
    }
    .pengumuman-alert.info {
        background-color: #EFF6FF;
        border: 1px solid #DBEAFE;
    }
    .pengumuman-alert .icon {
        font-size: 1.25rem;
        margin-top: -2px;
    }
    .pengumuman-alert.danger .icon { color: #DC2626; }
    .pengumuman-alert.info .icon { color: #2563EB; }
    
    .pengumuman-alert-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .pengumuman-alert.danger .pengumuman-alert-title { color: #B91C1C; }
    .pengumuman-alert.info .pengumuman-alert-title { color: #1D4ED8; }
    
    .pengumuman-alert-text {
        font-size: 0.85rem;
        color: #4B5563;
        margin-bottom: 0;
        line-height: 1.5;
    }
    
    .btn-outline-green {
        border: 1px solid #198754;
        color: #198754;
        font-weight: 600;
        border-radius: 10px;
        width: 100%;
        padding: 0.6rem;
        transition: all 0.2s;
    }
    .btn-outline-green:hover {
        background: #198754;
        color: #fff;
    }

    /* Sorotan UMKM Widget */
    .widget-umkm {
        background: linear-gradient(180deg, #F0FDF4 0%, #FFFFFF 100%);
        border: 1px solid #DCFCE7;
    }
    .widget-umkm .widget-title {
        color: #166534;
    }
    .umkm-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #E5E7EB;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .umkm-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }
    .umkm-card-body {
        padding: 1rem;
    }
    .btn-green-solid {
        background: #41966C;
        color: white;
        border: none;
        border-radius: 10px;
        width: 100%;
        padding: 0.6rem;
        font-weight: 600;
        transition: background 0.2s;
    }
    .btn-green-solid:hover {
        background: #2D7A54;
        color: white;
    }

    /* Detail Modal/Overlay styling for full view */
    #detailView {
        display: none;
    }
</style>
@endpush

@section('content')
<div id="listView">
    <div class="page-title-section">
        <h1 class="fw-bold text-success mb-1" style="font-size: 2.2rem;">Berita</h1>
        <p class="text-muted fs-5">Ikuti terus perkembangan terkini di komunitas kami.</p>
    </div>

    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8 pe-lg-4">
            
            <!-- Hero / Unggulan -->
            @if($unggulan)
            <div class="hero-card cursor-pointer" onclick="openDetail('{{ $unggulan['id'] }}', '{{ $unggulan['type'] }}')">
                <span class="hero-badge">Unggulan</span>
                @if($unggulan['image'])
                    <img src="{{ $unggulan['image'] }}" alt="Unggulan">
                @else
                    <div style="width: 100%; height: 100%; background: #198754; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-newspaper text-white opacity-50" style="font-size: 8rem;"></i>
                    </div>
                @endif
                <div class="hero-overlay">
                    <h2 class="hero-title">{{ $unggulan['title'] }}</h2>
                    <div class="hero-meta">
                        <span><i class="bi bi-calendar3 me-2"></i>{{ explode(',', $unggulan['date'])[0] }}</span>
                        <span><i class="bi bi-person me-2"></i>{{ $unggulan['author'] }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tabs -->
            <div class="custom-tabs-container">
                <button class="custom-tab-warga active" onclick="switchTab('Semua')">Semua Pembaharuan</button>
                <button class="custom-tab-warga" onclick="switchTab('Berita')">Berita</button>
                <button class="custom-tab-warga" onclick="switchTab('Pengumuman')">Pengumuman</button>
                <button class="custom-tab-warga" onclick="switchTab('UMKM News')">UMKM News</button>
            </div>

            <!-- Content Grid -->
            <div class="row g-4" id="contentGrid">
                <!-- Injected via JS -->
            </div>
            
            <div class="text-center mt-5 mb-5" id="emptyState" style="display: none;">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                <p class="text-muted mt-2">Belum ada konten di kategori ini.</p>
            </div>

        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            
            <!-- Pengumuman Penting -->
            <div class="widget-box">
                <h4 class="widget-title">
                    <i class="bi bi-megaphone-fill text-warning"></i> Pengumuman Penting
                </h4>
                
                @forelse($pengumumanPenting as $index => $pengumuman)
                    <div class="pengumuman-alert {{ $index % 2 == 0 ? 'danger' : 'info' }}" style="cursor: pointer;" onclick="openDetail('{{ $pengumuman['id'] }}', 'Pengumuman')">
                        <div class="icon">
                            <i class="bi {{ $index % 2 == 0 ? 'bi-exclamation-triangle' : 'bi-droplet' }}"></i>
                        </div>
                        <div>
                            <h5 class="pengumuman-alert-title">{{ $pengumuman['title'] }}</h5>
                            <p class="pengumuman-alert-text">{{ Str::limit($pengumuman['description'], 100) }}</p>
                            @if($index % 2 == 0)
                                <small class="text-danger fw-bold mt-1 d-block" style="font-size: 0.75rem;">Prioritas Tinggi</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted small text-center my-4">Tidak ada pengumuman penting saat ini.</p>
                @endforelse

                <button class="btn btn-outline-green mt-2" onclick="switchTab('Pengumuman')">Lihat Semua Pengumuman</button>
            </div>

            <!-- Sorotan UMKM -->
            @if($sorotanUmkm)
            <div class="widget-box widget-umkm">
                <h4 class="widget-title">
                    <i class="bi bi-star text-success"></i> Sorotan UMKM
                    <i class="bi bi-shop ms-auto opacity-25 fs-4"></i>
                </h4>
                
                <div class="umkm-card cursor-pointer" onclick="openDetail('{{ $sorotanUmkm['id'] }}', 'UMKM News')">
                    @if($sorotanUmkm['image'])
                        <img src="{{ $sorotanUmkm['image'] }}" alt="UMKM">
                    @else
                        <div class="text-center bg-light py-5 text-muted"><i class="bi bi-shop fs-1"></i></div>
                    @endif
                    <div class="umkm-card-body">
                        <small class="text-success fw-bold" style="font-size: 0.7rem;">Produk Minggu ini</small>
                        <h5 class="fw-bold text-dark mt-1 mb-2" style="font-size: 1.1rem;">{{ $sorotanUmkm['title'] }}</h5>
                        <p class="text-muted small" style="line-height: 1.4; margin-bottom: 1rem;">
                            {{ Str::limit($sorotanUmkm['description'], 80) }}
                        </p>
                        <button class="btn btn-green-solid">Lihat di Gallery</button>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- FULL PAGE DETAIL VIEW (SPA) -->
<div id="detailView" class="fade-in">
    <div class="d-flex align-items-center mb-4">
        <button class="btn btn-light border btn-sm me-3 text-muted rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;" onclick="closeDetail()" title="Kembali">
            <i class="bi bi-arrow-left fs-5"></i>
        </button>
        <nav aria-label="breadcrumb" class="mb-0">
            <ol class="breadcrumb mb-0" style="font-size: 15px; font-weight: 500;">
                <li class="breadcrumb-item"><a href="#" onclick="closeDetail(); return false;" class="text-decoration-none text-success">Berita</a></li>
                <li class="breadcrumb-item text-secondary" id="detailBreadcrumbType">Kategori</li>
                <li class="breadcrumb-item active text-dark text-truncate" id="detailBreadcrumbTitle" aria-current="page" style="max-width: 250px;">Judul</li>
            </ol>
        </nav>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; min-height: 70vh;">
        <div id="detailHeroContainer" style="height: 350px; position: relative; background: #000; display: none;">
            <img id="detailHeroImage" src="" alt="Cover" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
        </div>
        
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold" id="detailBadgeType" style="font-size: 0.85rem;"></span>
                <span class="text-muted small" style="font-family: monospace;" id="detailIdText"></span>
            </div>
            
            <h1 class="fw-bold text-dark mb-4" id="detailFullTitle" style="font-size: 2.2rem; line-height: 1.3;"></h1>
            
            <div class="d-flex flex-wrap align-items-center gap-4 mb-5 pb-4 border-bottom">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-person-circle fs-4"></i>
                    <span class="fw-semibold text-dark" id="detailAuthorName"></span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-calendar3"></i>
                    <span id="detailFullDate"></span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted d-none" id="detailLocationContainer">
                    <i class="bi bi-geo-alt-fill text-danger"></i>
                    <span id="detailLocationText"></span>
                </div>
            </div>

            <div id="detailContentHtml" class="content-html text-dark" style="font-size: 1.1rem; line-height: 1.8;">
                <!-- Content injected here -->
            </div>
            
            <div id="detailExtraInfoBox" class="mt-5 d-none">
                <hr class="mb-4" style="border-color: #E5E7EB;">
                <div class="row">
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
                        <div class="rounded-3 overflow-hidden border" style="border-color: #E5E7EB; height: 300px;" id="mapDetailAgenda"></div>
                    </div>
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
<script>
    const semuaData = @json($semua);
    let currentTab = 'Semua';

    function renderGrid() {
        const grid = document.getElementById('contentGrid');
        const emptyState = document.getElementById('emptyState');
        
        let filtered = semuaData;
        if (currentTab !== 'Semua') {
            filtered = semuaData.filter(item => item.type === currentTab);
        }

        if (filtered.length === 0) {
            grid.innerHTML = '';
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';
        let html = '';

        filtered.forEach(item => {
            const dateStr = item.date.split(',')[0]; // Extract just the date part for card
            const imgHtml = item.image 
                ? `<img src="${item.image}" alt="Thumbnail">`
                : `<div class="placeholder-img"><i class="bi ${item.type === 'Agenda' ? 'bi-calendar-event' : (item.type === 'Pengumuman' ? 'bi-megaphone' : 'bi-newspaper')}"></i></div>`;
            
            html += `
                <div class="col-md-6">
                    <div class="content-card cursor-pointer" onclick="openDetail('${item.id}', '${item.type}')">
                        <div class="content-card-img-wrapper">
                            <span class="card-badge">${item.kategori || item.type}</span>
                            ${imgHtml}
                        </div>
                        <div class="content-card-body">
                            <h4 class="content-card-title">${item.title}</h4>
                            <p class="content-card-text">${item.description}</p>
                            <div class="content-card-footer">
                                <span class="date">${dateStr}</span>
                                <span class="read-more">Baca selengkapnya <i class="bi bi-chevron-right small"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        grid.innerHTML = html;
    }

    function switchTab(tabName) {
        currentTab = tabName;
        
        // Update active class
        document.querySelectorAll('.custom-tab-warga').forEach(btn => {
            if(btn.textContent.trim() === tabName || (tabName === 'Semua' && btn.textContent.trim() === 'Semua Pembaharuan')) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        renderGrid();
    }

    function openDetail(id, type) {
        const item = semuaData.find(x => x.id == id && x.type == type);
        if(!item) return;

        // Hide List, Show Detail
        document.getElementById('listView').style.display = 'none';
        const detailView = document.getElementById('detailView');
        detailView.style.display = 'block';
        
        // Reflow for animation
        detailView.classList.remove('fade-in');
        void detailView.offsetWidth;
        detailView.classList.add('fade-in');

        // Populate Detail
        document.getElementById('detailBreadcrumbType').textContent = item.type;
        document.getElementById('detailBreadcrumbTitle').textContent = item.title;
        
        const heroContainer = document.getElementById('detailHeroContainer');
        if (item.image) {
            heroContainer.style.display = 'block';
            document.getElementById('detailHeroImage').src = item.image;
        } else {
            heroContainer.style.display = 'none';
        }

        document.getElementById('detailBadgeType').textContent = item.type;
        document.getElementById('detailIdText').textContent = `REF: ${item.id.toString().substring(0,8).toUpperCase()}`;
        document.getElementById('detailFullTitle').textContent = item.title;
        document.getElementById('detailAuthorName').textContent = item.author;
        document.getElementById('detailFullDate').textContent = item.date;

        const locContainer = document.getElementById('detailLocationContainer');
        if (item.location) {
            locContainer.classList.remove('d-none');
            document.getElementById('detailLocationText').textContent = item.location;
        } else {
            locContainer.classList.add('d-none');
        }

        document.getElementById('detailContentHtml').innerHTML = item.raw_content || '<i class="text-muted">Tidak ada konten deskripsi yang tersedia.</i>';

        // Extra Info Box (RSVP + Map)
        const extraInfoBox = document.getElementById('detailExtraInfoBox');
        if (item.type === 'Agenda') {
            extraInfoBox.classList.remove('d-none');
            
            // RSVP
            const rsvpStatus = document.getElementById('detailRsvpStatus');
            if (item.is_rsvp_enabled == 1) {
                rsvpStatus.textContent = 'Aktif - Anda dapat mengonfirmasi kehadiran';
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
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function closeDetail() {
        document.getElementById('detailView').style.display = 'none';
        const listView = document.getElementById('listView');
        listView.style.display = 'block';
        
        listView.classList.remove('fade-in');
        void listView.offsetWidth;
        listView.classList.add('fade-in');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
        renderGrid();
    });
</script>
<style>
    .cursor-pointer { cursor: pointer; }
    .fade-in {
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .content-html img {
        max-width: 100%;
        border-radius: 12px;
        margin: 1.5rem 0;
    }
</style>
@endpush
