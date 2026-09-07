@extends('layouts.global')

@section('title', 'Dashboard Operator Konten')

@push('styles')
<style>
    /* Bento CSS */
    .bento-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .bento-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        border-color: #D1D5DB;
    }
    .bento-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    /* Table Enhancements */
    .thead-green-gradient {
        background: linear-gradient(135deg, #2E7D32 0%, #4CAF50 100%) !important;
    }
    .thead-green-gradient th {
        background-color: transparent !important;
        color: white !important;
        border: none !important;
        font-weight: 600;
    }
    .table-modern td {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.2s ease;
    }
    .table-modern tbody tr:hover td {
        background-color: #f8fafc;
    }
    .table-scrollable {
        max-height: 380px;
        overflow-y: auto;
    }
    .table-scrollable::-webkit-scrollbar { width: 6px; }
    .table-scrollable::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .table-scrollable::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
    .table-scrollable::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }

    /* Modern Badges for Content Types */
    .badge-type-berita { background-color: #E0F2FE; color: #0284C7; }
    .badge-type-agenda { background-color: #FFEDD5; color: #EA580C; }
    .badge-type-pengumuman { background-color: #F3E8FF; color: #9333EA; }

    /* Carousel Custom Style */
    .carousel-pinned {
        border-radius: 16px;
        overflow: hidden;
    }
    .carousel-pinned .carousel-indicators {
        bottom: 5px;
    }
    .carousel-pinned .carousel-indicators [data-bs-target] {
        background-color: #d1d5db;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        border: none;
        margin: 0 4px;
        opacity: 0.6;
    }
    .carousel-pinned .carousel-indicators .active {
        background-color: #198754;
        opacity: 1;
        width: 12px;
        border-radius: 4px;
    }
    /* Sleek glassy arrows */
    .carousel-control-prev, .carousel-control-next {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(4px);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .carousel-pinned:hover .carousel-control-prev, 
    .carousel-pinned:hover .carousel-control-next {
        opacity: 1;
    }
    .carousel-control-prev { left: 10px; }
    .carousel-control-next { right: 10px; }
    .carousel-control-prev-icon, .carousel-control-next-icon {
        width: 1.2rem;
        height: 1.2rem;
        filter: invert(40%) sepia(85%) saturate(1324%) hue-rotate(119deg) brightness(92%) contrast(85%); /* Green icon */
    }

    /* Custom Calendar CSS */
    .cal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .cal-btn {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        color: #495057;
    }
    .cal-btn:hover {
        background: #e9ecef;
    }
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 5px;
        text-align: center;
    }
    .cal-day-header {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .cal-date {
        height: 38px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        position: relative;
        color: #212529;
        transition: all 0.2s;
    }
    .cal-date:hover:not(.empty) {
        background-color: #f1f5f9;
    }
    .cal-date.empty {
        cursor: default;
    }
    .cal-date.has-event-future {
        font-weight: 700;
        color: #198754;
    }
    .cal-date.has-event-past {
        color: #9ca3af;
    }
    .cal-date.active {
        background-color: #198754 !important;
        color: #fff !important;
        box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
    }
    .cal-date.active .cal-dot {
        background-color: #fff !important;
    }
    .cal-date.today:not(.active) {
        color: #198754;
        font-weight: 700;
        border: 1px solid rgba(25, 135, 84, 0.3);
    }
    .cal-dot {
        width: 4px;
        height: 4px;
        background-color: #198754;
        border-radius: 50%;
        position: absolute;
        bottom: 4px;
    }
    .cal-dot.past {
        background-color: #6c757d;
    }
    .cal-date.active .cal-dot.past {
        background-color: #e9ecef !important;
    }
</style>
@endpush

@section('content')
{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Dasbor</h2>
        <p class="text-muted mb-0">Gambaran umum status konten dan aktivitas terkini.</p>
    </div>
</div>

{{-- Top 4 Stat Cards (Bento Style) --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="bento-card h-100">
            <div class="bento-icon" style="background: rgba(46, 125, 50, 0.10); color: #007232;">
                <i class="bi bi-file-earmark-text fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">Berita Terbit</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ number_format($stats['berita_aktif']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="bento-card h-100">
            <div class="bento-icon" style="background: rgba(13, 110, 253, 0.10); color: #0d6efd;">
                <i class="bi bi-megaphone fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">Pengumuman Aktif</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ number_format($stats['pengumuman_aktif']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="bento-card h-100">
            <div class="bento-icon" style="background: rgba(46, 125, 50, 0.10); color: #007232;">
                <i class="bi bi-calendar-event fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #40493D; font-size: 11px; letter-spacing: 0.6px;">Agenda Mendatang</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ number_format($stats['agenda_mendatang']) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="bento-card h-100 position-relative">
            <div class="bento-icon" style="background: #FEE2E2; color: #991B1B;">
                <i class="bi bi-exclamation-triangle fs-5"></i>
            </div>
            <div>
                <div class="fw-medium text-uppercase mb-1" style="color: #991B1B; font-size: 11px; letter-spacing: 0.6px;">Butuh Tindakan</div>
                <div class="fw-bold text-dark" style="font-size: 20px; line-height: 1;">{{ number_format($stats['butuh_tindakan']) }}</div>
            </div>
            @if($stats['butuh_tindakan'] > 0)
                <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                    <span class="visually-hidden">New alerts</span>
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Main Grid --}}
<div class="row g-4">
    {{-- Left Column --}}
    <div class="col-lg-8 d-flex flex-column gap-4">
        
        {{-- Antrean Revisi --}}
        <div class="card card-custom p-0 shadow-sm border-0 overflow-hidden" style="border-radius: 16px;">
            <div class="p-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-danger mb-0"><i class="bi bi-journal-x me-2"></i>Antrean Revisi</h5>
            </div>

            @if($antreanRevisi->isEmpty())
                <div class="text-center py-5 text-muted bg-light">
                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-success"></i>
                    Kerja bagus! Tidak ada konten yang butuh direvisi.
                </div>
            @else
                <div class="table-responsive table-scrollable">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="thead-green-gradient position-sticky top-0 z-1" style="z-index: 10;">
                            <tr class="text-uppercase text-xs" style="letter-spacing: 0.5px;">
                                <th scope="col" class="py-3 text-center">Tipe</th>
                                <th scope="col" class="py-3 text-center">Judul</th>
                                <th scope="col" class="py-3 text-center">Catatan Revisi</th>
                                <th scope="col" class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antreanRevisi as $item)
                                @php
                                    $typeBadge = 'bg-secondary text-white';
                                    if(strtolower($item['type']) == 'berita') $typeBadge = 'badge-type-berita';
                                    elseif(strtolower($item['type']) == 'agenda') $typeBadge = 'badge-type-agenda';
                                    elseif(strtolower($item['type']) == 'pengumuman') $typeBadge = 'badge-type-pengumuman';
                                @endphp
                                <tr>
                                    <td class="py-3 text-center">
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold {{ $typeBadge }}" style="font-size: 0.75rem;">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark py-3 small text-center">{{ Str::limit($item['title'], 35) }}</td>
                                    <td class="text-danger small py-3 fst-italic text-center">{{ Str::limit($item['note'], 40) }}</td>
                                    <td class="text-center py-3">
                                        <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold" style="font-size: 0.8rem;">Perbaiki</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Aktivitas Terkini --}}
        <div class="card card-custom p-0 shadow-sm border-0 overflow-hidden" style="border-radius: 16px;">
            <div class="p-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Terkini</h5>
            </div>

            @if($aktivitasTerkini->isEmpty())
                <div class="text-center py-5 text-muted bg-light">
                    <p class="mb-0">Belum ada aktivitas konten.</p>
                </div>
            @else
                <div class="table-responsive table-scrollable">
                    <table class="table table-modern align-middle mb-0">
                        <thead class="thead-green-gradient position-sticky top-0 z-1" style="z-index: 10;">
                            <tr class="text-uppercase text-xs" style="letter-spacing: 0.5px;">
                                <th scope="col" class="py-3 text-center">Tipe</th>
                                <th scope="col" class="py-3 text-center">Judul</th>
                                <th scope="col" class="py-3 text-center">Status</th>
                                <th scope="col" class="py-3 text-center">Pembaruan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aktivitasTerkini as $item)
                                @php
                                    $statusColors = [
                                        'Draft' => ['bg' => '#F3F4F6', 'color' => '#6B7280', 'label' => 'Draf'],
                                        'Review' => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Ditinjau'], 
                                        'Revisi' => ['bg' => '#FEE2E2', 'color' => '#991B1B', 'label' => 'Revisi'], 
                                        'Publish' => ['bg' => '#D1FAE5', 'color' => '#065F46', 'label' => 'Terbit'], 
                                    ];
                                    $s_bg = $statusColors[$item['status']]['bg'] ?? '#F3F4F6';
                                    $s_color = $statusColors[$item['status']]['color'] ?? '#6B7280';
                                    $s_label = $statusColors[$item['status']]['label'] ?? $item['status'];

                                    $typeBadge = 'bg-secondary text-white';
                                    if(strtolower($item['type']) == 'berita') $typeBadge = 'badge-type-berita';
                                    elseif(strtolower($item['type']) == 'agenda') $typeBadge = 'badge-type-agenda';
                                    elseif(strtolower($item['type']) == 'pengumuman') $typeBadge = 'badge-type-pengumuman';
                                @endphp
                                <tr>
                                    <td class="py-3 text-center">
                                        <span class="badge rounded-pill px-3 py-1 fw-semibold {{ $typeBadge }}" style="font-size: 0.75rem;">
                                            {{ $item['type'] }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark py-3 small text-center">{{ Str::limit($item['title'], 40) }}</td>
                                    <td class="py-3 text-center">
                                        <span class="badge rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-1" style="background: {{ $s_bg }}; color: {{ $s_color }};">
                                            <span class="rounded-circle" style="width: 6px; height: 6px; display: inline-block; background: {{ $s_color }};"></span>
                                            {{ $s_label }}
                                        </span>
                                    </td>
                                    <td class="text-muted small py-3 text-center">{{ \Carbon\Carbon::parse($item['updated_at'])->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4 d-flex flex-column gap-4">
        
        {{-- Sorotan Pengumuman (Carousel) --}}
        <div class="card card-custom p-0 shadow-sm border-0 position-relative" style="border-radius: 16px;">
            <div class="p-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-stars me-2"></i>Sorotan Pengumuman</h5>
            </div>

            @if($pengumumanPin->isEmpty())
                <div class="text-center py-4 text-muted mx-4 mb-4 rounded-3 bg-light">
                    <small>Tidak ada sorotan pengumuman.</small>
                </div>
            @else
                <div id="carouselPengumuman" class="carousel slide carousel-pinned mx-4 mb-4 mt-2" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($pengumumanPin as $index => $pin)
                            <button type="button" data-bs-target="#carouselPengumuman" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner" style="border-radius: 12px;">
                        @foreach($pengumumanPin as $index => $pin)
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="p-4 text-center d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); min-height: 180px;">
                                    <div class="mb-3 text-success" style="font-size: 2.5rem; opacity: 0.8;">
                                        <i class="bi bi-megaphone-fill"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $pin->judul_pengumuman }}</h6>
                                    <p class="text-muted small mb-3">
                                        {{ \Carbon\Carbon::parse($pin->tanggal_publish)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPengumuman" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselPengumuman" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Agenda Mendatang (Kalender Interaktif & List) --}}
        <div class="card card-custom p-4 shadow-sm border-0" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-calendar-week me-2"></i>Kalender Agenda</h5>
                <a href="{{ route('opkonten.agenda.index') }}" class="text-success text-decoration-none small fw-semibold">Kelola</a>
            </div>

            {{-- Calendar Widget --}}
            <div class="calendar-widget mb-3">
                <div class="cal-header">
                    <button type="button" class="cal-btn" id="cal-prev"><i class="bi bi-chevron-left"></i></button>
                    <div class="fw-bold text-dark fs-6" id="cal-month-year">September 2026</div>
                    <button type="button" class="cal-btn" id="cal-next"><i class="bi bi-chevron-right"></i></button>
                </div>
                <div class="cal-grid">
                    <div class="cal-day-header">Min</div>
                    <div class="cal-day-header">Sen</div>
                    <div class="cal-day-header">Sel</div>
                    <div class="cal-day-header">Rab</div>
                    <div class="cal-day-header">Kam</div>
                    <div class="cal-day-header">Jum</div>
                    <div class="cal-day-header">Sab</div>
                </div>
                <div class="cal-grid" id="cal-days"></div>
            </div>

            {{-- Agenda List below calendar (Selected Date) --}}
            <div id="cal-agenda-list" class="d-flex flex-column gap-2 mb-4">
                <div class="text-center text-muted small py-2">Pilih tanggal untuk melihat agenda</div>
            </div>

            {{-- Separator --}}
            <hr class="border-secondary border-opacity-25 mb-4">

            {{-- Agenda Terdekat List --}}
            <h6 class="fw-bold text-dark mb-3">Agenda Terdekat Lainnya</h6>
            <div id="upcoming-agenda-list" class="d-flex flex-column gap-3">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawAgenda = {!! json_encode($semuaAgenda) !!};
    
    // Convert to easier lookup format: { "2026-09-08": [ {agenda} ] }
    const agendaMap = {};
    rawAgenda.forEach(item => {
        if(item.date_str) {
            const dateStr = item.date_str;
            if(!agendaMap[dateStr]) agendaMap[dateStr] = [];
            agendaMap[dateStr].push(item);
        }
    });

    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    let currentDate = new Date(); // Actual current date
    
    const calDaysContainer = document.getElementById('cal-days');
    const calMonthYear = document.getElementById('cal-month-year');
    const agendaListContainer = document.getElementById('cal-agenda-list');
    const upcomingListContainer = document.getElementById('upcoming-agenda-list');
    let selectedDateStr = null;

    // Get strictly local today string (YYYY-MM-DD) avoiding timezone shifts
    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    function renderCalendar() {
        calDaysContainer.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        calMonthYear.textContent = `${monthNames[month]} ${year}`;
        
        const firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        // Add empty cells for days before the 1st
        for (let i = 0; i < firstDay; i++) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'cal-date empty';
            calDaysContainer.appendChild(emptyDiv);
        }
        
        // Add days
        for (let i = 1; i <= daysInMonth; i++) {
            const dateDiv = document.createElement('div');
            dateDiv.className = 'cal-date';
            dateDiv.textContent = i;
            
            const loopDateStr = `${year}-${String(month+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            dateDiv.dataset.date = loopDateStr;
            
            if (loopDateStr === todayStr) {
                dateDiv.classList.add('today');
            }
            if (loopDateStr === selectedDateStr) {
                dateDiv.classList.add('active');
            }
            
            // Check if there's agenda on this date
            if (agendaMap[loopDateStr] && agendaMap[loopDateStr].length > 0) {
                const dot = document.createElement('div');
                dot.className = 'cal-dot';
                
                // If the date is past, make the dot gray and text gray
                if (loopDateStr < todayStr) {
                    dot.classList.add('past');
                    dateDiv.classList.add('has-event-past'); // use class instead of inline style
                } else {
                    dateDiv.classList.add('has-event-future'); // use class instead of inline style
                }
                dateDiv.appendChild(dot);
            }
            
            dateDiv.addEventListener('click', function() {
                // Remove active class from all
                document.querySelectorAll('.cal-date').forEach(el => el.classList.remove('active'));
                this.classList.add('active');
                selectedDateStr = this.dataset.date;
                showAgendasForDate(selectedDateStr);
                renderUpcomingAgendas(); // update upcoming to exclude selected date
            });
            
            calDaysContainer.appendChild(dateDiv);
        }
    }

    function showAgendasForDate(dateStr) {
        agendaListContainer.innerHTML = '';
        
        // Format the display date
        const dParts = dateStr.split('-');
        const displayDate = `${parseInt(dParts[2])} ${monthNames[parseInt(dParts[1])-1]} ${dParts[0]}`;
        
        if (!agendaMap[dateStr] || agendaMap[dateStr].length === 0) {
            agendaListContainer.innerHTML = `<div class="p-3 rounded-3 bg-light border border-light-subtle text-center text-muted small">Tidak ada agenda pada ${displayDate}</div>`;
            return;
        }
        
        const header = document.createElement('div');
        header.className = 'fw-bold text-dark small mb-2';
        header.textContent = `Agenda Terjadwal (${displayDate}):`;
        agendaListContainer.appendChild(header);
        
        agendaMap[dateStr].forEach(agenda => {
            const card = document.createElement('div');
            // If past event, use gray styling, else green
            const isPast = dateStr < todayStr;
            const borderColor = isPast ? 'border-secondary' : 'border-success';
            const bgColor = isPast ? 'bg-secondary' : 'bg-success';
            
            const timeShort = agenda.time_str || '';

            card.className = `p-2 rounded-3 bg-white border ${borderColor} border-opacity-25 shadow-sm d-flex align-items-center gap-2 mb-2`;
            card.innerHTML = `
                <div class="${bgColor} text-white rounded px-2 py-1 flex-shrink-0 fw-bold" style="font-size: 0.75rem;">
                    ${timeShort}
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem; line-height: 1.2;">${agenda.judul_agenda}</h6>
                    <span class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-geo-alt"></i> ${agenda.lokasi}</span>
                </div>
            `;
            agendaListContainer.appendChild(card);
        });
    }

    function renderUpcomingAgendas() {
        upcomingListContainer.innerHTML = '';
        
        // Find next 4 agendas that are >= today and NOT on selectedDateStr
        let count = 0;
        const sortedDates = Object.keys(agendaMap).sort();
        
        for (const dateStr of sortedDates) {
            if (dateStr >= todayStr && dateStr !== selectedDateStr) {
                agendaMap[dateStr].forEach(agenda => {
                    if (count >= 4) return;
                    
                    const card = document.createElement('div');
                    card.className = 'p-3 rounded-3 bg-white border border-success border-opacity-25 shadow-sm transition-hover';
                    
                    // Simple title truncation
                    let judul = agenda.judul_agenda;
                    if(judul.length > 40) judul = judul.substring(0, 40) + '...';
                    
                    let lokasi = agenda.lokasi;
                    if(lokasi.length > 25) lokasi = lokasi.substring(0, 25) + '...';

                    card.innerHTML = `
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-3 text-center p-2 flex-shrink-0" style="min-width: 65px;">
                                <span class="d-block text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">${agenda.month_short}</span>
                                <span class="d-block fw-bold fs-3" style="line-height: 1;">${agenda.day_num}</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1 small" style="line-height: 1.4;">${judul}</h6>
                                <p class="text-muted mb-0 d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                    <i class="bi bi-geo-alt-fill text-success"></i> ${lokasi}
                                </p>
                            </div>
                        </div>
                    `;
                    upcomingListContainer.appendChild(card);
                    count++;
                });
            }
            if(count >= 4) break;
        }

        if (count === 0) {
            upcomingListContainer.innerHTML = `
                <div class="text-center py-3 text-muted border rounded-3 bg-light">
                    <p class="mb-0 small">Tidak ada agenda mendatang lainnya.</p>
                </div>
            `;
        }
    }

    document.getElementById('cal-prev').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    
    document.getElementById('cal-next').addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Initial logic
    let autoSelectDate = null;
    const sortedDates = Object.keys(agendaMap).sort();
    for (const d of sortedDates) {
        if (d >= todayStr) {
            autoSelectDate = d;
            break;
        }
    }
    
    if (autoSelectDate) {
        selectedDateStr = autoSelectDate;
    } else if (sortedDates.length > 0) {
        selectedDateStr = sortedDates[sortedDates.length - 1]; // last past event
    } else {
        selectedDateStr = todayStr; // today
    }
    
    // Highlight and show
    renderCalendar();
    showAgendasForDate(selectedDateStr);
    renderUpcomingAgendas();
});
</script>
@endpush
