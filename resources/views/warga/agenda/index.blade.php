@extends('layouts.global')

@section('title', 'Agenda Warga')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    /* ===== FullCalendar Custom Theme ===== */
    #calendar-container {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
    }
    #calendar {
        flex-grow: 1;
    }
    /* Header Container to align left controls */
    .header-controls-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .header-title-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-left: 10px;
    }
    /* Hide native fullcalendar toolbar since we build a custom one */
    .fc .fc-toolbar.fc-header-toolbar {
        display: none;
    }
    .month-picker {
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        color: #374151;
        font-size: 0.9rem;
        cursor: pointer;
        background: #f8f9fa;
    }

    /* Toolbar */
    .fc .fc-toolbar.fc-header-toolbar {
        margin-bottom: 1.2rem;
    }
    .fc .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }
    .fc .fc-button {
        background-color: #f8f9fa;
        border: 1px solid #e5e7eb;
        color: #374151;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 6px 14px;
        border-radius: 8px;
        text-transform: capitalize;
        transition: all 0.2s;
    }
    .fc .fc-button:hover {
        background-color: #e5e7eb;
        color: #111827;
    }
    .fc .fc-button-active,
    .fc .fc-button:active {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: #fff !important;
        box-shadow: none !important;
    }
    .fc .fc-today-button {
        background-color: #fff;
        border: 1.5px solid #198754;
        color: #198754;
        font-weight: 600;
        border-radius: 20px;
        padding: 6px 18px;
    }
    .fc .fc-today-button:hover {
        background-color: #198754;
        color: #fff;
    }
    .fc .fc-today-button:disabled {
        opacity: 0.5;
    }
    .fc .fc-button-group .fc-button {
        border-radius: 0;
    }
    .fc .fc-button-group .fc-button:first-child {
        border-radius: 8px 0 0 8px;
    }
    .fc .fc-button-group .fc-button:last-child {
        border-radius: 0 8px 8px 0;
    }

    /* Calendar Grid */
    .fc .fc-col-header-cell {
        background-color: #e5e7eb; /* Abu-abu lebih gelap sedikit agar tidak putih */
        border-bottom: 2px solid #d1d5db;
        padding: 12px 0;
    }
    /* Event Fixes */
    .fc .fc-event-main-frame {
        color: inherit; /* inherit from textColor */
    }
    .fc .fc-event {
        border-radius: 4px;
        border-width: 1px;
        padding: 2px 4px;
    }
    .fc-h-event .fc-event-title-container {
        color: inherit;
    }
    .fc .fc-col-header-cell-cushion {
        color: #6b7280;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        text-decoration: none;
    }
    .fc .fc-daygrid-day-number,
    .fc .fc-timegrid-slot-label-cushion {
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none;
    }
    .fc .fc-day-today {
        background-color: rgba(25, 135, 84, 0.04) !important;
    }
    .fc .fc-day-today .fc-daygrid-day-number {
        background-color: #198754;
        color: #fff;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    /* Events */
    .fc .fc-event {
        border-radius: 6px;
        border-left-width: 3px;
        padding: 3px 6px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .fc .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }
    .fc .fc-event .fc-event-title {
        font-weight: 600;
    }
    .fc .fc-event .fc-event-time {
        font-weight: 400;
        font-size: 0.7rem;
    }
    .fc .fc-timegrid-event .fc-event-main {
        padding: 4px 6px;
    }

    /* Scrollbar */
    .fc .fc-scroller::-webkit-scrollbar {
        width: 6px;
    }
    .fc .fc-scroller::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    /* Responsif */
    @media (max-width: 768px) {
        #calendar-container { padding: 12px; }
        .fc .fc-toolbar-title { font-size: 1.1rem; }
        .fc .fc-button { font-size: 0.75rem; padding: 4px 10px; }
    }

    /* Modal Detail */
    .agenda-detail-modal .modal-content {
        border-radius: 16px;
        border: none;
        overflow: hidden;
    }
    .agenda-detail-modal .modal-header {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-bottom: 1px solid #bbf7d0;
        padding: 20px 24px;
    }
    .agenda-detail-modal .modal-body { padding: 24px; }
    .detail-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }
    .detail-row .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .detail-row .detail-label {
        font-size: 0.75rem;
        color: #9ca3af;
        font-weight: 500;
        margin-bottom: 2px;
    }
    .detail-row .detail-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 600;
    }

    /* Sidebar Agenda List */
    .agenda-sidebar {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .agenda-sidebar-header {
        padding: 16px 20px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .agenda-sidebar-header h6 {
        margin: 0;
        font-weight: 700;
        color: #1f2937;
        font-size: 0.95rem;
    }
    .agenda-sidebar-body {
        padding: 12px;
        overflow-y: auto;
        flex: 1;
        max-height: calc(100vh - 250px);
    }
    .agenda-list-card {
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #f3f4f6;
        margin-bottom: 10px;
        transition: all 0.2s;
        background: #fff;
    }
    .agenda-list-card:hover {
        border-color: #bbf7d0;
        box-shadow: 0 2px 8px rgba(25,135,84,0.08);
    }
    .agenda-list-card .agenda-card-title {
        font-weight: 700;
        font-size: 0.85rem;
        color: #1f2937;
        line-height: 1.3;
        margin-bottom: 4px;
    }
    .agenda-list-card .agenda-card-meta {
        font-size: 0.72rem;
        color: #9ca3af;
    }
    .agenda-list-card .agenda-card-meta i {
        font-size: 0.7rem;
    }
    .agenda-list-empty {
        text-align: center;
        padding: 40px 20px;
        color: #9ca3af;
    }
    .agenda-list-empty i {
        font-size: 2.5rem;
        margin-bottom: 8px;
        display: block;
    }

    /* Map Container */
    #modalMapContainer {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    #modalMap {
        height: 200px;
        width: 100%;
        border-radius: 12px;
        z-index: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row g-3">
        <!-- Sidebar: Daftar Agenda -->
        <div class="col-lg-4 col-xl-3">
            <div class="agenda-sidebar">
                <div class="agenda-sidebar-header">
                    <h6><i class="bi bi-list-ul me-2 text-success"></i>Daftar Agenda</h6>
                    <span id="agendaListCount" class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size: 0.75rem;">0</span>
                </div>
                <div class="agenda-sidebar-body" id="agendaSidebarList">
                    <div class="agenda-list-empty">
                        <i class="bi bi-calendar-x"></i>
                        <span>Tidak ada agenda bulan ini</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="col-lg-8 col-xl-9">
            <div id="calendar-container">
                <div class="header-controls-wrapper">
                    <div class="header-left">
                        <button id="btn-today" class="btn btn-outline-success btn-sm px-3 rounded-pill fw-bold">Hari Ini</button>
                        <div class="btn-group">
                            <button id="btn-prev" class="btn btn-light border btn-sm"><i class="bi bi-chevron-left"></i></button>
                            <button id="btn-next" class="btn btn-light border btn-sm"><i class="bi bi-chevron-right"></i></button>
                        </div>
                        <div id="custom-title" class="header-title-text">September 2026</div>
                    </div>
                    <div class="header-right d-flex gap-2">
                        <input type="month" id="month-picker" class="month-picker">
                        <div class="btn-group">
                            <button id="btn-view-month" class="btn btn-success btn-sm px-3">Bulan</button>
                            <button id="btn-view-week" class="btn btn-light border btn-sm px-3">Minggu</button>
                        </div>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Agenda -->
<div class="modal fade agenda-detail-modal" id="agendaDetailModal" tabindex="-1" aria-labelledby="agendaDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <div>
                    <span id="modalKategoriBadge" class="badge rounded-pill mb-2" style="font-size: 0.75rem;"></span>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="agendaDetailLabel"></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <div class="icon-box bg-success bg-opacity-10">
                        <i class="bi bi-calendar-event text-success"></i>
                    </div>
                    <div>
                        <div class="detail-label">Tanggal</div>
                        <div class="detail-value" id="modalTanggal"></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="icon-box bg-primary bg-opacity-10">
                        <i class="bi bi-clock text-primary"></i>
                    </div>
                    <div>
                        <div class="detail-label">Waktu</div>
                        <div class="detail-value" id="modalWaktu"></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="icon-box bg-danger bg-opacity-10">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                    </div>
                    <div>
                        <div class="detail-label">Lokasi</div>
                        <div class="detail-value" id="modalLokasi"></div>
                        <a href="#" id="modalGmapsLink" class="text-success small text-decoration-none d-none" target="_blank">
                            <i class="bi bi-map me-1"></i>Buka di Google Maps
                        </a>
                    </div>
                </div>
                <!-- Peta Lokasi -->
                <div id="modalMapContainer" class="detail-row d-none" style="margin-top: -4px;">
                    <div class="icon-box bg-info bg-opacity-10">
                        <i class="bi bi-pin-map-fill text-info"></i>
                    </div>
                    <div class="w-100">
                        <div class="detail-label">Peta Lokasi</div>
                        <div id="modalMap"></div>
                    </div>
                </div>
                <div id="modalDeskripsiContainer" class="detail-row">
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="bi bi-text-paragraph text-warning"></i>
                    </div>
                    <div>
                        <div class="detail-label">Keterangan</div>
                        <div class="detail-value fw-normal pe-2" id="modalDeskripsi" style="font-size: 0.85rem; color: #4b5563; max-height: 250px; overflow-y: auto;"></div>
                    </div>
                </div>

                <!-- RSVP Section -->
                <div id="modalRsvpContainer" class="d-none mt-3 pt-3 border-top">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-people-fill text-success"></i>
                        <span class="fw-bold text-dark" style="font-size: 0.85rem;">Konfirmasi Kehadiran</span>
                        <span id="modalTotalHadir" class="badge bg-success bg-opacity-10 text-success rounded-pill ms-auto" style="font-size: 0.75rem;"></span>
                    </div>
                    <div class="d-flex gap-2" id="modalRsvpBtns">
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill flex-fill rsvp-cal-btn" data-status="Hadir" onclick="sendCalendarRsvp(this)">
                            <i class="bi bi-check-circle me-1"></i>Hadir
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill flex-fill rsvp-cal-btn" data-status="Tidak Hadir" onclick="sendCalendarRsvp(this)">
                            <i class="bi bi-x-circle me-1"></i>Tidak
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill flex-fill rsvp-cal-btn text-dark" data-status="Ragu-ragu" onclick="sendCalendarRsvp(this)">
                            <i class="bi bi-question-circle me-1"></i>Ragu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const rawEvents = @json($agendas);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: false, // We use custom toolbar
        firstDay: 0, // Minggu
        slotMinTime: '06:00:00',
        slotMaxTime: '22:00:00',
        slotDuration: '01:00:00',
        slotLabelFormat: {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            meridiem: 'short'
        },
        allDaySlot: false,
        nowIndicator: true,
        height: '100%',
        events: rawEvents,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const props = info.event.extendedProps;

            document.getElementById('agendaDetailLabel').textContent = info.event.title;

            // Tanggal: support multi-day
            if (props.is_multi_day == 1) {
                document.getElementById('modalTanggal').textContent = (props.tanggal_mulai_formatted || '-') + '  s/d  ' + (props.tanggal_selesai_formatted || '-');
            } else {
                document.getElementById('modalTanggal').textContent = props.tanggal_mulai_formatted || '-';
            }

            document.getElementById('modalWaktu').textContent = (props.jam_mulai || '') + ' - ' + (props.jam_selesai || '') + ' WIB';
            document.getElementById('modalLokasi').textContent = props.lokasi || '-';

            // Kategori badge
            const badge = document.getElementById('modalKategoriBadge');
            badge.textContent = props.kategori || 'Lainnya';
            badge.style.backgroundColor = info.event.backgroundColor;
            badge.style.color = info.event.textColor;
            badge.style.border = '1px solid ' + info.event.borderColor;

            // Google Maps link
            const gmapsLink = document.getElementById('modalGmapsLink');
            if (props.link_gmaps) {
                gmapsLink.href = props.link_gmaps;
                gmapsLink.classList.remove('d-none');
            } else {
                gmapsLink.classList.add('d-none');
            }

            // Peta Lokasi (Leaflet)
            const mapContainer = document.getElementById('modalMapContainer');
            const mapDiv = document.getElementById('modalMap');
            if (props.latitude && props.longitude && props.latitude !== '' && props.longitude !== '') {
                mapContainer.classList.remove('d-none');
                // Destroy previous map instance if exists
                if (window._agendaMap) {
                    window._agendaMap.remove();
                    window._agendaMap = null;
                }
                // Delay initialization so modal is visible (needed for Leaflet)
                setTimeout(() => {
                    const lat = parseFloat(props.latitude);
                    const lng = parseFloat(props.longitude);
                    const map = L.map('modalMap', { scrollWheelZoom: false, dragging: true, zoomControl: true }).setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 19,
                    }).addTo(map);
                    L.marker([lat, lng]).addTo(map).bindPopup('<b>' + (props.lokasi || 'Lokasi Agenda') + '</b>').openPopup();
                    window._agendaMap = map;
                    // Invalidate size after modal animation completes
                    setTimeout(() => map.invalidateSize(), 300);
                }, 250);
            } else {
                mapContainer.classList.add('d-none');
                if (window._agendaMap) {
                    window._agendaMap.remove();
                    window._agendaMap = null;
                }
            }

            // Deskripsi
            const descContainer = document.getElementById('modalDeskripsiContainer');
            const descEl = document.getElementById('modalDeskripsi');
            if (props.deskripsi) {
                descEl.innerHTML = props.deskripsi;
                descContainer.style.display = 'flex';
            } else {
                descContainer.style.display = 'none';
            }

            // RSVP Section
            const rsvpContainer = document.getElementById('modalRsvpContainer');
            const totalHadirBadge = document.getElementById('modalTotalHadir');
            if (props.is_rsvp_enabled == 1) {
                rsvpContainer.classList.remove('d-none');
                rsvpContainer.dataset.agendaId = info.event.id;
                totalHadirBadge.textContent = (props.total_hadir || 0) + ' Warga Hadir';
                // Update button states
                document.querySelectorAll('.rsvp-cal-btn').forEach(btn => {
                    const st = btn.dataset.status;
                    btn.disabled = false;
                    if (st === 'Hadir') {
                        btn.className = (props.user_rsvp_status === 'Hadir') ? 'btn btn-sm btn-success text-white rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-success rounded-pill flex-fill rsvp-cal-btn';
                    } else if (st === 'Tidak Hadir') {
                        btn.className = (props.user_rsvp_status === 'Tidak Hadir') ? 'btn btn-sm btn-danger text-white rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-danger rounded-pill flex-fill rsvp-cal-btn';
                    } else if (st === 'Ragu-ragu') {
                        btn.className = (props.user_rsvp_status === 'Ragu-ragu') ? 'btn btn-sm btn-warning text-dark rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-warning rounded-pill flex-fill rsvp-cal-btn text-dark';
                    }
                });
            } else {
                rsvpContainer.classList.add('d-none');
            }

            const modal = new bootstrap.Modal(document.getElementById('agendaDetailModal'));
            modal.show();
        },
        eventDidMount: function(info) {
            // Add location tooltip
            const lokasi = info.event.extendedProps.lokasi;
            if (lokasi) {
                info.el.title = info.event.title + ' — ' + lokasi;
            }
        }
    });

    calendar.render();

    // ===== Sidebar List Rendering =====
    function renderAgendaSidebar() {
        const container = document.getElementById('agendaSidebarList');
        const countBadge = document.getElementById('agendaListCount');
        const currentDate = calendar.getDate();
        const currentMonth = currentDate.getMonth();
        const currentYear = currentDate.getFullYear();

        // Filter events for current month view and ensure they are not in the past
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Start of today
        
        const monthEvents = rawEvents.filter(ev => {
            const evStart = new Date(ev.start);
            const evEnd = new Date(ev.end || ev.start);
            
            // Check if it's in the current month view
            const isCurrentMonth = evStart.getMonth() === currentMonth && evStart.getFullYear() === currentYear;
            
            // Check if it's not past (we compare end date if available, otherwise start date)
            const isNotPast = evEnd >= today;
            
            return isCurrentMonth && isNotPast;
        }).sort((a, b) => new Date(a.start) - new Date(b.start));

        countBadge.textContent = monthEvents.length;

        if (monthEvents.length === 0) {
            container.innerHTML = `
                <div class="agenda-list-empty">
                    <i class="bi bi-calendar-x"></i>
                    <span>Tidak ada agenda bulan ini</span>
                </div>`;
            return;
        }

        container.innerHTML = '';
        monthEvents.forEach(ev => {
            const props = ev.extendedProps;
            const startDate = new Date(ev.start);
            const day = startDate.getDate();
            const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];
            const monthShort = monthNames[startDate.getMonth()];

            let rsvpHtml = '';
            if (props.is_rsvp_enabled == 1) {
                const hadirClass = props.user_rsvp_status === 'Hadir' ? 'btn-success text-white' : 'btn-outline-success';
                const tidakClass = props.user_rsvp_status === 'Tidak Hadir' ? 'btn-danger text-white' : 'btn-outline-danger';
                const raguClass = props.user_rsvp_status === 'Ragu-ragu' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark';
                rsvpHtml = `
                    <div class="d-flex align-items-center gap-1 mt-2">
                        <button type="button" class="btn btn-sm ${hadirClass} rounded-pill flex-fill list-rsvp-btn" data-agenda="${ev.id}" data-status="Hadir" onclick="sendListRsvp(this)" style="font-size: 0.65rem; padding: 2px 6px;">
                            <i class="bi bi-check-circle"></i> Ya
                        </button>
                        <button type="button" class="btn btn-sm ${tidakClass} rounded-pill flex-fill list-rsvp-btn" data-agenda="${ev.id}" data-status="Tidak Hadir" onclick="sendListRsvp(this)" style="font-size: 0.65rem; padding: 2px 6px;">
                            <i class="bi bi-x-circle"></i> Tidak
                        </button>
                        <button type="button" class="btn btn-sm ${raguClass} rounded-pill flex-fill list-rsvp-btn" data-agenda="${ev.id}" data-status="Ragu-ragu" onclick="sendListRsvp(this)" style="font-size: 0.65rem; padding: 2px 6px;">
                            <i class="bi bi-question-circle"></i> Ragu
                        </button>
                        <span class="text-success ms-1" style="font-size: 0.65rem; white-space: nowrap;" id="listHadir-${ev.id}"><i class="bi bi-people-fill"></i> ${props.total_hadir || 0}</span>
                    </div>`;
            }

            const card = document.createElement('div');
            card.className = 'agenda-list-card';
            card.innerHTML = `
                <div class="d-flex align-items-start gap-2">
                    <div class="text-center flex-shrink-0 rounded-3 p-1 px-2" style="background: ${ev.backgroundColor}; border: 1px solid ${ev.borderColor}; min-width: 42px;">
                        <div class="fw-bold" style="font-size: 0.65rem; color: ${ev.textColor}; text-transform: uppercase;">${monthShort}</div>
                        <div class="fw-bold" style="font-size: 1.1rem; line-height: 1; color: ${ev.textColor};">${day}</div>
                    </div>
                    <div class="w-100">
                        <div class="agenda-card-title">${ev.title}</div>
                        <div class="agenda-card-meta d-flex flex-wrap gap-2">
                            <span><i class="bi bi-clock me-1"></i>${props.jam_mulai} - ${props.jam_selesai}</span>
                            ${props.lokasi ? '<span><i class="bi bi-geo-alt me-1"></i>' + (props.lokasi.length > 15 ? props.lokasi.substring(0,15) + '...' : props.lokasi) + '</span>' : ''}
                        </div>
                        ${rsvpHtml}
                        <a href="/warga/agenda/${ev.id}" class="btn btn-sm btn-success rounded-pill w-100 mt-2" style="font-size: 0.75rem;">
                            <i class="bi bi-eye me-1"></i>Lihat Detail
                        </a>
                    </div>
                </div>`;
            container.appendChild(card);
        });
    }

    // RSVP function for sidebar list
    window.sendListRsvp = function(btnEl) {
        const agendaId = btnEl.dataset.agenda;
        const status = btnEl.dataset.status;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Find sibling buttons in same card
        const card = btnEl.closest('.agenda-list-card');
        const buttons = card.querySelectorAll('.list-rsvp-btn');
        buttons.forEach(b => b.disabled = true);
        const originalHtml = btnEl.innerHTML;
        btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>';

        fetch('/warga/agenda/rsvp', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ agenda_id: agendaId, status_kehadiran: status }),
        })
        .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
        .then(data => {
            if (data.success) {
                buttons.forEach(b => {
                    b.disabled = false;
                    const st = b.dataset.status;
                    if (st === 'Hadir') {
                        b.className = (st === status) ? 'btn btn-sm btn-success text-white rounded-pill flex-fill list-rsvp-btn' : 'btn btn-sm btn-outline-success rounded-pill flex-fill list-rsvp-btn';
                    } else if (st === 'Tidak Hadir') {
                        b.className = (st === status) ? 'btn btn-sm btn-danger text-white rounded-pill flex-fill list-rsvp-btn' : 'btn btn-sm btn-outline-danger rounded-pill flex-fill list-rsvp-btn';
                    } else if (st === 'Ragu-ragu') {
                        b.className = (st === status) ? 'btn btn-sm btn-warning text-dark rounded-pill flex-fill list-rsvp-btn' : 'btn btn-sm btn-outline-warning text-dark rounded-pill flex-fill list-rsvp-btn';
                    }
                });
                const hadirEl = document.getElementById('listHadir-' + agendaId);
                if (hadirEl) hadirEl.innerHTML = '<i class="bi bi-people-fill"></i> ' + data.total_hadir;
                // Update rawEvents memory
                rawEvents.forEach(ev => {
                    if (ev.id === agendaId) {
                        ev.extendedProps.user_rsvp_status = status;
                        ev.extendedProps.total_hadir = data.total_hadir;
                    }
                });
            }
        })
        .catch(err => {
            console.error('List RSVP error:', err);
            buttons.forEach(b => b.disabled = false);
            alert('Gagal menyimpan respons kehadiran.');
        })
        .finally(() => {
            if (btnEl.innerHTML.includes('spinner')) btnEl.innerHTML = originalHtml;
        });
    };

    // Initial render
    renderAgendaSidebar();

    // Bind custom toolbar
    const updateHeader = () => {
        document.getElementById('custom-title').textContent = calendar.view.title;
        // Sync month picker
        const currentDate = calendar.getDate();
        const y = currentDate.getFullYear();
        const m = String(currentDate.getMonth() + 1).padStart(2, '0');
        document.getElementById('month-picker').value = `${y}-${m}`;
    };

    updateHeader();
    calendar.on('datesSet', () => {
        updateHeader();
        renderAgendaSidebar();
    });

    // RSVP function for calendar modal
    window.sendCalendarRsvp = function(btnEl) {
        const container = document.getElementById('modalRsvpContainer');
        const agendaId = container.dataset.agendaId;
        const status = btnEl.dataset.status;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        // Disable all buttons
        const buttons = document.querySelectorAll('.rsvp-cal-btn');
        buttons.forEach(b => b.disabled = true);
        const originalHtml = btnEl.innerHTML;
        btnEl.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        
        fetch('/warga/agenda/rsvp', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ agenda_id: agendaId, status_kehadiran: status }),
        })
        .then(res => {
            if (!res.ok) return res.text().then(t => { throw new Error('HTTP ' + res.status); });
            return res.json();
        })
        .then(data => {
            if (data.success) {
                // Update button UI
                buttons.forEach(b => {
                    b.disabled = false;
                    const st = b.dataset.status;
                    if (st === 'Hadir') {
                        b.className = (st === status) ? 'btn btn-sm btn-success text-white rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-success rounded-pill flex-fill rsvp-cal-btn';
                    } else if (st === 'Tidak Hadir') {
                        b.className = (st === status) ? 'btn btn-sm btn-danger text-white rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-danger rounded-pill flex-fill rsvp-cal-btn';
                    } else if (st === 'Ragu-ragu') {
                        b.className = (st === status) ? 'btn btn-sm btn-warning text-dark rounded-pill flex-fill rsvp-cal-btn' : 'btn btn-sm btn-outline-warning rounded-pill flex-fill rsvp-cal-btn text-dark';
                    }
                });
                // Update total hadir badge
                document.getElementById('modalTotalHadir').textContent = data.total_hadir + ' Warga Hadir';
                // Update rawEvents memory
                rawEvents.forEach(ev => {
                    if (ev.id === agendaId) {
                        ev.extendedProps.user_rsvp_status = status;
                        ev.extendedProps.total_hadir = data.total_hadir;
                    }
                });
            } else {
                buttons.forEach(b => b.disabled = false);
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(err => {
            console.error('RSVP error:', err);
            buttons.forEach(b => b.disabled = false);
            alert('Gagal menyimpan respons kehadiran.');
        })
        .finally(() => {
            if (btnEl.innerHTML.includes('spinner')) btnEl.innerHTML = originalHtml;
        });
    };

    document.getElementById('btn-today').addEventListener('click', () => calendar.today());
    document.getElementById('btn-prev').addEventListener('click', () => calendar.prev());
    document.getElementById('btn-next').addEventListener('click', () => calendar.next());

    document.getElementById('btn-view-month').addEventListener('click', function() {
        calendar.changeView('dayGridMonth');
        this.classList.replace('btn-light', 'btn-success');
        document.getElementById('btn-view-week').classList.replace('btn-success', 'btn-light');
    });
    
    document.getElementById('btn-view-week').addEventListener('click', function() {
        calendar.changeView('timeGridWeek');
        this.classList.replace('btn-light', 'btn-success');
        document.getElementById('btn-view-month').classList.replace('btn-success', 'btn-light');
    });

    document.getElementById('month-picker').addEventListener('change', function(e) {
        if(e.target.value) {
            // YYYY-MM
            calendar.gotoDate(e.target.value + '-01');
        }
    });

});
</script>
@endpush

