@extends('layouts.global')

@section('title', 'Dashboard Warga')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h2 class="fw-bold text-success mb-1">Selamat Datang, {{ Auth::user()->name ?? 'Bapak/Ibu' }}</h2>
        <p class="text-muted mb-0">Kepala Keluarga - RW 21 Desa Tanimulya</p>
    </div>
    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold">
        <i class="bi bi-check-circle me-1"></i> STATUS: AKTIF
    </span>
</div>

@if(isset($pendingUmkmListCount) && ($pendingUmkmListCount) > 0)
    <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-3" style="background-color: #ffffff;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background-color: rgba(245, 158, 11, 0.2); width: 42px; height: 42px;">
                <i class="bi bi-clock-history text-warning fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">Pendaftaran Usaha Anda Sedang Ditinjau RW</h6>
                <p class="text-muted small mb-0">Terdapat <strong>{{ $pendingUmkmListCount }}</strong> usaha yang Anda daftarkan berstatus Pending dan menunggu verifikasi dari Pengurus RW.</p>
            </div>
        </div>
        <a href="{{ route('warga.umkm.kelola') }}" class="btn btn-warning btn-sm text-light fw-bold rounded-pill px-3 py-2 text-nowrap shadow-sm">
            <i class="bi bi-shop me-1"></i> Cek di Kelola UMKM
        </a>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.surat.index') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-file-earmark-plus mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Ajukan Surat<br>Keterangan</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.surat.index') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-search mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Cek Status Surat<br>Keterangan</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('transparansi') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-cash-coin mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Laporan Kas &<br>Iuran Warga</span>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('warga.umkm.kelola') }}" class="text-decoration-none text-dark">
            <div class="card card-custom action-card text-center p-3 h-100 shadow-sm border-0">
                <i class="bi bi-shop mb-2 fs-3 text-success"></i>
                <span class="fw-semibold small">Kelola Produk<br>Saya (UMKM)</span>
            </div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Data Anggota Keluarga</h5>
            <a href="{{ route('warga.keluarga.index') }}" class="text-success text-decoration-none small fw-semibold">+ Kelola Keluarga</a>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card card-custom p-3 h-100 d-flex flex-row align-items-center gap-3 shadow-sm border-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Kepala Keluarga') }}&background=random" class="rounded-circle" width="48" height="48">
                    <div>
                        <h6 class="mb-0 fw-bold">{{ Auth::user()->name ?? 'Kepala Keluarga' }}</h6>
                        <small class="text-muted d-block">Kepala Keluarga</small>
                        <small class="text-muted" style="font-size: 0.75rem;">Status: Terverifikasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-custom p-3 h-100 d-flex flex-row align-items-center gap-3 bg-light shadow-sm border-0">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people text-secondary fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">Daftar Tanggungan</h6>
                        <small class="text-muted d-block">Lihat anggota keluarga lainnya</small>
                        <a href="{{ route('warga.keluarga.index') }}" class="text-success text-decoration-none" style="font-size: 0.75rem;">Kelola Data &rarr;</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Transparansi Keuangan RW 21</h5>
            <a href="{{ route('transparansi') }}" class="text-success text-decoration-none small fw-semibold">Lihat Detail</a>
        </div>
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">DANA KEMATIAN</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Aktif / Transparan</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">KAS RT & RW</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Terbuka</h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom p-3 bg-success bg-opacity-10 h-100 border-0 shadow-sm">
                    <small class="text-success fw-bold mb-2">LAPORAN</small>
                    <h4 class="fw-bold text-success mb-0 fs-5">Unduh PDF</h4>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mb-3">
            <h5 class="fw-bold mb-0">Update Berita Terkini</h5>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('warga.berita.index') }}" class="badge bg-success rounded-pill px-3 py-2 text-decoration-none text-white">Semua Berita</a>
            </div>
        </div>
        
        @if(isset($beritaTerkini) && $beritaTerkini->count() > 0)
            @foreach($beritaTerkini as $berita)
            <a href="{{ route('warga.berita.index') }}?open={{ $berita->id }}&type=Berita" class="text-decoration-none">
                <div class="card card-custom mb-3 overflow-hidden shadow-sm border-0 transition-hover">
                    <div class="row g-0">
                        <div class="col-md-3 bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 140px; position: relative;">
                            @if($berita->featured_image)
                                <img src="{{ asset($berita->featured_image) }}" alt="Berita" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;">
                            @else
                                <i class="bi bi-newspaper text-muted fs-1 p-4"></i>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge bg-light text-secondary border text-uppercase">{{ $berita->kategori ?? 'Berita' }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($berita->created_at)->diffForHumans() }}</small>
                                </div>
                                <h6 class="fw-bold text-dark">{{ $berita->judul_berita }}</h6>
                                <p class="card-text text-muted small mb-0">{{ Str::limit(strip_tags($berita->isi_berita), 80) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        @else
            <div class="text-center py-4 text-muted border rounded-3 bg-light mb-4">
                <small>Belum ada update berita terkini.</small>
            </div>
        @endif

    </div>

    <div class="col-lg-4">
        {{-- Sorotan Pengumuman (Carousel) --}}
        <div class="card card-custom p-0 mb-4 shadow-sm border-0 position-relative" style="border-radius: 16px;">
            <div class="p-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-stars me-2"></i>Sorotan Pengumuman</h5>
                <a href="{{ route('warga.berita.index') }}?tab=pengumuman" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 text-decoration-none border border-success border-opacity-25" style="font-size: 0.85rem;">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

            @if(!isset($pengumumanPin) || $pengumumanPin->isEmpty())
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
                            @php
                                $isPenting = $pin->is_priority;
                                $bgStyle = $isPenting ? 'background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);' : 'background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);';
                                $iconColor = $isPenting ? 'text-danger' : 'text-success';
                            @endphp
                            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                <div class="p-4 text-center d-flex flex-column justify-content-center position-relative" style="{{ $bgStyle }} min-height: 180px;">
                                    @if($isPenting)
                                        <span class="position-absolute top-0 end-0 mt-3 me-3 badge bg-danger rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.5px;">PENTING</span>
                                    @endif
                                    
                                    <div class="mb-3 {{ $iconColor }}" style="font-size: 2.5rem; opacity: 0.8;">
                                        <i class="bi bi-megaphone-fill"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="line-height: 1.4;">{{ $pin->judul_pengumuman }}</h6>
                                    <p class="text-muted small mb-3">
                                        {{ \Carbon\Carbon::parse($pin->tanggal_publish)->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}
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
                <a href="{{ route('warga.agenda.index') }}" class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 text-decoration-none border border-success border-opacity-25" style="font-size: 0.85rem;">Lihat Semua <i class="bi bi-arrow-right ms-1"></i></a>
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
            <h6 class="fw-bold text-dark mb-3">Agenda Terdekat (7 Hari Kedepan)</h6>
            <div id="upcoming-agenda-list" class="d-flex flex-column gap-3">
                <!-- Dynamically populated by JS -->
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3 mt-2">
    <div>
        <h5 class="fw-bold mb-1">Pojok UMKM</h5>
        <p class="text-muted small mb-0">Dukung potensi usaha mandiri warga RW 21 Tanimulya</p>
    </div>
    <a href="{{ route('warga.umkm.galeri') }}" class="text-success text-decoration-none small fw-semibold">Lihat Semua <i class="bi bi-arrow-right"></i></a>
</div>
@include('warga.umkm.components.carouselProduct', [
    'items' => $daftarProdukTerbaru,
    'showEmpty' => true,
    'showOwner' => true
])

@include('warga.umkm.components.footerUmkm')

@push('styles')
<style>
    /* Carousel Custom Style (Mirrored from OP Konten) */
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
        
        // Find next 4 agendas that are >= today and <= 7 days from today, and NOT on selectedDateStr
        let count = 0;
        const sortedDates = Object.keys(agendaMap).sort();
        
        const nextWeekDate = new Date();
        nextWeekDate.setDate(nextWeekDate.getDate() + 7);
        const nextWeekStr = nextWeekDate.getFullYear() + '-' + String(nextWeekDate.getMonth()+1).padStart(2, '0') + '-' + String(nextWeekDate.getDate()).padStart(2, '0');
        
        for (const dateStr of sortedDates) {
            if (dateStr >= todayStr && dateStr <= nextWeekStr && dateStr !== selectedDateStr) {
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
