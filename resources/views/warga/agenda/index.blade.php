@extends('layouts.global')

@section('title', 'Agenda Warga')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.css" rel="stylesheet">
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
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Calendar Container -->
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
                <div id="modalDeskripsiContainer" class="detail-row">
                    <div class="icon-box bg-warning bg-opacity-10">
                        <i class="bi bi-text-paragraph text-warning"></i>
                    </div>
                    <div>
                        <div class="detail-label">Keterangan</div>
                        <div class="detail-value fw-normal" id="modalDeskripsi" style="font-size: 0.85rem; color: #4b5563;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
            document.getElementById('modalTanggal').textContent = props.tanggal_mulai_formatted || '-';
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

            // Deskripsi
            const descContainer = document.getElementById('modalDeskripsiContainer');
            const descEl = document.getElementById('modalDeskripsi');
            if (props.deskripsi) {
                descEl.innerHTML = props.deskripsi;
                descContainer.style.display = 'flex';
            } else {
                descContainer.style.display = 'none';
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
    calendar.on('datesSet', updateHeader);

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
