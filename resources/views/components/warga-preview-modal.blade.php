{{-- Preview Warga Modal - Reusable Component --}}
{{-- Usage: @include('components.warga-preview-modal') --}}

<div class="modal fade" id="modalPreviewWarga" tabindex="-1" aria-labelledby="modalPreviewWargaLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content" style="background: #F3F4F6;">
            {{-- Header Bar --}}
            <div class="modal-header border-bottom px-4 py-3" style="background: white;">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-light border btn-sm text-muted rounded-circle d-flex justify-content-center align-items-center" style="width: 36px; height: 36px;" data-bs-dismiss="modal" title="Kembali">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </button>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">Pratinjau Tampilan Warga</h6>
                        <span class="text-muted" style="font-size: 12px;">Seperti inilah tampilan yang akan dilihat oleh warga</span>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill fw-semibold ms-2" style="font-size: 12px;">
                        <i class="bi bi-eye me-1"></i> Mode Pratinjau
                    </span>
                </div>
            </div>

            {{-- Body: Clone of Warga Detail View --}}
            <div class="modal-body p-0" style="overflow-y: auto;">
                <div class="container-fluid py-4 px-4 px-md-5">
                    {{-- Breadcrumb --}}
                    <div class="d-flex align-items-center mb-4">
                        <nav aria-label="breadcrumb" class="mb-0">
                            <ol class="breadcrumb mb-0" style="font-size: 15px; font-weight: 500;">
                                <li class="breadcrumb-item"><span class="text-success">Beranda Warga</span></li>
                                <li class="breadcrumb-item text-secondary" id="pvBreadcrumbType">Kategori</li>
                                <li class="breadcrumb-item active text-dark text-truncate" id="pvBreadcrumbTitle" aria-current="page" style="max-width: 250px;">Judul</li>
                            </ol>
                        </nav>
                    </div>

                    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; min-height: 70vh;">
                        {{-- Hero Image --}}
                        <div id="pvHeroContainer" style="height: 350px; position: relative; background: #000; display: none;">
                            <img id="pvHeroImage" src="" alt="Cover" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.7;">
                        </div>

                        <div class="card-body p-4 p-md-5">
                            {{-- Badge & ID --}}
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold" id="pvBadgeType" style="font-size: 0.85rem;"></span>
                                    <span class="badge rounded-pill px-3 py-1 fw-semibold d-none" style="background: white; color: #374151; font-size: 12px; border: 1px solid #D1D5DB;" id="pvBadgeKategori"></span>
                                </div>
                                <span class="text-muted small" style="font-family: monospace;" id="pvIdText"></span>
                            </div>

                            {{-- Title --}}
                            <h1 class="fw-bold text-dark mb-4" id="pvFullTitle" style="font-size: 2.2rem; line-height: 1.3;"></h1>

                            {{-- Meta Info --}}
                            <div class="d-flex flex-wrap align-items-center gap-4 mb-5 pb-4 border-bottom">
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <i class="bi bi-person-circle fs-4"></i>
                                    <span class="fw-semibold text-dark" id="pvAuthorName"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    <span id="pvFullDate"></span>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-muted d-none" id="pvLocationContainer">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <span id="pvLocationText"></span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div id="pvContentHtml" class="content-html text-dark" style="font-size: 1.1rem; line-height: 1.8;">
                            </div>

                            {{-- Extra Info (Agenda Only: RSVP + Map) --}}
                            <div id="pvExtraInfoBox" class="mt-5 d-none">
                                <hr class="mb-4" style="border-color: #E5E7EB;">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="p-3 bg-light rounded-3 border" style="border-color: #E5E7EB;">
                                            <div class="d-flex align-items-center mb-1">
                                                <i class="bi bi-people-fill text-success me-2 fs-5"></i>
                                                <span class="fw-bold text-dark" style="font-size: 14px;">Konfirmasi Kehadiran (RSVP)</span>
                                            </div>
                                            <span id="pvRsvpStatus" class="d-block text-muted" style="font-size: 13px;">Aktif</span>
                                        </div>
                                    </div>
                                    <div class="col-12" id="pvMapContainer" style="display: none;">
                                        <span class="d-block text-muted mb-2" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">Titik Lokasi Peta</span>
                                        <div class="rounded-3 overflow-hidden border" style="border-color: #E5E7EB; height: 300px;" id="pvMapElement"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-html p {
        margin-bottom: 1rem;
        line-height: 1.8;
    }
    .content-html blockquote {
        border-left: 4px solid #10B981;
        padding-left: 1rem;
        margin-left: 0;
        margin-right: 0;
        font-style: italic;
        background-color: #F9FAFB;
        padding: 1rem;
        border-radius: 0 8px 8px 0;
    }
    .content-html ul, .content-html ol {
        padding-left: 2rem;
        margin-bottom: 1rem;
    }
    .content-html li {
        margin-bottom: 0.5rem;
    }
    .content-html h1, .content-html h2, .content-html h3, .content-html h4, .content-html h5, .content-html h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .content-html img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
</style>

<script>
    let pvMap = null;
    let pvMarker = null;

    function openPreviewWarga(data) {
        // data = { type, title, author, date, location, image, content, latitude, longitude, is_rsvp_enabled, id }

        // Breadcrumb
        document.getElementById('pvBreadcrumbType').textContent = data.type || '';
        document.getElementById('pvBreadcrumbTitle').textContent = data.title || '';

        // Hero Image
        const heroContainer = document.getElementById('pvHeroContainer');
        if (data.image) {
            heroContainer.style.display = 'block';
            document.getElementById('pvHeroImage').src = data.image;
        } else {
            heroContainer.style.display = 'none';
        }

        // Badge, Kategori & ID
        document.getElementById('pvBadgeType').textContent = data.type || '';
        const pvCatBadge = document.getElementById('pvBadgeKategori');
        if (data.kategori) {
            pvCatBadge.textContent = data.kategori;
            pvCatBadge.classList.remove('d-none');
        } else {
            pvCatBadge.classList.add('d-none');
        }
        document.getElementById('pvIdText').textContent = 'REF: ' + (data.id || '').toString().substring(0, 8).toUpperCase();

        // Title
        document.getElementById('pvFullTitle').textContent = data.title || '';

        // Author & Date
        document.getElementById('pvAuthorName').textContent = data.author || 'Operator';
        document.getElementById('pvFullDate').textContent = data.date || '';

        // Location
        const locContainer = document.getElementById('pvLocationContainer');
        if (data.location) {
            locContainer.classList.remove('d-none');
            document.getElementById('pvLocationText').textContent = data.location;
        } else {
            locContainer.classList.add('d-none');
        }

        // Content
        document.getElementById('pvContentHtml').innerHTML = data.content || '<i class="text-muted">Tidak ada konten deskripsi yang tersedia.</i>';

        // Extra Info (Agenda only)
        const extraInfoBox = document.getElementById('pvExtraInfoBox');
        if (data.type === 'Agenda') {
            extraInfoBox.classList.remove('d-none');

            // RSVP
            const rsvpStatus = document.getElementById('pvRsvpStatus');
            if (data.is_rsvp_enabled == 1) {
                rsvpStatus.textContent = 'Aktif - Anda dapat mengonfirmasi kehadiran';
                rsvpStatus.className = 'd-block text-success fw-medium';
            } else {
                rsvpStatus.textContent = 'Tidak Aktif';
                rsvpStatus.className = 'd-block text-muted';
            }

            // Map
            const mapContainer = document.getElementById('pvMapContainer');
            if (data.latitude && data.longitude) {
                mapContainer.style.display = 'block';
                setTimeout(function() {
                    if (!pvMap) {
                        pvMap = L.map('pvMapElement').setView([data.latitude, data.longitude], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(pvMap);
                        pvMarker = L.marker([data.latitude, data.longitude]).addTo(pvMap);
                    } else {
                        pvMap.setView([data.latitude, data.longitude], 15);
                        pvMarker.setLatLng([data.latitude, data.longitude]);
                        pvMap.invalidateSize();
                    }
                }, 400);
            } else {
                mapContainer.style.display = 'none';
            }
        } else {
            extraInfoBox.classList.add('d-none');
        }

        // Open the modal
        const previewModal = new bootstrap.Modal(document.getElementById('modalPreviewWarga'));
        previewModal.show();
    }
</script>
